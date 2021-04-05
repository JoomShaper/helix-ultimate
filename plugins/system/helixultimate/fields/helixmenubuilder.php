<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
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
 * @since		2.0.0
 * @deprecated	3.0		Use the Same Class from the src/fields instead.
 */
class JFormFieldHelixMenuBuilder extends FormField
{
	/**
	 * Field type
	 *
	 * @var		string	$type
	 * @since	2.0.0
	 */
	protected $type = "HelixMenuBuilder";

	/**
	 * Override getInput function form FormField
	 *
	 * @return	string	Field HTML string
	 * @since	2.0.0
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

		$html = [];
		$html[] = '<div id="hu-menu-builder">';
		$html[] = '<div id="hu-menu-builder-container"></div>';
		$html[] = '<button class="hu-btn hu-btn-primary hu-add-menu-item">Add New Item</button>';
		$html[] = '</div>';

		return implode("\n", $html);
	
	}


}
