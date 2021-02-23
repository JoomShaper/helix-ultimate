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

$data = $displayData;
$offcanvas_position = $displayData->params->get('offcanvas_position', 'right');
$menu_type = $displayData->params->get('menu_type');

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

				<!-- Menu -->
				<div id="sp-menu" class="col-auto flex-auto">
					<div class="sp-column d-flex justify-content-between">
						<div class="d-flex justify-content-between flex-auto">
							<?php echo $menu->renderFeature(); ?>
						</div>
					</div>
				</div>
				
				<!-- Menu Right position -->
				<div id="menu-right" class="d-flex align-items-center">
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

					<!-- if offcanvas position right -->
					<?php if($offcanvas_position === 'right' && $menu_type === 'mega_offcanvas') : ?>
						<a id="offcanvas-toggler"  aria-label="<?php echo Text::_('HELIX_ULTIMATE_NAVIGATION'); ?>" aria-hidden="true" title="<?php echo Text::_('HELIX_ULTIMATE_NAVIGATION'); ?>"  class="offcanvas-toggler-secondary offcanvas-toggler-right d-flex align-items-center" href="#"><div class="burger-icon"><span></span><span></span><span></span></div></a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</header>