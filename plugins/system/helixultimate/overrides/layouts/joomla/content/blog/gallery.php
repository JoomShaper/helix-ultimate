<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('JPATH_BASE') or die();

extract($displayData);
?>

<?php if(isset($attribs->helix_ultimate_gallery) && $attribs->helix_ultimate_gallery) : ?>
	<?php $gallery = json_decode($attribs->helix_ultimate_gallery); ?>
	<?php $images = (isset($gallery->helix_ultimate_gallery_images) && $gallery->helix_ultimate_gallery_images) ? $gallery->helix_ultimate_gallery_images : array(); ?>

	<?php if(count((array)$images)) : ?>
		<div class="article-feature-gallery">
			<div id="article-feature-gallery-<?php echo $id; ?>" class="carousel slide" data-bs-ride="carousel">
				<div class="carousel-inner" role="listbox">
					<?php foreach ( $images as $key => $image ) : ?>
						<div class="carousel-item<?php echo ($key===0) ? ' active': ''; ?>">
							<img src="<?php echo $image; ?>" <?php echo !empty($attribs->helix_ultimate_image_alt_txt) ? "alt='" . $attribs->helix_ultimate_image_alt_txt . "'" : '' ?>>
						</div>
					<?php endforeach; ?>
				</div>

				<button class="carousel-control-prev" data-bs-target="#article-feature-gallery-<?php echo $id; ?>" type="button" data-bs-slide="prev">
					<span class="carousel-control-prev-icon" aria-hidden="true"></span>
					<span class="visually-hidden">Previous</span>
				</button>

				<button class="carousel-control-next" data-bs-target="#article-feature-gallery-<?php echo $id; ?>" type="button" data-bs-slide="next">
					<span class="carousel-control-next-icon" aria-hidden="true"></span>
					<span class="visually-hidden">Next</span>
				</button>
			</div>
		</div>
	<?php endif; ?>
<?php endif; ?>
