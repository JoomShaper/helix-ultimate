<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace HelixUltimate\Framework\Platform;

use DateTime;
use HelixUltimate\Framework\Platform\Provider;
use HelixUltimate\Framework\System\HelixCache;
use HelixUltimate\Framework\System\JoomlaBridge;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

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
		static $cache = [];
		if (isset($cache[$id])) {
			return $cache[$id];
		}
		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select('*')
			->from($db->quoteName('#__template_styles'))
			->where($db->quoteName('id') . ' = ' . (int) $id);

		$db->setQuery($query);

		$cache[$id] = $db->loadObject();

		return $cache[$id];
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

			if (Multilanguage::isEnabled())
			{
				$query->where($db->quoteName('home') . ' IN(' . $db->quote(Factory::getLanguage()->getTag()) . ', ' . $db->quote('1', false));
			}

			$db->setQuery($query);

			return (int) $db->loadResult();
		}
		catch (\Exception $e)
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

		$key = self::generateKey($keyOptions);
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

		$manifest_cache = json_decode($result->manifest_cache ?? "");

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
			$templateId = $template->id;
		}
		else
		{
			if ($app->input->get('option') === 'com_ajax' && $app->input->get('helix') === 'ultimate')
			{
				$templateId = $app->input->get('id', 0, 'INT');
			}
		}

		$draftKeyOptions = [
			'option' => 'com_ajax',
			'helix' => 'ultimate',
			'status' => 'draft',
			'id' => $templateId
		];

		$key = self::generateKey($draftKeyOptions);
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

	private static function checkTemplateStyleValidity(int $id) : bool
	{
		$db 	= Factory::getDbo();
		$query 	= $db->getQuery(true);
		$query->select('id')->from($db->quoteName('#__template_styles'))
			->where($db->quoteName('id') . ' = ' . $id);
		$db->setQuery($query);
		$result = $db->loadResult();

		return isset($result);
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

        if ($app->isClient('site'))
        {
            $currentTemplate = $app->getTemplate(true);
            $templateId = $currentTemplate->id ?? 0;

			/**
			 * If a page/menu is assigned to a specific template
			 * then get the template ID.
			 */
			$activeMenu = $app->getMenu()->getActive();

			if (!empty($activeMenu) && !empty($activeMenu->template_style_id))
			{
				$templateId = $activeMenu->template_style_id;
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
			$templateId = $app->input->get('helix_id', 0, 'INT');
		}

        if($templateId)
        {
            $template = [];

            $draftKeyOptions = [
                'option' => 'com_ajax',
                'helix' => 'ultimate',
                'status' => 'draft',
                'id' => $templateId
            ];

            $draftKey = self::generateKey($draftKeyOptions);
            $cache = new HelixCache($draftKey);

            /**
             * Check the fetch destination. If it is iframe then load the settings
             * from draft, otherwise if it is document that means this request
             * comes from the original site visit. So load from saved cache.
             */
            $requestFromIframe = $app->input->get('helixMode', '') === 'edit';
    
            if ($cache->contains() && $requestFromIframe)
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
    
                $key = self::generateKey($keyOptions);
                $cache->setCacheKey($key);
    
                if ($cache->contains())
                {
                    $template = $cache->loadData();
                }
                else
                {
                    $template = self::getTemplateStyle($templateId);        
                }
            }

			if (isset($template->template) && !empty($template->template))
			{
				if (!empty($template->params) && \is_string($template->params))
				{
					$template->params = new Registry($template->params);
				}

				/**
				 * If params field is found empty in the database or cache then
				 * read the default options.json file from the template and assign
				 * the options as template params.
				 */
				elseif (empty($template->params))
				{
					$filePath = JPATH_ROOT  . '/templates/' . $template->template . '/' . 'options.json';

					if (\file_exists($filePath))
					{
						$defaultParams = \file_get_contents($filePath);
						$template->params = new Registry($defaultParams);
					}
					else
					{
						$template->params = new Registry;
					}
				}

				return $template;
			}
        }

        $template = new \stdClass;
        $template->template = 'system';
        $template->params = new Registry;

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
		
		$loadTemplateData = self::loadTemplateData();
		$stickyOffset	= $loadTemplateData->params->get('sticky_offset', '100');

		$data = array(
			'breakpoints' => array(
				'tablet' => 991,
				'mobile' => 480
			),
			'header' => array(
				'stickyOffset' => $stickyOffset
			)
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
		catch (\Exception $e)
		{
			return [];
		}

		$lang = Factory::getLanguage();
		$client = ApplicationHelper::getClientInfo(0);

		if (!empty($modules))
		{
			foreach ($modules as &$module)
			{
				$module->desc = '';

				if (isset($module->manifest_cache) && \is_string($module->manifest_cache))
				{
					// $lang->load($module->module . '.sys', $client->path, null, false, true)
					// 	|| $lang->load($module->module . '.sys', $client->path . '/modules/' . $module->module, null, false, true);

					$module->manifest_cache = \json_decode($module->manifest_cache ?? "");
					
					if (!empty($module->manifest_cache->description))
					{
						// $module->desc = Text::_($module->manifest_cache->description);
					}
					else
					{
						// $module->desc = Text::_('COM_MODULES_NODESCRIPTION');
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
		$template = self::loadTemplateData();

		$templateBaseDir = JPATH_SITE;
		$filePath = Path::clean($templateBaseDir . '/templates/' . $template->template . '/templateDetails.xml');

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
		catch (\Exception $e)
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

				$elementId = $element->id;
				$temp = new \stdClass;
				$temp->id = $element->id;
				$temp->title = $element->title;
				$temp->level = $element->level;
				$temp->children = [];
				$menuItemList->$elementId = $temp;

				self::getMenuItems($element->id, $menuItemList);
			}
		}
	}

	/**
	 * Get the search module for the pre-defined headers.
	 *
	 * @return	Object	The module object.
	 * @since	2.0.0
	 */
	public static function getSearchModule()
	{
		$version = JoomlaBridge::getVersion('major');
		$name = $version < 4 ? 'mod_search' : 'mod_finder';

		$module = self::createModule($name, [
			'title' => 'Search',
			'params' => '{"show_label": 0, "label":"","width":20,"text":"","button":0,"button_pos":"right","imagebutton":0,"button_text":"","opensearch":1,"opensearch_title":"","set_itemid":0,"layout":"_:default","moduleclass_sfx":"","cache":1,"cache_time":900,"cachemode":"itemid","module_tag":"div","bootstrap_size":"0","header_tag":"h3","header_class":"","style":"0"}'
		]);

		return $module;
	}

	/**
	 * Create a module object which is not created or published.
	 *
	 * @param	string	$name		The module name with mod_ prefixed.
	 * @param	array	$options	The module options.
	 *
	 * @return	object	The module object.
	 * @since	2.0.0
	 */
	public static function createModule($name, $options = [])
	{
		if (empty($name))
		{
			throw new \Exception(\sprintf('%s method expect the module $name as first argument!', __METHOD__));
		}

		if (!empty($options) && \is_object($options))
		{
			$options = (array) $options;
		}

		$defaultOptions = ['id' => 0, 'title' => '', 'module' => $name, 'position' => '', 'content' => '', 'showtitle' => 0, 'control' => '', 'params' => '', 'menuid' => 0, 'style' => ''];

		return ArrayHelper::toObject(\array_merge($defaultOptions, $options));
	}

	/**
	 * Check a string ends with a needle or not.
	 *
	 * @param	string	$haystack	The main string.
	 * @param	string	$needle		The needle to search at the end.
	 *
	 * @return	bool	True if find at the end, false otherwise.
	 * @since 	2.0.2
	 */
	public static function endsWith(string $haystack, string $needle): bool
	{
		$isEight = \version_compare(PHP_VERSION, '8.0.0') >= 0;
		$length = strlen($needle);

		if ($isEight)
		{
			return \str_ends_with($haystack, $needle);
		}

		return !$length ? true : substr($haystack, -$length) === $needle;
	}

	/**
	 * Check a string starts with a needle or not.
	 *
	 * @param	string	$haystack	The main string.
	 * @param	string	$needle		The needle to search at the beginning.
	 *
	 * @return	bool	True if find at the starting position, false otherwise.
	 * @since 	2.0.2
	 */
	public static function startsWith(string $haystack, string $needle): bool
	{
		$isEight = \version_compare(PHP_VERSION, '8.0.0') >= 0;
		$length = strlen($needle);

		if ($isEight)
		{
			return \str_starts_with($haystack, $needle);
		}

		return substr($haystack, 0, $length) === $needle;
	}

	private static function getMenuAliasById(int $pageId)
	{
		$db 	= Factory::getDbo();
		$query 	= $db->getQuery(true);
		$query->select('alias')
			->from($db->quoteName('#__menu'))
			->where($db->quoteName('link') . ' = ' . $db->quote('index.php?option=com_sppagebuilder&view=page&id=' . $pageId));
		$db->setQuery($query);

		try
		{
			return $db->loadResult();
		}
		catch (\Exception $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage());
			return '404';
		}

		return '404';
	}
	
	public static function renderPage(string $code, int $pageId)
	{
		$app = Factory::getApplication();
		$config = $app->getConfig();
		$sef = $config->get('sef');
		$sef_rewrite = $config->get('sef_rewrite');
		$sef_suffix = $config->get('sef_suffix');

		$redirect_url = Uri::base();

		if(!$sef_rewrite)
		{
			$redirect_url .= 'index.php/';
		}

		$redirect_url .= self::getMenuAliasById($pageId);

		if($sef_suffix)
		{
			$redirect_url .= '.html';
		}

		// If sef is turned off
		if(!$sef)
		{
			$redirect_url = 'index.php?option=com_sppagebuilder&view=page&id=' . $pageId;
		}

		if ($code == '404')
		{
			header('Location: ' . $redirect_url, true, 301);
			exit;
		}
	}

	/**
	 * Function to set default column
	 *
	 * @param integer $num_columns
	 * @param integer $default
	 * @return integer
	 */
	public static function SetColumn($num_columns, $default = 3)
	{
		return empty($num_columns) ? $default : $num_columns;
	}

	/**
	 * Function to check if Null then replace with empty string [for php 8.1 fix]
	 *
	 * @return string
	 */
	public static function CheckNull($value = null)
	{
		return ($value == null) ? '' : $value;
	}

	/**
	 * Helix article attribs keys allowed to merge on frontend save.
	 *
	 * @return  array
	 * @since   2.0.19-j3sec
	 */
	public static function getHelixAttribKeys()
	{
		return array(
			'helix_ultimate_image',
			'helix_ultimate_image_alt_txt',
			'helix_ultimate_article_format',
			'helix_ultimate_audio',
			'helix_ultimate_gallery',
			'helix_ultimate_video',
		);
	}

	/**
	 * Map Helix AJAX actions to required Joomla permissions (administrator).
	 *
	 * @return  array
	 * @since   2.0.19-j3sec
	 */
	public static function getActionPermissions()
	{
		$templateActions = array(
			'save-tmpl-style',
			'draft-tmpl-style',
			'reset-drafted-settings',
			'save-layout',
			'render-layout',
			'remove-layout-file',
			'purge-css-file',
			'import-tmpl-style',
			'update-font-list',
			'fontVariants',
			'view-media',
			'delete-media',
			'create-folder',
			'upload-media',
		);

		$menuActions = array(
			'getMenuItems',
			'parentAdoption',
			'rebuildMenu',
			'generateMegaMenuBody',
			'saveMegaMenuSettings',
			'updateRowLayout',
			'generateRow',
			'generatePopoverContents',
			'generateNewCell',
			'getModuleList',
		);

		$blogActions = array(
			'upload-blog-image',
			'remove-blog-image',
		);

		$permissions = array();

		foreach ($templateActions as $action)
		{
			$permissions[$action] = array('com_templates' => 'core.edit');
		}

		foreach ($menuActions as $action)
		{
			$permissions[$action] = array('com_menus' => 'core.edit');
		}

		foreach ($blogActions as $action)
		{
			$permissions[$action] = array('com_content' => 'core.edit');
		}

		return $permissions;
	}

	/**
	 * Site-client permission overrides for frontend AJAX actions.
	 *
	 * @return  array
	 * @since   2.0.19-j3sec
	 */
	public static function getSiteActionPermissions()
	{
		return array(
			'upload-blog-image' => array(
				'com_content' => 'core.edit',
				'com_media'   => 'core.create',
			),
			'remove-blog-image' => array(
				'com_content' => 'core.edit',
				'com_media'   => 'core.delete',
			),
			'view-media' => array(
				'com_media' => 'core.create',
			),
			'delete-media' => array(
				'com_media' => 'core.delete',
			),
			'upload-media' => array(
				'com_media' => 'core.create',
			),
		);
	}

	/**
	 * Check whether the current user may execute a Helix AJAX action.
	 *
	 * @param   string  $action  Action name.
	 *
	 * @return  bool
	 * @since   2.0.19-j3sec
	 */
	public static function authorizeAction($action)
	{
		$app = Factory::getApplication();
		$user = Factory::getUser();

		if (!$user || !$user->id)
		{
			return false;
		}

		$map = $app->isClient('site')
			? self::getSiteActionPermissions()
			: self::getActionPermissions();

		if (!isset($map[$action]))
		{
			return false;
		}

		foreach ($map[$action] as $asset => $permission)
		{
			if (!$user->authorise($permission, $asset))
			{
				if ($asset === 'com_content' && $permission === 'core.edit'
					&& $user->authorise('core.edit.own', 'com_content'))
				{
					continue;
				}

				return false;
			}
		}

		return true;
	}

	/**
	 * Enforce CSRF token and ACL for a Helix AJAX action.
	 *
	 * @param   string  $action  Action name.
	 *
	 * @return  void
	 * @since   2.0.19-j3sec
	 */
	public static function guardAjaxRequest($action)
	{
		$report = array(
			'status'  => false,
			'message' => Text::_('JINVALID_TOKEN'),
			'output'  => Text::_('JINVALID_TOKEN'),
		);

		if (!Session::checkToken())
		{
			die(json_encode($report));
		}

		$report['message'] = Text::_('JERROR_ALERTNOAUTHOR');
		$report['output']  = Text::_('JERROR_ALERTNOAUTHOR');

		if (!self::authorizeAction($action))
		{
			die(json_encode($report));
		}
	}

	/**
	 * Sanitize a layout file name for template layout JSON storage.
	 *
	 * @param   string  $name  Layout name from request data.
	 *
	 * @return  string|null  Safe filename including .json extension.
	 * @since   2.0.19-j3sec
	 */
	public static function sanitizeLayoutName($name)
	{
		$name = basename(str_replace('\\', '/', $name));
		$name = preg_replace('/\.json$/i', '', $name);

		if ($name === null || !preg_match('/^[A-Za-z0-9_-]+$/', $name))
		{
			return null;
		}

		return $name . '.json';
	}

	/**
	 * Resolve and validate a media path under the configured media/image root.
	 *
	 * @param   string  $path  Relative path (with or without leading slash).
	 *
	 * @return  string|null  Absolute filesystem path or null if invalid.
	 * @since   2.0.19-j3sec
	 */
	public static function resolveMediaPath($path)
	{
		$path = trim(str_replace('\\', '/', $path));

		if ($path === '' || strpos($path, '..') !== false)
		{
			return null;
		}

		$path = ltrim($path, '/');
		$params = ComponentHelper::getParams('com_media');
		$mediaRoot = trim($params->get('image_path', 'images'), '/');
		$allowedRoots = array_unique(array($mediaRoot, 'images'));

		$fullPath = Path::clean(JPATH_ROOT . '/' . $path);
		$isAllowed = false;

		foreach ($allowedRoots as $root)
		{
			$allowedPath = Path::clean(JPATH_ROOT . '/' . $root);

			if ($fullPath === $allowedPath || strpos($fullPath, $allowedPath . '/') === 0)
			{
				$isAllowed = true;
				break;
			}
		}

		if (!$isAllowed)
		{
			return null;
		}

		try
		{
			Path::check($fullPath);
		}
		catch (\Exception $e)
		{
			return null;
		}

		return $fullPath;
	}

	/**
	 * Check whether the current user may edit a content article.
	 *
	 * @param   int  $articleId  Article ID.
	 *
	 * @return  bool
	 * @since   2.0.19-j3sec
	 */
	public static function canEditArticle($articleId)
	{
		$user = Factory::getUser();

		if (!$user || !$user->id || $articleId <= 0)
		{
			return false;
		}

		if ($user->authorise('core.edit', 'com_content'))
		{
			return true;
		}

		if (!$user->authorise('core.edit.own', 'com_content'))
		{
			return false;
		}

		$db = Factory::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName('created_by'))
			->from($db->quoteName('#__content'))
			->where($db->quoteName('id') . ' = ' . (int) $articleId);

		$db->setQuery($query);

		return (int) $db->loadResult() === (int) $user->id;
	}

	/**
	 * Validate a base64-encoded internal redirect URL.
	 *
	 * @param   string  $encoded  Base64-encoded URL.
	 *
	 * @return  string|null  Safe internal URL or null.
	 * @since   2.0.19-j3sec
	 */
	public static function validateInternalRedirect($encoded)
	{
		$decoded = base64_decode($encoded, true);

		if ($decoded === false || $decoded === '')
		{
			return null;
		}

		if (!Uri::isInternal($decoded))
		{
			return null;
		}

		return $decoded;
	}

	/**
	 * Sanitize embed HTML using an allowlist of safe tags and attributes.
	 *
	 * @param   string  $html  Raw embed HTML.
	 *
	 * @return  string
	 * @since   2.0.19-j3sec
	 */
	public static function sanitizeEmbed($html)
	{
		if ($html === '')
		{
			return '';
		}

		$filter = InputFilter::getInstance(
			array('iframe', 'audio', 'video', 'source', 'a', 'img'),
			array('src', 'href', 'type', 'controls', 'width', 'height', 'allow', 'allowfullscreen', 'frameborder', 'alt', 'class', 'style'),
			1,
			1
		);

		return $filter->clean($html, 'html');
	}

	/**
	 * Sanitize mega menu settings before persisting to menu item params.
	 *
	 * @param   array  $settings  Raw settings from request.
	 *
	 * @return  array
	 * @since   2.0.19-j3sec
	 */
	public static function sanitizeMegaMenuSettings(array $settings)
	{
		$clean = array();

		$clean['megamenu'] = !empty($settings['megamenu']) ? 1 : 0;
		$clean['showtitle'] = !empty($settings['showtitle']) ? 1 : 0;

		$clean['menualign'] = self::sanitizeMegaMenuEnum(
			isset($settings['menualign']) ? $settings['menualign'] : '',
			array('left', 'center', 'right', 'full'),
			'full'
		);
		$clean['dropdown'] = self::sanitizeMegaMenuEnum(
			isset($settings['dropdown']) ? $settings['dropdown'] : '',
			array('left', 'right'),
			'right'
		);
		$clean['badge_position'] = self::sanitizeMegaMenuEnum(
			isset($settings['badge_position']) ? $settings['badge_position'] : '',
			array('left', 'right'),
			'right'
		);

		$clean['width'] = self::sanitizeMegaMenuWidth(isset($settings['width']) ? $settings['width'] : '600px');
		$clean['customclass'] = self::sanitizeMegaMenuCustomClass(isset($settings['customclass']) ? $settings['customclass'] : '');
		$clean['faicon'] = self::sanitizeMegaMenuFaIcon(isset($settings['faicon']) ? $settings['faicon'] : '');
		$clean['badge'] = self::sanitizeMegaMenuBadge(isset($settings['badge']) ? $settings['badge'] : '');
		$clean['badge_bg_color'] = self::sanitizeMegaMenuColor(isset($settings['badge_bg_color']) ? $settings['badge_bg_color'] : '');
		$clean['badge_text_color'] = self::sanitizeMegaMenuColor(isset($settings['badge_text_color']) ? $settings['badge_text_color'] : '');

		$layout = isset($settings['layout']) ? $settings['layout'] : array();

		if (!is_array($layout))
		{
			$layout = array();
		}

		$clean['layout'] = self::sanitizeMegaMenuLayout($layout);

		return $clean;
	}

	public static function sanitizeMegaMenuCustomClass($value)
	{
		$value = (string) $value;

		if (preg_match('/[<>"\'=]/', $value))
		{
			return '';
		}

		$value = strip_tags($value);
		$value = preg_replace('/[^a-zA-Z0-9_\-\s]/', '', $value);

		return trim(preg_replace('/\s+/', ' ', $value));
	}

	public static function sanitizeMegaMenuFaIcon($value)
	{
		$value = trim(strip_tags((string) $value));

		if ($value === '')
		{
			return '';
		}

		if (!preg_match('/^fa[sbr]?\s+fa-[a-z0-9-]+$/i', $value))
		{
			return '';
		}

		return $value;
	}

	public static function sanitizeMegaMenuBadge($value)
	{
		return htmlspecialchars(trim(strip_tags((string) $value)), ENT_QUOTES, 'UTF-8');
	}

	public static function sanitizeMegaMenuColor($value)
	{
		$value = htmlspecialchars(trim(strip_tags((string) $value)), ENT_QUOTES, 'UTF-8');

		if ($value === '')
		{
			return '';
		}

		if (!preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/', $value))
		{
			return '';
		}

		return $value;
	}

	private static function sanitizeMegaMenuWidth($value)
	{
		$value = htmlspecialchars(trim(strip_tags((string) $value)), ENT_QUOTES, 'UTF-8');

		if (preg_match('/^[0-9]+(px|%|em|rem)$/', $value))
		{
			return $value;
		}

		return '600px';
	}

	private static function sanitizeMegaMenuEnum($value, array $allowed, $default)
	{
		$value = htmlspecialchars(trim(strip_tags((string) $value)), ENT_QUOTES, 'UTF-8');

		return in_array($value, $allowed, true) ? $value : $default;
	}

	private static function sanitizeMegaMenuLayout(array $layout)
	{
		$clean = array();

		foreach ($layout as $row)
		{
			if (!is_array($row) && !is_object($row))
			{
				continue;
			}

			$row = (array) $row;
			$cleanRow = array(
				'type' => 'row',
				'attr' => array(),
			);

			$columns = isset($row['attr']) ? $row['attr'] : array();

			if (!is_array($columns))
			{
				$columns = array();
			}

			foreach ($columns as $column)
			{
				if (!is_array($column) && !is_object($column))
				{
					continue;
				}

				$column = (array) $column;
				$cleanColumn = array(
					'type' => 'column',
					'colGrid' => self::sanitizeMegaMenuColGrid(isset($column['colGrid']) ? $column['colGrid'] : '12'),
					'menuParentId' => (string) (int) (isset($column['menuParentId']) ? $column['menuParentId'] : 0),
					'moduleId' => (string) (int) (isset($column['moduleId']) ? $column['moduleId'] : 0),
					'items' => array(),
				);

				$items = isset($column['items']) ? $column['items'] : array();

				if (is_array($items))
				{
					foreach ($items as $cell)
					{
						if (!is_array($cell) && !is_object($cell))
						{
							continue;
						}

						$cell = (array) $cell;
						$type = (isset($cell['type']) ? $cell['type'] : '') === 'module' ? 'module' : 'menu_item';
						$cellId = (string) (int) (isset($cell['item_id']) ? $cell['item_id'] : (isset($cell['id']) ? $cell['id'] : 0));
						$cleanCell = array(
							'type' => $type,
							'item_id' => $cellId,
						);

						if ($type === 'module')
						{
							$cleanCell['moduleId'] = (string) (int) (isset($cell['moduleId']) ? $cell['moduleId'] : (isset($cell['item_id']) ? $cell['item_id'] : (isset($cell['id']) ? $cell['id'] : 0)));
						}

						$cleanColumn['items'][] = $cleanCell;
					}
				}

				$cleanRow['attr'][] = $cleanColumn;
			}

			$clean[] = $cleanRow;
		}

		return $clean;
	}

	private static function sanitizeMegaMenuColGrid($value)
	{
		$value = htmlspecialchars(trim(strip_tags((string) $value)), ENT_QUOTES, 'UTF-8');

		if (preg_match('/^[0-9]{1,2}$/', $value))
		{
			$grid = (int) $value;

			if ($grid >= 1 && $grid <= 12)
			{
				return (string) $grid;
			}
		}

		return '12';
	}

	/**
	 * Count read time buy content text
	 *
	 * @param string $text
	 * @return string
	 */
	public static function getReadTime($text)
	{
		$words_per_minute 	= 200;
		$word_count 		= str_word_count(strip_tags($text));
		$read_time			= ceil($word_count / $words_per_minute);

		if ($read_time == 1) { //grammar conversion
			$label = Text::_('HELIX_ULTIMATE_BLOG_MINUTE_READ');
		} else {
			$label = Text::_('HELIX_ULTIMATE_BLOG_MINUTES_READ');
		}
			
		$totalString = $read_time . " " . $label; //adds time with minute/minutes label

		return $totalString;
	}
}
