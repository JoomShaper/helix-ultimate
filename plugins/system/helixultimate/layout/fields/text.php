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
		$output .= '<div class="control-group-inner">';
		$output .= '<div class="control-label">';
		$output .= '<label>' . $attr['title'];
		$output .= !empty($attr['desc']) ? '<span class="hu-help-icon hu-ml-2 fas fa-info-circle"></span>' : '';
		$output .= '</label>';

		$output .= '</div>';
		$output .= !empty($attr['desc']) ? '<p class="hu-control-help">' . $attr['desc'] . '</p>' : '';

		$output .= '</div>';

		if ($isMenuBuilder)
		{
			$output .= '<input class="hu-input hu-megamenu-builder-' . $key . $internal . '" type="text" ' . $dataAttrs . ' name="' . $key . '" value="' . $value . '" ' . $attributes . ' />';
		}
		else
		{
			$output	.= '<input class="hu-input addon-' . $key . '" type="text" name="' . $key . '" data-attrname="' . $key . '" value="" ' . $attributes . ' />';
		}

		$output .= '</div>';

		return $output;
	}

}
