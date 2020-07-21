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
		$isMenuBuilder = isset($attr['menu-builder']) && $attr['menu-builder'] === true;

		if ($isMenuBuilder)
		{
			$value = !empty($attr['value']) ? $attr['value'] : '';
		}

		$output   = '<div class="control-group hu-style-switcher">';
		$output  .= '<div class="checkbox clearfix">';
		$output  .= '<label class="control-label">' . $attr['title'];

		if (!empty($attr['desc']))
		{
			$output  .= '<span class="hu-help-icon hu-ml-2 fas fa-info-circle"></span>';
		}

		if ($isMenuBuilder)
		{
			$output .= '<input class="hu-input hu-menu-builder-' . $key . '" type="checkbox" name="' . $key . '" value="' . $value . '" />';
		}
		else
		{
			$output  .= '<input class="hu-input hu-input-' . $key . '" data-attrname="' . $key . '" type="checkbox">';
		}

		$output  .= '</label>';

		if (!empty($attr['desc']))
		{
			$output .= '<p class="control-help">' . $attr['desc'] . '</p>';
		}

		$output  .= '</div>';
		$output  .= '</div>';

		return $output;
	}

}
