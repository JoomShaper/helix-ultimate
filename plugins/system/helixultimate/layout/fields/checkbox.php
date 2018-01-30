<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die ();

class HelixUltimateFieldCheckbox{

	static function getInput($key, $attr)
	{

		$output   = '<div class="control-group">';
		$output  .= '<div class="checkbox">';
		$output  .= '<label>';
		$output  .= '<input class="helix-ultimate-input helix-ultimate-input-'.$key.'" data-attrname="'.$key.'" type="checkbox"> ' .$attr['title'];
		$output  .= '</label>';
		$output  .= '</div>';

		if( ( isset($attr['desc']) ) && ( isset($attr['desc']) != '' ) )
		{
			$output .= '<p class="control-help">' . $attr['desc'] . '</p>';
		}

		$output  .= '</div>';

		return $output;
	}

}
