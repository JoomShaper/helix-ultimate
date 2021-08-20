<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;
/**
 * Form field for Helix mega menu
 *
 * @since	2.0.0
 */
class JFormFieldHelixMenuBuilder extends FormField
{
	/**
	 * Field type
	 *
	 * @var		string	$type
	 * @since	2.0.0
	 */
	protected $type = "HelixMenuBuilder";

	/**
	 * Override getInput function form FormField
	 *
	 * @return	string	Field HTML string
	 * @since	2.0.0
	 */
	public function getInput()
	{
		$html = [];
		$html[] = '<div id="hu-menu-builder">';
		$html[] = '<div id="hu-menu-builder-container"></div>';
		$html[] = '<button type="button" class="hu-btn hu-btn-primary hu-add-menu-item"><span class="fas fa-plus" aria-hidden="true"></span> ' . Text::_('HELIX_ULTIMATE_ADD_NEW_MENU_ITEM') . '</button>';
		$html[] = '</div>';

		return implode("\n", $html);
	
	}


}
