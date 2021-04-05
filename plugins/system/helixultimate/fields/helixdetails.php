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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * Form field for helix details.
 *
 * @since   	1.0.0
 * @deprecated	3.0		Use the Same Class from the src/fields instead.
 */
class JFormFieldHelixdetails extends FormField
{
	/**
	 * Field type
	 *
	 * @var		string	$type
	 * @since	1.0.0
	 */
	protected $type = 'Helixdetails';

	/**
	 * Override getInput function form FormField
	 *
	 * @return	string	Field HTML string
	 * @since	1.0.0
	 */
	protected function getInput()
	{
		HTMLHelper::_('jquery.framework');
		$doc = Factory::getDocument();

		$plg_path = Uri::root(true) . '/plugins/system/helixultimate';
		$doc->addScript($plg_path . '/assets/js/admin/details.js');
		$doc->addStyleSheet($plg_path . '/assets/css/admin/details.css');

		$app = Factory::getApplication();
		$id  = $app->input->get('id', 0, 'INT');

		$url = Route::_('index.php?option=com_ajax&helix=ultimate&id=' . $id);
		$html = '<a href="' . $url . '" class="hu-options"><i class="icon-options"></i> Template Options</a>';

		return $html;
	}

	/**
	 * Override the getLabel method from FormField class.
	 *
	 * @return 	boolean
	 * @since	1.0.0
	 */
	public function getLabel()
	{
		return false;
	}
}
