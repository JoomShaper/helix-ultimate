<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace HelixUltimate\Framework\Platform;

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

		return $path;
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
		$relativePath = '';
		$overridePath = '';
		
		/**
		 * If the callee file is in the template's html directory.
		 */
		if (\strpos($callPath, $staticHtmlPath) === 0)
		{
			$relativePath = \substr($callPath, \strlen($staticHtmlPath));
		}

		if (!empty($relativePath))
		{
			$overridePath = $staticOverridePath . $relativePath;
		}

		if (\file_exists($overridePath))
		{
			return $overridePath;
		}

		return '';
	}
}
