<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined ('_JEXEC') or die('Restricted Access');

use HelixUltimate\Framework\Platform\Helper;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$data = $displayData;
$offcanvas_position = $displayData->params->get('offcanvas_position', 'right');
$menu_type = $displayData->params->get('menu_type');

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
$social    	= new HelixUltimateFeatureSocial($data->params);
$contact    	= new HelixUltimateFeatureContact($data->params);

/**
 * Get related modules
 * The modules are mod_search
 */
$searchModule = Helper::getSearchModule();

?>

<?php if( $displayData->params->get('sticky_header')) { ?>
	<div class="sticky-header-placeholder"></div>
<?php } ?>
<header id="sp-header" class="header-with-modal-menu center-layout">
	<div class="container">
		<div class="container-inner">
			<div class="row align-items-center justify-content-between">
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
				
				<!-- Menu Right position -->
				<div id="logo-right" class="col-auto d-flex align-items-center">
					<?php echo $contact->renderFeature(); ?>

					<!-- Related Modules -->
					<div class="d-none d-lg-flex align-items-center header-modules">
						<?php if ($data->params->get('enable_search', 0)): ?>
							<?php echo ModuleHelper::renderModule($searchModule, ['style' => 'sp_xhtml']); ?>
						<?php endif ?>

						<?php if ($data->params->get('enable_login', 0)): ?>
							<?php echo $menu->renderLogin(); ?>
						<?php endif ?>
					</div>
					
					<jdoc:include type="modules" name="menu" style="sp_xhtml" />
					
					<!-- if offcanvas position right -->
					<?php if($offcanvas_position === 'right') : ?>
						<a id="offcanvas-toggler"  aria-label="<?php echo Text::_('HELIX_ULTIMATE_NAVIGATION'); ?>" title="<?php echo Text::_('HELIX_ULTIMATE_NAVIGATION'); ?>"  class="<?php echo $menu_type; ?> ms-3 offcanvas-toggler-secondary offcanvas-toggler-right d-flex align-items-center" href="#"><div class="burger-icon"><span></span><span></span><span></span></div></a>
					<?php endif; ?>		

					<!-- Modal menu toggler -->
					<a id="modal-menu-toggler" aria-label="<?php echo Text::_('HELIX_ULTIMATE_NAVIGATION'); ?>" title="<?php echo Text::_('HELIX_ULTIMATE_NAVIGATION'); ?>" class="ms-3" href="#">
						<div class="burger-icon">
							<span></span>
							<span></span>
							<span></span>
						</div>
					</a>

					<!-- Modal menu -->
					<div id="modal-menu" class="modal-menu">
						<div class="modal-menu-inner">
							<div class="container">
								<div class="row">
									<div class="col-sm-12">
										<div class="modules-wrapper header-modules">
											<?php echo ModuleHelper::renderModule($searchModule, ['style' => 'sp_xhtml']); ?>
											<jdoc:include type="modules" name="menu-modal" style="sp_xhtml" />
										</div>
										
										<?php echo $menu->renderFeature(); ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</header>