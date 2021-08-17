<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use HelixUltimate\Framework\Platform\Settings;

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
		$options = !empty($attr['options']) ? $attr['options'] : (!empty($attr['values']) ? $attr['values'] : []);
		$depend = isset($attr['depend']) ? $attr['depend'] : false;
		$className = $attr['class'] ?? '';
		$dataAttrs = '';
		$dataShowon = '';
		$internal = !empty($attr['internal']) ? ' internal-use-only' : '';

		if ($depend)
		{
			$showon = Settings::parseShowOnConditions($attr['depend']);
			$dataShowon = ' data-revealon=\'' . json_encode($showon) . '\' ';
		}

		if (!empty($attr['data']))
		{
			foreach ($attr['data'] as $dataName => $dataValue)
			{
				$dataAttrs .= ' data-' . $dataName . '=' . $dataValue;
			}
		}

		$output  = '<div class="control-group ' . $className . ' ' . $key . '" ' . $dataShowon . '>';
		$output .= '<label>' . $attr['title'];

		$output .= !empty($attr['desc']) ? '<span class="hu-help-icon hu-ml-2 fas fa-info-circle"></span>' : '';
		$output .= '</label>';
		$output .= !empty($attr['desc']) ? '<p class="hu-control-help">' . $attr['desc'] . '</p>' : '';

		if ($isMenuBuilder)
		{
			$output .= '<select class="hu-input input-select hu-megamenu-builder-' . $key . $internal . '" name="' . $key . '" ' . $dataAttrs . '>';
		}
		else
		{
			$output .= '<select class="hu-input input-select" data-attrname="' . $key . '">';
		}

		foreach ($options as $optKey => $text)
		{
			$output .= '<option value="' . $optKey . '" ' . ($optKey === $value ? 'selected="selected"' : '') . '>' . $text . '</option>';
		}

		$output .= '</select>';

		$output .= '</div>';

		return $output;
	}

}
