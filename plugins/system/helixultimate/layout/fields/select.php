<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

/**
 * Select field
 *
 * @since	1.0.0
 */
class HelixultimateFieldSelect
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

		$value = !empty($attr['value']) ? $attr['value'] : '';
		$options = !empty($attr['options']) ? $attr['options'] : ($attr['values'] || []);
		$depend = isset($attr['depend']) ? $attr['depend'] : false;

		$dataAttrs = '';

		if (!empty($attr['data']))
		{
			foreach ($attr['data'] as $dataName => $dataValue)
			{
				$dataAttrs .= ' data-' . $dataName . '=' . $dataValue;
			}
		}

		$output  = '<div class="control-group ' . $key . ' ' . ($depend ? 'hidden' : '') . '" ' . ($depend ? 'data-depend="' . $depend . '"' : '') . '>';
		// $output  = '<div class="control-group ' . $key . '">';
		$output .= '<label>' . $attr['title'] . '</label>';

		if (!empty($attr['desc']))
		{
			$output .= '<span class="hu-help-icon hu-ml-2 fas fa-info-circle"></span>';
			$output .= '<p class="control-help">' . $attr['desc'] . '</p>';
		}

		if ($isMenuBuilder)
		{
			$output .= '<select class="hu-input input-select hu-menu-builder-"' . $key . ' name="' . $key . '" ' . $dataAttrs . '>';
		}
		else
		{
			$output .= '<select class="hu-input input-select" data-attrname="' . $key . '">';
		}

		foreach ($options as $key => $text)
		{
			$output .= '<option value="' . $key . '" ' . ($key === $value ? 'selected="selected"' : '') . '>' . $text . '</option>';
		}

		$output .= '</select>';

		$output .= '</div>';

		return $output;
	}

}
