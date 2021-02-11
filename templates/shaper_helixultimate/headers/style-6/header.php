<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2020 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined ('_JEXEC') or die('Restricted Access');

$data = $displayData;
$offcanvas_position = $displayData->params->get('offcanvas_position', 'right');

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
 * Logo and menu html classes
 *
 */
?>

<header id="sp-header" class="lg-header">
	<div class="container">
		<div class="container-inner">
			<div class="top-part">
				<div class="row align-items-center">
					<!-- Contact -->
					<div id="sp-contact" class="col-12 col-sm-4">
					<?php echo $contact->renderFeature(); ?>
					</div>
	
					<!-- Show logo on header -->
					<div id="sp-logo" class="col-12 col-sm-4">
						<div class="sp-column d-flex align-items-center  justify-content-center">
						<a id="offcanvas-toggler" aria-label="' . JText::_('HELIX_ULTIMATE_NAVIGATION') . '" class="offcanvas-toggler-right d-block d-lg-none mr-3" href="#"><i class="fas fa-bars" aria-hidden="true" title="' . JText::_('HELIX_ULTIMATE_NAVIGATION') . '"></i></a>
						
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
					<div id="sp-social" class="col-12 col-sm-4">
						<div class="sp-column d-flex justify-content-end">
							<!-- Social icons -->
							<div class="social-wrap d-flex align-items-center">
								<?php echo $social->renderFeature(); ?>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Menu -->
			<div class="bottom-part d-none d-lg-block ">
				<div class="row">
					<div class="col-12">
						<div class="d-flex justify-content-center align-items-center flex-auto">
								<?php echo $menu->renderFeature(); ?>
								<jdoc:include type="modules" name="menu" style="sp_xhtml" />
							</div>			
					</div>
				</div>
			</div>
		</div>
	</div>
</header>