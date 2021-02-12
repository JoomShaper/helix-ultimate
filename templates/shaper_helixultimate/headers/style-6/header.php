<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2020 JoomShaper
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
$social 	= new HelixUltimateFeatureSocial($data->params);
$contact 	= new HelixUltimateFeatureContact($data->params);

/**
 * Get related modules
 * The modules are mod_search
 */
$searchModule = Helper::getSearchModule();

?>

<div id="sp-header-topbar">
	<div class="container">
		<div class="container-inner">
		<div class="row align-items-center">
					<!-- Contact -->
					<div id="sp-contact" class="col-6 col-xl-4">
					<?php echo $contact->renderFeature(); ?>
					</div>
	
					<!-- Logo -->
					<div id="sp-logo" class="col-12 col-xl-4 d-none d-xl-block">
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
					<div id="sp-social" class="col-6 col-xl-4">
						<div class="sp-column d-flex justify-content-end">
							<!-- Social icons -->
							<div class="social-wrap d-flex align-items-center">
								<?php echo $social->renderFeature(); ?>
							</div>

							<!-- Related Modules -->
							<div class="d-none d-lg-flex header-modules">
								<?php if ($data->params->get('enable_search', 0)): ?>
									<?php echo ModuleHelper::renderModule($searchModule, ['style' => 'sp_xhtml']); ?>
								<?php endif ?>

								<?php if ($data->params->get('enable_login', 0)): ?>
									<div class="sp-module">
										<a class="sp-sign-in" href="<?php echo Route::_('index.php?option=com_users&view=login'); ?>"></a>
									</div>
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
				<div class="col-sm-6 d-block d-xl-none">
					<div class="sp-column d-flex justify-content-between align-items-center">
						<div id="sp-logo">
							<jdoc:include type="modules" name="logo" style="sp_xhtml" />
							<?php echo $logo->renderFeature(); ?>
						</div>
					</div>
				</div>

				<div class="col-sm-6 col-xl-12">
					<?php if ($menu_type === 'mega_offcanvas') {?>
						<div class="d-flex justify-content-end align-items-center">
					<?php } else { ?>
						<div class="d-flex justify-content-center align-items-center">
					<?php } ?>
						<!-- if offcanvas position left -->
						<?php if($offcanvas_position === 'left') { ?>
							<a id="offcanvas-toggler" aria-label="' . JText::_('HELIX_ULTIMATE_NAVIGATION') . '" class="offcanvas-toggler-secondary offcanvas-toggler-right" href="#"><i class="fas fa-bars" aria-hidden="true" title="' . JText::_('HELIX_ULTIMATE_NAVIGATION') . '"></i></a>
						<?php } ?>

						<?php echo $menu->renderFeature(); ?>
						<jdoc:include type="modules" name="menu" style="sp_xhtml" />

						<!-- if offcanvas position right -->
						<?php if($offcanvas_position === 'right') { ?>
							<a id="offcanvas-toggler" aria-label="' . JText::_('HELIX_ULTIMATE_NAVIGATION') . '" class="ml-3 offcanvas-toggler-secondary offcanvas-toggler-right" href="#"><i class="fas fa-bars" aria-hidden="true" title="' . JText::_('HELIX_ULTIMATE_NAVIGATION') . '"></i></a>
						<?php } ?>		
					</div>	
				</div>
			</div>
		</div>
	</div>
</header>