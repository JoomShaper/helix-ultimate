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
 * Color field
 *
 * @since 	1.0.0
 */
class HelixultimateFieldColor
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
		// JHtml::_('jquery.framework');
		// JHtml::_('script', 'system/html5fallback.js', false, true);

		$isMenuBuilder = isset($attr['menu-builder']) && $attr['menu-builder'] === true;
		$value = !empty($attr['value']) ? $attr['value'] : '';
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

		$output  = '<div class="control-group ' . $className . '">';
		$output .= '<label>' . $attr['title'] . '</label>';

		if (!empty($attr['desc']))
		{
			$output .= '<span class="hu-help-icon hu-ml-2 fas fa-info-circle"></span>';
			$output .= '<p class="hu-control-help">' . $attr['desc'] . '</p>';
		}

		if ($isMenuBuilder)
		{
			$output .= '<input type="text" class="hu-input hu-input-color hu-megamenu-builder-' . $key . $internal . '" placeholder="#rrggbb" ' . $dataAttrs . ' name="' . $key . '" value="' . $value . '" />';
		}
		else
		{
			$output .= '<input type="text" class="hu-input hu-input-color" data-attrname="' . $key . '" placeholder="#rrggbb" value="">';
		}

		$output .= '</div>';

		return $output;
	}

}
