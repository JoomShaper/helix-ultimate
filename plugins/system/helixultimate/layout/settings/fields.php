<?php
/**
* @package Helix3 Framework
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2015 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

//no direct accees
defined ('_JEXEC') or die ('resticted aceess');

class HelixUltimateFieldsHelper
{
	protected function __construct()
	{
		$fields = JFolder::files( dirname( __FILE__ ) . '/fields', '\.php$', false, true);
		foreach ($fields as $field)
		{
			require_once $field;
		}
	}

	protected static function getInputElements($key, $attr)
	{
		return call_user_func(array('HelixUltimateField' . ucfirst($attr['field']), 'getInput'), $key, $attr);
	}
}
