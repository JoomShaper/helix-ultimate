<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die ();

jimport('joomla.form.formfield');

class JFormFieldHelixexportimport extends JFormField
{
	protected $type = 'Helixexportimport';

	protected function getInput()
	{
		$input = JFactory::getApplication()->input;
		$template_id = $input->get('id',0,'INT');
		$export_url = 'index.php?option=com_ajax&helix=ultimate&task=export&id=' . $template_id;

		$output  = '';
		$output .= '<div class="import-export clearfix" style="margin-bottom:30px;">';
		$output .= '<a class="btn btn-success" target="_blank" href="'. $export_url .'">'. JText::_("HELIX_ULTIMATE_SETTINGS_EXPORT") .'</a>';
		$output .= '</div>';
		$output .= '<div class="import-export clearfix">';
		$output .= '<textarea id="import-data" name="import-data" rows="5" style="margin-bottom:20px;"></textarea>';
		$output .= '<a id="import-settings" class="btn btn-primary" data-template_id="'. $template_id .'" target="_blank" href="#">'. JText::_("HELIX_ULTIMATE_SETTINGS_IMPORT") .'</a>';
		$output .= '</div>';

		return $output;
	}
}
