<?php
/**
 * @package 	Helix_Ultimate_Framework
 * @author 		JoomShaper <joomshaper@js.com>
 * @copyright 	Copyright (c) 2010 - 2020 JoomShaper
 * @license 	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
namespace HelixUltimate\Framework\HttpResponse;

use HelixUltimate\Framework\Platform\Builders\MegaMenuBuilder;
use HelixUltimate\Framework\Platform\Helper;
use HelixUltimate\Framework\System\JoomlaBridge;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Menu\SiteMenu;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die();

/**
 * Class for Ajax Http Response.
 * This is handle Ajax request.
 *
 * @since   1.0.0
 */
class Response
{
	/**
	 * Response for the request getMenuItems.
	 *
	 * @return	array
	 * @since	2.0.0
	 */
	public static function getMenuItems()
	{
		$input = Factory::getApplication()->input;
		$menuType = $input->get('menutype', 'mainmenu', 'STRING');

		$items = self::getItems($menuType);

		return [
			'status' => true,
			'data' => self::generateMenuTree($items),
			'items' => $items
		];
	}

	/**
	 * Response for the request parentAdoption
	 *
	 * @return	array
	 * @since	2.0.0
	 */
	public static function parentAdoption()
	{
		$input = Factory::getApplication()->input;
		$itemId = $input->post->get('id', 0, 'INT');
		$parentId = $input->post->get('parent', 0, 'INT');

		if ($itemId > 0 && $parentId > 0)
		{
			$data = new \stdClass;
			$data->id = $itemId;
			$data->parent_id = $parentId;

			try
			{
				$itemModel = self::getMenuItemModel();
				$db = Factory::getDbo();
				$db->updateObject('#__menu', $data, 'id');

				$itemModel->rebuild();
			}
			catch (Exception $e)
			{
				echo $e->getMessage();
			}
		}

		return [
			'status' => true,
			'data' => [$data]
		];
	}

	/**
	 * Get menu item model instance.
	 *
	 * @return	ItemModel
	 * @since	2.0.0
	 */
	private static function getMenuItemModel()
	{
		if (JoomlaBridge::getVersion('major') < 4)
		{
			$classUrl = JPATH_ADMINISTRATOR . '/components/com_menus/models/item.php';
			$tablePath = JPATH_ADMINISTRATOR . '/components/com_menus/tables';
		}

		if (JoomlaBridge::getVersion('major') < 4)
		{
			if (!\class_exists('MenusModelItem') && \file_exists($classUrl))
			{
				require_once $classUrl;
			}

			Table::addIncludePath($tablePath);
		}


		return JoomlaBridge::getVersion('major') >= 4
			? new \Joomla\Component\Menus\Administrator\Model\ItemModel
			: new \MenusModelItem;
	}

	/**
	 * Rebuild the menu tree
	 *
	 * @return	void
	 * @since	2.0.0
	 */
	public static function rebuildMenu()
	{
		try
		{
			$itemModel = self::getMenuItemModel();
			$itemModel->rebuild();
		}
		catch (\Exception $e)
		{
			return [
				'status' => false,
				'message' => $e->getMessage()
			];
		}

		return [
			'status' => true,
			'message' => 'Rebuilding done'
		];
	}

	/**
	 * Get Menu Items for a specific menu type
	 *
	 * @return	string
	 * @since	2.0.0
	 */
	private static function getItems($menuType)
	{
		$items = [];

		try
		{
			$db 	= Factory::getDbo();
			$query 	= $db->getQuery(true);

			$query->select('id, title, menutype, alias, parent_id, level, lft, rgt, published')
				->from($db->qn('#__menu'))
				->where($db->qn('menutype') . ' = ' . $db->q($menuType))
				->where($db->qn('published') . ' IN (0,1)');
			$query->order($db->qn('lft') . ' ASC');

			$db->setQuery($query);

			$items = $db->loadObjectList();
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}

		return $items;
	}

	/**
	 * Generate Menu Item Tree
	 *
	 * @param	array	$items	The items array.
	 *
	 * @return 	string	The HTML string.
	 * @since	2.0.0
	 */
	private static function generateMenuTree($items)
	{
		$html = [];

		if (!empty($items))
		{
			$editSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16"><path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456l-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/><path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/></svg>';

			$deleteSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4L4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg>';

			$megaSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" class="bi bi-grid-1x2" viewBox="0 0 16 16"><path d="M6 1H1v14h5V1zm9 0h-5v5h5V1zm0 9v5h-5v-5h5zM0 1a1 1 0 0 1 1-1h5a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1V1zm9 0a1 1 0 0 1 1-1h5a1 1 0 0 1 1 1v5a1 1 0 0 1-1 1h-5a1 1 0 0 1-1-1V1zm1 8a1 1 0 0 0-1 1v5a1 1 0 0 0 1 1h5a1 1 0 0 0 1-1v-5a1 1 0 0 0-1-1h-5z"/></svg>';

			$settingsSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" class="bi bi-gear" viewBox="0 0 16 16"><path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z"/><path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115l.094-.319z"/></svg>';

			$html[] = '<ul id="hu-menu-tree">';
			$count = count($items) ?? 0;

			foreach ($items as $key => $item)
			{
				$html[] = '<li class="hu-menu-tree-branch hu-branch-level-' . $item->level . ' ' . ((int) $item->published === 0 ? 'hu-megamenu-branch-muted' : '') . '" data-alias="' .  $item->alias . '" data-itemid="' . $item->id . '" data-parent="' . $item->parent_id . '" style="z-index: ' . (max(1, $count - $key)) . '" >';
				$html[] = '	<div class="hu-menu-tree-contents">';
				$html[] = '		<span class="hu-menu-branch-path"></span>';
				$html[] = '		<div class="hu-branch-drag-handler">';
				$html[] = '			<span class="hu-branch-icon"><svg width="6" height="10" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx=".904" cy=".904" r=".904" /><circle cx=".904" cy="4.7" r=".904" /><circle cx=".904" cy="8.496" r=".904" /><circle cx="4.7" cy=".904" r=".904" /><circle cx="4.7" cy="4.7" r=".904" /><circle cx="4.7" cy="8.496" r=".904" /></svg></span>';
				$html[] = '			<span class="hu-branch-title">' . $item->title . '</span>';

				if ((int) $item->published === 0)
				{
					$html[] = '<span class="hu-branch-unpublished far fa-eye-slash" title="' . Text::_('Unpublished') . '"></span>';
				}

				$html[] = '			<div class="hu-branch-tools">';
				$html[] = '				<ul class="hu-branch-tools-list">';
				$html[] = '					<li><a href="#" class="hu-branch-tools-list-edit" aria-label="' . Text::_('HELIX_ULTIMATE_MENU_EDIT') .'" title="' . Text::_('HELIX_ULTIMATE_MENU_EDIT') .'">'. $editSvg .'</a></li>';
				$html[] = '					<li><a href="#" class="hu-branch-tools-list-delete" aira-label="'. Text::_('HELIX_ULTIMATE_MENU_DELETE') .'" title="'. Text::_('HELIX_ULTIMATE_MENU_DELETE') .'">'. $deleteSvg .'</a></li>';
				$html[] = '					<li><a href="#" class="hu-branch-tools-list-megamenu disabled" aria-label="'. Text::_($item->parent_id > 1 ? 'HELIX_ULTIMATE_MENU_OPTIONS': 'HELIX_ULTIMATE_MENU_MEGAMENU') .'" title="'. Text::_($item->parent_id > 1 ? 'HELIX_ULTIMATE_MENU_OPTIONS': 'HELIX_ULTIMATE_MENU_MEGAMENU') .'"> ' . ($item->parent_id > 1 ? $settingsSvg  : $megaSvg ) .'</a></li>';
				$html[] = '				</ul>';
				$html[] = '			</div>';

				$html[] = '		</div>';
				$html[] = '	</div>';
				$html[] = '<div class="hu-menu-children-bus"></div>';
				$html[] = '</li>';
			}

			$html[] = '</ul>';
		}

		return implode("\n", $html);
	}

	/**
	 * Generate mega menu builder body contents.
	 *
	 * @param	int		$itemId	The Menu Item ID.
	 *
	 * @return 	array	The HTML string.
	 * @since	2.0.0
	 */
	public static function generateMegaMenuBody()
	{
		$input = Factory::getApplication()->input;
		$itemId = $input->get('id', 0, 'INT');
		$layout = new FileLayout('megaMenu.container', HELIX_LAYOUT_PATH);
		$builder = new MegaMenuBuilder($itemId);

		return [
			'status' => true,
			'html' => $layout->render(['itemId' => $itemId, 'builder' => $builder])
		];
	}

	/**
	 * Save mega menu settings.
	 *
	 * @return	array	The response array.
	 * @since	2.0.0
	 */
	public static function saveMegaMenuSettings()
	{
		$input = Factory::getApplication()->input;
		$settings = $input->post->get('settings', [], 'ARRAY');
		$itemId = $input->post->get('id', 0, 'INT');

		$menu = new SiteMenu;
		$item = $menu->getItem($itemId);
		$params = $item->getParams();
		$params->set('helixultimatemenulayout', \json_encode($settings));

		$response = self::updateMenuItem($itemId, $params);

		return [
			'status' => true,
			'data' => $response
		];
	}

	private static function updateMenuItem($itemId, $params)
	{
		try
		{
			$data = new \stdClass;
			$data->id = $itemId;
			$data->params = $params->toString();
			$db = Factory::getDbo();
			$db->updateObject('#__menu', $data, 'id', true);

			return true;
		}
		catch (Exception $e)
		{
			return $e->getMessage();
		}
	}

	/**
	 * Load slots for the rows.
	 *
	 * @return	array	The response array
	 * @since	2.0.0
	 */
	public static function updateRowLayout()
	{
		$input = Factory::getApplication()->input;
		$layout = $input->post->get('layout', '12', 'STRING');
		$rowData = $input->post->get('data', null, 'RAW');
		$rowId = $input->post->get('rowId', 0, 'INT');
		$itemId = $input->post->get('itemId', 0, 'INT');

		$rowData = \json_decode($rowData);
		$layout = \preg_replace("@\s@", '', $layout);
		$layoutArray = explode('+', $layout);

		$columns = [];

		foreach ($layoutArray as $key => $col)
		{
			if (isset($rowData->attr[$key]))
			{
				$tmp = $rowData->attr[$key];
			}
			else
			{
				$tmp = new \stdClass;
				$tmp->menuParentId = '';
				$tmp->moduleId = '';
				$tmp->type = 'column';
				$tmp->items = [];
			}

			$tmp->colGrid = $col;

			$columns[] = $tmp;
		}

		$rowData->attr = $columns;

		$columnLayout = new FileLayout('megaMenu.column', HELIX_LAYOUT_PATH);
		$builder = new MegaMenuBuilder($itemId);

		$html = [];

		foreach ($columns as $key => $column)
		{
			$html[] = $columnLayout->render([
				'itemId' => $itemId,
				'builder' => $builder,
				'column' => $column,
				'rowId' => $rowId,
				'columnId' => $key + 1
			]);
		}

		return [
			'status' => true,
			'html' => implode("\n", $html),
			'data' => $rowData
		];
	}

	/**
	 * Generate row from the user data.
	 *
	 * @return	array	the response array
	 * @since	2.0.0
	 */
	public static function generateRow()
	{
		$input = Factory::getApplication()->input;
		$layout = $input->post->get('layout', '12', 'STRING');
		$rowId = $input->post->get('rowId', 0, 'INT');
		$itemId = $input->post->get('itemId', 0, 'INT');

		$layout = \preg_replace("@\s@", '', $layout);

		$layoutArray = explode('+', $layout);

		$rowData = new \stdClass;
		$rowData->type = 'row';
		$rowData->attr = [];

		if (!empty($layoutArray))
		{
			foreach ($layoutArray as $column)
			{
				$item = new \stdClass;
				$item->type = 'column';
				$item->colGrid = $column;
				$item->items = [];
				$item->menuParentId = '';
				$item->moduleId = '';
				$rowData->attr[] = $item;
			}
		}

		$builder = new MegaMenuBuilder($itemId);
		$isNew = \count($builder->getMegaMenuSettings()->layout ?? []) === 0;

		/**
		 * If no row exists before, then get the child items of the item
		 *
		 */
		if ($isNew)
		{
			$children = $builder->getItemChildren();
			
			if (!empty($children))
			{
				$perColumn = ceil(\count($children) / \count($layoutArray));
				$chunks = \array_chunk($children, $perColumn);

				foreach ($chunks as $key => $children)
				{
					$cells = [];

					foreach ($children as $child)
					{
						$tmp = new \stdClass;
						$tmp->type = 'menu_item';
						$tmp->item_id = $child->id;
						$cells[] = $tmp;
					}

					$rowData->attr[$key]->items = $cells;
				}


			}
		}

		$rowLayout = new FileLayout('megaMenu.row', HELIX_LAYOUT_PATH);

		$rowHTML = $rowLayout->render([
			'itemId' => $itemId,
			'builder' => $builder,
			'row' => $rowData,
			'rowId' => $rowId
		]);

		return [
			'status' => true,
			'data' => $rowHTML,
			'row' => $rowData
		];
	}

	/**
	 * Generate popover for manipulating the menu item.
	 *
	 * @return	array	The response array.
	 * @since	2.0.0
	 */
	public static function generatePopoverContents()
	{
		$input = Factory::getApplication()->input;
		
		// The menu item id
		$itemId = $input->post->get('itemId', 0, 'INT');
		$type = $input->post->get('type', 'module', 'STRING');

		$builder = new MegaMenuBuilder($itemId); 

		$html = [];

		if ($type === 'module')
		{
			$modules = Helper::getModules();
			$html = [];
			$html[] = '<select class="hu-input hu-megamenu-module" data-type="module" data-husearch="1">';
			$html[] = '<option value="">Select Module</option>';
			foreach ($modules as $module)
			{
				$html[] = '<option value="' . $module->id . '">' . $module->title . '</option>';
			}

			$html[] = '</select>';
		}
		elseif ($type === 'menu')
		{
			$children = $builder->getItemChildren();
			$html = [];
			$html[] = '<select class="hu-input hu-megamenu-menuitem" data-type="menu_item">';
			$html[] = '<option value="">Select Menu Item</option>';

			foreach ($children as $child)
			{
				$html[] = '<option value="' . $child->id . '">' . $child->title . '</option>';
			}

			$html[] = '</select>';
		}


		return [
			'status' => true,
			'html' => implode("\n", $html)		
		];
	}

	public static function generateNewCell()
	{
		$input = Factory::getApplication()->input;
		
		$itemId 	= $input->post->get('itemId', 0, 'INT');
		$type 		= $input->post->get('type', 'module', 'STRING');
		$elementId 	= $input->post->get('item_id', 0, 'INT');
		$rowId 		= $input->post->get('rowId', 0, 'INT');
		$columnId 	= $input->post->get('columnId', 0, 'INT');
		$cellId 	= $input->post->get('cellId', 0, 'INT');

		$builder = new MegaMenuBuilder($itemId);

		$cell = new \stdClass;
		$cell->type = $type;
		$cell->item_id = $elementId;

		$cellLayout = new FileLayout('megaMenu.cell', HELIX_LAYOUT_PATH);
		$html = $cellLayout->render([
			'itemId' => $itemId,
			'builder' => $builder,
			'cell' => $cell,
			'rowId' => $rowId,
			'columnId' => $columnId,
			'cellId' => $cellId
		]);

		return [
			'status' => true,
			'html' => $html
		];
	}

	/**
	 * Get the module list and render the list
	 *
	 * @return	array	The response array.
	 * @since	2.0.0
	 */
	public static function getModuleList()
	{
		$input = Factory::getApplication()->input;
		$keyword = $input->get('keyword', '', 'STRING');

		$moduleLayout = new FileLayout('megaMenu.modules', HELIX_LAYOUT_PATH);

		return [
			'status' => true,
			'html' => $moduleLayout->render(['keyword' => $keyword])
		];
	}
}
