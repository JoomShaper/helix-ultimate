<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined ('_JEXEC') or die('Restricted Access');

use HelixUltimate\Framework\Platform\Helper;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Language\Text;

$params = $displayData->params;
$template = $displayData->template->template;

include_once JPATH_THEMES . '/' . $template . '/features/menu.php';
include_once JPATH_THEMES . '/' . $template . '/features/social.php';
include_once JPATH_THEMES . '/' . $template . '/features/contact.php';
include_once JPATH_THEMES . '/' . $template . '/features/logo.php';

$menu = new HelixUltimateFeatureMenu($params);
$social = new HelixUltimateFeatureSocial($params);
$contact = new HelixUltimateFeatureContact($params);
$logo = new HelixUltimateFeatureLogo($params);

$hasModMenu = array_search('mod_menu', array_column(ModuleHelper::getModules('offcanvas'), 'module'));

$menuType = $params->get('offcanvas_menu', 'mainmenu', 'STRING');
$maxLevel = $params->get('offcanvas_max_level', 0, 'INT');

$menuModule = Helper::createModule('mod_menu', [
	'title' => 'Main Menu',
	'params' => '{"menutype":"' . $menuType . '","base":"","startLevel":1,"endLevel":' . $maxLevel . ',"showAllChildren":1,"tag_id":"","class_sfx":" nav-pills","window_open":"","layout":"_:default","moduleclass_sfx":"","cache":"1","cache_time":"900","cachemode":"itemid","module_tag":"div","bootstrap_size":"0","header_tag":"h3","header_class":"","style":"0", "hu_offcanvas": 1}',
	'name' => 'menu'
]);

$searchModule = Helper::getSearchModule();

?>
<div class="offcanvas-menu border-menu">
	<div class="d-flex align-items-center p-3 pt-4">
		<?php echo $logo->renderFeature(); ?>
		<a href="#" class="close-offcanvas" aria-label="<?php echo Text::_('HELIX_ULTIMATE_CLOSE_OFFCANVAS_ARIA_LABEL'); ?>">
			<div class="burger-icon">
				<span></span>
				<span></span>
				<span></span>
			</div>
		</a>
	</div>
	<div class="offcanvas-inner">
		<div class="d-flex header-modules mb-3">
			<?php if ($params->get('offcanvas_enable_search', 0)): ?>
				<?php echo ModuleHelper::renderModule($searchModule, ['style' => 'sp_xhtml']); ?>
			<?php endif ?>

			<?php if ($params->get('offcanvas_enable_login', 0)): ?>
				<?php echo $menu->renderLogin(); ?>
			<?php endif ?>
		</div>
		
		<?php if ($hasModMenu === false): ?>
			<?php echo ModuleHelper::renderModule($menuModule, ['style' => 'sp_xhtml']); ?>
		<?php else: ?>
			<jdoc:include type="modules" name="offcanvas" style="sp_xhtml" />
		<?php endif ?>

		
		<?php if ($params->get('offcanvas_enable_contact')): ?>
			<div class="mb-4">
				<?php echo $contact->renderFeature(); ?>
			</div>
		<?php endif ?>

		<?php if ($params->get('offcanvas_enable_social')): ?>
			<?php echo $social->renderFeature(); ?>
		<?php endif ?>
		
		<?php if ($hasModMenu === false): ?>
			<jdoc:include type="modules" name="offcanvas" style="sp_xhtml" />
		<?php endif ?>

		<!-- custom module position -->
		<jdoc:include type="modules" name="offcanvas-modules" style="sp_xhtml" />
	</div>
</div>