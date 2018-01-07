<?php
/**
* @package Helix3 Framework
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2015 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

//no direct accees
defined ('_JEXEC') or die ('resticted aceess');

class HelixUltimateFieldMedia
{
	static function getInput($key, $attr)
	{

		if(!isset($attr['std'])){
			$attr['std'] = '';
		}

		$output  = '<div class="form-group">';
		$output .= '<label>' . $attr['title'] . '</label>';

		$output .= '<div class="helix-ultimate-image-holder">';
		if($attr['std'] != '') {
			$output .= '<img src="'. \JURI::root() . $attr['std'] .'" alt="">';
		}
		$output .= '</div>';

		$output .= '<input type="hidden" data-attrname="' . $key . '" value="' . $attr['std'] . '">';
		$output .= '<a href="#" class="helix-ultimate-media-picker btn btn-primary btn-sm" data-id=""><span class="fa fa-picture-o"></span> Select Media</a>';
		$output .= '<a href="#" class="helix-ultimate-media-clear btn btn-secondary btn-sm"><span class="fa fa-times"></span> Clear</a>';

		if( ( isset($attr['desc']) ) && ( isset($attr['desc']) != '' ) )
		{
			$output .= '<p class="control-help">' . $attr['desc'] . '</p>';
		}

		$output .= '</div>';

		return $output;

	}
}
