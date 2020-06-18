<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use Joomla\CMS\Uri\Uri;

/**
 * Media field.
 *
 * @since	1.0.0
 */
class HelixultimateFieldMedia
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

		$output  = '<div class="control-group">';
		$output .= '<label>' . $attr['title'] . '</label>';

		$output .= '<div class="helix-ultimate-image-holder"></div>';

		$output .= '<input type="hidden" class="helix-ultimate-input helix-ultimate-input-media" data-attrname="' . $key . '" data-baseurl="' . Uri::root() . '" value="">';
		$output .= '<a href="#" class="helix-ultimate-media-picker btn btn-primary btn-sm" data-target="' . $key . '"><span class="fas fa-image"></span> Select Media</a>';
		$output .= '<a href="#" class="helix-ultimate-media-clear btn btn-secondary btn-sm"><span class="fas fa-times"></span> Clear</a>';

		if ((isset($attr['desc'])) && (isset($attr['desc']) !== ''))
		{
			$output .= '<p class="control-help">' . $attr['desc'] . '</p>';
		}

		$output .= '</div>';

		return $output;

	}
}
