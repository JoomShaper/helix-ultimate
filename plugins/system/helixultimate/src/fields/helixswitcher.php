<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
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

		$fixedWidth = (string) $this->element['fixedwidth'];
		$fixedWidth = !empty($fixedWidth) && ($fixedWidth === 'true' || $fixedWidth === 'on');

		$alignment = (string) $this->element['alignment'];
		$alignment = !empty($alignment) ? 'align-' . $alignment : '';

		$switcherClasses = '';

		if ($fixedWidth)
		{
			$switcherClasses .= ' fixed-width';
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
		$html[] = '		<div class="hu-btn-group">';

		if (!empty($options))
		{
			foreach ($options as $option)
			{
				$html[] = '<button type="button" class="hu-btn ' .
					($value === (string) $option['value'] ? 'active' : '') .
					$option->class .
					'" value="' . ((string) $option['value']) . '">';

				switch ($rule)
				{
					case 'icon':
						$icon = (string) $option['icon'];

						if (empty($icon))
						{
							return  sprintf('<code>Error: You\'ve given the ruletype as icon but forget to set icon attribute to the option element.</code>');
						}

						$html[] = '<span class="' . $icon . '"></span>';

					break;
					case 'text-icon':
						$icon = (string) $option['icon'];
						$text = (string) $option;

						if (empty($icon) || empty($text))
						{
							return  sprintf('<code>Error: You\'ve given the ruletype as "text-icon" but forget to set text or icon attribute to the option element.</code>');
						}

						$html[] = '<span class="hu-switcher-text">' . Text::_($text) . '</span>';
						$html[] = '<span class="' . $icon . '"></span>';

					break;
					case 'icon-text':
						$icon = (string) $option['icon'];
						$text = (string) $option;

						if (empty($icon) || empty($text))
						{
							return  sprintf('<code>Error: You\'ve given the ruletype as "icon-text" but forget to set text or icon attribute to the option element.</code>');
						}

						$html[] = '<span class="' . $icon . '"></span>';
						$html[] = '<span class="hu-switcher-text">' . Text::_($text) . '</span>';

					break;
					case 'image':
						$src = (string) $option['src'];

						if (empty($src))
						{
							return  sprintf('<code>Error: You\'ve given the ruletype as "image" but forget to set "src" attribute to the option element.</code>');
						}

						$html[] = '<div class="img-wrapper">';

						if (preg_match("#\.svg$#", $src))
						{
							$svg = file_get_contents(Uri::root() . $src);

							if (preg_match("#^\<\?xml.*\?\>$#", $svg))
							{
								$svg = preg_replace("#^\<\?xml.*\?\>$#", "", $svg);
							}

							$html[] = $svg;
						}
						else
						{
							$html[] = '<img class="image" alt="image" src="' . $src . '" />';
						}

						$html[] = '</div>';
					break;

					case 'image-text':
						$src = (string) $option['src'];
						$text = (string) $option;

						if (empty($src) || empty($text))
						{
							return  sprintf('<code>Error: You\'ve given the ruletype as "image-text" but forget to set "src" or "text" attribute to the option element.</code>');
						}

						$html[] = '<div class="img-wrapper">';

						if (preg_match("#\.svg$#", $src))
						{
							$svg = file_get_contents(Uri::root() . $src);

							if (preg_match("#^\<\?xml.*\?\>$#", $svg))
							{
								$svg = preg_replace("#^\<\?xml.*\?\>$#", "", $svg);
							}

							$html[] = $svg;
						}
						else
						{
							$html[] = '<img class="image" alt="image" src="' . $src . '" />';
						}

						$html[] = '<p class="text">' . Text::_($text) . '</p>';

						$html[] = '</div>';
					break;

					case 'text-image':
						$src = (string) $option['src'];
						$text = (string) $option;

						if (empty($src) || empty($text))
						{
							return  sprintf('<code>Error: You\'ve given the ruletype as "text-image" but forget to set "src" or "text" attribute to the option element.</code>');
						}

						$html[] = '<div class="img-wrapper">';
						$html[] = '<p class="text">' . Text::_($text) . '</p>';

						if (preg_match("#\.svg$#", $src))
						{
							$svg = file_get_contents(Uri::root() . $src);

							if (preg_match("#^\<\?xml.*\?\>$#", $svg))
							{
								$svg = preg_replace("#^\<\?xml.*\?\>$#", "", $svg);
							}

							$html[] = $svg;
						}
						else
						{
							$html[] = '<img class="image" alt="image" src="' . $src . '" />';
						}

						$html[] = '</div>';
					break;

					default:
						$html[] = Text::_((string) $option);
				}

				$html[] = '</button>';
			}
		}

		$html[] = '</div>';
		$html[] = '<input type="hidden" name="' . $this->name . '" id="' . $this->id . '" value="' . $value . '" />';
		$html[] = '</div>';

		return implode("\n", $html);
	}
}
