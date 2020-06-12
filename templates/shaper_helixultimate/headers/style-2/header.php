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
$logoClass = 'col-8 col-lg-3';
$menuClass = 'col-4 col-lg-9';

if($offcanvas_position === 'left')
{
	$logoClass = 'col-12 col-lg-3';
	$menuClass = 'd-none d-lg-block col-lg-9';
}

?>

<header id="sp-header">
	<div class="container">
		<div class="container-inner">
			<div class="row">
				<!-- Show logo on header -->
				<div id="sp-logo" class="<?php echo $logoClass; ?>">
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

				<!-- Show menu on header -->
				<div id="sp-menu" class="<?php echo $menuClass; ?>">
					<div class="sp-column">
						<?php if (isset($menu->load_pos) && $menu->load_pos === 'before') : ?>
							<?php echo $menu->renderFeature(); ?>
							<jdoc:include type="modules" name="menu" style="sp_xhtml" />
						<?php else : ?>
							<jdoc:include type="modules" name="menu" style="sp_xhtml" />
							<?php echo $menu->renderFeature(); ?>
						<?php endif ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</header>