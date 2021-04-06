<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\FormField;

/**
 * Form field for helixButton
 *
 * @since	1.0.0
 */
class JFormFieldHelixbutton extends FormField
{
	/**
	 * Field type
	 *
	 * @var		string	$type
	 * @since	1.0.0
	 */
	protected $type = 'Helixbutton';

	/**
	 * Override getInput function form FormField
	 *
	 * @return	string	Field HTML string
	 * @since	1.0.0
	 */
	protected function getInput()
	{

		$url = !empty($this->element['url']) ? $this->element['url'] : '#';
		$class = !empty($this->element['class']) ? ' ' . $this->element['class'] : '';
		$text = !empty($this->element['text']) ? $this->element['text'] : 'Button';
		$target = !empty($this->element['target']) ? $this->element['target'] : '_self';

		return '<a id="' . $this->id . '" class="hu-btn' . str_replace('btn-', 'hu-btn-', $class) . '" href="' . $url . '" target="' . $target . '">' . Text::_($text) . '</a>';	
	}
}
