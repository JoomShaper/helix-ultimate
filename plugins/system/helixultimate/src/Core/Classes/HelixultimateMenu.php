<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
namespace HelixUltimate\Framework\Core\Classes;

use HelixUltimate\Framework\Platform\Helper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Router\Route;

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

		$this->initMenu();
		$this->render();
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
		$active_item 	= ($menu->getActive())
			? $menu->getActive()
			: $menu->getDefault();

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
			$ariaLabelOpen = '';

			if ($item->id == $this->active)
			{
				$class .= ' current-item';
				$ariaLabelOpen .= 'aria-current="page"';
			}

			if (in_array($item->id, $this->active_tree))
			{
				$class .= ' active';
			}
			elseif ($item->type == 'alias')
			{
				$aliasToId = $item->getParams()->get('aliasoptions');

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
			$item->ariaLabelOpen   = $ariaLabelOpen;
			$item->dropdown = 0;
			$item->flink = $item->link;

			if (isset($this->children[$item->id]))
			{
				$item->dropdown = 1;
			}

			switch ($item->type)
			{
				case 'separator':
				case 'heading':
					break;

				case 'url':
					if ((int) (strpos($item->link, 'index.php?') === 0) && (strpos($item->link, 'Itemid=') === false))
					{
						// If this is an internal Joomla link, ensure the Itemid is set.
						$item->flink = $item->link . '&Itemid=' . $item->id;
					}

					break;

				case 'alias':
					$item->flink = 'index.php?Itemid=' . $item->getParams()->get('aliasoptions');
					break;

				default:
					$item->flink = 'index.php?Itemid=' . $item->id;
					break;
			}

			if ((strpos($item->flink, 'index.php?') !== false) && strcasecmp(substr($item->flink, 0, 4), 'http'))
			{
				$item->flink = Route::_($item->flink, true, $item->getParams()->get('secure'));
			}
			else
			{
				$item->flink = Route::_($item->flink);
			}

			$item->title = htmlspecialchars($item->title, ENT_COMPAT, 'UTF-8', false);
			$item->anchor_css   = htmlspecialchars($item->getParams()->get('menu-anchor_css', ''), ENT_COMPAT, 'UTF-8', false);
			$item->anchor_title = htmlspecialchars($item->getParams()->get('menu-anchor_title', ''), ENT_COMPAT, 'UTF-8', false);
			$item->anchor_rel = htmlspecialchars($item->getParams()->get('menu-anchor_rel', ''), ENT_COMPAT, 'UTF-8', false);
			$item->menu_image   = $item->getParams()->get('menu_image', '') ? htmlspecialchars($item->getParams()->get('menu_image', ''), ENT_COMPAT, 'UTF-8', false) : '';
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

		$menulayout = json_decode(Helper::CheckNull($item->getParams()->get('helixultimatemenulayout')));

		if (isset($menulayout->megamenu) && $menulayout->megamenu)
		{
			$this->mega($item);
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
		$dropdown_width = $this->_params->get('dropdown_width', '240px');
		$dropdown_width = preg_match("@(px|em|rem|%)$@", $dropdown_width) ? $dropdown_width : $dropdown_width . 'px';
		$dropdown_alignment = 'right';
		$dropdown_style = 'width: ' . $dropdown_width . ';';
		$layout = json_decode(Helper::CheckNull($this->_items[$item->id]->getParams()->get('helixultimatemenulayout')));

		if (isset($layout->dropdown) && $layout->dropdown === 'left')
		{
			if ((int) $item->parent_id !== 1)
			{
				$dropdown_style .= 'left: -' . $dropdown_width . ';';
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
			if ((int) $menu_item->getParams()->get('menu_show', 1) === 1)
			{
				$show_menu ++;
			}
		}

		return $show_menu;
	}

	/**
	 * Helix mega menu.
	 *
	 * @param	object	$item	Menu item.
	 *
	 * @return 	void
	 * @since	1.0.0
	 */
	private function mega($item)
	{
		$items     = isset($this->children[$item->id]) ? $this->children[$item->id] : array();
		$firstitem = count($items) ? $items[0]->id : 0;

		$mega = json_decode($item->getParams()->get('helixultimatemenulayout'));

		$layout = $mega->layout ?? [];

		$mega_style = 'width: ' . (preg_match("@(px|em|rem|%)$@", $mega->width) ? $mega->width : $mega->width . 'px');
		$mega_style .= ';';

		if ($mega->menualign === 'center')
		{
			$mega_style .= 'left: -' . ((float) $mega->width / 2) . 'px;';
		}

		if ($mega->menualign === 'full')
		{
			$mega_style = '';
			$mega->menualign = $mega->menualign . ' container';
		}

		$this->menu .= '<div class="sp-dropdown sp-dropdown-main sp-dropdown-mega sp-menu-' . $mega->menualign . '" style="' . $mega_style . '">';
		$this->menu .= '<div class="sp-dropdown-inner">';

		foreach ($layout as $row)
		{
			$this->menu .= '<div class="row">';

			foreach ($row->attr as $col)
			{
				$this->menu .= '<div class="col-sm-' . $col->colGrid . '">';

				if (!empty($col->items))
				{
					$this->menu .= $this->start_lvl('sp-mega-group');

					foreach ($col->items as $builder_item)
					{
						$li_head = '';

						if ($builder_item->type === 'menu_item')
						{
							$li_head = 'item-header';
						}

						$item_class = array(
							'item-' . $builder_item->item_id,
							$builder_item->type,
							$li_head
						);

						$this->menu .= '<li class="' . implode(' ', $item_class) . '">';

						if ($builder_item->type === 'module')
						{
							$this->menu .= $this->load_module($builder_item->item_id);
						}
						elseif ($builder_item->type === 'menu_item')
						{
							if (!empty($this->_items[$builder_item->item_id]))
							{
								$item 	= $this->_items[$builder_item->item_id];
								$items  = isset($this->children[$builder_item->item_id]) ? $this->children[$builder_item->item_id] : array();

								$firstitem = count($items) ? $items[0]->id : 0;

								if (isset($this->children[$item->id]))
								{
									$this->menu .= $this->item($item, 'sp-group-title');
								}
								else
								{
									$this->menu .= $this->item($item);
								}

								if ($firstitem)
								{
									$this->navigation(null, $firstitem, 0, 'sp-mega-group-child sp-dropdown-items');
								}
							}
						}

						$this->menu .= $this->end_el();
					}

					$this->menu .= $this->end_lvl();
				}

				$this->menu .= '</div>';
			}

			$this->menu .= '</div>';
		}

		$this->menu .= '</div>';
		$this->menu .= '</div>';
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

		$layout = json_decode(Helper::CheckNull($item->getParams()->get('helixultimatemenulayout')));

		$item->hasChild = 0;

		if (!empty($this->children[$item->id]) && $menu_show !== 0)
		{
			$class .= ' sp-has-child';
			$item->hasChild = 1;
		}
		elseif (isset($layout->megamenu) && ($layout->megamenu))
		{
			$class .= ' sp-has-child';
			$item->hasChild = 1;
		}

		if (isset($layout->customclass) && ($layout->customclass))
		{
			$class .= ' ' . $layout->customclass;
		}

		$class .= $item->class;

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
		$title = $item->anchor_title ? 'title="' . $item->anchor_title . '" ' : '';

		$class = $extra_class;
		$class .= ($item->anchor_css && $class) ? ' ' . $item->anchor_css : $item->anchor_css;

		$rel = $item->anchor_rel ? 'rel="' . $item->anchor_rel . '" ' : '';

		if ($item->type === 'separator')
		{
			$class .= ' sp-menu-separator';
		}
		elseif ($item->type === 'heading')
		{
			$class .= ' sp-menu-heading';
		}
		

		$class = !empty($class) ? 'class="' . $class . '"' : '';

		if ($item->menu_image)
		{
			$item->getParams()->get('menu_text', 1) ?
				$linktitle = '<img src="' . $item->menu_image . '" alt="' . $item->title . '" /><span class="image-title">' . $item->title . '</span> ' :
				$linktitle = '<img src="' . $item->menu_image . '" alt="' . $item->title . '" />';
		}
		else
		{
			$linktitle = $item->title;
		}

		$layout = json_decode(Helper::CheckNull($item->getParams()->get('helixultimatemenulayout')));

		$showmenutitle = (isset($layout->showtitle)) ? $layout->showtitle : 1;
		$icon = (isset($layout->faicon)) ? $layout->faicon : '';

		if (!empty($icon) && !preg_match("@^fa[sbr]@", $icon))
		{
			$icon = 'fas ' . $icon;
		}
		

		if (!$showmenutitle)
		{
			$linktitle = '';
		}

		// Add Menu Icon
		if ($icon)
		{
			if ($showmenutitle)
			{
				$linktitle = '<span class="' . $icon . '"></span> ' . $linktitle;
			}
			else
			{
				$linktitle = '<span class="' . $icon . '"></span>';
			}
		}

		$flink = $item->flink;
		$ariaLabelOpen = $item->ariaLabelOpen;
		$flink = str_replace('&amp;', '&', OutputFilter::ampReplace(htmlspecialchars($flink)));

		$badge_html = '';

		if (isset($layout->badge) && $layout->badge)
		{
			$badge_style = '';
			$badge_class = 'sp-menu-badge sp-menu-badge-right';

			if (isset($layout->badge_bg_color) && $layout->badge_bg_color)
			{
				$badge_style .= 'background-color: ' . $layout->badge_bg_color . ';';
			}

			if (isset($layout->badge_text_color) && $layout->badge_text_color)
			{
				$badge_style .= 'color: ' . $layout->badge_text_color . ';';
			}

			if (isset($layout->badge_position) && $layout->badge_position === 'left')
			{
				$badge_class = 'sp-menu-badge sp-menu-badge-left';
			}

			$badge_html = '<span class="' . $badge_class . '" style="' . $badge_style . '">' . $layout->badge . '</span>';
		}

		$output = '';
		$options = '';

		if ($badge_html)
		{
			if (isset($layout->badge_position) && $layout->badge_position === 'left')
			{
				$linktitle = $badge_html . $linktitle;
			}
			else
			{
				$linktitle = $linktitle . $badge_html;
			}
		}

		if (isset($item->hasChild) && $item->hasChild)
		{
			// $linktitle = $linktitle . ' <span class="fas fa-angle-down" aria-hidden="true"></span>';
		}

		if ($item->getParams()->get('menu_show', 1) !== 0)
		{
			switch ($item->browserNav)
			{
				default:
				case 0:
					if ($item->type === 'separator' || $item->type === 'heading')
					{
						$output .= '<span ' . $ariaLabelOpen . ' ' . $class . ' ' . $title . ' ' . $rel . '>' . $linktitle . '</span>';
					}
					else
					{
						$output .= '<a ' . $ariaLabelOpen .  ' ' . $class . ' href="' . $flink . '" ' . $title . ' ' . $rel . '>' . $linktitle . '</a>';
					}

					break;

				case 1:
					$output .= '<a ' . $class . ' rel="noopener noreferrer" href="' . $flink . '" target="_blank" ' . $title . ' ' . $rel . '>' . $linktitle . '</a>';
					break;

				case 2:
					$options .= 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,' . $item->getParams()->get('window_open');
					$output .= '<a ' . $class . ' href="' . $flink . '" onclick="window.open(this.href, \'targetWindow\', \'' . $options . '\');return false;"' . $title . ' ' . $rel . '>' . $linktitle . '</a>';
					break;
			}
		}

		return $output;
	}

	/**
	 * Load module to the menu
	 *
	 * @param	array	$mod	Modules
	 *
	 * @return	string	Modules
	 * @since	1.0.0
	 */
	private function load_module($mod)
	{
		if (!is_numeric($mod))
		{
			return null;
		}

		$groups		= implode(',', Factory::getUser()->getAuthorisedViewLevels());
		$lang 		= Factory::getLanguage()->getTag();
		$clientId 	= (int) $this->app->getClientId();

		$db	= Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('m.id, m.title, m.module, m.position, m.content, m.showtitle, m.params');
		$query->from('#__modules AS m');
		$query->where('m.published = 1');
		$query->where('m.id = ' . $mod);

		$date = Factory::getDate();
		$now = $date->toSql();
		$nullDate = $db->getNullDate();

		$query->where('(m.publish_up IS NULL OR m.publish_up = ' . $db->Quote($nullDate) . ' OR m.publish_up <= ' . $db->Quote($now) . ')');
		$query->where('(m.publish_down IS NULL OR m.publish_down = ' . $db->Quote($nullDate) . ' OR m.publish_down >= ' . $db->Quote($now) . ')');
		$query->where('m.access IN (' . $groups . ')');
		$query->where('m.client_id = ' . $clientId);

		if ($this->app->isClient('site') && $this->app->getLanguageFilter())
		{
			$query->where('m.language IN (' . $db->Quote($lang) . ',' . $db->Quote('*') . ')');
		}

		$query->order('position, ordering');
		$db->setQuery($query);
		$module = $db->loadObject();

		if (!$module)
		{
			return null;
		}

		$options = array('style' => 'sp_xhtml');

		$file				= $module->module;
		$custom				= substr($file, 0, 4) == 'mod_' ?  0 : 1;
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
