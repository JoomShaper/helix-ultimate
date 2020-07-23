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

$grids = array(
	array(
		'12',
		'<svg xmlns="http://www.w3.org/2000/svg" width="51" height="17" fill="none"><rect width="50.78" height="16.927" fill-opacity=".3" rx="2"/></svg>'
	),
	array(
		'6+6',
		'<svg xmlns="http://www.w3.org/2000/svg" width="50" height="17" fill="none"><rect width="23.79" height="16.221" fill-opacity=".3" rx="2"/><rect width="23.79" height="16.221" fill-opacity=".7" rx="2"/><rect width="23.79" height="16.221" x="25.681" fill-opacity=".3" rx="2"/></svg>'
	),
	array(
		'4+4+4',
		'<svg xmlns="http://www.w3.org/2000/svg" width="50" height="17" fill="none"><rect width="15.139" height="16.221" fill-opacity=".3" rx="2"/><rect width="15.139" height="16.221" x="17.302" fill-opacity=".3" rx="2"/><rect width="15.139" height="16.221" x="17.302" fill-opacity=".7" rx="2"/><rect width="15.139" height="16.221" x="34.605" fill-opacity=".3" rx="2"/></svg>'
	),
	array(
		'3+3+3+3',
		'<svg xmlns="http://www.w3.org/2000/svg" width="51" height="17" fill="none"><rect width="10.814" height="16.221" fill-opacity=".3" rx="2"/><rect width="10.814" height="16.221" x="12.974" fill-opacity=".3" rx="2"/><rect width="10.814" height="16.221" x="12.974" fill-opacity=".7" rx="2"/><rect width="10.814" height="16.221" x="25.95" fill-opacity=".3" rx="2"/><rect width="11.354" height="16.221" x="38.929" fill-opacity=".3" rx="2"/><rect width="11.354" height="16.221" x="38.929" fill-opacity=".7" rx="2"/></svg>'
	),
	array(
		'4+8',
		'<svg xmlns="http://www.w3.org/2000/svg" width="50" height="17" fill="none"><rect width="15.139" height="16.221" fill-opacity=".3" rx="2"/><rect width="33" height="16" x="17" fill-opacity=".3" rx="2"/><rect width="33" height="16" x="17" fill-opacity=".7" rx="2"/></svg>'
	),
	array(
		'3+9',
		'<svg xmlns="http://www.w3.org/2000/svg" width="50" height="17" fill="none"><rect width="10.814" height="16.221" fill-opacity=".7" rx="2"/><rect width="37" height="16" x="13" fill-opacity=".3" rx="2"/></svg>'
	),
	array(
		'3+6+3',
		'<svg xmlns="http://www.w3.org/2000/svg" width="50" height="17" fill="none"><rect width="10.543" height="16.221" fill-opacity=".3" rx="2"/><rect width="11.084" height="16.221" x="38.659" fill-opacity=".3" rx="2"/><rect width="23.79" height="16.221" x="12.704" fill-opacity=".7" rx="2"/></svg>'
	),
	array(
		'2+6+4',
		'<svg xmlns="http://www.w3.org/2000/svg" width="51" height="17" fill="none"><rect width="6.488" height="16.221" x=".143" fill-opacity=".7" rx="2"/><rect width="23.79" height="16.221" x="9" fill-opacity=".3" rx="2"/><rect width="15.139" height="16.221" x="35" fill-opacity=".7" rx="2"/></svg>'
	),
	array(
		'2+10',
		'<svg xmlns="http://www.w3.org/2000/svg" width="50" height="17" fill="none"><rect width="6.488" height="16.221" x=".143" fill-opacity=".3" rx="2"/><rect width="41" height="16" x="9" fill-opacity=".7" rx="2"/></svg>'
	),
	array(
		'5+7',
		'<svg xmlns="http://www.w3.org/2000/svg" width="50" height="17" fill="none"><rect width="28.927" height="16.221" x="20.653" fill-opacity=".7" rx="2"/><rect width="18.654" height="16.221" fill-opacity=".3" rx="2"/></svg>'
	),
	array(
		'2+3+7',
		'<svg xmlns="http://www.w3.org/2000/svg" width="50" height="17" fill="none"><rect width="6.488" height="16.221" x=".143" fill-opacity=".7" rx="2"/><rect width="10" height="16.221" x="8.7" fill-opacity=".3" rx="2"/><rect width="28.927" height="16.221" x="20.653" fill-opacity=".7" rx="2"/></svg>'
	)
);

$layout = new FileLayout('fields.menuBuilder.row', HELIX_LAYOUT_PATH);

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

	<?php echo $layout->render(['item' => $item, 'active' => $active, 'params' => $params, 'builder' => $builder, 'reserve' => true]); ?>

	<div id="hu-megamenu-layout-container" class="<?php echo $active ? 'active-layout' : ''; ?>">
		<?php
			echo $layout->render(['item' => $item, 'active' => $active, 'params' => $params, 'builder' => $builder]);
		?>
	</div>
</div>
