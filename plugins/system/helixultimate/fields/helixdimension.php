<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die();

/**
 * Form field for Helix dimension.
 *
 * @since		2.0.0
 * @deprecated	3.0		Use the Same Class from the src/fields instead.
 */
class JFormFieldHelixdimension extends FormField
{
	/**
	 * Field type
	 *
	 * @var		string	$type
	 * @since	2.0.0
	 */
	protected $type = 'Helixdimension';

	/**
	 * Override getInput function form FormField
	 *
	 * @return	string	Field HTML string
	 * @since	2.0.0
	 */
	public function getInput()
	{
		$unit = $this->getAttribute('unit', 'px');
		list($width, $height) = explode('x', strtolower($this->value));

		// Output
		$output = '';

		$output .= '<div class="row">';

		$output .= '<div class="col-6">';
		$output .= '<div class="hu-d-flex hu-align-items-center">';
		$output .= '<span class="hu-mr-1">W</span>';
		$output .= '<div class="hu-input-group">';
		$output .= '<input type="text" class="hu-field-dimension-width form-control" value="' . $width . '" /><span class="hu-input-group-text">' . $unit . '</span>';
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';

		$output .= '<div class="col-6">';
		$output .= '<div class="hu-d-flex hu-align-items-center">';
		$output .= '<span class="hu-mr-1">H</span>';
		$output .= '<div class="hu-input-group">';
		$output .= '<input type="text" class="hu-field-dimension-height form-control" value="' . $height . '" /><span class="hu-input-group-text">' . $unit . '</span>';
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';

		$output .= '</div>';

		$output .= '<input type="hidden" name="' . $this->name . '" id="' . $this->id . '" class="hu-field-dimension-input ' . $this->class . '" value="' . $this->value . '" />';

		return $output;

	}
}
