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
$params = $displayData->params;
$attribs = json_decode($displayData->attribs);

$template = JFactory::getApplication('site')->getTemplate(true);
$tplParams = $template->params;
$full_image = '';
if(isset($attribs->helix_featured_image) && $attribs->helix_featured_image != '')
{
	$full_image = $attribs->helix_featured_image;
}
?>
<?php if($full_image) : ?>
	<div class="article-full-image">
		<img src="<?php echo $full_image; ?>" alt="<?php echo htmlspecialchars($displayData->title, ENT_COMPAT, 'UTF-8'); ?>" itemprop="image">
	</div>
<?php else: ?>
	<?php $images = json_decode($displayData->images); ?>
	<?php if (isset($images->image_fulltext) && !empty($images->image_fulltext)) : ?>
		<?php $imgfloat = empty($images->float_fulltext) ? $params->get('float_fulltext') : $images->float_fulltext; ?>
		<div class="article-full-image fltoa-<?php echo htmlspecialchars($imgfloat); ?>"> <img
			<?php if ($images->image_fulltext_caption) :
				echo 'class="caption"' . ' title="' . htmlspecialchars($images->image_fulltext_caption) . '"';
			endif; ?>
			src="<?php echo htmlspecialchars($images->image_fulltext); ?>" alt="<?php echo htmlspecialchars($images->image_fulltext_alt); ?>" itemprop="image"> </div>
		<?php endif; ?>
	<?php endif; ?>
