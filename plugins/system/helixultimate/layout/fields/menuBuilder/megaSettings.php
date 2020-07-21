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
use Joomla\CMS\Uri\Uri;

extract($displayData);

$fields = [
	'mega-width' => [
		'type' => 'text',
		'title' => Text::_('HELIX_ULTIMATE_MEGA_MENU_WIDTH'),
		'menu-builder' => true
	],
	'mega-custom-classes' => [
		'type' => 'text',
		'title' => Text::_('HELIX_ULTIMATE_MEGA_MENU_CUSTOM_CLASSES'),
		'menu-builder' => true
	],
	'mega-alignment' => [
		'type' => 'alignment',
		'title' => 'Alignments',
		'desc' => 'Set mega menu alignment.',
		'default' => 'left'
	]
];

?>

<div class="hu-mega-basic-settings">
	<div class="row">
		<div class="col-6">
			<?php echo $builder->renderFieldElement('mega-width', $fields['mega-width']); ?>
		</div>
		<div class="col-6">
			<?php echo $builder->renderFieldElement('mega-custom-classes', $fields['mega-custom-classes']); ?>
		</div>
	</div>
	<div class="row">
		<div class="col-6">
			<?php echo $builder->renderFieldElement('mega-alignment', $fields['mega-alignment']); ?>
		</div>
	</div>

	<div class="hu-mega-layout">
		<div class="hu-mega-row-container">
			<div class="hu-mega-row-settings">
				<span class="setting-icon fas fa-arrows-alt"></span>
				<span class="setting-icon fas fa-cog"></span>
				<span class="setting-icon fas fa-trash"></span>
			</div>
			<div class="hu-mega-row">
				<div class="hu-mega-col">
					<span class="col-content"><?php echo Text::_('none'); ?></span>
					<span class="col-setting-icon fas fa-cog"></span>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-12 text-center">
			<button type="button" role="button" class="hu-btn hu-btn-primary">
				<span class="fas fa-plus"></span>
				Add Row
			</button>
		</div>
	</div>
</div>
