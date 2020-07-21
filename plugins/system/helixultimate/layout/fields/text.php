<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

/**
 * Text field.
 *
 * @since	 1.0.0
 */
class HelixultimateFieldText
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

		$attributes = (isset($attr['placeholder']) && $attr['placeholder']) ? 'placeholder="' . $attr['placeholder'] . '"' : '';

		if ($isMenuBuilder)
		{
			$value = !empty($attr['value']) ? $attr['value'] : '';
		}

		$output  = '<div class="control-group">';
		$output .= '<label>' . $attr['title'] . '</label>';

		if (!empty($attr['desc']))
		{
			$output .= '<span class="hu-help-icon hu-ml-2 fas fa-info-circle"></span>';
			$output .= '<p class="control-help">' . $attr['desc'] . '</p>';
		}

		if ($isMenuBuilder)
		{
			$output .= '<input class="hu-input hu-menu-builder-' . $key . '" type="text" name="' . $key . '" value="' . $value . '" ' . $attributes . ' />';
		}
		else
		{
			$output	.= '<input class="hu-input addon-' . $key . '" type="text" data-attrname="' . $key . '" value="" ' . $attributes . ' />';
		}

		$output .= '</div>';

		return $output;
	}

}
