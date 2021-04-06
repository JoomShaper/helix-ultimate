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
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Folder;

/**
 * Helper class for building menu
 *
 * @since	2.0.0
 */
class MenuBuilder extends Builder
{
	/**
	 * Constructor function for the Menu Builder
	 *
	 * @since	2.0.0
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Get Menu Types and their menu items.
	 *
	 * @param	int			$client	The client id
	 *
	 * @return	stdClass	The menu types with the items.
	 * @since	2.0.0
	 */
	public function getMenuTypes($client = 0)
	{
		$menu = [];

		try
		{
			$db 	= Factory::getDbo();
			$query 	= $db->getQuery(true);
			$query->select('id, menutype, title')
				->from($db->quoteName('#__menu_types'))
				->where($db->quoteName('client_id') . ' = ' . (int) $client);
			$db->setQuery($query);
			$menu = $db->loadObjectList();
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}

		$menuTypes = new \stdClass;

		if (!empty($menu))
		{
			foreach ($menu as $m)
			{
				$type = $m->menutype;
				$menuTypes->$type = $this->getMenuItems($type, $client);
			}
		}

		return $menuTypes;
	}

	/**
	 * Get Menu Item for the menu type.
	 *
	 * @param	string			$menuType	The menu type
	 * @param	string|array	$filter		The filter string.
	 * 										A dot(.) separated key value pair or an array of key/value pairs
	 *
	 * @return	array			The items list array.
	 * @since	1.0.0
	 *
	 * @throws	Exception
	 */
	public function getMenuItems($menuType = 'mainmenu', $client = 0, $filter = 'level.1')
	{
		$menuItems = [];

		try
		{
			$db 	= Factory::getDbo();
			$query 	= $db->getQuery(true);

			/**
			 * Generate conditions based on the filters and others.
			 */
			$conditions = [
				$db->quoteName('menutype') . ' = ' . $db->quote($menuType),
				$db->quoteName('published') . ' = 1',
				$db->quoteName('client_id') . ' = ' . (int) $client,
				$db->quoteName('access') . ' IN (0, 1)',
			];

			if (!empty($filter) && \is_string($filter))
			{
				if (strpos($filter, '.') === false)
				{
					throw new \InvalidArgumentException(sprintf('The filter should be a dot separated string'));
				}

				list ($key, $value) = explode('.', $filter);
				$conditions[] = $db->quoteName($key) . ' = ' . (\is_numeric($value) ? (int) $value : $db->quote($value));
			}
			elseif (!empty($filter) && \is_array($filter))
			{
				foreach ($filter as $str)
				{
					if (strpos($str, '.') === false)
					{
						throw new \InvalidArgumentException(sprintf('The filter should be a dot separated string'));
						break;
					}

					list ($key, $value) = explode('.', $str);
					$conditions[] = $db->quoteName($key) . ' = ' . (\is_numeric($value) ? (int) $value : $db->quote($value));
				}
			}

			$query->select('id, title, alias, menutype, path, link')
				->from($db->quoteName('#__menu'))
				->where($conditions);

			$query->order($db->quoteName('lft') . ' ASC');

			$db->setQuery($query);

			$menuItems = $db->loadObjectList();
		}
		catch (\InvalidArgumentException $e)
		{
			echo $e->getMessage();
		}
		catch (Exception $e)
		{
			echo $e->getMessage();

			return $e->getMessage();
		}

		return $menuItems;
	}
}
