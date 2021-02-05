<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace HelixUltimate\Framework\Platform;

use HelixUltimate\Framework\System\HelixCache;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Language\Text;
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
			'status' => 'init',
			'id' => $id
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
		$app = Factory::getApplication();
		$template = $app->getTemplate(true);
		$templateId = 0;

		if ($app->isClient('site'))
		{
			if ($template->template === 'shaper_helixultimate')
			{
				$templateId = $template->id;
			}
		}
		else
		{
			if ($app->input->get('option') === 'com_ajax' && $app->input->get('helix') === 'ultimate')
			{
				$templateId = $app->input->get('id', 0, 'INT');
			}
		}

		if (empty($templateId))
		{
			$templateId = self::getTemplateId('shaper_helixultimate');
		}

		$draftKeyOptions = [
			'option' => 'com_ajax',
			'helix' => 'ultimate',
			'status' => 'draft',
			'id' => $templateId
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
		$templateId = 0;

		$app = Factory::getApplication();
		$currentTemplate = $app->getTemplate(true);

		if ($app->isClient('site'))
		{
			$templateId = $currentTemplate->id;
		}
		else
		{
			if ($app->input->get('option') === 'com_ajax' && $app->input->get('helix') === 'ultimate')
			{
				$templateId = $app->input->get('id', 0, 'INT');
			}
		}

		/**
		 * If still the template ID not found then try to get the
		 * template ID from db.
		 */
		if (empty($templateId))
		{
			$templateId = self::getTemplateId('shaper_helixultimate');
		}

		$template = [];

		$draftKeyOptions = [
			'option' => 'com_ajax',
			'helix' => 'ultimate',
			'status' => 'draft',
			'id' => $templateId
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
				'status' => 'init',
				'id' => $templateId
			];

			$key = static::generateKey($keyOptions);
			$cache->setCacheKey($key);

			if ($cache->contains())
			{
				$template = $cache->loadData();
			}
			else
			{
				$template = static::getTemplateStyle($templateId);				
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

	public static function getModules($keyword = '')
	{
		$modules = [];

		if (!empty($keyword))
		{
			$keyword = preg_replace("@\s+@", ' ', trim($keyword));
			$keyword = implode('|', explode(' ', $keyword));
		}

		try
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select('DISTINCT m.id, m.title, m.module, m.position, m.params, e.manifest_cache')
				->from($db->quoteName('#__modules', 'm'))
				->where($db->quoteName('m.client_id') . ' = 0');
			$query->join('LEFT', $db->quoteName('#__extensions', 'e') . ' ON (' . $db->quoteName('e.element') . ' = ' . $db->quoteName('m.module') . ')');
			
			if (!empty($keyword))
			{
				$query->where($db->quoteName('m.title') . ' REGEXP ' . $db->quote($keyword));
			}

			$query->order($db->quoteName('m.title') . ' ASC');
			$db->setQuery($query);

			$modules = $db->loadObjectList();
		}
		catch (Exception $e)
		{
			return [];
		}

		$lang = Factory::getLanguage();
		$client = ApplicationHelper::getClientInfo(0);

		if (!empty($modules))
		{
			foreach ($modules as &$module)
			{
				$module->desc = Text::_('COM_MODULES_NODESCRIPTION');

				if (isset($module->manifest_cache) && \is_string($module->manifest_cache))
				{
					$lang->load($module->module . '.sys', $client->path, null, false, true)
						|| $lang->load($module->module . '.sys', $client->path . '/modules/' . $module->module, null, false, true);

					$module->manifest_cache = \json_decode($module->manifest_cache);
					
					if (!empty($module->manifest_cache->description))
					{
						$module->desc = Text::_($module->manifest_cache->description);
					}
					else
					{
						$module->desc = Text::_('COM_MODULES_NODESCRIPTION');
					}
				}
			}

			unset($module);
		}

		return $modules;
	}

	/**
	 * Get template position
	 */
	public static function getTemplatePositions()
	{
		$positions = array();

		$templateBaseDir = JPATH_SITE;
		$filePath = Path::clean($templateBaseDir . '/templates/shaper_helixultimate/templateDetails.xml');

		if (is_file($filePath))
		{
			// Read the file to see if it's a valid component XML file
			$xml = simplexml_load_file($filePath);

			if (!$xml)
			{
				return false;
			}

			// Check for a valid XML root tag.
			// Extensions use 'extension' as the root tag.  Languages use 'metafile' instead
			if ($xml->getName() != 'extension' && $xml->getName() != 'metafile')
			{
				unset($xml);

				return false;
			}

			$positions = (array) $xml->positions;

			if (isset($positions['position']))
			{
				$positions = (array) $positions['position'];
			}
			else
			{
				$positions = array();
			}
		}

		return $positions;
	}

	public static function getMenuItems($parentId, &$menuItemList)
	{
		$elements = [];

		try
		{
			$db 	= Factory::getDbo();
			$query 	= $db->getQuery(true);
			$query->select('id, title, parent_id, level')
				->from($db->quoteName('#__menu'))
				->where($db->quoteName('parent_id') . ' = ' . (int) $parentId)
				->where($db->quoteName('published') . ' = 1')
				->where($db->quoteName('client_id') . ' = 0');
			$db->setQuery($query);

			$elements = $db->loadObjectList();
		}
		catch (Exception $e)
		{
			return [];
		}

		if (!empty($elements))
		{
			foreach ($elements as $element)
			{
				if (isset($menuItemList->$parentId))
				{
					$menuItemList->$parentId->children[] = $element->id;
				}

				$temp = new \stdClass;
				$temp->id = $element->id;
				$temp->title = $element->title;
				$temp->level = $element->level;
				$temp->children = [];
				$menuItemList->{$element->id} = $temp;

				self::getMenuItems($element->id, $menuItemList);
			}
		}
	}
}
