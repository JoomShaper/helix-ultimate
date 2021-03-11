<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\FormField;

/**
 * Form field for helix import
 *
 * @since	1.0.0
 */
class JFormFieldHelixexportimport extends FormField
{
	/**
	 * Field type
	 *
	 * @var		string	$type
	 * @since	1.0.0
	 */
	protected $type = 'Helixexportimport';

	/**
	 * Override getInput function form FormField
	 *
	 * @return	string	Field HTML string
	 * @since	1.0.0
	 */
	protected function getInput()
	{
		$input = Factory::getApplication()->input;
		$template_id = $input->get('id', 0, 'INT');

		$export_url = 'index.php?option=com_ajax&helix=ultimate&task=export&id=' . $template_id;

		$output  = '<div class="hu-importer-wrapper">';
		$output .= '<a class="hu-btn hu-btn-primary" id="btn-hu-export-settings" rel="noopener noreferrer" target="_blank" href="' . $export_url . '"><svg width="18" height="18" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.75 10.5a.75.75 0 00-.75.75v3a.75.75 0 01-.75.75H3.75a.75.75 0 01-.75-.75v-3a.75.75 0 10-1.5 0v3a2.25 2.25 0 002.25 2.25h10.5a2.25 2.25 0 002.25-2.25v-3a.75.75 0 00-.75-.75zm-7.283 1.283a.75.75 0 00.248.157.705.705 0 00.57 0 .75.75 0 00.248-.157l3-3a.753.753 0 00-1.066-1.065L9.75 9.443V2.25a.75.75 0 00-1.5 0v7.193L6.532 7.718a.753.753 0 10-1.064 1.065l3 3z" fill="#fff"/></svg> ' . Text::_("HELIX_ULTIMATE_SETTINGS_EXPORT") . '</a>';
		$output .= '<input type="file" id="helix-import-file" accept="application/JSON" style="display: none;"/>';
		$output .= '<a id="btn-hu-import-settings" class="hu-btn hu-btn-primary" rel="noopener noreferrer" data-template_id="' . $template_id . '" target="_blank" href="#"><svg width="18" height="18" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.75 10.5a.75.75 0 00-.75.75v3a.75.75 0 01-.75.75H3.75a.75.75 0 01-.75-.75v-3a.75.75 0 10-1.5 0v3a2.25 2.25 0 002.25 2.25h10.5a2.25 2.25 0 002.25-2.25v-3a.75.75 0 00-.75-.75z" fill="#fff"/><path d="M9.285 1.56a.75.75 0 01.247.158l3 3a.753.753 0 11-1.065 1.065L9.75 4.058v7.192a.75.75 0 11-1.5 0V4.058L6.532 5.783a.753.753 0 01-1.065-1.065l3-3a.75.75 0 01.248-.158.705.705 0 01.57 0z" fill="#fff"/></svg> ' . Text::_("HELIX_ULTIMATE_SETTINGS_IMPORT") . '</a>';
		$output .= '</div>';

		return $output;
	}
}
