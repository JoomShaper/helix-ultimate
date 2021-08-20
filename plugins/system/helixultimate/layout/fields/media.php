<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
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
		$className = $attr['class'] ?? '';
		$output  = '<div class="control-group ' . $className . '">';
		$output .= '<label>' . $attr['title'] . '</label>';

		if (!empty($attr['desc']))
		{
			$output .= '<span class="hu-help-icon hu-ml-2 fas fa-info-circle"></span>';
			$output .= '<p class="hu-control-help">' . $attr['desc'] . '</p>';
		}

		$output .= '<div class="hu-image-holder"></div>';
		$output .= '<input type="hidden" class="hu-input hu-input-media" data-attrname="' . $key . '" data-baseurl="' . Uri::root() . '" value="">';
		$output .= '<a href="#" class="hu-media-picker hu-btn hu-btn-primary hu-mr-2" data-target="' . $key . '"><span class="fas fa-image" aria-hidden="true"></span> Select Media</a>';
		$output .= '<a href="#" class="hu-media-clear hu-btn hu-btn-secondary hide"><span class="fas fa-times" aria-hidden="true"></span> Clear</a>';

		$output .= '</div>';

		return $output;

	}
}
