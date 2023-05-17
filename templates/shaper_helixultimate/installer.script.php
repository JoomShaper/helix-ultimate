<?php

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Installer\Installer;
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined ('_JEXEC') or die();

/**
 * Installer class of the Helix Ultimate Template
 *
 * @since	1.0.0
 */
class plgSystemTmp_helixultInstallerScript
{
	/**
	 * Post Flight hook
	 *
	 * @param	string	$type
	 * @param	object	$parent
	 *
	 * @return	void
	 * @since	1.0.0
	 */
	public function postflight($type, $parent)
	{
		$src = $parent->getParent()->getPath('source');
		$manifest = $parent->getParent()->manifest;
		$plugins = $manifest->xpath('plugins/plugin');

		foreach ($plugins as $key => $plugin)
		{
			$name = (string) $plugin->attributes()->plugin;
			$group = (string) $plugin->attributes()->group;
			$installer = new Installer;

			$path = $src.'/plugins/'.$group;
			if (Folder::exists($src.'/plugins/'.$group.'/'.$name))
			{
				$path = $src.'/plugins/'.$group.'/'.$name;
			}

			$plugin_info = $this->getPluginInfoByName($name, $group);

			if($plugin_info)
			{
				$manifest_cache = json_decode($plugin_info->manifest_cache ?? "");
				$cache_version = $manifest_cache->version;

				$plg_manifest = $installer->parseXMLInstallFile($path.'/'.$name.'.xml');
				$version = $plg_manifest['version'];

				if (version_compare($version, $cache_version, '<'))
				{
					continue;
				}
				
				// Check if directory "/overrides/com_finder/tmpl" exists then deletes it
				if ($version >= '2.0.12') 
				{
					$plg_path = JPATH_PLUGINS;
					$dir = $plg_path.'/'.$group.'/'.$name.'/overrides/com_finder/tmpl';
					
					if (Folder::exists($dir))
					{
						Folder::delete($dir);
					}
				}
			}

			$result = $installer->install($path);

			if ($result)
			{
				$this->activeInstalledPlugin($name, $group);
			}
		}

		$template_path = $src . '/template';
		$plugin_path = $src . '/plugins/system';

		if (Folder::exists( $template_path ))
		{
			$installer = new Installer;
			$result = $installer->install($template_path);
		}

		$templates = $manifest->xpath('template');

		foreach($templates as $key => $template)
		{
			$tmpl_name = (string) $template->attributes()->name;
			$tmpl_info = $this->getTemplateInfoByName($tmpl_name);

			$params = json_decode($tmpl_info->params ?? "");
			$params_array = (array) $params;

			if(empty($params_array))
			{
				$options_default = file_get_contents($template_path .'/options.json');

				$db = Factory::getDBO();
				$query = $db->getQuery(true);
				$fields = array(
					$db->quoteName('params') . ' = ' . $db->quote($options_default)
				);

				$conditions = array(
					$db->quoteName('client_id') . ' = 0',
					$db->quoteName('template') . ' = ' . $db->quote($tmpl_name)
				);

				$query->update($db->quoteName('#__template_styles'))->set($fields)->where($conditions);
				$db->setQuery($query);
				$db->execute();
			}
		}

		$conf = Factory::getConfig();
		$conf->set('debug', false);
		$parent->getParent()->abort();
	}

	/**
	 * Get template information by name
	 *
	 * @param	string	$name	Template name
	 *
	 * @return	object
	 * @since	1.0.0
	 */
	private function getTemplateInfoByName($name)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__template_styles'));
		$query->where($db->quoteName('client_id') . ' = 0');
		$query->where($db->quoteName('template') . ' = ' . $db->quote( $name ));

		$db->setQuery($query);

		return $db->loadObject();
	}

	/**
	 * Activate the installed plugin
	 *
	 * @param	string	$name	Plugin name
	 * @param	string	$group	Plugin group
	 *
	 * @return	void
	 * @since	1.0.0
	 */
	private function activeInstalledPlugin($name, $group)
	{
		$db = Factory::getDBO();
		$query = $db->getQuery(true);
		$fields = array(
			$db->quoteName('enabled') . ' = 1'
		);

		$conditions = array(
			$db->quoteName('type') . ' = ' . $db->quote('plugin'),
			$db->quoteName('element') . ' = ' . $db->quote($name),
			$db->quoteName('folder') . ' = ' . $db->quote($group)
		);

		$query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);
		$db->setQuery($query);
		$db->execute();
	}

	/**
	 * Get plugins information by name
	 *
	 * @param	string	$name	Plugin name
	 * @param	string	$group	Plugin group
	 *
	 * @return	object
	 * @since	1.0.0
	 */
	private function getPluginInfoByName($name, $group)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__extensions'));
		$query->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));
		$query->where($db->quoteName('element') . ' = ' . $db->quote( $name ));
		$query->where($db->quoteName('folder') . ' = ' . $db->quote( $group ));

		$db->setQuery($query);

		return $db->loadObject();
	}

	/**
	 * Abort installation
	 *
	 * @param	string	$msg	Abortion message
	 * @param	string	$type
	 *
	 * @return	void
	 * @since	1.0.0
	 */
	public function abort($msg = null, $type = null){
		if ($msg) {
			//JError::raiseWarning(100, $msg);
			Factory::getApplication()->enqueueMessage($msg, 100);
		}
		foreach ($this->packages as $package) {
			$package['installer']->abort(null, $type);
		}
	}
}
