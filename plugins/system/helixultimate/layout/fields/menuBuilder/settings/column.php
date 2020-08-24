<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use HelixUltimate\Framework\Platform\Helper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Uri\Uri;

extract($displayData);

$settingsId = 'hu-mega-row-' . $item->id . '-' . $id;
$positions = Helper::getTemplatePositions();
$modules = Helper::getModules();
$positionOptions = [];
$moduleOptions = [];

foreach ($positions as $position)
{
	$positionOptions[$position] = $position;
}

foreach ($modules as $module)
{
	$moduleOptions[$module->id] = $module->title;
}

$fields = [
	'col_label' => [
		'type' => 'text',
		'title' => Text::_('HELIX_ULTIMATE_MEGA_COL_LABEL'),
		'desc' => Text::_('HELIX_ULTIMATE_MEGA_COL_LABEL_DESC'),
		'menu-builder' => true,
		'data' => ['rowid' => $id, 'itemid' => $item->id, 'columnid' => $id],
		'value' => !empty($settings->col_label) ? $settings->col_label : '',
	],
	'col_type' => [
		'type' => 'select',
		'title' => Text::_('HELIX_ULTIMATE_MEGA_COL_TYPE'),
		'desc' => Text::_('HELIX_ULTIMATE_MEGA_COL_TYPE_DESC'),
		'menu-builder' => true,
		'data' => ['rowid' => $id, 'itemid' => $item->id, 'columnid' => $id],
		'options' => [
			'position' => Text::_('Module Position'),
			'module' => Text::_('Module'),
			'menu' => Text::_('Menu Item'),
		],
		'value' => !empty($settings->col_type) ? $settings->col_type : '',
	],
	'module_positions' => [
		'type' => 'select',
		'title' => Text::_('HELIX_ULTIMATE_MEGA_MODULE_POSITIONS'),
		'desc' => Text::_('HELIX_ULTIMATE_MEGA_MODULE_POSITIONS_DESC'),
		'menu-builder' => true,
		'data' => ['rowid' => $id, 'itemid' => $item->id, 'columnid' => $id],
		'options' => $positionOptions,
		'value' => !empty($settings->module_positions) ? $settings->module_positions : '',
	],
	// 'module' => [
	// 	'type' => 'select',
	// 	'title' => Text::_('HELIX_ULTIMATE_MEGA_MODULE'),
	// 	'desc' => Text::_('HELIX_ULTIMATE_MEGA_MODULE_DESC'),
	// 	'menu-builder' => true,
	// 	'data' => ['rowid' => $id, 'itemid' => $item->id, 'columnid' => $id],
	// 	'options' => $moduleOptions,
	// 	'value' => !empty($settings->module) ? $settings->module : '',
	// ],
	'module_style' => [
		'type' => 'select',
		'title' => Text::_('HELIX_ULTIMATE_MEGA_MODULE_STYLE'),
		'desc' => Text::_('HELIX_ULTIMATE_MEGA_MODULE_STYLE_DESC'),
		'menu-builder' => true,
		'data' => ['rowid' => $id, 'itemid' => $item->id, 'columnid' => $id],
		'options' => [
			'sp_xhtml' => Text::_('sp_xhtml'),
			'default' => Text::_('Default'),
			'none' => Text::_('None'),
		],
		'value' => !empty($settings->module_style) ? $settings->module_style : '',
	],
	'menu_items' => [
		'type' => 'menuHierarchy',
		'title' => 'Menu Items',
		'itemid' => $item->id
	],
];
?>

<div class="hu-mega-column-setting hidden"
	data-itemid="<?php echo $item->id; ?>"
	data-rowid="<?php echo $rowId; ?>"
	data-columnid="<?php echo $id; ?>"
	>
	<div class="hu-option-group-list">
		<?php foreach ($fields as $key => $field): ?>
			<?php echo $builder->renderFieldElement($key, $field); ?>
		<?php endforeach ?>
	</div>
</div>
