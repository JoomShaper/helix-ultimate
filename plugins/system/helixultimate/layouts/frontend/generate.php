<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();


$layout_path  = JPATH_ROOT .'/plugins/system/helixultimate/layouts';

$data = $displayData;

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

	<?php echo (new JLayoutFile('frontend.rows', $layout_path))->render($data); ?>

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

</<?php echo $sematic; ?>>