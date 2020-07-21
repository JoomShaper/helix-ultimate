<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
namespace HelixUltimate\Framework\Platform\Builders;

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Folder;

/**
 * Helper class for building menu
 *
 * @since	2.0.0
 */
class MenuBuilder
{
	/**
	 * Menu Type for which the builder perform its tasks.
	 *
	 * @var		string	$menuType	The type name
	 * @since	2.0.0
	 */
	private $menuType;

	/**
	 * Constructor function for the builder.
	 *
	 * @param	string	$menuType	The menu type
	 *
	 * @since	2.0.0
	 */
	public function __construct($menuType = 'mainmenu')
	{
		$this->menuType = $menuType;
		$this->includeFields();
	}

	/**
	 * Include fields
	 *
	 * @return	void
	 * @since	2.0.0
	 */
	public function includeFields()
	{
		$fields = Folder::files(HELIX_LAYOUT_PATH . '/fields', '\.php$', false, true);

		if (!empty($fields))
		{
			foreach ($fields as $field)
			{
				require_once $field;
			}
		}
	}

	/**
	 * Render field element
	 *
	 * @param	string	$key
	 * @param	array	$attr
	 *
	 * @return	string	HTML string for the field element rendering
	 * @since	2.0.0
	 */
	public function renderFieldElement($key, $attr)
	{
		return \call_user_func_array(
			['HelixultimateField' . ucfirst($attr['type']), 'getInput'],
			[$key, $attr]
		);
	}

	/**
	 * Set menu type if we need to overwrite the menu type by using
	 * the same class instance.
	 *
	 * @param	string	$_menuType		The menu type.
	 *
	 * @return	void
	 * @since	2.0.0
	 */
	public function setMenuType($_menuType)
	{
		$this->menuType = $_menuType;
	}

	/**
	 * Get Menu Item for the menu type.
	 *
	 * @param	string|array	$filter		The filter string.
	 * 										A dot(.) separated key value pair or an array of key/value pairs
	 *
	 * @return	array			The items list array.
	 * @since	1.0.0
	 *
	 * @throws	Exception
	 */
	public function getMenuItems($filter = 'level.1')
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
				$db->quoteName('menutype') . ' = ' . $db->quote($this->menuType),
				$db->quoteName('published') . ' = 1'
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

			$query->select('*')
				->from($db->quoteName('#__menu'))
				->where($conditions);

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
