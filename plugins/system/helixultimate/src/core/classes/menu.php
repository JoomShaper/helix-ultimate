<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
namespace HelixUltimate\Framework\Core\Classes;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Helper\ModuleHelper;
use HelixUltimate\Framework\Platform\Helper;

defined('_JEXEC') or die();

/**
 * HelixUltimate menu
 *
 * @since   1.0.0
 */
class HelixultimateMenu
{
	/**
	 * Menu items.
	 *
	 * @var		array	Menu items.
	 * @since	1.0.0
	 */
	protected $_items = array();

	/**
	 * Is active menu
	 *
	 * @var		boolean		Menu status.
	 * @since	1.0.0
	 */
	protected $active = 0;

	/**
	 * Active tree.
	 *
	 * @var		array	Menu tree.
	 * @since	1.0.0
	 */
	protected $active_tree = array();

	/**
	 * Menu
	 *
	 * @var		string		menu
	 * @since	1.0.0
	 */
	protected $menu = '';

	/**
	 * Menu params.
	 *
	 * @var		object	Menu params.
	 * @since	1.0.0
	 */
	public $_params = null;

	/**
	 * Menu direction.
	 *
	 * @var		string		Menu direction.
	 * @since	1.0.0
	 */
	public $direction = 'ltr';

	/**
	 * Menu type.
	 *
	 * @var		string		Menutype
	 * @since	1.0.0
	 */
	public $menuname = 'mainmenu';

	/**
	 * Mega menu settings from menu builder
	 *
	 * @var		object		$megamenu
	 * @since	2.0.0
	 */
	private $menuItems = null;

	/**
	 * Constructor class.
	 *
	 * @param	string	$class	Classes.
	 * @param	string	$name	Name attribute
	 *
	 * @return	void
	 * @since	1.0.0
	 */
	public function __construct($class = '', $name = '')
	{
		$lang = Factory::getLanguage();
		$this->app = Factory::getApplication();

		$this->template = Helper::loadTemplateData();
		$this->_params = $this->template->params;

		$this->extraclass = $class;
		$this->direction = $lang->get('rtl') ? 'rtl' : 'ltr';

		if ($name)
		{
			$this->menuname = $name;
		}
		else
		{
			$this->menuname = $this->_params->get('menu');
		}

		$menuSettings = $this->_params->get('megamenu');

		if (!empty($menuSettings) && \is_string($menuSettings))
		{
			$menuSettings = json_decode($menuSettings);
			$menuType = $this->menuname;
			$this->menuItems = $menuSettings->menu->$menuType->menuItems;
		}
		else
		{
			$defaultMenuItems = $this->getMenuItemsDefaultValue();
			$menuType = $this->menuname;
			$this->menuItems = $defaultMenuItems->menu->$menuType->menuItems;
		}
		

		$this->initMenu();
		$this->render();
	}

	/**
	 * Get menu item's default value in case of not value found
	 *
	 * @return 	object	The object containing of default value
	 * @since	2.0.0
	 */
	private function getMenuItemsDefaultValue()
	{
		$menu = $this->app->getMenu('site');
		$attributes 	= array('menutype');
		$menuName     	= array($this->menuname);
		$items = $menu->getItems($attributes, $menuName);
		$menuType = $this->menuname;

		$menuSettings = new \stdClass;
		$menuSettings->menu = new \stdClass;
		$menuSettings->menu->$menuType = new \stdClass;
		$defaultItem = ['id' => null, 'title' => '', 'menu_custom_classes' => '', 'menu_icon' => '', 'menu_caption' => '', 'mega_menu' => 0, 'mega_width' => '', 'mega_custom_classes' => '', 'mega_alignment' => 'left', 'menu_badge' => '', 'menu_badge_position' => 'left', 'menu_badge_background' => '', 'menu_badge_color' => ''];

		if (!empty($items))
		{
			$menuSettings->menu->$menuType->menuItems = new \stdClass;

			foreach ($items as $item)
			{
				$itemId = $item->id;
				$copyItem = $defaultItem;
				$copyItem['id'] = $item->id;
				$copyItem['title'] = $item->alias;
				$menuSettings->menu->$menuType->menuItems->$itemId = (object) $copyItem;
			}
		}

		return $menuSettings;
	}

	/**
	 * Initialized the menu functionalities.
	 *
	 * @return	void
	 * @since	1.0.0
	 */
	public function initMenu()
	{
		$menu  	= $this->app->getMenu('site');

		$attributes 	= array('menutype');
		$menu_name     	= array($this->menuname);
		$items 			= $menu->getItems($attributes, $menu_name);
		$active_item 	= ($menu->getActive()) ? $menu->getActive() : $menu->getDefault();

		$this->active   	= $active_item ? $active_item->id : 0;
		$this->active_tree 	= $active_item->tree;

		foreach ($items as &$item)
		{
			if ($item->level >= 2 && !isset($this->_items[$item->parent_id]))
			{
				continue;
			}

			$parent                           = isset($this->children[$item->parent_id]) ? $this->children[$item->parent_id] : array();
			$parent[]                         = $item;
			$this->children[$item->parent_id] = $parent;
			$this->_items[$item->id]          = $item;
		}

		foreach ($items as &$item)
		{
			$class = '';

			if ($item->id == $this->active)
			{
				$class .= ' current-item';
			}

			if (in_array($item->id, $this->active_tree))
			{
				$class .= ' active';
			}
			elseif ($item->type == 'alias')
			{
				$aliasToId = $item->params->get('aliasoptions');

				if (count($this->active_tree) > 0 && $aliasToId == $this->active_tree[count($this->active_tree) - 1])
				{
					$class .= ' active';
				}
				elseif (in_array($aliasToId, $this->active_tree))
				{
					$class .= ' alias-parent-active';
				}
			}

			$item->class   = $class;
			$item->dropdown = 0;
			$item->flink = $item->link;

			if (isset($this->children[$item->id]))
			{
				$item->dropdown = 1;
			}

			switch ($item->type)
			{
				case 'separator':
				break;

				case 'heading':
					// No further action needed.
					break;

				case 'url':
					if ((int) (strpos($item->link, 'index.php?') === 0) && (strpos($item->link, 'Itemid=') === false))
					{
						// If this is an internal Joomla link, ensure the Itemid is set.
						$item->flink = $item->link . '&Itemid=' . $item->id;
					}
					break;

				case 'alias':
					$item->flink = 'index.php?Itemid=' . $item->params->get('aliasoptions');
					break;

				default:
					$item->flink = 'index.php?Itemid=' . $item->id;
					break;
			}

			if ((strpos($item->flink, 'index.php?') !== false) && strcasecmp(substr($item->flink, 0, 4), 'http'))
			{
				$item->flink = Route::_($item->flink, true, $item->params->get('secure'));
			}
			else
			{
				$item->flink = Route::_($item->flink);
			}

			$item->title = htmlspecialchars($item->title, ENT_COMPAT, 'UTF-8', false);
			$item->anchor_css   = htmlspecialchars($item->params->get('menu-anchor_css', ''), ENT_COMPAT, 'UTF-8', false);
			$item->anchor_title = htmlspecialchars($item->params->get('menu-anchor_title', ''), ENT_COMPAT, 'UTF-8', false);
			$item->menu_image   = $item->params->get('menu_image', '') ? htmlspecialchars($item->params->get('menu_image', ''), ENT_COMPAT, 'UTF-8', false) : '';
		}
	}

	/**
	 * Render menu.
	 *
	 * @return	string
	 * @since	1.0.0
	 */
	public function render()
	{
		$this->menu = '';
		$keys = array_keys($this->_items);

		if (!empty($keys))
		{
			$this->navigation(null, $keys[0]);
		}

		return $this->menu;
	}

	/**
	 * Menu navigation.
	 *
	 * @param	object	$pitem	Parent item.
	 * @param	integer	$start	Start index.
	 * @param	integer	$end	End index.
	 * @param	string	$class	Class value.
	 *
	 * @return	void
	 * @since	1.0.0
	 */
	public function navigation($pitem, $start = 0, $end = 0, $class = '')
	{
		if ($start > 0)
		{
			if (!isset($this->_items[$start]))
			{
				return;
			}

			$pid     = $this->_items[$start]->parent_id;
			$items   = array();
			$started = false;

			foreach ($this->children[$pid] as $item)
			{
				if ($started)
				{
					if ((int) $item->id === (int) $end)
					{
						break;
					}

					$items[] = $item;
				}
				else
				{
					if ((int) $item->id === (int) $start)
					{
						$started = true;
						$items[] = $item;
					}
				}
			}

			if (empty($items))
			{
				return;
			}
		}
		elseif ((int) $start === 0)
		{
			$pid = $pitem->id;

			if (!isset($this->children[$pid]))
			{
				return;
			}

			$items = $this->children[$pid];
		}
		else
		{
			return;
		}

		// Parent class
		if ((int) $pid === 1)
		{
			if ($this->_params->get('menu_animation') !== 'none')
			{
				$animation = ' ' . $this->_params->get('menu_animation');
			}
			else
			{
				$animation = '';
			}

			$class = 'sp-megamenu-parent' . $animation;

			if ($this->extraclass)
			{
				$class = $class . ' ' . $this->extraclass;
			}

			$this->menu .= $this->start_lvl($class);
		}
		else
		{
			$this->menu .= $this->start_lvl($class);
		}

		foreach ($items as $item)
		{
			$this->getItem($item);
		}

		$this->menu .= $this->end_lvl();
	}

	/**
	 * Get menu item.
	 *
	 * @param	object	$item	The menu
	 *
	 * @return 	void
	 * @since	1.0.0
	 */
	private function getItem($item)
	{
		$this->menu .= $this->start_el(array('item' => $item));
		$this->menu .= $this->item($item);

		$menuItem = null;

		if ((int) $item->level === 1)
		{
			$itemId = $item->id;
			$menuItem = $this->menuItems->$itemId;
		}

		if (!empty($menuItem) && !empty($menuItem->mega_menu))
		{
			$this->mega($menuItem);
		}
		elseif ($item->dropdown)
		{
			$this->dropdown($item);
		}

		$this->menu .= $this->end_el();
	}

	/**
	 * Menu dropdown
	 *
	 * @param	object	$item	Menu item.
	 *
	 * @return 	void
	 * @since 	1.0.0
	 */
	private function dropdown($item)
	{
		$items     = isset($this->children[$item->id]) ? $this->children[$item->id] : array();
		$firstitem = !empty($items) ? $items[0]->id : 0;
		$class = ((int) $item->level === 1) ? 'sp-dropdown sp-dropdown-main' : 'sp-dropdown sp-dropdown-sub';

		// Menu_show
		$menu_show = $this->getMenuShow($item->id);
		$dropdown_width = $this->_params->get('dropdown_width', 240);
		$dropdown_alignment = 'right';
		$dropdown_style = 'width: ' . $dropdown_width . 'px;';
		$layout = json_decode($this->_items[$item->id]->params->get('helixultimatemenulayout'));

		if (isset($layout->dropdown) && $layout->dropdown === 'left')
		{
			if ((int) $item->parent_id !== 1)
			{
				$dropdown_style .= 'left: -' . $dropdown_width . 'px;';
			}

			$dropdown_alignment = 'left';
		}

		if ((int) $menu_show !== 0)
		{
			$this->menu .= '<div class="' . $class . ' sp-menu-' . $dropdown_alignment . '" style="' . $dropdown_style . '">';
			$this->menu .= '<div class="sp-dropdown-inner">';
			$this->navigation($item, $firstitem, 0,  'sp-dropdown-items');
			$this->menu .= '</div>';
			$this->menu .= '</div>';
		}
	}

	/**
	 * Check show menu.
	 *
	 * @param	integer		$parent_id	The parent menu id.
	 *
	 * @return	integer		Show menu.
	 * @since 	1.0.0
	 */
	private function getMenuShow($parent_id)
	{
		$items     = isset($this->children[$parent_id]) ? $this->children[$parent_id] : array();
		$show_menu = 0;

		foreach ($items as $menu_item)
		{
			if ((int) $menu_item->params->get('menu_show', 1) === 1)
			{
				$show_menu ++;
			}
		}

		return $show_menu;
	}

	/**
	 * Helix mega menu.
	 *
	 * @param	object	$menuItem	Menu Item
	 *
	 * @return 	void
	 * @since	1.0.0
	 */
	private function mega($menuItem)
	{
		$containerStyle = '';
		$containerClass = '';
		$width = !empty($menuItem->mega_width) ? $menuItem->mega_width : '600px';
		$width = \is_numeric($width) ? $width . 'px' : $width;
		$widthValue = (int) $width;

		if (!empty($menuItem->mega_alignment) && $menuItem->mega_alignment !== 'justify')
		{
			$containerStyle .= 'width: ' . $width . '; ';
		}

		if (!empty($menuItem->mega_alignment)
			&& ($menuItem->mega_alignment === 'left'
			|| $menuItem->mega_alignment === 'center'))
		{
			$containerStyle .= 'left: -' . ($widthValue / 2) . 'px; ';
		}

		$alignment = $menuItem->mega_alignment === 'justify' ? 'full' : $menuItem->mega_alignment;
		$containerClass .= !empty($menuItem->mega_custom_classes) ? $menuItem->mega_custom_classes : '';
		$containerClass .= !empty($alignment) ? ' sp-menu-' . $alignment : '';
		$containerClass .= !empty($alignment) && $alignment === 'full' ? ' container' : '';

		$this->menu .= '<div class="sp-dropdown sp-dropdown-main sp-dropdown-mega ' . $containerClass . '" style="' . $containerStyle . '">';
		$this->menu .= '<div class="sp-dropdown-inner">';
		$rows = !empty($menuItem->mega_rows) ? $menuItem->mega_rows : [];

		if (!empty($rows))
		{
			foreach ($rows as $row)
			{
				$this->menu .= $this->renderRow($row);
			}
		}

		$this->menu .= '</div>';
		$this->menu .= '</div>';
	}

	/**
	 * Render Row of the megamenu
	 *
	 * @param	object	$row	The row object
	 *
	 * @return	string	The HTML string
	 * @since	2.0.0
	 */
	private function renderRow($row)
	{
		$html = [];

		if (!empty($row))
		{
			$rowId = 'sp-megamenu-row-' . $row->itemId . '-' . $row->id;
			$rowClass = !empty($row->settings->row_class) ? $row->settings->row_class : '';
			$rowStyle = '';
			$rowStyle .= !empty($row->settings->row_margin) ? 'margin: ' . $row->settings->row_margin . '; ' : '';
			$rowStyle .= !empty($row->settings->row_padding) ? 'padding: ' . $row->settings->row_padding . '; ' : '';

			$html[] = '<div id="' . $rowId . '" class="row ' . $rowClass . '" style="' . $rowStyle . '">';

			if (!empty($row->settings->enable_row_title))
			{
				if (!empty($row->settings->row_title))
				{
					$html[] = '<div class="sp-megamenu-title">' . $row->settings->row_title . '</div>';
				}
			}

			if (!empty($row->columns))
			{
				foreach ($row->columns as $column)
				{
					$html[] = $this->renderColumn($column);
				}
			}

			$html[] = '</div>';
		}

		return implode("\n", $html);
	}

	/**
	 * Render column of the megamenu
	 *
	 * @param	object	$column		The column object
	 *
	 * @return	string	The HTML string
	 * @since	2.0.0
	 */
	private function renderColumn($column)
	{
		$html = [];

		if (!empty($column))
		{
			$columnClass = 'col-' . (!empty($column->settings->col) ? $column->settings->col : 12);
			$columnClass .= !empty($column->settings->col_class) ? ' ' . $column->settings->col_class : '';
			$columnId = !empty($column->settings->col_id) ? $column->settings->col_id : 'sp-megamenu-column-' . $column->itemId . '-' . $column->rowId . '-' . $column->id;
			$columnStyle = '';
			$columnStyle .= !empty($column->settings->col_margin) ? 'margin: ' . $column->settings->col_margin . ';' : '';
			$columnStyle .= !empty($column->settings->col_padding) ? 'padding: ' . $column->settings->col_padding . ';' : '';

			$html[] = '<div class="'
				. $columnClass . '" id="'
				. $columnId . '" style="'
				. $columnStyle . '">';

			if (!empty($column->settings->enable_col_title))
			{
				if (!empty($column->settings->col_title))
				{
					$html[] = '<div class="sp-megamenu-title">' . $column->settings->col_title . '</div>';
				}
			}

			$html[] = $this->renderMegamenuElement($column);

			$html[] = '</div>';
		}

		return implode("\n", $html);
	}

	/**
	 * Render megamenu element.
	 *
	 * @param	mixed	$element	The element array/object
	 *
	 * @return	string	The HTML string
	 * @since	2.0.0
	 */
	private function renderMegamenuElement($element)
	{
		$html = [];

		if (!empty($element))
		{
			$settings = $element->settings;
			$type = $settings->col_type;

			switch ($type)
			{
				case 'module':
					$html[] = $this->renderModule($settings->module, $settings->module_style);
				break;
				case 'module_position':
					$position = $settings->module_position;

					if ($position === 'custom')
					{
						if (!empty($settings->custom_position))
						{
							$position = $settings->custom_position;
						}
						else
						{
							$position = null;
						}
					}

					$html[] = $this->renderModulePosition($position, $settings->module_style);
				break;
				case 'menu_items':
					$html[] = $this->renderMenuItems($settings->menu_items);
				break;
			}
		}

		return implode("\n", $html);
	}

	/**
	 * Render module by it's ID
	 *
	 * @param	int		$moduleId	The module ID
	 * @param	string	$style		The module style
	 *
	 * @return	string	The HTML string
	 * @since	2.0.0
	 */
	private function renderModule($moduleId, $style = 'default')
	{
		$module = '';

		if (!empty($moduleId))
		{
			$module .= $this->loadModule($moduleId, $style);
		}

		return $module;
	}

	/**
	 * Render module position
	 *
	 * @param	string	$position	The module position
	 * @param	string	$style		The module style
	 *
	 * @return	string	The HTML string
	 * @since	2.0.0
	 */
	private function renderModulePosition($position, $style = 'default')
	{
		$include = '';

		if (!empty($position))
		{
			$include .= '<jdoc:include type="modules" name="' . $position . '" ';

			if ($style !== 'default')
			{
				$include .= 'style="' . $style . '" />';
			}
			else
			{
				$include .= '/>';
			}
		}

		return $include;
	}

	/**
	 * Render menu items by item ids.
	 *
	 * @param	string	$itemId		JSON encoded item ids array string,
	 *
	 * @return	string	The HTML string
	 * @since	2.0.0
	 */
	private function renderMenuItems($itemIds)
	{
		$html = [];

		if (!empty($itemIds) && \is_string($itemIds))
		{
			$itemIds = json_decode($itemIds, true);
		}

		if (!empty($itemIds))
		{
			$html[] = '<ul class="hu-megamenu-menu-items">';

			foreach ($itemIds as $id)
			{
				$html[] = $this->renderMenuItem($id);
			}

			$html[] = '</ul>';
		}

		return implode("\n", $html);
	}

	/**
	 * Render Menu Item by an ID
	 *
	 * @param	int		$id		The menu ID
	 *
	 * @return	string	The rendered HTML string
	 * @since	2.0.0
	 */
	private function renderMenuItem($id)
	{
		$menu = $this->app->getMenu('site');
		$item = $menu->getItem($id);

		$title = !empty($item->anchor_title) ? $item->anchor_title : '';
		$class = !empty($item->anchor_css) ? $item->anchor_css : '';

		$linkTitle = '';

		if ($item->menu_image)
		{
			$linkTitle = $item->params->get('menu_text', 1) ?
				'<img src="' . $item->menu_image . '" alt="' . $item->title . '" /><span class="image-title">' . $item->title . '</span> ' :
				'<img src="' . $item->menu_image . '" alt="' . $item->title . '" />';
		}
		else
		{
			$linkTitle = $item->title;
		}

		$flink = $item->flink;
		$flink = str_replace('&amp;', '&', \JFilterOutput::ampReplace(htmlspecialchars($flink)));
		$anchor = '';

		if ($item->params->get('menu_show', 1) !== 0)
		{
			switch ($item->browserNav)
			{
				default:
				case 0:
					$anchor .= '<a ' . $class . ' href="' . $flink . '" ' . $title . '>' . $linkTitle . '</a>';
					break;
				case 1:
					$anchor .= '<a ' . $class . ' rel="noopener noreferrer" href="' . $flink . '" target="_blank" ' . $title . '>' . $linkTitle . '</a>';
					break;
				case 2:
					$options .= 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,' . $item->params->get('window_open');
					$anchor .= '<a ' . $class . ' href="' . $flink . '" onclick="window.open(this.href, \'targetWindow\', \'' . $options . '\');return false;"' . $title . '>' . $linkTitle . '</a>';
					break;
			}
		}

		$menuItem = [];
		$menuItem[] = '<li class="sp-megamenu-menu-item">';

		if (!empty($anchor))
		{
			$menuItem[] = $anchor;
		}

		$menuItem[] = '</li>';

		return implode("\n", $menuItem);
	}

	/**
	 * Start label.
	 *
	 * @param	string	$cls	The classes.
	 *
	 * @return	string	starting tag of the label.
	 * @since	1.0.0
	 */
	private function start_lvl($cls = '')
	{
		$class = trim($cls);

		return '<ul class="' . $class . '">';
	}

	/**
	 * End label.
	 *
	 * @return 	string	The ending tag of the label.
	 * @since	1.0.0
	 */
	private function end_lvl()
	{
		return '</ul>';
	}

	/**
	 * Start element.
	 *
	 * @param	array	$args	The arguments.
	 *
	 * @return 	string	The starting element
	 * @since	1.0.0
	 */
	private function start_el($args = array())
	{
		$item 	= $args['item'];
		$class 	= 'sp-menu-item';

		// Menu show
		$menu_show = $this->getMenuShow($args['item']->id);

		// $layout = json_decode($item->params->get('helixultimatemenulayout'));
		$menuItem = null;

		if ((int) $item->level === 1)
		{
			$itemId = $item->id;
			$menuItem = $this->menuItems->$itemId;
		}

		$item->hasChild = 0;

		if (!empty($this->children[$item->id]) && $menu_show !== 0)
		{
			$class .= ' sp-has-child';
			$item->hasChild = 1;
		}
		elseif (!empty($menuItem) && (int) $menuItem->mega_menu === 1)
		{
			$class .= ' sp-has-child';
			$item->hasChild = 1;
		}

		if (!empty($menuItem) && $menuItem->mega_alignment === 'justify')
		{
			$class .= ' menu-justify';
		}

		$class .= ' ' . $item->class;

		return '<li class="' . $class . '">';
	}

	/**
	 * End element.
	 *
	 * @return	string	The ending element.
	 * @since	1.0.0
	 */
	private function end_el()
	{
		return '</li>';
	}

	/**
	 * Menu item.
	 *
	 * @param	object	$item			The item object.
	 * @param	string	$extra_class	Any extra class for the menu.
	 *
	 * @return	string	The menu item
	 * @since	1.0.0
	 */
	private function item($item, $extra_class='')
	{
		$menuItem = null;

		if ((int) $item->level === 1)
		{
			$itemId = $item->id;
			$menuItem = $this->menuItems->$itemId;
		}

		$title = $item->anchor_title ? 'title="' . $item->anchor_title . '" ' : '';

		$class = $extra_class;

		if (!empty($menuItem) && !empty($menuItem->menu_custom_classes))
		{
			$class .= ' ' . $menuItem->menu_custom_classes;
		}

		$class .= !empty($item->anchor_css) ? ' ' . $item->anchor_css : '';
		$class = !empty($class) ? 'class="' . $class . '"' : '';

		if ($item->menu_image)
		{
			$item->params->get('menu_text', 1) ?
				$linkTitle = '<img src="' . $item->menu_image . '" alt="' . $item->title . '" /><span class="image-title">' . $item->title . '</span> ' :
				$linkTitle = '<img src="' . $item->menu_image . '" alt="' . $item->title . '" />';
		}
		else
		{
			$linkTitle = $item->title;
		}

		$showMenuTitle = true;
		$icon = '';

		if (!empty($menuItem) && !empty($menuItem->menu_icon))
		{
			$icon = $menuItem->menu_icon;
		}

		// Add Menu Icon
		if ($icon)
		{
			if ($showMenuTitle)
			{
				$linkTitle = '<span class="' . $icon . '"></span> ' . $linkTitle;
			}
			else
			{
				$linkTitle = '<span class="' . $icon . '"></span>';
			}
		}

		$flink = $item->flink;
		$flink = str_replace('&amp;', '&', \JFilterOutput::ampReplace(htmlspecialchars($flink)));

		$badge_html = '';
		$output = '';
		$options = '';

		if (!empty($menuItem))
		{
			if (!empty($menuItem->menu_badge))
			{
				$badge_style = '';
				$badge_class = 'sp-menu-badge sp-menu-badge-right';

				if (!empty($menuItem->menu_badge_background))
				{
					$badge_style .= 'background-color: ' . $menuItem->menu_badge_background . ';';
				}

				if (!empty($menuItem->menu_badge_color))
				{
					$badge_style .= 'color: ' . $menuItem->menu_badge_color . ';';
				}

				if (!empty($menuItem->menu_badge_position) && $menuItem->menu_badge_position === 'left')
				{
					$badge_class = 'sp-menu-badge sp-menu-badge-left';
				}

				$badge_html = '<span class="' . $badge_class . '" style="' . $badge_style . '">' . $menuItem->menu_badge . '</span>';
			}

			if (!empty($badge_html))
			{
				if (isset($menuItem->menu_badge_position) && $menuItem->menu_badge_position === 'left')
				{
					$linkTitle = $badge_html . $linkTitle;
				}
				else
				{
					$linkTitle = $linkTitle . $badge_html;
				}
			}
		}

		$captionHtml = '';

		if (!empty($menuItem))
		{
			if (!empty($menuItem->menu_caption))
			{
				$captionHtml .= '<small class="menu-item-caption">' . $menuItem->menu_caption . '</small>';
			}
		}

		if (!empty($captionHtml))
		{
			$linkTitle .= $captionHtml;
		}

		if ($item->params->get('menu_show', 1) !== 0)
		{
			switch ($item->browserNav)
			{
				default:
				case 0:
					$output .= '<a ' . $class . ' href="' . $flink . '" ' . $title . '>' . $linkTitle . '</a>';
					break;
				case 1:
					$output .= '<a ' . $class . ' rel="noopener noreferrer" href="' . $flink . '" target="_blank" ' . $title . '>' . $linkTitle . '</a>';
					break;
				case 2:
					$options .= 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,' . $item->params->get('window_open');
					$output .= '<a ' . $class . ' href="' . $flink . '" onclick="window.open(this.href, \'targetWindow\', \'' . $options . '\');return false;"' . $title . '>' . $linkTitle . '</a>';
					break;
			}
		}

		return $output;
	}

	/**
	 * Load module to the menu
	 *
	 * @param	int		$moduleId	The module ID
	 * @param	string	$style		The module style
	 *
	 * @return	string	The rendered Module string
	 * @since	1.0.0
	 */
	private function loadModule($moduleId, $style = 'default')
	{
		if (!is_numeric($moduleId))
		{
			return null;
		}

		$groups		= implode(',', Factory::getUser()->getAuthorisedViewLevels());
		$lang 		= Factory::getLanguage()->getTag();
		$clientId 	= (int) $this->app->getClientId();

		$db	= Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id, title, module, position, content, showtitle, params');
		$query->from($db->quoteName('#__modules', 'm'));
		$query->where($db->quoteName('m.published') . ' = 1');
		$query->where($db->quoteName('m.id') . ' = ' . (int) $moduleId);

		$date = Factory::getDate();
		$now = $date->toSql();
		$nullDate = $db->getNullDate();

		$query->where('(m.publish_up = ' . $db->Quote($nullDate) . ' OR m.publish_up <= ' . $db->Quote($now) . ')');
		$query->where('(m.publish_down = ' . $db->Quote($nullDate) . ' OR m.publish_down >= ' . $db->Quote($now) . ')');
		$query->where($db->quoteName('m.access') . ' IN (' . $groups . ')');
		$query->where($db->quoteName('m.client_id') . ' = ' . (int) $clientId);

		if ($this->app->isClient('site') && $this->app->getLanguageFilter())
		{
			$query->where('m.language IN (' . $db->Quote($lang) . ',' . $db->Quote('*') . ')');
		}

		$query->order('position, ordering');
		$db->setQuery($query);
		$module = $db->loadObject();

		if (empty($module))
		{
			return null;
		}

		$options = array('style' => '');

		if ($style !== 'default')
		{
			$options['style'] = $style;
		}

		$output = '';

		$file				= $module->module;
		$custom				= substr($file, 0, 4) == 'mod_' ? 0 : 1;
		$module->user		= $custom;
		$module->name		= $custom ? $module->title : substr($file, 4);
		$module->style		= null;
		$module->client_id  = 1;
		$module->position	= strtolower($module->position);
		$clean[$module->id]	= $module;
		$output = ModuleHelper::renderModule($module, $options);

		return $output;
	}
}
