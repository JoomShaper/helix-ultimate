<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Content\Site\Helper\RouteHelper;

$params   = $displayData->params ?? null;
$images   = json_decode($displayData->images ?? '');
$attribs  = json_decode($displayData->attribs ?? '');

$introImage = '';

$tplParams = null;
if (class_exists('HelixUltimate\\Framework\\Platform\\Helper')) {
    $template  = HelixUltimate\Framework\Platform\Helper::loadTemplateData();
    $tplParams = $template ? ($template->params ?? null) : null;
}

$leading = !empty($displayData->leading);

// Preferred size from template params
$blogListSize = 'thumbnail';

if ($tplParams) {
    $blogListSize = $leading
        ? $tplParams->get('leading_blog_list_image', 'large')
        : $tplParams->get('blog_list_image', 'thumbnail');
}

if (!empty($attribs->helix_ultimate_image)) {
    $introImage = $attribs->helix_ultimate_image;

    if ($blogListSize !== 'default') {
        $basename = basename($introImage);
        $dirname = dirname($introImage);
        $ext = pathinfo($basename, PATHINFO_EXTENSION);
        $name = pathinfo($basename, PATHINFO_FILENAME);
        $listImage = JPATH_ROOT . '/' . $dirname . '/' . $name . '_' . $blogListSize . '.' . $ext;

        if (file_exists($listImage)) {
            $introImage = Uri::root(true) . '/' . $dirname . '/' . $name . '_' . $blogListSize . '.' . $ext;
        }

    }
}

if (empty($introImage) && !empty($images->image_intro)) {
    $introImage = $images->image_intro;
}

if (empty($introImage)) {
    return;
}

$titleForAlt     = !empty($images->image_intro_alt) ? $images->image_intro_alt : ($displayData->title ?? '');
$altText         = $titleForAlt !== '' ? htmlspecialchars($titleForAlt, ENT_COMPAT, 'UTF-8') : false;
$captionText     = isset($images->image_intro_caption) ? $images->image_intro_caption : '';
$canView         = ($params && ($params->get('access-view') || $params->get('show_noauth', '0') == '1'));
$linkIntroImage  = $params ? ( (int)$params->get('link_intro_image') === 1 || ( (int)$params->get('link_titles') === 1 ) ) : false;
$shouldLink      = $linkIntroImage && $canView;

$articleRoute = Route::_(RouteHelper::getArticleRoute($displayData->slug, $displayData->catid, $displayData->language));

// Prepare layout attrs for Joomla image helper
$layoutAttr = [
    'src'      => htmlspecialchars($introImage, ENT_COMPAT, 'UTF-8'),
    'alt'      => $altText ?: false,
    'itemprop' => 'thumbnailUrl',
];

if ($captionText !== '') {
    $layoutAttr['class'] = 'caption';
    $layoutAttr['title'] = htmlspecialchars($captionText, ENT_COMPAT, 'UTF-8');
}

$imgfloat  = !empty($images->float_intro) ? $images->float_intro : ($params ? $params->get('float_intro') : '');
$imgClass  = trim(($imgfloat ? 'float-' . $imgfloat : '') . ' item-image article-intro-image');
?>

<?php if ($introImage) : ?>
	<?php if ($params->get('link_titles') && $params->get('access-view')) : ?>
		<a href="<?php echo Route::_(Joomla\Component\Content\Site\Helper\RouteHelper::getArticleRoute($displayData->slug, $displayData->catid, $displayData->language)); ?>">
		<?php endif; ?>
		<div class="article-intro-image">
			<?php 
				$layoutAttr = [
					'src' => $introImage,
					'alt' => empty($displayData->title) ? false : htmlspecialchars($displayData->title ?? "", ENT_COMPAT, 'UTF-8'),
				];
				echo LayoutHelper::render('joomla.html.image', array_merge($layoutAttr, ['itemprop' => 'thumbnailUrl']));
			?>
			
		</div>
		<?php if ($params->get('link_titles') && $params->get('access-view')) : ?>
		</a>
	<?php endif; ?>
<?php else : ?>

	<?php $images = json_decode($displayData->images ?? ""); ?>
	<?php if (isset($images->image_intro) && !empty($images->image_intro)) : ?>
		<?php $imgfloat = empty($images->float_intro) ? $params->get('float_intro') : $images->float_intro; ?>
		<div class="article-intro-image float-<?php echo htmlspecialchars($imgfloat, ENT_COMPAT, 'UTF-8'); ?>">
			<?php if ($params->get('link_titles') && $params->get('access-view')) : ?>
				
				<a href="<?php echo Route::_(Joomla\Component\Content\Site\Helper\RouteHelper::getArticleRoute($displayData->slug, $displayData->catid, $displayData->language)); ?>">
					<?php
                        $layoutAttr = [
							'src' => htmlspecialchars($images->image_intro, ENT_COMPAT, 'UTF-8'),
							'alt' => empty($images->image_intro_alt) ? false : htmlspecialchars($images->image_intro_alt ?? "", ENT_COMPAT, 'UTF-8'),
						];
						if (isset($images->image_intro_caption) && $images->image_intro_caption !== '') 
						{
							$layoutAttr['class'] = 'caption';
							$layoutAttr['title'] = htmlspecialchars($images->image_intro_caption ?? "");
						}
						echo LayoutHelper::render('joomla.html.image', array_merge($layoutAttr, ['itemprop' => 'thumbnailUrl']));
						// Image Caption 
						if (isset($images->image_intro_caption) && $images->image_intro_caption !== '') 
						{ ?>
							<figcaption class="caption text-dark"><?php echo $this->escape($images->image_intro_caption); ?></figcaption>
						<?php 
						}
					?>
				</a>
			<?php else : ?>
				<?php 
				$layoutAttr = [
						'src' => htmlspecialchars($images->image_intro ?? "", ENT_COMPAT, 'UTF-8'),
						'alt' => empty($images->image_intro_alt) ? false : htmlspecialchars($images->image_intro_alt, ENT_COMPAT, 'UTF-8'),
					];
					if (isset($images->image_intro_caption) && $images->image_intro_caption !== '') 
					{
						$layoutAttr['class'] = 'caption';
						$layoutAttr['title'] = htmlspecialchars($images->image_intro_caption ?? "", ENT_COMPAT, 'UTF-8');
					}
					echo LayoutHelper::render('joomla.html.image', array_merge($layoutAttr, ['itemprop' => 'thumbnailUrl']));
					// Image Caption 
					if (isset($images->image_intro_caption) && $images->image_intro_caption !== '') 
					{ ?>
						<figcaption class="caption text-dark"><?php echo $this->escape($images->image_intro_caption); ?></figcaption>
					<?php 
					}
				?>
			<?php endif; ?>
		</div>
	<?php endif; ?>
<?php endif; ?>
