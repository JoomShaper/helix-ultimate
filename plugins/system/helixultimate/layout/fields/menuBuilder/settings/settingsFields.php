<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;

/**
 * Get column fields
 *
 * @since	2.0.0
 */
class SettingsFields
{
	/**
	 * Fields data.
	 *
	 * @var		array	The data needed for the fields
	 * @since	2.0.0
	 */
	private $data = [];

	/**
	 * Constructor method.
	 *
	 * @param	array	$_data	The data array.
	 * @since	2.0.0
	 */
	public function __construct($_data)
	{
		$this->data = $_data;
	}

	/**
	 * Get column settings fields
	 *
	 * @return	array	The fields array.
	 * @since	2.0.0
	 */
	public function getColumnSettingsFields() : array
	{
		extract($this->data);

		return [
			'general' => [
				'title' => Text::_('HELIX_ULTIMATE_MEGA_ROW_SETTINGS_GROUP_GENERAL'),
				'active' => true,
				'icon' => 'fas fa-cogs',
				'group_fields' => [
					'col_label' => [
						'type' => 'text',
						'title' => Text::_('HELIX_ULTIMATE_MEGA_COL_LABEL'),
						'desc' => Text::_('HELIX_ULTIMATE_MEGA_COL_LABEL_DESC'),
						'menu-builder' => true,
						'data' => ['rowid' => $rowId, 'itemid' => $item->id, 'columnid' => $id],
						'value' => !empty($settings->col_label) ? $settings->col_label : '',
					],
					'enable_col_title' => [
						'type' => 'checkbox',
						'title' => Text::_('HELIX_ULTIMATE_MEGA_ENABLE_COL_TITLE'),
						'desc' => Text::_('HELIX_ULTIMATE_MEGA_ENABLE_COL_TITLE_DESC'),
						'menu-builder' => true,
						'data' => ['rowid' => $rowId, 'itemid' => $item->id, 'columnid' => $id],
						'value' => !empty($settings->enable_col_title) ? $settings->enable_col_title : '',
					],
					'col_title' => [
						'type' => 'text',
						'title' => Text::_('HELIX_ULTIMATE_MEGA_COL_TITLE'),
						'desc' => Text::_('HELIX_ULTIMATE_MEGA_COL_TITLE_DESC'),
						'menu-builder' => true,
						'data' => ['rowid' => $rowId, 'itemid' => $item->id, 'columnid' => $id],
						'value' => !empty($settings->col_title) ? $settings->col_title : '',
						'depend' => 'enable_col_title:1'
					],
					'col_type' => [
						'type' => 'select',
						'title' => Text::_('HELIX_ULTIMATE_MEGA_COL_TYPE'),
						'desc' => Text::_('HELIX_ULTIMATE_MEGA_COL_TYPE_DESC'),
						'menu-builder' => true,
						'data' => ['rowid' => $rowId, 'itemid' => $item->id, 'columnid' => $id],
						'options' => [
							'module_position' => Text::_('HELIX_ULTIMATE_COLUMN_SETTINGS_MODULE_POSITION'),
							'module' => Text::_('HELIX_ULTIMATE_COLUMN_SETTINGS_MODULE'),
							'menu_items' => Text::_('HELIX_ULTIMATE_COLUMN_SETTINGS_MENU_ITEMS'),
						],
						'value' => !empty($settings->col_type) ? $settings->col_type : '',
					],
					'module_position' => [
						'type' => 'select',
						'title' => Text::_('HELIX_ULTIMATE_MEGA_MODULE_POSITIONS'),
						'desc' => Text::_('HELIX_ULTIMATE_MEGA_MODULE_POSITIONS_DESC'),
						'menu-builder' => true,
						'data' => ['rowid' => $rowId, 'itemid' => $item->id, 'columnid' => $id],
						'options' => $positionOptions,
						'value' => !empty($settings->module_positions) ? $settings->module_positions : '',
						'depend' => 'col_type:module_position'
					],
					'module' => [
						'type' => 'select',
						'title' => Text::_('HELIX_ULTIMATE_MEGA_MODULE'),
						'desc' => Text::_('HELIX_ULTIMATE_MEGA_MODULE_DESC'),
						'menu-builder' => true,
						'data' => ['rowid' => $rowId, 'itemid' => $item->id, 'columnid' => $id],
						'options' => $moduleOptions,
						'value' => !empty($settings->module) ? $settings->module : '',
						'depend' => 'col_type:module'
					],
					'module_style' => [
						'type' => 'select',
						'title' => Text::_('HELIX_ULTIMATE_MEGA_MODULE_STYLE'),
						'desc' => Text::_('HELIX_ULTIMATE_MEGA_MODULE_STYLE_DESC'),
						'menu-builder' => true,
						'data' => ['rowid' => $rowId, 'itemid' => $item->id, 'columnid' => $id],
						'options' => [
							'sp_xhtml' => Text::_('sp_xhtml'),
							'default' => Text::_('Default'),
							'none' => Text::_('None'),
						],
						'value' => !empty($settings->module_style) ? $settings->module_style : '',
						'depend' => 'col_type:module|module_position'
					],
					'menu_items' => [
						'type' => 'menuHierarchy',
						'title' => 'Menu Items',
						'desc' => 'Check menu item(s)',
						'data' => ['rowid' => $rowId, 'itemid' => $item->id, 'columnid' => $id],
						'itemid' => $item->id,
						'value' => !empty($settings->menu_items) ? $settings->menu_items : '[]',
						'depend' => 'col_type:menu_items'
					],
					'col_id' => [
						'type' => 'text',
						'title' => Text::_('HELIX_ULTIMATE_MEGA_COL_SELECTOR_ID'),
						'desc' => Text::_('HELIX_ULTIMATE_MEGA_COL_SELECTOR_ID_DESC'),
						'menu-builder' => true,
						'data' => ['rowid' => $rowId, 'itemid' => $item->id, 'columnid' => $id],
						'value' => !empty($settings->row_id) ? $settings->row_id : '',
					],
					'col_class' => [
						'type' => 'text',
						'title' => Text::_('HELIX_ULTIMATE_MEGA_COL_SELECTOR_CLASS'),
						'desc' => Text::_('HELIX_ULTIMATE_MEGA_COL_SELECTOR_CLASS_DESC'),
						'menu-builder' => true,
						'data' => ['rowid' => $rowId, 'itemid' => $item->id, 'columnid' => $id],
						'value' => !empty($settings->row_class) ? $settings->row_class : '',
					]
				]
			],
			'styles' => [
				'title' => Text::_('HELIX_ULTIMATE_MEGA_ROW_SETTINGS_GROUP_STYLES'),
				'active' => false,
				'icon' => 'fas fa-paint-brush',
				'group_fields' => [
					'col_margin' => [
						'type' => 'text',
						'title' => Text::_('HELIX_ULTIMATE_MEGA_COL_MARGIN'),
						'desc' => Text::_('HELIX_ULTIMATE_MEGA_COL_MARGIN_DESC'),
						'menu-builder' => true,
						'data' => ['rowid' => $rowId, 'itemid' => $item->id, 'columnid' => $id],
						'value' => !empty($settings->col_margin) ? $settings->col_margin : '',
						'placeholder' => 'eg: 0px 0px 0px 0px'
					],
					'col_padding' => [
						'type' => 'text',
						'title' => Text::_('HELIX_ULTIMATE_MEGA_COL_PADDING'),
						'desc' => Text::_('HELIX_ULTIMATE_MEGA_COL_PADDING_DESC'),
						'menu-builder' => true,
						'data' => ['rowid' => $rowId, 'itemid' => $item->id, 'columnid' => $id],
						'value' => !empty($settings->col_padding) ? $settings->col_padding : '',
						'placeholder' => 'eg: 0px 0px 0px 0px'
					]
				]
			],
			'responsive' => [
				'title' => Text::_('HELIX_ULTIMATE_MEGA_ROW_SETTINGS_GROUP_RESPONSIVE'),
				'active' => false,
				'icon' => 'fas fa-laptop',
				'group_fields' => [
					'col_hide_phone' => [
						'type' => 'checkbox',
						'title' => Text::_('HELIX_ULTIMATE_MEGA_COL_HIDE_PHONE'),
						'desc' => Text::_('HELIX_ULTIMATE_MEGA_COL_HIDE_PHONE_DESC'),
						'menu-builder' => true,
						'data' => ['rowid' => $rowId, 'itemid' => $item->id, 'columnid' => $id],
						'value' => !empty($settings->col_hide_phone) ? $settings->col_hide_phone : '',
					],
					'col_hide_large_phone' => [
						'type' => 'checkbox',
						'title' => Text::_('HELIX_ULTIMATE_MEGA_COL_HIDE_LARGE_PHONE'),
						'desc' => Text::_('HELIX_ULTIMATE_MEGA_COL_HIDE_LARGE_PHONE_DESC'),
						'menu-builder' => true,
						'data' => ['rowid' => $rowId, 'itemid' => $item->id, 'columnid' => $id],
						'value' => !empty($settings->col_hide_large_phone) ? $settings->col_hide_large_phone : '',
					],
					'col_hide_tablet' => [
						'type' => 'checkbox',
						'title' => Text::_('HELIX_ULTIMATE_MEGA_COL_HIDE_TABLET'),
						'desc' => Text::_('HELIX_ULTIMATE_MEGA_COL_HIDE_TABLET_DESC'),
						'menu-builder' => true,
						'data' => ['rowid' => $rowId, 'itemid' => $item->id, 'columnid' => $id],
						'value' => !empty($settings->col_hide_tablet) ? $settings->col_hide_tablet : '',
					],
					'col_hide_small_desktop' => [
						'type' => 'checkbox',
						'title' => Text::_('HELIX_ULTIMATE_MEGA_COL_HIDE_SMALL_DESKTOP'),
						'desc' => Text::_('HELIX_ULTIMATE_MEGA_COL_HIDE_SMALL_DESKTOP_DESC'),
						'menu-builder' => true,
						'data' => ['rowid' => $rowId, 'itemid' => $item->id, 'columnid' => $id],
						'value' => !empty($settings->col_hide_small_desktop) ? $settings->col_hide_small_desktop : '',
					],
					'col_hide_desktop' => [
						'type' => 'checkbox',
						'title' => Text::_('HELIX_ULTIMATE_MEGA_COL_HIDE_DESKTOP'),
						'desc' => Text::_('HELIX_ULTIMATE_MEGA_COL_HIDE_DESKTOP_DESC'),
						'menu-builder' => true,
						'data' => ['rowid' => $rowId, 'itemid' => $item->id, 'columnid' => $id],
						'value' => !empty($settings->col_hide_desktop) ? $settings->col_hide_desktop : '',
					],
				]
			]
		];
	}

	public function getRowSettingsFields()
	{
		extract($this->data);

		return [
			'general' => [
				'title' => Text::_('HELIX_ULTIMATE_MEGA_ROW_SETTINGS_GROUP_GENERAL'),
				'active' => true,
				'icon' => 'fas fa-cogs',
				'group_fields' => [
					'row_label' => [
						'type' => 'text',
						'title' => Text::_('HELIX_ULTIMATE_MEGA_ROW_LABEL'),
						'desc' => Text::_('HELIX_ULTIMATE_MEGA_ROW_LABEL_DESC'),
						'menu-builder' => true,
						'data' => ['rowid' => $id, 'itemid' => $item->id],
						'value' => !empty($settings->row_label) ? $settings->row_label : '',
					],
					'enable_row_title' => [
						'type' => 'checkbox',
						'title' => Text::_('HELIX_ULTIMATE_MEGA_ROW_ENABLE_TITLE'),
						'desc' => Text::_('HELIX_ULTIMATE_MEGA_ROW_ENABLE_TITLE_DESC'),
						'menu-builder' => true,
						'data' => ['rowid' => $id, 'itemid' => $item->id],
						'value' => !empty($settings->enable_row_title) ? $settings->enable_row_title : '',
					],
					'row_title' => [
						'type' => 'text',
						'title' => Text::_('HELIX_ULTIMATE_MEGA_ROW_TITLE'),
						'desc' => Text::_('HELIX_ULTIMATE_MEGA_ROW_TITLE_DESC'),
						'menu-builder' => true,
						'data' => ['rowid' => $id, 'itemid' => $item->id],
						'value' => !empty($settings->row_title) ? $settings->row_title : '',
						'depend' => 'enable_row_title:1'
					],
					'row_id' => [
						'type' => 'text',
						'title' => Text::_('HELIX_ULTIMATE_MEGA_ROW_SELECTOR_ID'),
						'desc' => Text::_('HELIX_ULTIMATE_MEGA_ROW_SELECTOR_ID_DESC'),
						'menu-builder' => true,
						'data' => ['rowid' => $id, 'itemid' => $item->id],
						'value' => !empty($settings->row_id) ? $settings->row_id : '',
					],
					'row_class' => [
						'type' => 'text',
						'title' => Text::_('HELIX_ULTIMATE_MEGA_ROW_SELECTOR_CLASS'),
						'desc' => Text::_('HELIX_ULTIMATE_MEGA_ROW_SELECTOR_CLASS_DESC'),
						'menu-builder' => true,
						'data' => ['rowid' => $id, 'itemid' => $item->id],
						'value' => !empty($settings->row_class) ? $settings->row_class : '',
					]
				]
			],
			'styles' => [
				'title' => Text::_('HELIX_ULTIMATE_MEGA_ROW_SETTINGS_GROUP_STYLES'),
				'active' => false,
				'icon' => 'fas fa-paint-brush',
				'group_fields' => [
					'row_margin' => [
						'type' => 'text',
						'title' => Text::_('HELIX_ULTIMATE_MEGA_ROW_MARGIN'),
						'desc' => Text::_('HELIX_ULTIMATE_MEGA_ROW_MARGIN_DESC'),
						'menu-builder' => true,
						'data' => ['rowid' => $id, 'itemid' => $item->id],
						'value' => !empty($settings->row_margin) ? $settings->row_margin : '',
						'placeholder' => 'eg: 0px 0px 0px 0px'
					],
					'row_padding' => [
						'type' => 'text',
						'title' => Text::_('HELIX_ULTIMATE_MEGA_ROW_PADDING'),
						'desc' => Text::_('HELIX_ULTIMATE_MEGA_ROW_PADDING_DESC'),
						'menu-builder' => true,
						'data' => ['rowid' => $id, 'itemid' => $item->id],
						'value' => !empty($settings->row_padding) ? $settings->row_padding : '',
						'placeholder' => 'eg: 0px 0px 0px 0px'
					]
				]
			],
			'responsive' => [
				'title' => Text::_('HELIX_ULTIMATE_MEGA_ROW_SETTINGS_GROUP_RESPONSIVE'),
				'active' => false,
				'icon' => 'fas fa-laptop',
				'group_fields' => [
					'row_hide_phone' => [
						'type' => 'checkbox',
						'title' => Text::_('HELIX_ULTIMATE_MEGA_ROW_HIDE_PHONE'),
						'desc' => Text::_('HELIX_ULTIMATE_MEGA_ROW_HIDE_PHONE_DESC'),
						'menu-builder' => true,
						'data' => ['rowid' => $id, 'itemid' => $item->id],
						'value' => !empty($settings->row_hide_phone) ? $settings->row_hide_phone : '',
					],
					'row_hide_large_phone' => [
						'type' => 'checkbox',
						'title' => Text::_('HELIX_ULTIMATE_MEGA_ROW_HIDE_LARGE_PHONE'),
						'desc' => Text::_('HELIX_ULTIMATE_MEGA_ROW_HIDE_LARGE_PHONE_DESC'),
						'menu-builder' => true,
						'data' => ['rowid' => $id, 'itemid' => $item->id],
						'value' => !empty($settings->row_hide_large_phone) ? $settings->row_hide_large_phone : '',
					],
					'row_hide_tablet' => [
						'type' => 'checkbox',
						'title' => Text::_('HELIX_ULTIMATE_MEGA_ROW_HIDE_TABLET'),
						'desc' => Text::_('HELIX_ULTIMATE_MEGA_ROW_HIDE_TABLET_DESC'),
						'menu-builder' => true,
						'data' => ['rowid' => $id, 'itemid' => $item->id],
						'value' => !empty($settings->row_hide_tablet) ? $settings->row_hide_tablet : '',
					],
					'row_hide_small_desktop' => [
						'type' => 'checkbox',
						'title' => Text::_('HELIX_ULTIMATE_MEGA_ROW_HIDE_SMALL_DESKTOP'),
						'desc' => Text::_('HELIX_ULTIMATE_MEGA_ROW_HIDE_SMALL_DESKTOP_DESC'),
						'menu-builder' => true,
						'data' => ['rowid' => $id, 'itemid' => $item->id],
						'value' => !empty($settings->row_hide_small_desktop) ? $settings->row_hide_small_desktop : '',
					],
					'row_hide_desktop' => [
						'type' => 'checkbox',
						'title' => Text::_('HELIX_ULTIMATE_MEGA_ROW_HIDE_DESKTOP'),
						'desc' => Text::_('HELIX_ULTIMATE_MEGA_ROW_HIDE_DESKTOP_DESC'),
						'menu-builder' => true,
						'data' => ['rowid' => $id, 'itemid' => $item->id],
						'value' => !empty($settings->row_hide_desktop) ? $settings->row_hide_desktop : '',
					],
				]
			]
		];
	}
}

