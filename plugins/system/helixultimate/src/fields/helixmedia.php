<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Form\FormField;

/**
 * Form field for Helix media
 *
 * @since	 1.0.0
 */
class JFormFieldHelixmedia extends FormField
{
	/**
	 * Field type
	 *
	 * @var		string	$type
	 * @since	1.0.0
	 */
	protected $type = 'Helixmedia';

	/**
	 * Override getInput function form FormField
	 *
	 * @return	string	Field HTML string
	 * @since	1.0.0
	 */
	public function getInput()
	{
		$output = '<div class="hu-image-holder">';

		if (!empty($this->value))
		{
			$output .= '<img src="' . Uri::root() . $this->value . '" alt="">';
		}

		$output .= '</div>';

		$output .= '<input type="hidden" name="' . $this->name . '" id="' . $this->id . '" value="' . $this->value . '">';
		$output .= '<a href="#" class="hu-media-picker hu-btn hu-btn-primary hu-mr-2" data-id="' . $this->id . '"><span class="fas fa-image" aria-hidden="true"></span> Select</a>';
		$output .= '<a href="#" class="hu-media-clear hu-btn hu-btn-secondary' . (empty($this->value) ? ' hide' : '') . '"><span class="fas fa-times" aria-hidden="true"></span> Clear</a>';

		return $output;
	}
}

