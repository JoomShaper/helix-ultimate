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
 * Measurement unit field.
 *
 * @since	 2.0.0
 */
class HelixultimateFieldUnit
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
		$attributes = (isset($attr['placeholder']) && $attr['placeholder']) ? 'placeholder="' . $attr['placeholder'] . '"' : '';
		$className = $attr['class'] ?? '';

		$value = !empty($attr['value']) ? $attr['value'] : '';
		$depend = isset($attr['depend']) ? $attr['depend'] : false;
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

		$output  = '<div class="control-group ' . $className . '" ' . $dataShowon . ' >';
		$output .= '<label>' . $attr['title'] . '</label>';

		if (!empty($attr['desc']))
		{
			$output .= '<span class="hu-help-icon hu-ml-2 fas fa-info-circle"></span>';
			$output .= '<p class="hu-control-help">' . $attr['desc'] . '</p>';
		}

		// By default the unit is px.
		$unit = 'px';

		if (isset($value))
		{
			$matches = [];

			if (preg_match("@^([+-]?(?:\d+|\d*\.\d+))(px|em|rem|%)$@", $value, $matches))
			{
				if (count($matches) >= 3)
				{
					$value = $matches[1];

					if (isset($matches[2]))
					{
						$unit = strtolower($matches[2]);
					}
				}
			}
			elseif (is_numeric($value))
			{
				$value = (float) $value;
			}
			else
			{
				$value = '';
			}
		}

		$value = !isset($value) ? '' : $value;
		$finalValue = $value !== '' ? $value . $unit : '';

		$output .= '<div class="hu-input-group hu-unit-group">';
		$output .= '<input type="hidden" class="hu-unit-field-value" name="' . $key . '" value="' . $finalValue . '"/>';
		$output .= '	<input type="text" class="hu-field-dimension-width form-control hu-unit-field-input ' . $key . '" value="' . $value . '" />';
		$output .= '	<select class="hu-unit-select">';
		$output .= '		<option value="px" ' . ($unit === 'px' ? 'selected' : '') . '>px</option>';
		$output .= '		<option value="em" ' . ($unit === 'em' ? 'selected' : '') . '>em</option>';
		$output .= '		<option value="rem" ' . ($unit === 'rem' ? 'selected' : '') . '>rem</option>';
		$output .= '		<option value="%" ' . ($unit === '%' ? 'selected' : '') . '>%</option>';
		$output .= '	</select>';
		$output .= '</div>';

		// End of control group
		$output .= '</div>';

		return $output;
	}

}
