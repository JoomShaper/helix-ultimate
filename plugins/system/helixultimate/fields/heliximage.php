<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\HTML\HTMLHelper;


/**
 * Form field for Helix image.
 *
 * @since 		1.0.0
 * @deprecated	3.0		Use the Same Class from the src/fields instead.
 */
class JFormFieldHeliximage extends FormField
{
	/**
	 * Field type
	 *
	 * @var		string	$type
	 * @since	1.0.0
	 */
	protected $type = 'Heliximage';

	/**
	 * Override getInput function form FormField
	 *
	 * @return	string	Field HTML string
	 * @since	1.0.0
	 */
	protected function getInput()
	{
		$doc = Factory::getDocument();

		HTMLHelper::_('jquery.framework');

		$plg_path = Uri::root(true) . '/plugins/system/helixultimate';

		$class = ' hu-image-field-empty';

		if ($this->value)
		{
			$class = ' hu-image-field-has-image';
		}

		$output  = '<div class="hu-image-field' . $class . ' clearfix">';
		$output .= '<div class="hu-image-upload-wrapper">';

		if ($this->value)
		{
			$data_src = $this->value;
			$src = Uri::root(true) . '/' . $data_src;

			$basename = basename($data_src);
			$thumbnail = JPATH_ROOT . '/' . dirname($data_src) . '/' . File::stripExt($basename) . '_thumbnail.' . File::getExt($basename);

			if (file_exists($thumbnail))
			{
				$src = Uri::root(true) . '/' . dirname($data_src) . '/' . File::stripExt($basename) . '_thumbnail.' . File::getExt($basename);
			}

			$output .= '<img src="' . $src . '" data-src="' . $data_src . '" alt="">';
		}

		$output .= '</div>';

		$output .= '<input type="file" class="hu-image-upload" accept="image/*" style="display:none;">';
		$output .= '<a class="btn btn-primary btn-hu-image-upload" href="#"><i class="fas fa-plus" aria-hidden="true"></i> ' . Text::_('HELIX_ULTIMATE_UPLOAD_IMAGE') . '</a>';
		$output .= '<a class="btn btn-danger btn-hu-image-remove" href="#"><i class="fas fa-minus-circle" aria-hidden="true"></i> ' . Text::_('HELIX_ULTIMATE_REMOVE_IMAGE') . '</a>';

		$output .= '<input type="hidden" name="' . $this->name . '" id="' . $this->id . '" value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8')
				. '"  class="form-field-hu-image">';
		$output .= '</div>';

		return $output;
	}
}
