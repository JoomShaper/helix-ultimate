<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace HelixUltimate\Framework\Platform;

use Joomla\CMS\Filesystem\Path;

/**
 * Static class for managing the overrides.
 *
 * @since 2.0.2
 */
final class HTMLOverride
{

	/**
	 * The HTML path from the template.
	 *
	 * @var 	string	$htmlPath	The template's HTML directory path.
	 * @since 	2.0.2
	 */
	private static $htmlPath = JPATH_ROOT . '/templates/{{template}}/html';

	/**
	 * The `overrides` directory path from the plugin
	 *
	 * @var 	string	$overridePath	The plugin's overrides directory path.
	 * @since 	2.0.2
	 */
	private static $overridePath = JPATH_ROOT . '/plugins/system/helixultimate/overrides';

	/**
	 * The template override path from the template.
	 *
	 * @var 	string	$tmplOverridePath	The template's override path.
	 * @since 	2.0.3
	 */
	private static $tmplOverridePath = JPATH_ROOT . '/templates/{{template}}/overrides';

	/**
	 * Parse the path with proper template name.
	 *
	 * @param 	string 	$path	The location path.
	 *
	 * @return 	string	The parsed path.
	 * @since 	2.0.2
	 */
	private static function parsePath(string $path): string
	{
		$template = Helper::loadTemplateData();
		$path = \preg_replace("@\{\{template\}\}@", $template->template, $path);

		return Path::clean($path);
	}

	private static function extractPath(string $path): array
	{
		return explode('/', trim($path, '/'));
	}

	private static function generateComponentPath(string $path) : string
	{
		if (empty($path))
		{
			return '';
		}

		$path = self::extractPath($path);

		$version = JVERSION;
		$extension = $path[0];

		/** If the path is for component- */
		if (\strpos($extension, 'com_') === 0)
		{
			if ($version < 4)
			{
				\array_splice($path, 1, 0, ['views']);
				\array_splice($path, 3, 0, ['tmpl']);
			}
			else
			{
				\array_splice($path, 1, 0, ['tmpl']);
			}

			return JPATH_ROOT . '/components/' . \implode('/', $path);
		}
		elseif (\strpos($extension, 'mod_') === 0)
		{
			\array_splice($path, 1, 0, ['tmpl']);

			return JPATH_ROOT . '/modules/' . \implode('/', $path);
		}
		elseif ($extension === 'layouts')
		{
			return JPATH_ROOT . '/' . \implode('/', $path);
		}

		return \implode('/', $path);
	}

	/**
	 * load the template HTML from the plugin.
	 *
	 * @return	void
	 * @since 	2.0.2
	 */
	public static function loadTemplate(): string
	{
		$backtrace = \debug_backtrace();
		$callPath = $backtrace[0]['file'] ?? '';
		$staticHtmlPath = self::parsePath(self::$htmlPath);
		$staticOverridePath = self::parsePath(self::$overridePath);
		$templateOverrideUri = self::parsePath(self::$tmplOverridePath);

		$relativePath = '';
		$overridePath = '';

		/**
		 * If the callee file is in the template's html directory.
		 */
		if (\strpos($callPath, $staticHtmlPath) === 0)
		{
			$relativePath = \substr($callPath, \strlen($staticHtmlPath));
		}

		$templateOverridePath = $templateOverrideUri . $relativePath;

		if (\file_exists($templateOverridePath))
		{
			return $templateOverridePath;
		}

		$pluginOverridePath = $staticOverridePath . $relativePath;

		if (\file_exists($pluginOverridePath))
		{
			return $pluginOverridePath;
		}

		return self::generateComponentPath($relativePath);
	}
}
