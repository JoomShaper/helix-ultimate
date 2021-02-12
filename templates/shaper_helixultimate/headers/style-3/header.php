<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2020 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined ('_JEXEC') or die('Restricted Access');

use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$data = $displayData;
$offcanvas_position = $displayData->params->get('offcanvas_position', 'right');

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
$searchModule = ModuleHelper::getModule('mod_search');
?>

<header id="sp-header">
	<div class="container">
		<div class="container-inner">
			<div class="row flex-nowrap">
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
						<div class="d-flex menu-wrap justify-content-between align-items-center flex-auto">
							<?php echo $menu->renderFeature(); ?>
							<jdoc:include type="modules" name="menu" style="sp_xhtml" />
						</div>
						
						<!-- Related Modules -->
						<div class="d-none d-lg-flex header-modules">
							<?php if ($data->params->get('enable_search', 0)): ?>
								<?php echo ModuleHelper::renderModule($searchModule, ['style' => 'sp_xhtml']); ?>
							<?php endif ?>
	
							<?php if ($data->params->get('enable_login', 0)): ?>
								<div class="sp-module">
									<a class="sp-sign-in" href="<?php echo Route::_('index.php?option=com_users&view=login'); ?>" ><span class="d-none d-lg-inline-block"><?php echo Text::_('HELIX_ULTIMATE_SIGN_IN_MENU'); ?></span></a>
								</div>
							<?php endif ?>
						</div>

						<!-- Social icons -->
						<div class="social-wrap d-flex align-items-center">
							<?php echo $social->renderFeature(); ?>
						</div>

						<!-- if offcanvas position right -->
						<?php if($offcanvas_position === 'right') { ?>
							<a id="offcanvas-toggler" aria-label="' . JText::_('HELIX_ULTIMATE_NAVIGATION') . '" class="offcanvas-toggler-secondary offcanvas-toggler-right" href="#"><i class="fas fa-bars" aria-hidden="true" title="' . JText::_('HELIX_ULTIMATE_NAVIGATION') . '"></i></a>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</header>