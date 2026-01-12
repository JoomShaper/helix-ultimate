<?php

/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */


defined ('_JEXEC') or die('Restricted Access');

use HelixUltimate\Framework\Platform\Helper;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Language\Text;

$data               = $displayData;
$offcanvas_position = $data->params->get('offcanvas_position', 'right');
$menu_type          = $data->params->get('menu_type');

$feature_folder_path = JPATH_THEMES . '/' . $data->template->template . '/features';
include_once $feature_folder_path . '/logo.php';
include_once $feature_folder_path . '/menu.php';
include_once $feature_folder_path . '/social.php';
include_once $feature_folder_path . '/contact.php';

/**
 * Helper classes for-
 * site logo, Menu header.
 *
 */
$logo    	= new HelixUltimateFeatureLogo($data->params);
$menu    	= new HelixUltimateFeatureMenu($data->params);
$social 	= new HelixUltimateFeatureSocial($data->params);
$contact 	= new HelixUltimateFeatureContact($data->params);

/**
 * Get related modules
 * The modules are mod_search
 */
$searchModule   = Helper::getSearchModule('-header');
$visibilityClass = ($menu_type === 'mega') ? 'd-flex d-lg-none' : 'd-flex';
$sideClass      = ($offcanvas_position === 'left') ? 'offcanvas-toggler-left' : 'offcanvas-toggler-right';
$togglerHtml    = '
  <a id="offcanvas-toggler"
     class="offcanvas-toggler-secondary ' . $sideClass . ' ' . $visibilityClass . ' align-items-center"
     href="#"
     aria-label="' . Text::_('HELIX_ULTIMATE_NAVIGATION') . '"
     title="' . Text::_('HELIX_ULTIMATE_NAVIGATION') . '">
     <div class="burger-icon" aria-hidden="true"><span></span><span></span><span></span></div>
  </a>';
?>
<?php if ($data->params->get('sticky_header')): ?>
	<div class="sticky-header-placeholder"></div>
<?php endif; ?>

<div id="sp-header-topbar">
	<div class="container">
		<div class="container-inner">
		<div class="row align-items-center">
					<!-- Contact -->
					<div id="sp-contact" class="col-6 col-xl-5">
						<?php if ($data->params->get('social_position') === 'top1'): ?>
							<?php echo $social->renderFeature(); ?>
						<?php endif ?>

						<?php if ($data->params->get('contact_position') === 'top1'): ?>
							<?php echo $contact->renderFeature(); ?>
						<?php endif ?>
					</div>
	
					<!-- Logo -->
					<div id="sp-logo" class="col-12 col-xl-2 d-none d-xl-block">
						<div class="sp-column d-flex align-items-center  justify-content-center">
							<?php if (isset($logo->load_pos) && $logo->load_pos === 'before') : ?>
								<?php echo $logo->renderFeature(); ?>
								<jdoc:include type="modules" name="logo" style="sp_xhtml" />
							<?php else : ?>
								<jdoc:include type="modules" name="logo" style="sp_xhtml" />
								<?php echo $logo->renderFeature(); ?>
							<?php endif ?>
						</div>
					</div>

					<!-- Social -->
					<div id="sp-social" class="col-6 col-xl-5">
						<div class="sp-column d-flex justify-content-end">
							<!-- Social icons -->
							<div class="social-wrap d-flex align-items-center">
								<?php if ($data->params->get('social_position') === 'top2'): ?>
									<?php echo $social->renderFeature(); ?>
								<?php endif ?>

								<?php if ($data->params->get('contact_position') === 'top2'): ?>
									<?php echo $contact->renderFeature(); ?>
								<?php endif ?>
							</div>

							<!-- Related Modules -->
							<div class="d-none d-lg-flex header-modules align-items-center">
								<?php if ($data->params->get('enable_search', 0)): ?>
									<?php echo ModuleHelper::renderModule($searchModule, ['style' => 'sp_xhtml']); ?>
								<?php endif ?>

								<?php if ($data->params->get('enable_login', 0)): ?>
									<?php echo $menu->renderLogin(); ?>
								<?php endif ?>
							</div>
						</div>
					</div>
				</div>
		</div>
	</div>
</div>

<header id="sp-header" class="lg-header">
	<div class="container">
		<div class="container-inner">
			<!-- Menu -->
			<div class="row">
				<div class="col-lg-3 col-6 d-block d-xl-none">
					<div class="sp-column d-flex justify-content-between align-items-center">
						<div id="sp-logo" class="menu-with-offcanvas">
							<jdoc:include type="modules" name="logo" style="sp_xhtml" />
							<?php echo $logo->renderFeature(); ?>
						</div>
					</div>
				</div>

				<div class="col-lg-9 col-6 col-xl-12">
					<div class="d-flex justify-content-end justify-content-xl-center align-items-center">
						<!-- Left toggler  -->
						<?php if ($offcanvas_position === 'left'): ?>
							<?php echo $togglerHtml; ?>
						<?php endif; ?>

						<?php echo $menu->renderFeature(); ?>
						<div class="menu-with-offcanvas">
							<jdoc:include type="modules" name="menu" style="sp_xhtml" />
						</div>

						<!-- Right toggler  -->
						<?php if ($offcanvas_position === 'right' ): ?>
							<div class="ms-3 ps-2">
								<?php echo $togglerHtml; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</header>