<?php 
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

$item = $displayData;
$params = $item->params;
$info = $params->get('info_block_position', 0);
$attribs = json_decode($item->attribs);
HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
$article_format = (isset($attribs->helix_ultimate_article_format) && $attribs->helix_ultimate_article_format)
	? $attribs->helix_ultimate_article_format
	: 'standard';

?>
<div class="article related-article-large d-flex">
	<div class="article-image">
		<?php if($article_format === 'gallery') : ?>
			<?php echo LayoutHelper::render('joomla.content.blog.gallery', array('attribs' => $attribs, 'id' => $item->id)); ?>
		<?php elseif($article_format === 'video') : ?>
			<?php echo LayoutHelper::render('joomla.content.blog.video', array('attribs' => $attribs)); ?>
		<?php elseif($article_format === 'audio') : ?>
			<?php echo LayoutHelper::render('joomla.content.blog.audio', array('attribs' => $attribs)); ?>
		<?php else: ?>
			<a href="<?php echo Route::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catid, $item->language)); ?>">
				<?php echo LayoutHelper::render('joomla.content.full_image', $item); ?>
			</a>
		<?php endif; ?>
	</div>

	<div class="article-information">
		<?php echo LayoutHelper::render('joomla.content.blog_style_default_item_title', $item); ?>

		<?php if ($params->get('show_author') && !empty($item->author )) : ?>
			<?php echo LayoutHelper::render('joomla.content.info_block.author', array('item' => $item, 'params' => $params,'articleView'=>'intro')); ?>
		<?php endif; ?>
		<?php if ($params->get('show_publish_date')) : ?>
			<?php echo LayoutHelper::render('joomla.content.info_block.publish_date', array('item' => $item, 'params' => $params,'articleView'=>'intro')); ?>
		<?php endif; ?>

		<?php if ($item->introtext): ?>
			<div class="intro-text">
				<?php echo $item->introtext; ?>
			</div>
		<?php endif ?>

		<a href="<?php echo Route::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catid, $item->language)); ?>" class="btn btn-outline-secondary btn-sm"><?php echo Text::_('HELIX_ULTIMATE_READ_MORE') ?></a>
	</div>
</div>