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

$data = $displayData;
$offcanvas_position = $displayData->params->get('offcanvas_position', 'right');
$menu_type = $displayData->params->get('menu_type');

$feature_folder_path = JPATH_THEMES . '/' . $data->template->template . '/features';

include_once $feature_folder_path . '/social.php';
include_once $feature_folder_path . '/contact.php';
include_once $feature_folder_path . '/logo.php';
include_once $feature_folder_path . '/menu.php';

/**
 * Helper classes for-
 * social icons, contact info, site logo, Menu header.
 *
 */
$social 	= new HelixUltimateFeatureSocial($data->params);
$contact 	= new HelixUltimateFeatureContact($data->params);
$logo    	= new HelixUltimateFeatureLogo($data->params);
$menu    	= new HelixUltimateFeatureMenu($data->params);

/** Logo and menu html classes */
$logoClass = 'col-auto';
$menuClass = 'col-auto flex-auto';

/**
 * Get related modules
 * The modules are mod_search
 */
$searchModule = Helper::getSearchModule();
?>

<?php if($displayData->params->get('sticky_header')) { ?>
	<div class="sticky-header-placeholder"></div>
<?php } ?>
<div id="sp-top-bar">
	<div class="container">
		<div class="container-inner">
			<div class="row">
				<div id="sp-top1" class="col-lg-6">
					<div class="sp-column text-center text-lg-start">
						<?php if ($displayData->params->get('social_position') === 'top1'): ?>
							<?php echo $social->renderFeature(); ?>
						<?php endif ?>

						<?php if ($displayData->params->get('contact_position') === 'top1'): ?>
							<?php echo $contact->renderFeature(); ?>
						<?php endif ?>
						<jdoc:include type="modules" name="top1" style="sp_xhtml"/>
					</div>
				</div>

				<div id="sp-top2" class="col-lg-6">
					<div class="sp-column text-center text-lg-end">
						<?php if ($displayData->params->get('social_position') === 'top2'): ?>
							<?php echo $social->renderFeature(); ?>
						<?php endif ?>

						<?php if ($displayData->params->get('contact_position') === 'top2'): ?>
							<?php echo $contact->renderFeature(); ?>
						<?php endif ?>
						<jdoc:include type="modules" name="top2" style="sp_xhtml" />
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<header id="sp-header">
	<div class="container">
		<div class="container-inner">
			<div class="row">
				<!-- Logo -->
				<div id="sp-logo" class="<?php echo $logoClass; ?>">
					<div class="sp-column">
						<?php echo $logo->renderFeature(); ?>
						<jdoc:include type="modules" name="logo" style="sp_xhtml" />
					</div>
				</div>

				<!-- Menu -->
				<div id="sp-menu" class="<?php echo $menuClass; ?>">
					<div class="sp-column d-flex justify-content-end align-items-center">
						<?php echo $menu->renderFeature(); ?>
						<jdoc:include type="modules" name="menu" style="sp_xhtml" />

						<!-- Related Modules -->
						<div class="d-none d-lg-flex header-modules align-items-center">
							<?php if ($data->params->get('enable_search', 0)): ?>
								<?php echo ModuleHelper::renderModule($searchModule, ['style' => 'sp_xhtml']); ?>
							<?php endif ?>

							<?php if ($data->params->get('enable_login', 0)): ?>
								<?php echo $menu->renderLogin(); ?>
							<?php endif ?>
						</div>

						<!-- if offcanvas position right -->
						<?php if($offcanvas_position === 'right') : ?>
							<a id="offcanvas-toggler"  aria-label="<?php echo Text::_('HELIX_ULTIMATE_NAVIGATION'); ?>" title="<?php echo Text::_('HELIX_ULTIMATE_NAVIGATION'); ?>"  class="<?php echo $menu_type; ?> offcanvas-toggler-secondary offcanvas-toggler-right d-flex align-items-center" href="#">
							<div class="burger-icon" aria-hidden="true"><span></span><span></span><span></span></div>
							</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</header>
