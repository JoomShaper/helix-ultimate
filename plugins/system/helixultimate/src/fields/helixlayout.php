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
use Joomla\CMS\Filesystem\File;
use HelixUltimate\Framework\Platform\Helper;

/**
 * Form field for Helix layout
 *
 * @since	1.0.0
 */
class JFormFieldHelixlayout extends FormField
{
	/**
	 * Field type
	 *
	 * @var		string	$type
	 * @since	1.0.0
	 */
	protected $type = 'Helixlayout';

	/**
	 * Override getInput function form FormField
	 *
	 * @return	string	Field HTML string
	 * @since	1.0.0
	 */
	public function getInput()
	{
		$input  	= Factory::getApplication()->input;
		$style_id 	= $input->get('id', 0, 'INT');
		$style 		= Helper::getTemplateStyle($style_id);

		$helix_layout_path = JPATH_SITE . '/plugins/system/helixultimate/layout/';

		$json = json_decode($this->value);

		if (!empty($json))
		{
			$rows = $json;
		}
		else
		{
			// $layout_file = File::read(JPATH_SITE . '/templates/' . $style->template . '/options.json');
			$layout_file = file_get_contents(JPATH_SITE . '/templates/' . $style->template . '/options.json');
			$value = json_decode($layout_file);
			$rows = json_decode($value->layout);
		}

		$html = $this->generateLayout($helix_layout_path, $rows);
		$html .= '<input type="hidden" id="' . $this->id . '" name="' . $this->name . '">';

		return $html;
	}

	/**
	 * Generate Layout.
	 *
	 * @param	string		$path	Layout path
	 * @param	object		$layout_data	The layout data.
	 *
	 * @return	string		Layout HTML string.
	 * @since	1.0.0
	 */
	private function generateLayout($path, $layout_data = null)
	{
		$GLOBALS['tpl_layout_data'] = $layout_data;

		ob_start();
		include_once $path . 'generated.php';
		$items = ob_get_contents();
		ob_end_clean();

		return $items;
	}

	/**
	 * Get label for the field.
	 *
	 * @return	boolean
	 * @since	1.0.0
	 */
	public function getLabel()
	{
		return false;
	}
}
