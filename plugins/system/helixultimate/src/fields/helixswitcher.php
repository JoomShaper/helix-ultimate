<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use HelixUltimate\Framework\Platform\Helper;
use HelixUltimate\Framework\Platform\Settings;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Filesystem\File;
/**
 * Form field for helix presets.
 *
 * @since	1.0.0
 */
class JFormFieldHelixSwitcher extends FormField
{
	/**
	 * Field type
	 *
	 * @var		string	$type
	 * @since	1.0.0
	 */
	protected $type = 'HelixSwitcher';

	/**
	 * Override getInput function form FormField
	 *
	 * @return	string	Field HTML string
	 * @since	1.0.0
	 */
	protected function getInput()
	{
		$rule = (string) $this->element['textrule'];
		$rule = !empty($rule) ? $rule : 'text';

		$default = (string) $this->element['default'];

		$switcherStyle = (string) $this->element['switcherStyle'];
		$switcherStyle = !empty($switcherStyle) ? $switcherStyle : 'tab';

		$fixedWidth = (string) $this->element['fixedwidth'];
		$fixedWidth = !empty($fixedWidth) && ($fixedWidth === 'true' || $fixedWidth === 'on');

		$alignment = (string) $this->element['alignment'];
		$alignment = !empty($alignment) ? 'hu-align-' . $alignment : '';

		$switcherClasses = 'hu-switcher-style-' . $switcherStyle;

		if ($fixedWidth)
		{
			$switcherClasses .= ' hu-fixed-width';
		}

		if ($alignment)
		{
			$switcherClasses .= ' ' . $alignment;
		}

		$children = $this->element->children();
		$options = null;

		if (!empty($children))
		{
			$options = $children->option;
		}

		$value = empty($this->value) ? $default : $this->value;

		$html = [];
		$html[] = '<div class="hu-switcher ' . $switcherClasses . '">';
		$html[] = '<div class="hu-action-group">';

		if (!empty($options))
		{
			foreach ($options as $option)
			{
				$html[] = '<span class="hu-switcher-action ' .
					($value === (string) $option['value'] ? 'active' : '') .
					$option->class .
					'" data-value="' . ((string) $option['value']) . '" hu-switcher-action role="button">';

				$html[] = '<span class="hu-switcher-action-content">';

				if (isset($option['icon']) && !empty($option['icon']))
				{
					$html[] = '<span class="hu-switcher-icon"><span class="' . (string) $option['icon'] . '"></span></span>';
				}
				elseif (isset($option['svg']) && !empty($option['svg']))
				{
					$svg_path = JPATH_PLUGINS . '/system/helixultimate/assets/images/icons/' . (string) $option['svg'] . '.svg';
					// $svg = File::exists($svg_path) ? File::read($svg_path) : (string) $option['svg'];
					$svg = File::exists($svg_path) ? file_get_contents($svg_path) : (string) $option['svg'];
					$html[] = '<span class="hu-switcher-svg">' . $svg . '</span>';
				}
				elseif (isset($option['image']) && !empty($option['image']))
				{
					$html[] = '<span class="hu-switcher-img"><img src="' . (string) $option['image'] . '"  /></span>';
				}

				$html[] = '</span>';

				$html[] = '<span class="hu-switcher-label">' . Text::_((string) $option) . '</span>';

				$html[] = '</span>';
			}
		}

		$html[] = '</div>';
		$html[] = '<input type="hidden" name="' . $this->name . '" id="' . $this->id . '" value="' . $value . '" />';
		$html[] = '</div>';

		return implode("\n", $html);
	}
}
