<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2026 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;

/**
 * Installer script for System - Helix Ultimate Framework plugin.
 */
class plgSystemHelixultimateInstallerScript
{
	/**
	 * Post Flight hook
	 *
	 * @param	string	$type
	 * @param	object	$parent
	 *
	 * @return	void
	 */
	public function postflight($type, $parent)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$fields = array(
			$db->quoteName('enabled') . ' = 1'
		);

		$conditions = array(
			$db->quoteName('type') . ' = ' . $db->quote('plugin'),
			$db->quoteName('element') . ' = ' . $db->quote('helixultimate'),
			$db->quoteName('folder') . ' = ' . $db->quote('system')
		);

		$query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (\Exception $e)
		{
			// Silently fail if there's any issue with database execution
		}
	}
}
