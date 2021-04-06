<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
namespace HelixUltimate\Framework\Platform\Builders;

defined('_JEXEC') or die();

use HelixUltimate\Framework\Platform\Builders\Builder;
use HelixUltimate\Framework\Platform\Helper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Menu\MenuItem;
use Joomla\CMS\Menu\SiteMenu;
use Joomla\Registry\Registry;

/**
 * Helper class for building menu
 *
 * @since	2.0.0
 */
class MegaMenuBuilder extends Builder
{
	/**
	 * Menu Item ID
	 *
	 * @var		int		$itemId 	The menu item ID.
	 * @since	2.0.0
	 */
	protected $itemId = 0;

	/**
	 * Menu Item Params.
	 *
	 * @var		Registry	$params		The menu item params registry data.
	 * @since	2.0.0
	 */
	protected $params = null;

	/**
	 * Constructor function for the builder.
	 *
	 * @param	int		$itemId	The menu type
	 *
	 * @since	2.0.0
	 */
	public function __construct($itemId)
	{
		parent::__construct();

		$this->itemId = $itemId;
		$this->params = new Registry;
		$this->loadMenuItemParams();
	}

	/**
	 * Load the menu item params to the builder.
	 *
	 * @return 	void
	 * @since	2.0.0
	 */
	protected function loadMenuItemParams()
	{
		$item = $this->getMenuItem();
		$this->params = $item->getParams();
	}

	/**
	 * Get mega menu settings from the params.
	 *
	 * @return	stdClass	The mega menu settings object.
	 * @since	2.0.0
	 */
	public function getMegaMenuSettings()
	{
		$megaMenu = $this->params->get('helixultimatemenulayout', new \stdClass);

		if (!empty($megaMenu) && \is_string($megaMenu))
		{
			$megaMenu = \json_decode($megaMenu);
		}

		return $megaMenu;
	}

	/**
	 * Get menu Item by id
	 *
	 * @return	MenuItem	The menu item object.
	 * @since	2.0.0
	 */
	public function getMenuItem()
	{
		$item = new MenuItem;

		try
		{
			$db 	= Factory::getDbo();
			$query 	= $db->getQuery(true);
			$query->select('m.id, m.menutype, m.title, m.alias, m.note, m.path AS route, m.link, m.type, m.level, m.language')
				->select($db->quoteName('m.browserNav') . ', m.access, m.params, m.home, m.img, m.template_style_id, m.component_id, m.parent_id')
				->select('e.element as component')
				->from($db->quoteName('#__menu', 'm'))
				->join('LEFT', '#__extensions AS e ON m.component_id = e.extension_id')
				->where($db->quoteName('id') . ' = ' . (int) $this->itemId);

			$item = $db->setQuery($query)->loadObject();

			/**
			 * Make items object as MenuItem object so that we can use
			 * the MenuItem's functionalities.
			 */
			$item = new MenuItem((array) $item);
		}
		catch (Exception $e)
		{
			echo Factory::getApplication()->enqueueMessage($e->getMessage());

			return $item;
		}

		return $item;
	}

	/**
	 * Get Menu child menu items for a item id.
	 *
	 * @return	array	The menu item id items
	 * @since	2.0.0
	 */
	public function getItemChildren()
	{
		$children = [];

		try
		{
			$db 	= Factory::getDbo();
			$query 	= $db->getQuery(true);
			$query->select('id, title, parent_id')
				->from($db->quoteName('#__menu'))
				->where($db->quoteName('parent_id') . ' = ' . (int) $this->itemId)
				->where($db->quoteName('published') . ' = 1');
			$db->setQuery($query);

			$children = $db->loadObjectList();
		}
		catch (Exception $e)
		{
			echo $e->getMessage();

			return [];
		}

		return $children;
	}

	/**
	 * Get item title. If the item is a module then get the module title.
	 * If the item is a menu item then get the item title.
	 *
	 * @param	\stdClass	$item	The item object
	 *
	 * @return	string		The title string.
	 * @since	2.0.0
	 */
	public function getTitle($item)
	{
		$element = null;

		if ($item->type === 'module')
		{
			$modules = Helper::getModules();

			foreach ($modules as $mod)
			{
				if ((int) $mod->id === (int) $item->item_id)
				{
					$element = $mod;
					break;
				}
			}
		}
		else
		{
			$menu = new SiteMenu;
			$element = $menu->getItem($item->item_id);
		}

		return !empty($element) ? $element->title : '';
	}

	/**
	 * Get missing menu items.
	 *
	 * @return	array	the missing items array.
	 * @since	2.0.0
	 */
	public function getMissingItems()
	{
		$settings = $this->getMegaMenuSettings();
		$children = $this->getItemChildren();

		$rows = $settings->layout ?? [];

		$items = [];

		if (!empty($rows))
		{
			foreach ($rows as $row)
			{
				$columns = $row->attr ?? [];

				if (!empty($columns))
				{
					foreach ($columns as $column)
					{
						$cells = $column->items ?? [];
						$cells = array_filter($cells, function($cell) {
							return $cell->type === 'menu_item';
						});

						$items = array_merge($items, $cells);
					}
				}
			}
		}

		$missing = [];

		if (!empty($children) && !empty($items))
		{
			foreach ($children as $child)
			{
				$found = false;

				foreach ($items as $item)
				{
					if ((int) $child->id === (int) $item->item_id)
					{
						$found = true;
						break;
					}
				}

				if (!$found)
				{
					$tmp = new \stdClass;
					$tmp->type = 'menu_item';
					$tmp->item_id = $child->id;
					$missing[] = $tmp;
				}
			}
		}

		return $missing;
	}
}
