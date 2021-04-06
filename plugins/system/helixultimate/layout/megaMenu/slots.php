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
use Joomla\CMS\Uri\Uri;

extract($displayData);

$grids = array(
	array(
		'grid' => '12',
		'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="51" height="17" fill="none"><rect width="50.78" height="16.927" fill-opacity=".3" rx="2"/></svg>'
	),
	array(
		'grid' => '6+6',
		'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="50" height="17" fill="none"><rect width="23.79" height="16.221" fill-opacity=".3" rx="2"/><rect width="23.79" height="16.221" fill-opacity=".7" rx="2"/><rect width="23.79" height="16.221" x="25.681" fill-opacity=".3" rx="2"/></svg>'
	),
	array(
		'grid' => '4+4+4',
		'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="50" height="17" fill="none"><rect width="15.139" height="16.221" fill-opacity=".3" rx="2"/><rect width="15.139" height="16.221" x="17.302" fill-opacity=".3" rx="2"/><rect width="15.139" height="16.221" x="17.302" fill-opacity=".7" rx="2"/><rect width="15.139" height="16.221" x="34.605" fill-opacity=".3" rx="2"/></svg>'
	),
	array(
		'grid' => '3+3+3+3',
		'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="51" height="17" fill="none"><rect width="10.814" height="16.221" fill-opacity=".3" rx="2"/><rect width="10.814" height="16.221" x="12.974" fill-opacity=".3" rx="2"/><rect width="10.814" height="16.221" x="12.974" fill-opacity=".7" rx="2"/><rect width="10.814" height="16.221" x="25.95" fill-opacity=".3" rx="2"/><rect width="11.354" height="16.221" x="38.929" fill-opacity=".3" rx="2"/><rect width="11.354" height="16.221" x="38.929" fill-opacity=".7" rx="2"/></svg>'
	),
	array(
		'grid' => '4+8',
		'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="50" height="17" fill="none"><rect width="15.139" height="16.221" fill-opacity=".3" rx="2"/><rect width="33" height="16" x="17" fill-opacity=".3" rx="2"/><rect width="33" height="16" x="17" fill-opacity=".7" rx="2"/></svg>'
	),
	array(
		'grid' => '3+9',
		'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="50" height="17" fill="none"><rect width="10.814" height="16.221" fill-opacity=".7" rx="2"/><rect width="37" height="16" x="13" fill-opacity=".3" rx="2"/></svg>'
	),
	array(
		'grid' => '3+6+3',
		'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="50" height="17" fill="none"><rect width="10.543" height="16.221" fill-opacity=".3" rx="2"/><rect width="11.084" height="16.221" x="38.659" fill-opacity=".3" rx="2"/><rect width="23.79" height="16.221" x="12.704" fill-opacity=".7" rx="2"/></svg>'
	),
	array(
		'grid' => '2+6+4',
		'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="51" height="17" fill="none"><rect width="6.488" height="16.221" x=".143" fill-opacity=".7" rx="2"/><rect width="23.79" height="16.221" x="9" fill-opacity=".3" rx="2"/><rect width="15.139" height="16.221" x="35" fill-opacity=".7" rx="2"/></svg>'
	),
	array(
		'grid' => '2+10',
		'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="50" height="17" fill="none"><rect width="6.488" height="16.221" x=".143" fill-opacity=".3" rx="2"/><rect width="41" height="16" x="9" fill-opacity=".7" rx="2"/></svg>'
	),
	array(
		'grid' => '5+7',
		'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="50" height="17" fill="none"><rect width="28.927" height="16.221" x="20.653" fill-opacity=".7" rx="2"/><rect width="18.654" height="16.221" fill-opacity=".3" rx="2"/></svg>'
	),
	array(
		'grid' => '2+3+7',
		'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="50" height="17" fill="none"><rect width="6.488" height="16.221" x=".143" fill-opacity=".7" rx="2"/><rect width="10" height="16.221" x="8.7" fill-opacity=".3" rx="2"/><rect width="28.927" height="16.221" x="20.653" fill-opacity=".7" rx="2"/></svg>'
	)
);

?>
<div class="hu-megamenu-columns-layout">
	<div class="row">
		<?php foreach ($grids as $key => $grid): ?>
			<div class="col-3">
				<a href="#" class="hu-megamenu-column-layout" data-layout="<?php echo $grid['grid']; ?>">
					<div class="hu-megamenu-column-layout-preview">
						<?php echo $grid['icon']; ?>
					</div>
					<span class="hu-megamenu-column-layout-name"><?php echo $grid['grid']; ?></span>
				</a>
			</div>
		<?php endforeach ?>

		<div class="col-3">
			<a href="#" class="hu-megamenu-column-layout hu-megamenu-custom" data-layout="custom">
				<div class="hu-megamenu-column-layout-preview"><?php echo Text::_('HELIX_ULTIMATE_CUSTOM_LAYOUT_TEXT'); ?></div>
				<span class="hu-megamenu-column-layout-name hu-sr-only"><?php echo Text::_('HELIX_ULTIMATE_CUSTOM_LAYOUT_TEXT'); ?></span>
			</a>
		</div>
	</div>
	<div class="hu-megamenu-custom-layout">
		<label><?php echo Text::_('HELIX_ULTIMATE_CUSTOM_LAYOUT_LABEL'); ?></label>
		<div class="hu-d-flex hu-justify-content-between">
			<input type="text" class="hu-megamenu-custom-layout-field" value="6+3+3">
			<button class="hu-btn hu-btn-primary hu-megamenu-custom-layout-apply">
				<?php echo Text::_('HELIX_ULTIMATE_MEGAMENU_APPLY_TEXT'); ?>
			</button>
		</div>
	</div>
</div>
