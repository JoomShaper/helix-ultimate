<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
/**
 * Form field for helix presets.
 *
 * @since	2.0.0
 */
class JFormFieldHelixUnit extends FormField
{
	/**
	 * Field type
	 *
	 * @var		string	$type
	 * @since	2.0.0
	 */
	protected $type = 'HelixUnit';

	/**
	 * Override getInput function form FormField
	 *
	 * @return	string	Field HTML string
	 * @since	2.0.0
	 */
	protected function getInput()
	{
		// By default the unit is px.
		$unit = 'px';
		$value = $this->value;
		$name = $this->name;
		$hint = $this->getAttribute('hint', '', 'STRING');

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
		$output = '';

		$output .= '<div class="hu-input-group hu-unit-group">';
		$output .= '	<input type="hidden" class="hu-unit-field-value" name="' . $name . '" value="' . $finalValue . '"/>';
		$output .= '	<input type="text" class="hu-field-dimension-width form-control hu-unit-field-input ' . $name . '" value="' . $value . '" ' . ($hint !== '' ? 'placeholder="' . $hint . '"' : '') . ' />';
		$output .= '	<select class="hu-unit-select">';
		$output .= '		<option value="px" ' . ($unit === 'px' ? 'selected' : '') . '>px</option>';
		$output .= '		<option value="em" ' . ($unit === 'em' ? 'selected' : '') . '>em</option>';
		$output .= '		<option value="rem" ' . ($unit === 'rem' ? 'selected' : '') . '>rem</option>';
		$output .= '		<option value="%" ' . ($unit === '%' ? 'selected' : '') . '>%</option>';
		$output .= '	</select>';
		$output .= '</div>';

		return $output;
	}
}
