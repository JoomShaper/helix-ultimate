<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use Joomla\CMS\Filesystem\Folder;

/**
 * Fields helper
 *
 * @since	1.0.0
 */
class HelixUltimateFieldsHelper
{
	protected function __construct()
	{
		$fields = Folder::files(dirname(__FILE__) . '/fields', '\.php$', false, true);

		foreach ($fields as $field)
		{
			require_once $field;
		}
	}

	protected static function getInputElements($key, $attr)
	{
		return call_user_func(array('HelixultimateField' . ucfirst($attr['field']), 'getInput'), $key, $attr);
	}
}
