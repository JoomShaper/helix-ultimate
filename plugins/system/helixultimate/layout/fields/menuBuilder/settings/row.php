<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Uri\Uri;

extract($displayData);

$settingsId = 'hu-mega-row-' . $item->id . '-' . $id;

$fields = [
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


?>

<div class="hu-mega-row-settings hidden"
	id="<?php echo $settingsId; ?>"
	data-itemid="<?php echo $item->id; ?>"
	data-rowid="<?php echo $id; ?>"
>
	<?php foreach ($fields as $name => $group): ?>
		<?php if (!empty($group)): ?>
			<div class="hu-option-group hu-option-group-<?php echo strtolower($name) . ' ' . ($group['active'] ? 'active' : ''); ?>" >
				<div class="hu-option-group-title">
					<span class="fas fa-angle-right"></span>
					<?php if (!empty($group['icon'])): ?>
						<span class="<?php echo $group['icon']; ?>"></span>
					<?php endif ?>
					<?php echo $group['title']; ?>
				</div>

				<div class="hu-option-group-list">
					<?php foreach ($group['group_fields'] as $key => $field): ?>
						<?php echo $builder->renderFieldElement($key, $field); ?>
					<?php endforeach ?>	
				</div>
			</div>
		<?php endif ?>
	<?php endforeach ?>
</div>
