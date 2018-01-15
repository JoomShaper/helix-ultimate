<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;
$params = $displayData->params;
$attribs = json_decode($displayData->attribs);

$template = \JFactory::getApplication('site')->getTemplate(true);
$tplParams = $template->params;

$blog_list_image = $tplParams->get('blog_list_image', 'thumbnail');

if(isset($attribs->helix_featured_image) && $attribs->helix_featured_image != '')
{
	if($blog_list_image == 'default')
	{
		$intro_image = $attribs->helix_featured_image;
	}
	else
	{
		$intro_image = $attribs->helix_featured_image;
		$basename = basename($intro_image);
		$list_image = \JPATH_ROOT . '/' . dirname($intro_image) . '/' . \JFile::stripExt($basename) . '_'. $blog_list_image .'.' . \JFile::getExt($basename);
		if(\Jfile::exists($list_image)) {
			$intro_image = \JURI::root(true) . '/' . dirname($intro_image) . '/' . \JFile::stripExt($basename) . '_'. $blog_list_image .'.' . \JFile::getExt($basename);
		}
	}

}
elseif(isset($images->image_intro) && !empty($images->image_intro))
{
	$intro_image = $images->image_intro;
}

$link = '';
if ($params->get('access-view')) :
	$link = \JRoute::_(ContentHelperRoute::getArticleRoute($displayData->slug, $displayData->catid, $displayData->language));
endif;
?>
<?php if($link != '') : ?>
	<a href="<?php echo $link; ?>">
<?php endif; ?>
<?php if(isset($attribs->helix_featured_image) && !empty($attribs->helix_featured_image)) : ?>
	<div class="intro-image">
		<img src="<?php echo $intro_image; ?>" alt="<?php echo htmlspecialchars($displayData->title, ENT_COMPAT, 'UTF-8'); ?>">
	</div>
<?php else: ?>
	<?php $images = json_decode($displayData->images); ?>
	<?php if (isset($images->image_intro) && !empty($images->image_intro)) : ?>
		<?php $imgfloat = empty($images->float_intro) ? $params->get('float_intro') : $images->float_intro; ?>
		<div class="pull-<?php echo htmlspecialchars($imgfloat, ENT_COMPAT, 'UTF-8'); ?> item-image">
		<?php if ($params->get('link_titles') && $params->get('access-view')) : ?>
			<a href="<?php echo \JRoute::_(ContentHelperRoute::getArticleRoute($displayData->slug, $displayData->catid, $displayData->language)); ?>"><img
			<?php if ($images->image_intro_caption) : ?>
				<?php echo 'class="caption"' . ' title="' . htmlspecialchars($images->image_intro_caption) . '"'; ?>
			<?php endif; ?>
			src="<?php echo htmlspecialchars($images->image_intro, ENT_COMPAT, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($images->image_intro_alt, ENT_COMPAT, 'UTF-8'); ?>" itemprop="thumbnailUrl"></a>
		<?php else : ?><img
			<?php if ($images->image_intro_caption) : ?>
				<?php echo 'class="caption"' . ' title="' . htmlspecialchars($images->image_intro_caption, ENT_COMPAT, 'UTF-8') . '"'; ?>
			<?php endif; ?>
			src="<?php echo htmlspecialchars($images->image_intro, ENT_COMPAT, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($images->image_intro_alt, ENT_COMPAT, 'UTF-8'); ?>" itemprop="thumbnailUrl">
		<?php endif; ?>
		</div>
	<?php endif; ?>
<?php endif; ?>
<?php if($link != '') : ?>
	</a>
<?php endif; ?>
