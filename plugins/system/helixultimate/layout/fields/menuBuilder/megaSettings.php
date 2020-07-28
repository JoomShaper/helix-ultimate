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

$fields = [
	'mega_width' => [
		'type' => 'text',
		'title' => Text::_('HELIX_ULTIMATE_MEGA_MENU_WIDTH'),
		'menu-builder' => true,
		'itemId' => $item->id
	],
	'mega_custom_classes' => [
		'type' => 'text',
		'title' => Text::_('HELIX_ULTIMATE_MEGA_MENU_CUSTOM_CLASSES'),
		'menu-builder' => true,
		'itemId' => $item->id
	],
	'mega_alignment' => [
		'type' => 'alignment',
		'title' => 'Alignments',
		'desc' => 'Set mega menu alignment.',
		'default' => 'left',
		'itemId' => $item->id
	]
];

$layout = new FileLayout('fields.menuBuilder.row', HELIX_LAYOUT_PATH);

?>

<div class="hu-mega-basic-settings">
	<div class="row">
		<div class="col-6">
			<?php echo $builder->renderFieldElement('mega_width', $fields['mega_width']); ?>
		</div>
		<div class="col-6">
			<?php echo $builder->renderFieldElement('mega_custom_classes', $fields['mega_custom_classes']); ?>
		</div>
	</div>
	<div class="row">
		<div class="col-6">
			<?php echo $builder->renderFieldElement('mega_alignment', $fields['mega_alignment']); ?>
		</div>
	</div>

	<div id="hu-megamenu-layout-container" class="<?php echo $active ? 'active-layout' : ''; ?>">
		<?php echo $layout->render(['item' => $item, 'active' => $active, 'params' => $params, 'builder' => $builder, 'reserve' => true]); ?>
		<?php
			echo $layout->render(['item' => $item, 'active' => $active, 'params' => $params, 'builder' => $builder]);
		?>
	</div>
</div>
