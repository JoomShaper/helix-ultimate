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

function column_grid_system($device = 'lg')
{
	$col = array(0 => 'Inherit');

	for ($i = 1; $i <= 12; $i++)
	{
		if ($device === 'xs')
		{
			$col[$i] = 'col-' . $i;
		}
		else
		{
			$col[$i] = 'col-' . $device . '-' . $i;
		}
	}

	return $col;
}

$rowSettings = array(
	'type' => 'general',
	'title' => '',
	'attr' => array(
		'name' => array(
			'type' => 'text',
			'group' => 'general',
			'title' => Text::_('HELIX_ULTIMATE_SECTION_TITLE'),
			'desc' => Text::_('HELIX_ULTIMATE_SECTION_TITLE_DESC')
		),

		'fluidrow' => array(
			'type' => 'checkbox',
			'group' => 'general',
			'title' => Text::_('HELIX_ULTIMATE_ROW_FULL_WIDTH'),
			'desc' => Text::_('HELIX_ULTIMATE_ROW_FULL_WIDTH_DESC')
		),

		'custom_class' => array(
			'type' => 'text',
			'group' => 'general',
			'title' => Text::_('HELIX_ULTIMATE_CUSTOM_CLASS'),
			'desc' => Text::_('HELIX_ULTIMATE_CUSTOM_CLASS_DESC'),
			'std' => ''
		),

		'padding' => array(
			'type' => 'text',
			'group' => 'style',
			'title'	=> Text::_('HELIX_ULTIMATE_PADDING'),
			'placeholder' => '0px 0px 0px 0px'
		),

		'margin' => array(
			'type' => 'text',
			'group' => 'style',
			'title'	=> Text::_('HELIX_ULTIMATE_MARGIN'),
			'placeholder' => '0px 0px 0px 0px'
		),

		'color' => array(
			'type' => 'color',
			'group' => 'style',
			'title' => Text::_('HELIX_ULTIMATE_SECTION_TEXT_COLOR')
		),

		'link_color' => array(
			'type' => 'color',
			'group' => 'style',
			'title' => Text::_('HELIX_ULTIMATE_LINK_COLOR')
		),

		'link_hover_color' => array(
			'type' => 'color',
			'group' => 'style',
			'title' => Text::_('HELIX_ULTIMATE_LINK_HOVER_COLOR')
		),

		'background_color' => array(
			'type' => 'color',
			'group' => 'style',
			'title' => Text::_('HELIX_ULTIMATE_SECTION_BACKGROUND_COLOR')
		),

		'background_image' => array(
			'type' => 'media',
			'group' => 'style',
			'title' => Text::_('HELIX_ULTIMATE_SECTION_BACKGROUND_IMAGE')
		),

		'background_repeat' => array(
			'type' => 'select',
			'group' => 'style',
			'title' => Text::_('HELIX_ULTIMATE_BACKGROUND_REPEAT'),
			'values' => array(
				'no-repeat' => Text::_('HELIX_ULTIMATE_BACKGROUND_REPEAT_NO'),
				'repeat' => Text::_('HELIX_ULTIMATE_BACKGROUND_REPEAT_ALL'),
				'repeat-x' => Text::_('HELIX_ULTIMATE_BACKGROUND_REPEAT_HORIZ'),
				'repeat-y' => Text::_('HELIX_ULTIMATE_BACKGROUND_REPEAT_VERTI'),
				'inherit' => Text::_('HELIX_ULTIMATE_BACKGROUND_REPEAT_INHERIT'),
			)
		),

		'background_size' => array(
			'type' => 'select',
			'group' => 'style',
			'title' => Text::_('HELIX_ULTIMATE_BACKGROUND_SIZE'),
			'values' => array(
				'cover' => Text::_('HELIX_ULTIMATE_BACKGROUND_COVER'),
				'contain' => Text::_('HELIX_ULTIMATE_BACKGROUND_CONTAIN'),
				'inherit' => Text::_('HELIX_ULTIMATE_BACKGROUND_INHERIT'),
			)
		),

		'background_attachment' => array(
			'type' => 'select',
			'group' => 'style',
			'title' => Text::_('HELIX_ULTIMATE_BACKGROUND_ATTACHMENT'),
			'values' => array(
				'fixed' => Text::_('HELIX_ULTIMATE_BACKGROUND_ATTACHMENT_FIXED'),
				'scroll' => Text::_('HELIX_ULTIMATE_BACKGROUND_ATTACHMENT_SCROLL'),
				'inherit' => Text::_('HELIX_ULTIMATE_BACKGROUND_ATTACHMENT_INHERIT'),
			)
		),

		'background_position' => array(
			'type' => 'select',
			'group' => 'style',
			'title' => Text::_('HELIX_ULTIMATE_BACKGROUND_POSITION'),
			'values' => array(
				'0 0' => Text::_('HELIX_ULTIMATE_BACKGROUND_POSITION_LEFT_TOP'),
				'0 50%' => Text::_('HELIX_ULTIMATE_BACKGROUND_POSITION_LEFT_CENTER'),
				'0 100%' => Text::_('HELIX_ULTIMATE_BACKGROUND_POSITION_LEFT_BOTTOM'),
				'50% 0' => Text::_('HELIX_ULTIMATE_BACKGROUND_POSITION_CENTER_TOP'),
				'50% 50%' => Text::_('HELIX_ULTIMATE_BACKGROUND_POSITION_CENTER_CENTER'),
				'50% 100%' => Text::_('HELIX_ULTIMATE_BACKGROUND_POSITION_CENTER_BOTTOM'),
				'100% 0' => Text::_('HELIX_ULTIMATE_BACKGROUND_POSITION_RIGHT_TOP'),
				'100% 50%' => Text::_('HELIX_ULTIMATE_BACKGROUND_POSITION_RIGHT_CENTER'),
				'100% 100%' => Text::_('HELIX_ULTIMATE_BACKGROUND_POSITION_RIGHT_BOTTOM'),
			)
		),

		'hide_on_phone' => array(
			'type' => 'checkbox',
			'group' => 'responsive',
			'title' => Text::_('HELIX_ULTIMATE_HIDDEN_PHONE')
		),

		'hide_on_large_phone' => array(
			'type' => 'checkbox',
			'group' => 'responsive',
			'title' => Text::_('HELIX_ULTIMATE_HIDDEN_LARGER_PHONE')
		),

		'hide_on_tablet' => array(
			'type' => 'checkbox',
			'group' => 'responsive',
			'title' => Text::_('HELIX_ULTIMATE_HIDDEN_TABLET')
		),

		'hide_on_small_desktop' => array(
			'type' => 'checkbox',
			'group' => 'responsive',
			'title' => Text::_('HELIX_ULTIMATE_HIDDEN_SMALL_DESKTOP')
		),

		'hide_on_desktop' => array(
			'type' => 'checkbox',
			'group' => 'responsive',
			'title' => Text::_('HELIX_ULTIMATE_HIDDEN_DESKTOP')
		)
	)
);

$columnSettings = array(
	'type' => 'general',
	'title' => '',
	'attr' => array(
		'column_type' => array(
			'type' => 'checkbox',
			'group' => 'general',
			'title' => Text::_('HELIX_ULTIMATE_COMPONENT'),
			'desc' => Text::_('HELIX_ULTIMATE_COMPONENT_DESC'),
			'std' => '',
		),

		'name' => array(
			'type' => 'select',
			'group' => 'general',
			'title' => Text::_('HELIX_ULTIMATE_MODULE_POSITION'),
			'desc' => Text::_('HELIX_ULTIMATE_MODULE_POSITION_DESC'),
			'values' => array(),
			'std' => 'none',
		),

		'custom_class' => array(
			'type' => 'text',
			'group' => 'general',
			'title' => Text::_('HELIX_ULTIMATE_CUSTOM_CLASS'),
			'desc' => Text::_('HELIX_ULTIMATE_CUSTOM_CLASS_DESC'),
			'std' => ''
		),

		'xl_col' => array(
			'type' => 'select',
			'group' => 'grid',
			'title' => Text::_('HELIX_ULTIMATE_LARGER_DESKTOP_GRID'),
			'values' => column_grid_system('xl'),
			'std' => 0,
		),

		'lg_col' => array(
			'type' => 'select',
			'group' => 'grid',
			'title' => Text::_('HELIX_ULTIMATE_SMALLER_DESKTOP_GRID'),
			'values' => column_grid_system('lg'),
			'std' => 0,
		),

		'md_col' => array(
			'type' => 'select',
			'group' => 'grid',
			'title' => Text::_('HELIX_ULTIMATE_TABLET_GRID'),
			'values' => column_grid_system('md'),
			'std' => 0,
		),

		'sm_col' => array(
			'type' => 'select',
			'group' => 'grid',
			'title' => Text::_('HELIX_ULTIMATE_LARGER_PHONE_GRID'),
			'values' => column_grid_system('sm'),
			'std' => 0,
		),

		'xs_col' => array(
			'type' => 'select',
			'group' => 'grid',
			'title' => Text::_('HELIX_ULTIMATE_PHONE_GRID'),
			'values' => column_grid_system('xs'),
			'std' => 0,
		),

		'hide_on_phone' => array(
			'type' => 'checkbox',
			'group' => 'responsive',
			'title' => Text::_('HELIX_ULTIMATE_HIDDEN_PHONE')
		),

		'hide_on_large_phone' => array(
			'type' => 'checkbox',
			'group' => 'responsive',
			'title' => Text::_('HELIX_ULTIMATE_HIDDEN_LARGER_PHONE')
		),

		'hide_on_tablet' => array(
			'type' => 'checkbox',
			'group' => 'responsive',
			'title' => Text::_('HELIX_ULTIMATE_HIDDEN_TABLET')
		),

		'hide_on_small_desktop' => array(
			'type' => 'checkbox',
			'group' => 'responsive',
			'title' => Text::_('HELIX_ULTIMATE_HIDDEN_SMALL_DESKTOP')
		),

		'hide_on_desktop' => array(
			'type' => 'checkbox',
			'group' => 'responsive',
			'title' => Text::_('HELIX_ULTIMATE_HIDDEN_DESKTOP')
		),
	)
);

/**
 * Row colum settings.
 *
 * @since	1.0.0
 */
class RowColumnSettings
{

	/**
	 * Get the input elements by key attributes.
	 *
	 * @param	string	$key	The field key
	 * @param	array	$attr	Field attributes
	 *
	 * @return	string
	 * @since	1.0.0
	 */
	private static function getInputElements($key, $attr)
	{
		return call_user_func(array('HelixultimateField' . ucfirst($attr['type']), 'getInput'), $key, $attr);
	}

	/**
	 * Get row settings.
	 *
	 * @param	array	$row_settings	Row settings
	 *
	 * @return	string	Row string
	 * @since	1.0.0
	 */
	static public function getRowSettings($row_settings = array())
	{

		$output = '<div style="display: none;">';
		$output .= '<div id="hu-row-settings">';

		$options = array();

		foreach ($row_settings['attr'] as $key => $rowAttr)
		{
			if (isset($rowAttr['group']) && $rowAttr['group'])
			{
				$options[$rowAttr['group']][$key] = $rowAttr;
				unset($rowAttr['group']);
			}
			else
			{
				$options['general'][$key] = $rowAttr;
			}
		}

		$i = 0;

		foreach ($options as $key2 => $option_list)
		{
			$active = '';

			if ((int) $i === 0)
			{
				$active = ' active';
			}

			$output .= '<div class="hu-option-group hu-option-group-' . strtolower($key2) . $active . '">';
			$output .= '<div class="hu-option-group-title">';
			$output .= '<span class="fas fa-angle-right" aria-hidden="true"></span>' . Text::_('HELIX_ULTIMATE_OPTION_GROUP_' . strtoupper($key2));
			$output .= '</div>';
			$output .= '<div class="hu-option-group-list">';

			foreach ($option_list as $key3 => $option)
			{
				$output .= self::getInputElements($key3, $option);
			}

			$output .= '</div>';
			$output .= '</div>';
			$i++;
		}

		$output .= '</div>';
		$output .= '</div>';

		return $output;
	}

	/**
	 * Get column settings.
	 *
	 * @param	array	$col_settings	Columns settings
	 *
	 * @return	string	column settings.
	 * @since	1.0.0
	 */
	static public function getColumnSettings($col_settings = array())
	{

		$col_settings['attr']['name']['values'] = self::getPositions();

		$output = '<div style="display: none;">';
		$output .= '<div id="hu-column-settings">';

		$options = array();

		foreach ($col_settings['attr'] as $key => $colAttr)
		{
			if (isset($colAttr['group']) && $colAttr['group'])
			{
				$options[$colAttr['group']][$key] = $colAttr;
				unset($colAttr['group']);
			}
			else
			{
				$options['general'][$key] = $colAttr;
			}
		}

		$i = 0;

		foreach ($options as $key2 => $option_list)
		{
			$active = '';

			if ((int) $i === 0)
			{
				$active = ' active';
			}

			$output .= '<div class="hu-option-group hu-option-group-' . strtolower($key2) . $active . '">';
			$output .= '<div class="hu-option-group-title">';
			$output .= '<span class="fas fa-angle-right" aria-hidden="true"></span>' . Text::_('HELIX_ULTIMATE_OPTION_GROUP_' . strtoupper($key2));
			$output .= '</div>';
			$output .= '<div class="hu-option-group-list">';

			foreach ($option_list as $key3 => $option)
			{
				$output .= self::getInputElements($key3, $option);
			}

			$output .= '</div>';
			$output .= '</div>';
			$i++;
		}

		$output .= '</div>';
		$output .= '</div>';

		return $output;
	}

	/**
	 * Get template name
	 *
	 * @return	string	template name
	 * @since	1.0.0
	 */
	static public function getTemplateName()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('template')));
		$query->from($db->quoteName('#__template_styles'));
		$query->where($db->quoteName('client_id') . ' = 0');
		$query->where($db->quoteName('home') . ' = 1');
		$db->setQuery($query);

		return $db->loadObject()->template;
	}

	/**
	 * Get module positions
	 */
	static public function getPositions()
	{

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('position'));
		$query->from($db->quoteName('#__modules'));
		$query->where($db->quoteName('client_id') . ' = 0');
		$query->where($db->quoteName('published') . ' = 1');
		$query->group('position');
		$query->order('position ASC');
		$db->setQuery($query);
		$dbpositions = $db->loadObjectList();

		$template  = self::getTemplateName();

		$templateXML = JPATH_SITE . '/templates/' . $template . '/templateDetails.xml';
		$templateXml = simplexml_load_file($templateXML);
		$options = array();

		foreach ($dbpositions as $positions)
		{
			$options[] = $positions->position;
		}

		foreach ($templateXml->positions[0] as $position)
		{
			$options[] = (string) $position;
		}

		ksort($options);

		$opts = array_unique($options);

		$options = array();

		foreach ($opts as $opt)
		{
			$options[$opt] = $opt;
		}

		return $options;
	}

	/**
	 * Get settings
	 *
	 * @param	array	$config	The configuration array
	 *
	 * @return	string	settings HTML
	 * @since	1.0.0
	 */
	static public function getSettings($config = null)
	{
		$data = '';

		if (!empty($config))
		{
			foreach ($config as $key => $value)
			{
				$data .= ' data-' . $key . '="' . $value . '"';
			}
		}

		return $data;
	}
}
