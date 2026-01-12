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

$data = $displayData;
$offcanvas_position = $displayData->params->get('offcanvas_position', 'right');
$menu_type = $displayData->params->get('menu_type');

$feature_folder_path = JPATH_THEMES . '/' . $data->template->template . '/features';

include_once $feature_folder_path . '/logo.php';
include_once $feature_folder_path . '/menu.php';
include_once $feature_folder_path . '/social.php';

/**
 * Helper classes for-
 * site logo, Menu header.
 *
 */
$logo    	= new HelixUltimateFeatureLogo($data->params);
$menu    	= new HelixUltimateFeatureMenu($data->params);
$social 	= new HelixUltimateFeatureSocial($data->params);


/**
 * Get related modules
 * The modules are mod_search
 */
$searchModule    = Helper::getSearchModule('-header');
$visibilityClass = ($menu_type === 'mega') ? 'd-flex d-lg-none' : 'd-flex';
$sideClass       = ($offcanvas_position === 'left') ? 'offcanvas-toggler-left' : 'offcanvas-toggler-right';
$togglerHtml     = '
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
<header id="sp-header" class="header-with-social">
	<div class="container">
		<div class="container-inner">
			<div class="row align-items-center">

				<!-- Left toggler  -->
				<?php if ($offcanvas_position === 'left' ): ?>
					<div class="col-auto d-flex align-items-center">
						<?php echo $togglerHtml; ?>
					</div>
				<?php endif; ?>

				<!-- Logo -->
				<div id="sp-logo" class="has-border col-auto">
					<div class="sp-column">
						<?php if (isset($logo->load_pos) && $logo->load_pos === 'before') : ?>
							<?php echo $logo->renderFeature(); ?>
							<jdoc:include type="modules" name="logo" style="sp_xhtml" />
						<?php else : ?>
							<jdoc:include type="modules" name="logo" style="sp_xhtml" />
							<?php echo $logo->renderFeature(); ?>
						<?php endif ?>
					</div>
				</div>

				<!-- Menu -->
				<div id="sp-menu" class="menu-with-social col-auto flex-auto">
					<div class="sp-column d-flex justify-content-between align-items-center">
						<div class="d-flex menu-wrap menu-with-offcanvas justify-content-between align-items-center flex-auto">
							<?php echo $menu->renderFeature(); ?>
							<jdoc:include type="modules" name="menu" style="sp_xhtml" />
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

						<!-- Social icons -->
						<div class="social-wrap d-flex align-items-center">
							<?php echo $social->renderFeature(); ?>
						</div>

						<!-- Right toggler) -->
						<?php if ($offcanvas_position === 'right' ): ?>
							<?php echo $togglerHtml; ?>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</header>