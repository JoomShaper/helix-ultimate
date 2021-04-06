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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

/**
 * Form field for helixButton
 *
 * @since	2.0.0
 */
class JFormFieldHelixDevices extends FormField
{
	/**
	 * Field type
	 *
	 * @var		string	$type
	 * @since	2.0.0
	 */
	protected $type = 'HelixDevices';

	/**
	 * Override getInput function form FormField
	 *
	 * @return	string	Field HTML string
	 * @since	2.0.0
	 */
	protected function getInput()
	{
		$default 	= isset($this->element['default']) ? $this->element['default'] : 'lg';
		$value 		= empty($this->value) ? $default : $this->value;

		$output = '<div class="helix-field">';
		$output .= '	<div class="helix-devices">';
		$output .= '		<button class="device-btn ' . ($value === 'xs' ? 'active' : '') . '" data-device="xs" title="Mobile">';
		$output .= '			<svg width="14" height="14" viewBox="0 0 11 19" fill="none" xmlns="http://www.w3.org/2000/svg">';
		$output .= '				<path d="M6.58001 15.2C6.58001 15.54 6.32001 15.8 5.98001 15.8H4.43999C4.09999 15.8 3.83999 15.54 3.83999 15.2C3.83999 14.86 4.09999 14.6 4.43999 14.6H5.98001C6.30001 14.6 6.58001 14.86 6.58001 15.2ZM10.4 16.88C10.4 17.72 9.72001 18.4 8.88001 18.4H1.52C0.679995 18.4 0 17.72 0 16.88V1.52002C0 0.68002 0.679995 0 1.52 0H8.88001C9.72001 0 10.4 0.68002 10.4 1.52002V16.88ZM1.6 1.6V12.2H8.8V1.6H1.6ZM8.8 16.8V13.4H1.6V16.8H8.8Z" fill="#999"/>';
		$output .= '			</svg>';
		$output .= '		</button>';
		$output .= '		<button class="device-btn ' . ($value === 'sm' ? 'active' : '') . '" data-device="sm" title="Tablet">';
		$output .= '			<svg width="14" height="14" viewBox="0 0 16 19" fill="none" xmlns="http://www.w3.org/2000/svg">';
		$output .= '				<path d="M9.31998 15.4C9.31998 15.74 9.05998 16 8.71998 16H7.67999C7.33999 16 7.07999 15.74 7.07999 15.4C7.07999 15.06 7.33999 14.8 7.67999 14.8H8.71998C9.05998 14.8 9.31998 15.06 9.31998 15.4ZM15.6 16.88C15.6 17.72 14.92 18.4 14.08 18.4H2.31998C1.47998 18.4 0.799988 17.72 0.799988 16.88V1.52002C0.799988 0.68002 1.47998 0 2.31998 0H14.08C14.92 0 15.6 0.68002 15.6 1.52002V16.88ZM2.39999 1.6V12.6H14V1.6H2.39999ZM14 16.8V13.8H2.39999V16.8H14Z" fill="#999"/>';
		$output .= '			</svg>';
		$output .= '		</button>';
		$output .= '		<button class="device-btn ' . ($value === 'md' ? 'active' : '') . '" data-device="md" title="Desktop">';
		$output .= '			<svg width="14" height="14" viewBox="0 0 22 19" fill="none" xmlns="http://www.w3.org/2000/svg">';
		$output .= '				<path d="M19.477 0.200012H1.7539C0.784671 0.200012 6.10352e-05 0.98465 6.10352e-05 1.95388V12.1769C6.10352e-05 13.1461 0.784671 14.0462 1.7539 14.0462H7.61545V14.9923L5.8616 16.4C5.5616 16.6538 5.42314 17.1385 5.53852 17.5077C5.67698 17.8769 6.02315 18.2 6.41546 18.2H14.7231C15.1155 18.2 15.4847 17.8769 15.6231 17.5077C15.7616 17.1385 15.6462 16.6769 15.3462 16.4231L13.6154 14.9923V14.0462H19.477C20.4462 14.0462 21.2308 13.1461 21.2308 12.1769V1.95388C21.2308 0.98465 20.4462 0.200012 19.477 0.200012ZM12.277 16.0769L12.8308 16.5846H8.30775L8.90775 16.0538C9.09236 15.8923 9.23083 15.6154 9.23083 15.3615V14.0231H12.0001V15.3615C12.0001 15.6154 12.0924 15.9154 12.277 16.0769ZM19.3847 12.2H1.84621V2.04617H19.3847V12.2Z" fill="#999"/>';
		$output .= '			</svg>';
		$output .= '		</button>';
		$output .= '	</div>';

		$output .= '<input type="hidden" data-type="hu-devices" name="' . $this->name . '" id="' . $this->id . '" value="' . $value . '" />';

		// End of helix field div.
		$output .= '</div>';

		return $output;
	}

	/**
	 * Override the getLabel function.
	 *
	 * @return	boolean
	 * @since	2.0.0
	 */
	protected function getLabel()
	{
		return false;
	}
}
