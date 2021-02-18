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
<header id="sp-header" class="full-header header-has-modules">
	<div class="container-fluid">
		<div class="container-inner">
			<div class="row flex-nowrap align-items-center">
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
				<div id="logo-right" class="d-flex align-items-center">
					<!-- Related Modules -->
					<div class="d-none d-lg-flex header-modules">
						<?php if ($data->params->get('enable_search', 0)): ?>
							<?php echo ModuleHelper::renderModule($searchModule, ['style' => 'sp_xhtml']); ?>
						<?php endif ?>

						<?php if ($data->params->get('enable_login', 0)): ?>
							<?php echo $menu->renderLogin(); ?>
						<?php endif ?>
					</div>
					
					<jdoc:include type="modules" name="menu" style="sp_xhtml" />

					<a id="desktop-offcanvas-toggler" aria-label="' . JText::_('HELIX_ULTIMATE_NAVIGATION') . '" class="" href="#"><i class="fas fa-bars" aria-hidden="true" title="' . JText::_('HELIX_ULTIMATE_NAVIGATION') . '"></i></a>

					<!-- Modal menu for desktop -->
					<div id="desktop-offcanvas" class="desktop-offcanvas">
						<div class="desktop-offcanvas-inner">
							<span id="menu-dismiss" class="menu-dismiss"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg></span>
							<div class="container">
								<div class="row">
									<div class="col-sm-12">
										<div class="modules-wrapper header-modules">
											<?php echo ModuleHelper::renderModule($searchModule, ['style' => 'sp_xhtml']); ?>
											<?php echo $contact->renderFeature(); ?>
											<?php echo $social->renderFeature(); ?>
											<jdoc:include type="modules" name="menu" style="sp_xhtml" />
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