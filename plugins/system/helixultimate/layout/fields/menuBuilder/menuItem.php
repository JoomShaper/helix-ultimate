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
	'custom_class' => [
		'type' => 'text',
		'title' => Text::_('HELIX_ULTIMATE_MENU_EXTRA_CLASS'),
		'placeholder' => Text::_('HELIX_ULTIMATE_MENU_EXTRA_CLASS_PLACEHOLDER'),
		'menu-builder' => true
	],
	'menu_icon' => [
		'type' => 'text',
		'title' => Text::_('HELIX_ULTIMATE_MENU_ICON'),
		'placeholder' => Text::_('HELIX_ULTIMATE_MENU_ICON_PLACEHOLDER'),
		'menu-builder' => true
	],
	'caption' => [
		'type' => 'text',
		'title' => Text::_('HELIX_ULTIMATE_MENU_CAPTION'),
		'placeholder' => Text::_('HELIX_ULTIMATE_MENU_CAPTION_PLACEHOLDER'),
		'menu-builder' => true
	]
];

?>
<div class="hu-menu-item-settings hu-menu-item-<?php echo $item->alias . ($active ? ' active' : ''); ?>">
	<div class="hu-menu-item-modifiers">
		<div class="row">
			<div class="col-4">
				<?php echo $builder->renderFieldElement('custom_class', $fields['custom_class']); ?>
			</div>
			<div class="col-4">
				<?php echo $builder->renderFieldElement('menu_icon', $fields['menu_icon']); ?>
			</div>
			<div class="col-4">
				<?php echo $builder->renderFieldElement('caption', $fields['caption']); ?>
			</div>
		</div>
	</div>
</div>
