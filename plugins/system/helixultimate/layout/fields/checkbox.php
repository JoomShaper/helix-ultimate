<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

/**
 * Checkbox field
 *
 * @since	1.0.0
 */
class HelixultimateFieldCheckbox
{
	/**
	 * Get input for the field.
	 *
	 * @param	string	$key
	 * @param	array	$attr
	 *
	 * @return	string
	 * @since	1.0.0
	 */
	public static function getInput($key, $attr)
	{

		$output   = '<div class="control-group">';
		$output  .= '<div class="checkbox clearfix">';
		$output  .= '<label class="control-label">' . $attr['title'];
		$output  .= '<input class="hu-input hu-input-' . $key . '" data-attrname="' . $key . '" type="checkbox">';
		$output  .= '</label>';
		$output  .= '</div>';

		if ((isset($attr['desc'])) && (isset($attr['desc']) !== ''))
		{
			$output .= '<p class="control-help">' . $attr['desc'] . '</p>';
		}

		$output  .= '</div>';

		return $output;
	}

}
