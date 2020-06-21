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

		$output  = '<div class="control-group ' . $key . '">';
		$output .= '<label>' . $attr['title'] . '</label>';

		if (!empty($attr['desc']))
		{
			$output .= '<span class="hu-help-icon hu-ml-2 fas fa-info-circle"></span>';
			$output .= '<p class="control-help">' . $attr['desc'] . '</p>';
		}

		$output .= '<select class="hu-input input-select" data-attrname="' . $key . '">';

		foreach ($attr['values'] as $key => $value)
		{
			$output .= '<option value="' . $key . '">' . $value . '</option>';
		}

		$output .= '</select>';

		$output .= '</div>';

		return $output;
	}

}
