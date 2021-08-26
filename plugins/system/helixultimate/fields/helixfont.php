<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Filesystem\File;
use HelixUltimate\Framework\Platform\Helper;

/**
 * Form field for Helix font.
 *
 * @since		1.0.0
 * @deprecated	3.0		Use the Same Class from the src/fields instead.
 */
class JFormFieldHelixfont extends FormField
{
	/**
	 * Field type
	 *
	 * @var		string	$type
	 * @since	1.0.0
	 */
	protected $type = 'Helixfont';

	/**
	 * Override getInput function form FormField
	 *
	 * @return	string	Field HTML string
	 * @since	1.0.0
	 */
	protected function getInput()
	{

		$input  = Factory::getApplication()->input;
		$style_id = $input->get('id', 0, 'INT');
		$style = Helper::getTemplateStyle($style_id);

		$template_path = JPATH_SITE . '/templates/' . $style->template . '/webfonts/webfonts.json';
		$plugin_path   = JPATH_PLUGINS . '/system/helixultimate/assets/webfonts/webfonts.json';

		if (file_exists($template_path))
		{
			// $json = File::read($template_path);
			$json = file_get_contents($template_path);
		}
		else
		{
			// $json = File::read($plugin_path);
			$json = file_get_contents($plugin_path);
		}

		$webfonts   = json_decode($json);
		$items      = $webfonts->items;
		$value      = json_decode($this->value);

		if (isset($value->fontFamily))
		{
			$font = self::filterArray($items, $value->fontFamily);
		}

		$html  = '';
		$classes = (!empty($this->element['class'])) ? $this->element['class'] : '';

		$systemFonts = array(
			'Arial',
			'Tahoma',
			'Verdana',
			'Helvetica',
			'Times New Roman',
			'Trebuchet MS',
			'Georgia'
		);

		$fontWeights = array(
			'100' => 'Thin',
			'200' => 'Extra Light',
			'300' => 'Light',
			'400' => 'Normal',
			'500' => 'Medium',
			'600' => 'Semi Bold',
			'700' => 'Bold',
			'800' => 'Extra Bold',
			'900' => 'Black'
		);

		$fontStyles = array(
			'normal' => 'Normal',
			'italic' => 'Italic',
			'oblique' => 'Oblique'
		);

		// Font Family
		$html .= '<div class="hu-field-webfont ' . $classes . '">';

		/**
		 * Preview Row
		 */
		$html .= '<div class="hu-webfont-preview-wrapper">';
		$html .= '<div class="hu-webfont-preview">1 2 3 4 5 6 7 8 9 0 Grumpy wizards make toxic brew for the evil Queen and Jack.</div>';
		$html .= '</div>';

		/**
		 * Start Fonts List row
		 */
		$html .= '<div class="hu-webfont-family hu-mb-3">';
		$html .= $this->renderFontsList($systemFonts, $value, $items);
		$html .= '</div>';

		/**
		 * Font size, weight, color row
		 */
		$html .= '<div class="row">';

		/**
		 * Start Font Weight
		 */
		$html .= '<div class="col-5 hu-mb-3">';
		$html .= $this->renderFontWeight($fontWeights, $value);
		$html .= '</div>';

		/**
		 * Start Font Size
		 */
		$html .= '<div class="col-4 hu-mb-3 hu-narrow-input">';
		$html .= $this->renderFontSize($value);
		$html .= '</div>';

		/**
		 * Start Font Color
		 */
		$html .= '<div class="col-3 hu-mb-3 hu-narrow-input">';
		$html .= $this->renderFontColor($value);
		$html .= '</div>';

		$html .= '</div>';

		/**
		 * Font subset, letter spacing, line height row
		 */
		$html .= '<div class="row spacing-row">';

		/**
		 * Font subset section
		 */
		$html .= '<div class="col-5 hu-mb-3">';
		$html .= $this->renderFontSubset($systemFonts, $font, $value);
		$html .= '</div>';

		/**
		 * Set line height
		 */
		$html .= '<div class="col-3 hu-mb-3 hu-narrow-input">';
		$html .= $this->renderLineHeight($value);
		$html .= '</div>';

		/**
		 * Set Letter Spacing
		 */
		$html .= '<div class="col-4 hu-mb-3 hu-narrow-input">';
		$html .= $this->renderLetterSpacing($value);
		$html .= '</div>';

		$html .= '</div>';

		/**
		 * Font style, alignment row
		 */
		$html .= '<div class="row style-alignment">';

		/**
		 * Text Decoration
		 */
		$html .= '<div class="col-6 hu-mb-3">';
		$html .= $this->renderTextDecoration($value);
		$html .= '</div>';

		/**
		 * Font Alignment
		 */
		$html .= '<div class="col-6 hu-mb-3">';
		$html .= $this->renderFontAlignment($value);
		$html .= '</div>';

		$html .= '</div>';

		$html .= '<input type="hidden" name="' . $this->name . '" value=\'' . $this->value . '\' class="hu-webfont-input" id="' . $this->id . '">';
		$html .= '</div>';

		return $html;
	}

	/**
	 * Get select options for the field.
	 *
	 * @param	array	$items		The items form where the options will be generated.
	 * @param	string	$selected	The selected option item.
	 *
	 * @return	string	The option HTML string.
	 * @since	1.0.0
	 */
	private function generateSelectOptions( $items = array(), $selected = '' )
	{
		$html = '';

		foreach ($items as $item)
		{
			$html .= '<option ' . (($selected !== 'no-selection' && $item == $selected) ? 'selected="selected"' : '') . ' value="' . $item . '">' . $item . '</option>';
		}

		return $html;
	}

	/**
	 * Get Current font.
	 *
	 * @param	array	$items	The fonts array.
	 * @param	string	$key	The expected font.
	 *
	 * @return 	mixed
	 * @since	1.0.0
	 */
	private static function filterArray($items, $key)
	{
		foreach ($items as $item)
		{
			if ($item->family === $key)
			{
				return $item;
			}
		}

		return false;
	}

	private function renderFontsList($systemFonts, $value, $items)
	{
		$html = '';
		$html .= '<label class="hu-mb-2">' . Text::_('HELIX_ULTIMATE_FONT_FAMILY') . '</label>';
		$html .= '<select class="hu-webfont-list">';
		$html .= '<optgroup label="' . Text::_('HELIX_ULTIMATE_SYSTEM_FONT') . '">';

		foreach ($systemFonts as $systemFont)
		{
			$html .= '<option ' . ((isset($value->fontFamily) && $systemFont === $value->fontFamily) ? 'selected="selected"' : '') . ' value="' . $systemFont . '">' . $systemFont . '</option>';
		}

		$html .= '</optgroup>';

		$html .= '<optgroup label="' . Text::_('HELIX_ULTIMATE_GOOGLE_FONT') . '">';

		foreach ($items as $item)
		{
			$html .= '<option ' . ((isset($value->fontFamily) && $item->family === $value->fontFamily) ? 'selected="selected"' : '') . ' value="' . $item->family . '">' . $item->family . '</option>';
		}

		$html .= '</optgroup>';

		$html .= '</select>';

		return $html;
	}

	private function renderFontWeight($fontWeights, $value)
	{
		$html = '';
		$html .= '<div class="hu-webfont-weight">';
		$html .= '<label class="hu-mb-2">' . Text::_('HELIX_ULTIMATE_FONT_WEIGHT') . '</label>';
		$html .= '<select class="hu-webfont-weight-list">';
		$html .= '<option value="">' . Text::_('HELIX_ULTIMATE_SELECT') . '</option>';

		foreach ($fontWeights as $key => $fontWeight)
		{
			if (isset($value->fontWeight) && $value->fontWeight === $key)
			{
				$html .= '<option value="' . $key . '" selected>' . $fontWeight . '</option>';
			}
			else
			{
				$html .= '<option value="' . $key . '">' . $fontWeight . '</option>';
			}
		}

		$html .= '</select>';
		$html .= '</div>';

		return $html;
	}

	private function renderFontSize($value)
	{
		$html = '';

		$fontSize = (isset($value->fontSize)) ? $value->fontSize : '';
		$fontSize_sm = (isset($value->fontSize_sm)) ? $value->fontSize_sm : '';
		$fontSize_xs = (isset($value->fontSize_xs)) ? $value->fontSize_xs : '';
		$html .= '<div class="hu-webfont-size">';
		$html .= '<label class="hu-mb-2">' . Text::_('HELIX_ULTIMATE_FONT_SIZE') . '</label>';
		$html .= '<input type="number" value="' . $fontSize . '" class="form-control hu-webfont-size-input active" min="6" max="200">';
		$html .= '<input type="number" value="' . $fontSize_sm . '" class="form-control hu-webfont-size-input-sm" min="6" max="200">';
		$html .= '<input type="number" value="' . $fontSize_xs . '" class="form-control hu-webfont-size-input-xs" min="6" max="200">';
		$html .= '</div>';

		return $html;
	}

	private function renderFontColor($value)
	{
		$color = !empty($value->fontColor) ? $value->fontColor : '';
		$html = '';
		$html .= '<div class="hu-font-color">';
		$html .= '<label class="hu-mb-2">' . Text::_('HELIX_ULTIMATE_FONT_COLOR') . '</label>';
		$html .= '<input type="text" class="form-control hu-font-color-input minicolors" placeholder="Font Color" value="' . $color . '" />';
		$html .= '</div>';

		return $html;
	}

	private function renderFontSubset($systemFonts, $font, $value)
	{
		$html = '';
		$html .= '<label class="hu-mb-2">' . Text::_('HELIX_ULTIMATE_FONT_SUBSET') . '</label>';
		$html .= '<select class="hu-webfont-subset-list">';
		$html .= '<option value="">' . Text::_('HELIX_ULTIMATE_SELECT') . '</option>';

		if (isset($value->fontFamily) && $value->fontFamily)
		{
			if (!in_array($value->fontFamily, $systemFonts))
			{
				$html .= $this->generateSelectOptions($font->subsets, $value->fontSubset);
			}
		}

		$html .= '</select>';

		return $html;
	}

	private function renderLineHeight($value)
	{
		$height = !empty($value->fontLineHeight) ? $value->fontLineHeight : '';
		$html = '';
		$html .= '<div class="hu-font-line-height">';
		$html .= '<label class="hu-mb-2">' . Text::_('HELIX_ULTIMATE_FONT_LINE_HEIGHT') . '</label>';
		$html .= '<input type="number" class="form-control hu-font-line-height-input" min="1" max="200" value="' . $height . '" />';
		$html .= '</div>';

		return $html;
	}

	private function renderLetterSpacing($value)
	{
		$spacing = !empty($value->fontLetterSpacing) ? $value->fontLetterSpacing : '';
		$html = '';
		$html .= '<div class="hu-font-letter-spacing">';
		$html .= '<label class="hu-mb-2">' . Text::_('HELIX_ULTIMATE_FONT_LETTER_SPACING') . '</label>';
		$html .= '<input type="number" class="form-control hu-font-letter-spacing-input" value="' . $spacing . '" step=".1" />';
		$html .= '</div>';

		return $html;
	}

	private function renderTextDecoration($value)
	{
		$decoration = !empty($value->textDecoration) ? $value->textDecoration : 'none';

		$html = '';
		$html .= '<div class="hu-font-decoration">';
		$html .= '<label class="hu-mb-2">' . Text::_('HELIX_ULTIMATE_FONT_DECORATION') . '</label>';
		
		$html .= '<div class="hu-switcher hu-switcher-inline hu-switcher-style-tab hu-switcher-style-tab-sm">';
		$html .= '<div class="hu-action-group">';
		$html .= '<span data-value="none" class="hu-switcher-action ' . ($decoration === 'none' ? 'active' : '') . '" role="button">';
		$html .= '<span class="fas fa-times" aria-hidden="true"></span>';
		$html .= '</span>';

		$html .= '<span data-value="underline" class="hu-switcher-action ' . ($decoration === 'underline' ? 'active' : '') . '" role="button">';
		$html .= '<span class="fas fa-underline" aria-hidden="true"></span>';
		$html .= '</span>';

		$html .= '<span data-value="line-through" class="hu-switcher-action ' . ($decoration === 'strikethrough' ? 'active' : '') . '" role="button">';
		$html .= '<span class="fas fa-strikethrough" aria-hidden="true"></span>';
		$html .= '</span>';

		$html .= '<span data-value="overline" class="hu-switcher-action ' . ($decoration === 'overline' ? 'active' : '') . '" role="button">';
		$html .= '<span class="fas fa-overline" aria-hidden="true">O</span>';
		$html .= '</span>';
		$html .= '</div>';
		$html .= '</div>';

		$html .= '<input type="hidden" class="hu-text-decoration" value="' . $decoration . '" />';
		$html .= '</div>';

		return $html;
	}

	private function renderFontAlignment($value)
	{
		$alignment = !empty($value->textAlign) ? $value->textAlign : '';

		$html = '';
		$html .= '<div class="hu-font-alignment">';
		$html .= '<label class="hu-mb-2">' . Text::_('HELIX_ULTIMATE_FONT_ALIGNMENT') . '</label>';
		$html .= '<div class="hu-switcher hu-switcher-inline hu-switcher-style-tab hu-switcher-style-tab-sm">';

		$html .= '<div class="hu-action-group">';
		$html .= '<span data-value="left" class="hu-switcher-action ' . ($alignment === 'left' ? 'active' : '') . '" role="button">';
		$html .= '<span class="fas fa-align-left" aria-hidden="true"></span>';
		$html .= '</span>';

		$html .= '<span data-value="center" class="hu-switcher-action ' . ($alignment === 'center' ? 'active' : '') . '" role="button">';
		$html .= '<span class="fas fa-align-center" aria-hidden="true"></span>';
		$html .= '</span>';

		$html .= '<span data-value="right" class="hu-switcher-action ' . ($alignment === 'right' ? 'active' : '') . '" role="button">';
		$html .= '<span class="fas fa-align-right" aria-hidden="true"></span>';
		$html .= '</span>';

		$html .= '<span data-value="justify" class="hu-switcher-action ' . ($alignment === 'justify' ? 'active' : '') . '" role="button">';
		$html .= '<span class="fas fa-align-justify" aria-hidden="true"></span>';
		$html .= '</span>';
		$html .= '</div>';

		$html .= '</div>';
		$html .= '<input type="hidden" class="hu-text-align" value="' . $alignment . '" />';
		$html .= '</div>';

		return $html;
	}

}
