<?php
/**
* @package Helix3 Framework
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2015 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die ('resticted aceess');

class JFormFieldHeliximage extends JFormField
{

	protected $type = 'Heliximage';

	protected function getInput()
	{
		$doc = JFactory::getDocument();

		JHtml::_('jquery.framework');

		$plg_path = JURI::root(true) . '/plugins/system/helixultimate';
		$doc->addScript($plg_path . '/assets/js/image.js');
		$doc->addStyleSheet($plg_path . '/assets/css/image.css');

		if($this->value) {
			$class1 = ' hide';
			$class2 = '';
		} else {
			$class1 = '';
			$class2 = ' hide';
		}

		$output  = '<div class="helix-image-field clearfix">';
		$output .= '<div class="helix-image-upload-wrapper">';

		if($this->value) {
			$data_src = $this->value;
			$src = JURI::root(true) . '/' . $data_src;

			$basename = basename($data_src);
			$thumbnail = JPATH_ROOT . '/' . dirname($data_src) . '/' . JFile::stripExt($basename) . '_thumbnail.' . JFile::getExt($basename);

			if(file_exists($thumbnail)) {
				$src = JURI::root(true) . '/' . dirname($data_src) . '/' . JFile::stripExt($basename) . '_thumbnail.' . JFile::getExt($basename);
			}

			$output .= '<img src="'. $src .'" data-src="' . $data_src . '" alt="">';
		}

		$output .= '</div>';

		$output .= '<input type="file" class="helix-image-upload" accept="image/*" style="display:none;">';
		$output .= '<a class="btn btn-primary btn-helix-image-upload'. $class1 .'" href="#"><i class="fa fa-plus"></i> Upload Image</a>';
		$output .= '<a class="btn btn-danger btn-helix-image-remove'. $class2 .'" href="#"><i class="fa fa-minus-circle"></i> Remove Image</a>';

		$output .= '<input type="hidden" name="'. $this->name .'" id="' . $this->id . '" value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8')
				. '"  class="form-field-helix-image">';
		$output .= '</div>';

		return $output;
	}
}
