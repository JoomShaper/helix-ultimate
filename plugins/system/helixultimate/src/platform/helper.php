<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace HelixUltimate\Framework\Platform;

use HelixUltimate\Framework\System\HelixCache;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

defined('_JEXEC') or die();

/**
 * Helix framework helper class
 *
 * @since   1.0.0
 */
class Helper
{
	/**
	 * Get template styles from Database.
	 *
	 * @param	integer		$id		The template ID.
	 *
	 * @return	object		Template data object.
	 * @since	1.0.0
	 */
	public static function getTemplateStyle($id = 0)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select(array('*'));
		$query->from($db->quoteName('#__template_styles'));

		$query->where($db->quoteName('client_id') . ' = 0');
		$query->where($db->quoteName('id') . ' = ' . $db->quote($id));

		$db->setQuery($query);

		return $db->loadObject();
	}

	/**
	 * Get template ID by template name.
	 *
	 * @param	string	$template	Template name.
	 *
	 * @return	integer				Template ID.
	 * @since	2.0.0
	 */
	public static function getTemplateId($template) : int
	{
		try
		{
			$db 	= Factory::getDbo();
			$query 	= $db->getQuery(true);

			$query->select('id')
				->from($db->quoteName('#__template_styles'))
				->where($db->quoteName('template') . ' = ' . $db->quote($template));

			$db->setQuery($query);

			return (int) $db->loadResult();
		}
		catch (Exception $e)
		{
			return 0;
		}
	}

	/**
	 * Update Helix template styles.
	 *
	 * @param	integer		$id		The helix template ID.
	 * @param	object		$data	The updated contents.
	 *
	 * @return	void
	 * @since	1.0.0
	 */
	public static function updateTemplateStyle($id = 0, $data = null)
	{
		if (empty($data))
		{
			return;
		}

		$keyOptions = [
			'option' => 'com_ajax',
			'helix' => 'ultimate',
			'status' => 'init'
		];

		$key = static::generateKey($keyOptions);
		$cache = new HelixCache($key);

		// If cache contains for the $key generated before
		if ($cache->contains())
		{
			$cachedData = $cache->loadData();
			$cachedData->params = new Registry($data);
			$cache->removeCache()->storeCache($cachedData);
		}

		$data = json_encode($data);

		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		$fields = array($db->quoteName('params') . ' = ' . $db->quote($data));
		$conditions = array(
			$db->quoteName('id') . ' = ' . $db->quote($id),
			$db->quoteName('client_id') . ' = 0'
		);

		$query->update($db->quoteName('#__template_styles'))->set($fields)->where($conditions);
		$db->setQuery($query);

		return $db->execute();
	}

	/**
	 * Get Helix Template version.
	 *
	 * @return	string	The version number.
	 * @since	1.0.0
	 */
	public static function getVersion()
	{
		$db = Factory::getDBO();
		$query = $db->getQuery(true);

		$query->select(array('*'));
		$query->from($db->quoteName('#__extensions'));

		$query->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
			->where($db->quoteName('element') . ' = ' . $db->quote('helixultimate'))
			->where($db->quoteName('folder') . ' = ' . $db->quote('system'));

		$db->setQuery($query);
		$result = $db->loadObject();

		$manifest_cache = json_decode($result->manifest_cache);

		if (isset($manifest_cache->version))
		{
			return $manifest_cache->version;
		}

		return;
	}

	/**
	 * Check if the data drafted or not.
	 *
	 * @return 	boolean	True if the data is drafted, false otherwise
	 * @since	2.0.0
	 */
	public static function isDrafted()
	{
		$draftKeyOptions = [
			'option' => 'com_ajax',
			'helix' => 'ultimate',
			'status' => 'draft'
		];

		$key = static::generateKey($draftKeyOptions);
		$cache = new HelixCache($key);

		return $cache->contains();
	}

	/**
	 * Generate a md5 cache key from option(s)
	 *
	 * @param	mixed	$options	string or array.
	 *
	 * @return	string	Cache key.
	 * @since	2.0.0
	 */
	public static function generateKey($options)
	{
		if (is_array($options))
		{
			$string = '';

			foreach ($options as $key => $option)
			{
				$string .= $key . ':' . $option . ';';
			}
		}
		elseif (is_string($options))
		{
			$string = $options;
		}
		else
		{
			$string = 'helixultimate';
		}

		return md5($string);
	}

	/**
	 * Load template data from cache or database.
	 *
	 * @return	object	Template style object
	 * @since	2.0.0
	 */
	public static function loadTemplateData()
	{
		$template = [];

		$draftKeyOptions = [
			'option' => 'com_ajax',
			'helix' => 'ultimate',
			'status' => 'draft'
		];

		$draftKey = static::generateKey($draftKeyOptions);
		$cache = new HelixCache($draftKey);

		if ($cache->contains())
		{
			$template = $cache->loadData();
		}
		else
		{
			$keyOptions = [
				'option' => 'com_ajax',
				'helix' => 'ultimate',
				'status' => 'init'
			];

			$key = static::generateKey($keyOptions);
			$cache->setCacheKey($key);

			if ($cache->contains())
			{
				$template = $cache->loadData();
			}
			else
			{
				$input = Factory::getApplication();

				$option = $input->get('option', '', 'STRING');
				$helix = $input->get('helix', '', 'STRING');
				$template_name = $input->get('template', 'shaper_helixultimate', 'STRING');

				if ($option === 'com_ajax' && $helix === 'ultimate')
				{
					$template_id = $input->get('id', 0, 'INT');
				}
				else
				{
					$template_id = static::getTemplateId($template_name);
				}

				$template = static::getTemplateStyle($template_id);
			}
		}

		if (!empty($template->params) && \is_string($template->params))
		{
			$template->params = new Registry($template->params);
		}

		return $template;
	}

	/**
	 * Flush settings data towards the javascript using addScriptOptions
	 *
	 * @return	void
	 * @since	2.0.0
	 */
	public static function flushSettingsDataToJs()
	{
		$doc = Factory::getDocument();

		$data = array(
			'breakpoints' => array(
				'tablet' => 991,
				'mobile' => 480
			),
			// 'topbarHeight' => 40
		);

		$doc->addScriptOptions('data', $data);
	}
}
