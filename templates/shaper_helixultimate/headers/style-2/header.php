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

/**
 * Helper classes for-
 * site logo, Menu header.
 *
 */
$logo    	= new HelixUltimateFeatureLogo($data->params);
$menu    	= new HelixUltimateFeatureMenu($data->params);


/**
 * Logo and menu html classes
 *
 */
$logoClass = 'col-auto';
$menuClass = 'col-auto flex-auto';

?>

<header id="sp-header">
	<div class="container">
		<div class="container-inner">
			<div class="row flex-nowrap">
				<!-- Show logo on header -->
				<div id="sp-logo" class="<?php echo $logoClass; ?>">
					<div class="sp-column">
						<?php echo $logo->renderFeature(); ?>
						<jdoc:include type="modules" name="logo" style="sp_xhtml" />
					</div>
				</div>

				<!-- Show menu on header -->
				<div id="sp-menu" class="<?php echo $menuClass; ?>">
					<div class="sp-column  d-flex justify-content-end align-items-center">
						<?php echo $menu->renderFeature(); ?>
						<jdoc:include type="modules" name="menu" style="sp_xhtml" />
						<?php if($offcanvas_position === 'right') { ?>
							<a id="offcanvas-toggler" aria-label="' . JText::_('HELIX_ULTIMATE_NAVIGATION') . '" class="offcanvas-toggler-secondary offcanvas-toggler-right" href="#"><i class="fas fa-bars" aria-hidden="true" title="' . JText::_('HELIX_ULTIMATE_NAVIGATION') . '"></i></a>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</header>