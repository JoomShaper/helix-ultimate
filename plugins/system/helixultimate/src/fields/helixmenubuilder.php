<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use HelixUltimate\Framework\Platform\Builders\MenuBuilder;
use HelixUltimate\Framework\Platform\Helper;
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
		$data = Helper::loadTemplateData();
		$params = $data->params;

		if (empty($this->value))
		{
			$this->value = new \stdClass;
			$value = json_encode($this->value);
		}
		else
		{
			if (!\is_string($this->value))
			{
				$value = json_encode($this->value);
			}
			else
			{
				$value = $this->value;
			}
		}

		$builder = new MenuBuilder($params->get('menu', 'mainmenu'));
		$items = $builder->getMenuItems();
		$html = [];

		$html[] = '<div class="hu-menu-builder">';

		if (!empty($items))
		{
			$layout = new FileLayout('fields.menuBuilder.menuItems', HELIX_LAYOUT_PATH);
			$html[] = $layout->render(['items' => $items, 'params' => $params, 'menuSettings' => $value]);
		}

		$html[] = '<input type="hidden" class="hu-megamenu-field" name="' . $this->name . '" id="' . $this->id . '" value=\'' . $value . '\' />';

		// End menu builder
		$html[] = '</div>';

		return implode("\n", $html);
	}


}
