<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use HelixUltimate\Framework\Platform\Builders\MenuBuilder;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;


/**
 * Form field for Helix mega menu
 *
 * @since	1.0.0
 */
class JFormFieldHelixMenuBuilder extends FormField
{
	/**
	 * Field type
	 *
	 * @var		string	$type
	 * @since	1.0.0
	 */
	protected $type = "HelixMenuBuilder";

	/**
	 * Override getInput function form FormField
	 *
	 * @return	string	Field HTML string
	 * @since	1.0.0
	 */
	public function getInput()
	{
		$builder = new MenuBuilder('mainmenu');
		$items = $builder->getMenuItems();
		$html = [];

		$html[] = '<div class="hu-menu-builder">';

		if (!empty($items))
		{
			$layout = new FileLayout('fields.menuBuilder.menuItems', HELIX_LAYOUT_PATH);
			$html[] = $layout->render(['items' => $items]);
		}

		// End menu builder
		$html[] = '</div>';

		return implode("\n", $html);
	}


}
