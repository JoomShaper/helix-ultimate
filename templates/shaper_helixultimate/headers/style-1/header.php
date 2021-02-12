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


/**
 * Logo and menu html classes
 *
 */
$logoClass = 'col-auto';
$menuClass = 'col-auto flex-auto';

/**
 * Get related modules
 * The modules are mod_search
 */
$searchModule = Helper::getSearchModule();

?>

<div id="sp-top-bar">
	<div class="container">
		<div class="container-inner">
			<div class="row">
				<div id="sp-top1" class="col-lg-6">
					<div class="sp-column text-center text-lg-left">
						<?php echo $social->renderFeature(); ?>
						<jdoc:include type="modules" name="top1" style="sp_xhtml" />
					</div>
				</div>

				<div id="sp-top2" class="col-lg-6">
					<div class="sp-column text-center text-lg-right">
							<?php echo $contact->renderFeature(); ?>
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
			<div class="row flex-nowrap">
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
						<?php if ($data->params->get('enable_search', 0)): ?>
							<?php echo ModuleHelper::renderModule($searchModule, ['style' => 'sp_xhtml']); ?>
						<?php endif ?>

						<?php if ($data->params->get('enable_login', 0)): ?>
							<div class="sp-module">
								<a class="sp-sign-in" href="<?php echo Route::_('index.php?option=com_users&view=login'); ?>" ><span class="text"><?php echo Text::_('HELIX_ULTIMATE_SIGN_IN_MENU'); ?></span></a>
							</div>
						<?php endif ?>
						
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