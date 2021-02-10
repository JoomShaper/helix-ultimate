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

if($offcanvas_position === 'left')
{
	$logoClass = 'col-12 col-lg-auto';
	$menuClass = 'd-none d-lg-block col-lg-auto flex-auto';
}

?>

<div id="sp-top-bar">
	<div class="container">
		<div class="container-inner">
			<div class="row">
				<div id="sp-top1" class="col-lg-6">
					<div class="sp-column text-center text-lg-left">
						<?php if (isset($social->load_pos) && $social->load_pos === 'before') : ?>
							<?php echo $social->renderFeature(); ?>
							<jdoc:include type="modules" name="top1" style="sp_xhtml" />
						<?php else : ?>
							<jdoc:include type="modules" name="top1" style="sp_xhtml" />
							<?php echo $social->renderFeature(); ?>
						<?php endif ?>
					</div>
				</div>

				<div id="sp-top2" class="col-lg-6">
					<div class="sp-column text-center text-lg-right">
						<?php if (isset($contact->load_pos) && $contact->load_pos === 'before') : ?>
							<?php echo $contact->renderFeature(); ?>
							<jdoc:include type="modules" name="top2" style="sp_xhtml" />
						<?php else : ?>
							<jdoc:include type="modules" name="top2" style="sp_xhtml" />
							<?php echo $contact->renderFeature(); ?>
						<?php endif ?>
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
					<div class="sp-column menu-flex">
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