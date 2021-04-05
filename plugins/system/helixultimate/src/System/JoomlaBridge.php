<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace HelixUltimate\Framework\System;

/**
 * Bridge between Joomla 3 and Joomla 4
 *
 * @since	2.0.0
 */
class JoomlaBridge
{
	/**
	 * Asset mapping between Joomla 3 and 4
	 *
	 * @var		array	The mapping array
	 * @since	2.0.0
	 */
	private static $assetMap = [];

	/**
	 * Joomla! core version with type.
	 *
	 * @param	string	$type	The version type. Available values are major, minor and patch.
	 *
	 * @return	int|string		Full version string if type is omitted, otherwise integer value of the version.
	 * @since	2.0.0
	 */
	public static function getVersion($type = '')
	{
		list($major, $minor, $patch) = explode('.', JVERSION);

		switch ($type)
		{
			case 'major':
				return (int) ($major ?? 0);
			case 'minor':
				return (int) ($minor ?? 0);
			case 'patch':
				return (int) ($patch ?? 0);
			default:
				return JVERSION;
		}
	}

	public static function getAssetMap() : array
	{
		/**
		 * The structure of the array is name => [j3, j4, j3AlreadyRegistered, j4AlreadyRegistered].
		 * That is asset name as key and first value for j3 and 2nd for j4.
		 */
		self::$assetMap = [
			'core'						=> ['core', 'core', ''],
			'jquery' 					=> ['jquery.framework', 'jquery'],
			'jquery-migrate'			=> ['', 'jquery-migrate'],
			'jquery-noconflict'			=> ['', 'jquery-noconflict'],
			'keepalive' 				=> ['behavior.keepalive', 'keepalive'],
			'script.chosen' 			=> ['formbehavior.chosen', 'vendor/chosen/chosen.jquery.js', 'registered'],
			'style.chosen' 				=> ['', 'vendor/chosen/chosen.css'],
			'script.colorPicker' 		=> ['jui/jquery.minicolors.min.js', 'vendor/minicolors/jquery.minicolors.min.js'],
			'style.colorPicker' 		=> ['jui/jquery.minicolors.css', 'vendor/minicolors/jquery.minicolors.css'],
			'cms' 						=> ['jui/cms.js', 'system/showon.min.js'],
			'script.bootstrap'			=> ['bootstrap.framework', 'vendor/bootstrap/bootstrap.min.js', 'registered'],
		];
	
		return self::$assetMap;
	}

}
