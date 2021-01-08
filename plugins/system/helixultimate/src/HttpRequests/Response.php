<?php
/**
 * @package 	Helix_Ultimate_Framework
 * @author 		JoomShaper <joomshaper@js.com>
 * @copyright 	Copyright (c) 2010 - 2020 JoomShaper
 * @license 	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
namespace HelixUltimate\Framework\HttpResponse;

use HelixUltimate\Framework\Platform\Builders\MenuBuilder;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Language\Text;
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
			'data' => self::generateMenuItemHTML($items, $items, 1),
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
		$ordering = $input->post->get('ordering', 0, 'INT');

		if ($itemId > 0 && $parentId > 0)
		{
			$data = new \stdClass;
			$data->id = $itemId;
			$data->parent_id = $parentId;

			$classUrl = JPATH_ADMINISTRATOR . '/components/com_menus/models/item.php';

			if (!\class_exists('MenusModelItem'))
			{
				require_once $classUrl;
			}

			\JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_menus/tables');

			try
			{
				$controller = new \MenusModelItem;

				if ($ordering)
				{
					$controller->saveorder();
				}
				else
				{
					$db = Factory::getDbo();
					$db->updateObject('#__menu', $data, 'id');

					$controller->rebuild();
				}
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

			$query->select('id, title, menutype, alias, parent_id, level, lft, rgt')
				->from($db->qn('#__menu'))
				->where($db->qn('menutype') . ' = ' . $db->q($menuType))
				->where($db->qn('published') . ' = 1');
			$query->order($db->qn('lft') . ' ASC');

			$db->setQuery($query);

			$items = $db->loadObjectList('id');
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}

		/**
		 * Generate children array with ID for each item
		 * Add `visited = false` for every item for future traversal.
		 */
		if (!empty($items))
		{
			foreach ($items as $itemId => $item)
			{
				$parentId = $item->parent_id;

				if (!isset($item->children))
				{
					$items[$itemId]->children = [];
				}

				/**
				 * If the item's `parent_id` exists in the items array
				 * then then add the itemId as the children of parent item.
				*/
				if (isset($items[$parentId]))
				{
					if (isset($items[$parentId]->children))
					{
						$items[$parentId]->children[] = $itemId;
					}
					else
					{
						$items[$parentId]->children = [$itemId];
					}
				}

				// Set the visited to false initially
				$items[$itemId]->visited = false;
			}
		}

		return $items;
	}

	/**
	 * Generate the HTML string from the menu Items.
	 *
	 * @param	array	$original	The original items array for reference. This will be untouched.
	 * @param	array	$items		The items array for traversal.
	 * @param	int		$container	The container id, if children exists then the inner UL get the item id as container id.
	 *
	 * @return	string	The HTML string.
	 * @since	2.0.0
	 */
	private static function generateMenuItemHTML($original, $items, $container = null, $containerLevel = 1)
	{
		$html = [];

		if (!empty($items))
		{
			$html[] = '<ul class="hu-menuitem-list" data-level="' . $containerLevel
				. '" data-container="' . $container
				. '">';

			foreach ($items as $itemId => $item)
			{
				if (!$item->visited)
				{
					$html[] = '<li class="hu-menuitem" data-itemid="' . $item->id
						. '" data-level="' . $item->level
						. '" data-parent="' . $item->parent_id . '">';
					$html[] = '<div class="contents">';
					$html[] = '<div class="hu-drag-handler">';
					$html[] = '<span class="icon fas fa-grip-vertical"></span>';
					$html[] = '<span class="hu-menuitem-title">' . $item->title . '</span>';
					$html[] = '</div>'; // End of drag handler
					$html[] = '</div>'; // End of contents
					

					if (!empty($item->children))
					{
						$html[] = self::generateMenuItemHTML($original, self::getChildren($original, $item->children), $item->id, $item->level + 1);
					}
					else
					{
						$html[] = '<ul class="hu-has-children" data-level="' . ($item->level + 1)
							. '" data-container="' . $item->id
							. '"></ul>';
					}

					$html[] = '</li>';
					$original[$itemId]->visited = true;
				}
			}

			$html[] = '</ul>';
		}

		return implode("\n", $html);
	}

	/**
	 * Get Children items from the original items array.
	 *
	 * @param	array	$items		The items array.
	 * @param	array	$children	The children IDs.
	 *
	 * @return	array	The children items.
	 * @since	2.0.0
	 */
	private static function getChildren($items, $children)
	{
		$childrenItems = [];

		foreach ($children as $child)
		{
			$childrenItems[$child] = $items[$child];
		}

		return $childrenItems;
	}

	/**
	 * Get menu items adding modal body.
	 *
	 * @return	string	The HTML string.
	 * @since	2.0.0
	 */
	public static function getMenuItemModalContents()
	{
		$builder = new MenuBuilder;

		$fields = [
			'title' => [
				'type' => 'text',
				'title' => 'Title',
				'data' => ['itemid' => 1],
				'value' => '',
				'internal' => true,
			],
			'alias' => [
				'type' => 'text',
				'title' => 'Alias',
				'data' => ['itemid' => 1],
				'value' => '',
				'internal' => true,
			],
			'menutype' => [
				'type' => 'menuType',
				'title' => 'Menu Item Type',
				'internal' => true
			]
		];

		$html = [];
		$html[] = '<div class="hu-modal-content">';
		$html[] = '<div class="hu-add-item-wrapper">';
		
		foreach ($fields as $key => $attr)
		{
			$html[] = $builder->renderFieldElement($key, $attr);
		}

		$html[] = '<div class="hu-item-request"></div>';
		$html[] = '<div class="hu-item-link"></div>';

		$html[] = '</div>';
		$html[] = '</div>';

		return [
			'status' => true,
			'data' => implode("\n", $html),
			'item' => \HelixultimateFieldMenuType::getMenuTypes()
		];
	}

	public static function setMenuType()
	{
		$input = Factory::getApplication()->input;
		$type = $input->get('type', '', 'STRING');
		$link = '';

		$data = [];
		$type = \json_decode(\base64_decode($type));

		$title = isset($type->title) ? $type->title : null;
		$recordId = isset($type->id) ? $type->id : 0;
		$request = isset($type->request) ? $type->request : null;

		$specialTypes = ['alias', 'separator', 'url', 'heading', 'container'];

		if (!\in_array($title, $specialTypes))
		{
			$title = 'component';
		}
		else
		{
			$data['component_id'] = 0;
		}

		if ($title === 'component')
		{
			if (!empty($request))
			{
				$request->option = InputFilter::getInstance()->clean($request->option, 'CMD');

				$component = ComponentHelper::getComponent($request->option);
				$data['component_id'] = $component->id;

				$link = 'index.php?' . Uri::buildQuery((array) $type->request);
			}
		}
		elseif ($title == 'alias')
		{
			$link = 'index.php?Itemid=';
		}

		$data['type'] = $title;

		$form = self::makeForm($title, $link);
		$builder = new MenuBuilder;

		return [
			'status' => true,
			'data' => $data,
			'link' => $builder->renderFieldElement('link', [
				'type' => 'text',
				'title' => 'Link',
				'value' => $link,
				'internal' => true,
			]),
			'request' => $form->renderFieldset('request')
		];
	}

	public static function makeForm($type, $link, $clientId = 0)
	{
		$form = new Form('request');
		$formFile = false;
		$typeFile   = $clientId === 1 ? 'itemadmin_' . $type : 'item_' . $type;

		$clientInfo = ApplicationHelper::getClientInfo($clientId);

		// Initialize form with component view params if available.
		if ($type === 'component')
		{
			$link = \htmlspecialchars_decode($link);

			// Parse the link arguments.
			$args = array();
			\parse_str(\parse_url(\htmlspecialchars_decode($link), PHP_URL_QUERY), $args);

			// Confirm that the option is defined.
			$option = '';
			$base = '';

			if (isset($args['option']))
			{
				// The option determines the base path to work with.
				$option = $args['option'];
				$base = $clientInfo->path . '/components/' . $option;
			}

			if (isset($args['view']))
			{
				$view = $args['view'];

				// Determine the layout to search for.
				if (isset($args['layout']))
				{
					$layout = $args['layout'];
				}
				else
				{
					$layout = 'default';
				}

				// Check for the layout XML file. Use standard xml file if it exists.
				$tplFolders = array(
					$base . '/views/' . $view . '/tmpl',
					$base . '/view/' . $view . '/tmpl'
				);
				$path = Path::find($tplFolders, $layout . '.xml');

				if (\is_file($path))
				{
					$formFile = $path;
				}

				// If custom layout, get the xml file from the template folder
				// template folder is first part of file name -- template:folder
				if (!$formFile && (\strpos($layout, ':') > 0))
				{
					list($altTmpl, $altLayout) = \explode(':', $layout);

					$templatePath = Path::clean($clientInfo->path . '/templates/' . $altTmpl . '/html/' . $option . '/' . $view . '/' . $altLayout . '.xml');

					if (\is_file($templatePath))
					{
						$formFile = $templatePath;
					}
				}
			}

			// Now check for a view manifest file
			if (!$formFile)
			{
				if (isset($view))
				{
					$metadataFolders = array(
						$base . '/view/' . $view,
						$base . '/views/' . $view
					);
					$metaPath = Path::find($metadataFolders, 'metadata.xml');

					if (\is_file($path = Path::clean($metaPath)))
					{
						$formFile = $path;
					}
				}
				else
				{
					// Now check for a component manifest file
					$path = Path::clean($base . '/metadata.xml');

					if (\is_file($path))
					{
						$formFile = $path;
					}
				}
			}
		}

		if ($formFile)
		{
			// If an XML file was found in the component, load it first.
			// We need to qualify the full path to avoid collisions with component file names.
			if ($form->loadFile($formFile, true, '/metadata') == false)
			{
				throw new \Exception(Text::_('JERROR_LOADFILE_FAILED'));
			}

			// Attempt to load the xml file.
			if (!$xml = \simplexml_load_file($formFile))
			{
				throw new \Exception(Text::_('JERROR_LOADFILE_FAILED'));
			}

			// Get the help data from the XML file if present.
			$help = $xml->xpath('/metadata/layout/help');
		}
		else
		{
			// We don't have a component. Load the form XML to get the help path
			$xmlFile = Path::find(JPATH_ADMINISTRATOR . '/components/com_menus/models/forms', $typeFile . '.xml');

			if ($xmlFile)
			{
				if (!$xml = \simplexml_load_file($xmlFile))
				{
					throw new \Exception(Text::_('JERROR_LOADFILE_FAILED'));
				}

				// Get the help data from the XML file if present.
				$help = $xml->xpath('/form/help');
			}
		}

		if (!empty($help))
		{
			$helpKey = \trim((string) $help[0]['key']);
			$helpURL = \trim((string) $help[0]['url']);
			$helpLoc = \trim((string) $help[0]['local']);
		}

		if (!$form->loadFile(JPATH_ADMINISTRATOR . '/components/com_menus/models/forms/' . $typeFile . '.xml', true, false))
		{
			throw new \Exception(Text::_('JERROR_LOADFILE_FAILED'));
		}

		// Association menu items, we currently do not support this for admin menu… may be later
		if ($clientId == 0 && Associations::isEnabled())
		{
			$languages = LanguageHelper::getContentLanguages(false, true, null, 'ordering', 'asc');

			if (\count($languages) > 1)
			{
				$addform = new \SimpleXMLElement('<form />');
				$fields = $addform->addChild('fields');
				$fields->addAttribute('name', 'associations');
				$fieldset = $fields->addChild('fieldset');
				$fieldset->addAttribute('name', 'item_associations');

				foreach ($languages as $language)
				{
					$field = $fieldset->addChild('field');
					$field->addAttribute('name', $language->lang_code);
					$field->addAttribute('type', 'modal_menu');
					$field->addAttribute('language', $language->lang_code);
					$field->addAttribute('label', $language->title);
					$field->addAttribute('translate_label', 'false');
					$field->addAttribute('select', 'true');
					$field->addAttribute('new', 'true');
					$field->addAttribute('edit', 'true');
					$field->addAttribute('clear', 'true');
					$field->addAttribute('propagate', 'true');
					$option = $field->addChild('option', 'COM_MENUS_ITEM_FIELD_ASSOCIATION_NO_VALUE');
					$option->addAttribute('value', '');
				}

				$form->load($addform, false);
			}
		}

		return $form;
	}
}
