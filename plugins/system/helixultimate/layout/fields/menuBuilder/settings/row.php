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
		'title' => Text::_('General'),
		'active' => true,
		'group_fields' => [
			'row_label' => [
				'type' => 'text',
				'title' => Text::_('Label'),
				'desc' => Text::_('Add an identical label for the row'),
				'menu-builder' => true,
				'itemId' => $item->id,
				'value' => ''
			],
			'enable_row_title' => [
				'type' => 'checkbox',
				'title' => Text::_('Show Title'),
				'desc' => Text::_('Enable row title for showing'),
				'menu-builder' => true,
				'itemId' => $item->id,
				'value' => 1
			],
			'row_title' => [
				'type' => 'text',
				'title' => Text::_('Title'),
				'desc' => Text::_('Title of the row.'),
				'menu-builder' => true,
				'itemId' => $item->id,
				'value' => '',
				'depend' => 'enable_row_title:1'
			],
			'row_id' => [
				'type' => 'text',
				'title' => Text::_('Row ID'),
				'desc' => Text::_('Add an ID for the row.'),
				'menu-builder' => true,
				'itemId' => $item->id,
				'value' => '',
			],
			'row_class' => [
				'type' => 'text',
				'title' => Text::_('Row CSS Class'),
				'desc' => Text::_('Add an class for the row.'),
				'menu-builder' => true,
				'itemId' => $item->id,
				'value' => '',
			]
		]
	],
	'styles' => [
		'title' => Text::_('Styles'),
		'active' => false,
		'group_fields' => [
			'row_margin' => [
				'type' => 'text',
				'title' => Text::_('Margin'),
				'desc' => Text::_('Margin of the row.'),
				'menu-builder' => true,
				'itemId' => $item->id,
				'value' => '',
			],
			'row_padding' => [
				'type' => 'text',
				'title' => Text::_('Padding'),
				'desc' => Text::_('Padding of the row.'),
				'menu-builder' => true,
				'itemId' => $item->id,
				'value' => '',
			]
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
