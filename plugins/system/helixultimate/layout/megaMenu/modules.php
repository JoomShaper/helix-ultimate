<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use HelixUltimate\Framework\Platform\Helper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Uri\Uri;

extract($displayData);

$modules = Helper::getModules($keyword);
$children = $children  ?? [];
?>

<div class="hu-switcher hu-switcher-inline hu-switcher-style-tab hu-switcher-style-tab-sm" style="margin-bottom: 1rem;">
	<div class="hu-action-group">
		<span id="toggle-module-btn" data-value="module" class="hu-switcher-action active" role="button" onclick="toggleMegaMenuView('module')">
			<?php echo Text::_('HELIX_ULTIMATE_MODULES'); ?>
		</span>
		<span id="toggle-menu-btn" data-value="menu" class="hu-switcher-action" role="button" onclick="toggleMegaMenuView('menu')">
			<?php echo Text::_('HELIX_ULTIMATE_MENU_ITEMS'); ?>
		</span>
	</div>
</div>

<div id="hu-megamenu-module-section" style="display: block;">
	<?php if (!empty($modules)): ?>
		<div class="row">
			<?php foreach ($modules as $module): ?>
				<div class="col-4 hu-megamenu-column">
					<div class="hu-megamenu-module-item">
						<strong class="hu-megamenu-module-title"><?php echo $module->title; ?></strong>
						<p class="hu-megamenu-module-desc"><?php echo (strlen($module->desc) > 80 ? substr($module->desc, 0, 80) . '...' : $module->desc); ?></p>
						<button type="button" role="button" class="hu-btn hu-btn-default hu-megamenu-insert-module" data-module="<?php echo $module->id; ?>"><?php echo Text::_('HELIX_ULTIMATE_MODULE_INSERT'); ?></button>
					</div>
				</div>
			<?php endforeach ?>
		</div>
	<?php else: ?>
		<div class="hu-megamenu-module-not-found">
			<h4><?php echo Text::_('HELIX_ULTIMATE_NOTHING_FOUND'); ?></h4>
		</div>
	<?php endif ?>
</div>

<div id="hu-megamenu-menu-section" style="display: none;">
	<?php if (!empty($children)): ?>
		<div class="row">
			<?php foreach ($children as $child): ?>
				<div class="col-4 hu-megamenu-column">
					<div class="hu-megamenu-module-item">
						<strong class="hu-megamenu-module-title"><?php echo htmlspecialchars($child->title, ENT_QUOTES, 'UTF-8'); ?></strong>
						<p class="hu-megamenu-module-desc"><?php echo !empty($child->desc) ? (strlen($child->desc) > 80 ? substr($child->desc, 0, 80) . '...' : $child->desc) : ''; ?></p>
						<button type="button" role="button" class="hu-btn hu-btn-default hu-megamenu-insert-menu" data-child="<?php echo $child->id; ?>">
							<?php echo Text::_('HELIX_ULTIMATE_MENU_INSERT'); ?>
						</button>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	<?php else: ?>
		<div class="hu-megamenu-module-not-found">
			<h4><?php echo Text::_('HELIX_ULTIMATE_NOTHING_FOUND'); ?></h4>
		</div>
	<?php endif; ?>
</div>

<script>
function toggleMegaMenuView(type) {
	const menuSection = document.getElementById('hu-megamenu-menu-section');
	const moduleSection = document.getElementById('hu-megamenu-module-section');
	const menuBtn = document.getElementById('toggle-menu-btn');
	const moduleBtn = document.getElementById('toggle-module-btn');

	if (type === 'module') {
		moduleSection.style.display = 'block';
		menuSection.style.display = 'none';
		moduleBtn.classList.add('active');
		menuBtn.classList.remove('active');
	} else {
		moduleSection.style.display = 'none';
		menuSection.style.display = 'block';
		moduleBtn.classList.remove('active');
		menuBtn.classList.add('active');
	}
}
</script>
