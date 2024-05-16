<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die();

$layout_path  = JPATH_ROOT .'/plugins/system/helixultimate/layouts';

$data = $displayData;

$doc = Factory::getDocument();
$plg_path = Uri::root(true) . '/plugins/system/helixultimate';

$app = Factory::getApplication();
$template = $app->getTemplate(true);
$layout = [];

$rightSticky = false;
$leftSticky = false;

// Check if position 'right' or 'left' is sticky from layout
if (!empty($template->params->get('layout'))) {
	$layout = json_decode($template->params->get('layout'));

	foreach ($layout as $row) {
		if (!empty($row->attr)) {
			foreach ($row->attr as $attr) {
				if (!empty($attr->settings) && !empty($attr->settings->name)) {
					if ($attr->settings->name == 'right' && !empty($attr->settings->sticky_position)) {
						if ($attr->settings->sticky_position) {
							$rightSticky = true;
						}
					}

					if ($attr->settings->name == 'left' && !empty($attr->settings->sticky_position)) {
						if ($attr->settings->sticky_position) {
							$leftSticky = true;
						}
					}
				}
			}
		}
	}
}

extract($displayData);
?>

<<?php echo $sematic; ?> id="<?php echo $id ?>" <?php echo $row_class ?>>

	<?php if ($componentArea): ?>
		<?php if (!$pagebuilder): ?>
			<?php if (!$fluidrow): ?>
				<div class="container">
					<div class="container-inner">
			<?php endif ?>
		<?php endif ?>
	<?php else: ?>
		<?php if (!$fluidrow): ?>
			<div class="container">
				<div class="container-inner">
		<?php endif ?>
	<?php endif ?>

	<?php echo (new FileLayout('frontend.rows', $layout_path))->render($data); ?>

	<?php if ($componentArea): ?>
		<?php if (!$pagebuilder): ?>
			<?php if (!$fluidrow): ?>
					</div>
				</div>
			<?php endif ?>
		<?php endif ?>
	<?php else: ?>
		<?php if (!$fluidrow): ?>
				</div>
			</div>
		<?php endif ?>
	<?php endif ?>

	<?php if ($rightSticky || $leftSticky) :?>
		<?php $doc->addScript($plg_path . '/assets/js/sticky-sidebar.js'); ?>
		<script>
			window.addEventListener('DOMContentLoaded', () => {
				<?php if ($rightSticky) :?>
					var isRight = document.querySelector('#sp-right .sp-column');
					if (isRight) {
						const rightSidebar = new StickySidebar('#sp-right .sp-column', {
							containerSelector: '#sp-main-body .row',
							topSpacing: 15,
							minWidth:991
						});
					}
				<?php endif; ?>
				<?php if ($leftSticky) :?>
					var isLeft = document.querySelector('#sp-left .sp-column');
					if (isLeft) {
						const leftSidebar = new StickySidebar('#sp-left .sp-column', {
							containerSelector: '#sp-main-body .row',
							topSpacing: 15,
							minWidth:991
						});
					}
				<?php endif; ?>
			})
		</script>
	<?php endif; ?>
</<?php echo $sematic; ?>>
