<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use HelixUltimate\Framework\Core\Lib\FontawesomeIcons;

defined('_JEXEC') or die();


/**
 * Form field for Helix icons.
 *
 * @since		1.0.0
 * @deprecated	3.0		Use the Same Class from the src/fields instead.
 */
class JFormFieldHelixicon extends FormField
{
	/**
	 * Field type
	 *
	 * @var		string	$type
	 * @since	1.0.0
	 */
	protected $type = 'Helixicon';

	/**
	 * Override getInput function form FormField
	 *
	 * @return	string	Field HTML string
	 * @since	1.0.0
	 */
	public function getInput()
	{
		$fontawesome = new FontawesomeIcons;
		$icons = $fontawesome->getIcons();

		$arr = array();
		$arr[] = HTMLHelper::_('select.option', '', '');

		foreach ($icons as $value)
		{
			$arr[] = HTMLHelper::_('select.option', $value, preg_replace('@^fa[sbr]\s+fa-@', '', $value));
		}

		return HTMLHelper::_('select.genericlist', $arr, $this->name, null, 'value', 'text', $this->value);

	}
}
