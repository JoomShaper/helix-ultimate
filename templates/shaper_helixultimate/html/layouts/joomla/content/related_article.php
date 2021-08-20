<?php 
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;

$item = $displayData;
$item->enableOpenGraph = false;
$params = $item->params;
$info = $params->get('info_block_position', 0);
$attribs = json_decode($item->attribs);
HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
$article_format = (isset($attribs->helix_ultimate_article_format) && $attribs->helix_ultimate_article_format) ? $attribs->helix_ultimate_article_format : 'standard';
?>
<div class="article">
    <?php if($article_format === 'gallery') : ?>
        <?php echo LayoutHelper::render('joomla.content.blog.gallery', array('attribs' => $attribs, 'id' => $item->id)); ?>
    <?php elseif($article_format === 'video') : ?>
        <?php echo LayoutHelper::render('joomla.content.blog.video', array('attribs' => $attribs)); ?>
    <?php elseif($article_format === 'audio') : ?>
        <?php echo LayoutHelper::render('joomla.content.blog.audio', array('attribs' => $attribs)); ?>
    <?php else: ?>
        <?php echo LayoutHelper::render('joomla.content.full_image', $item); ?>
    <?php endif; ?>

    <?php echo LayoutHelper::render('joomla.content.blog_style_default_item_title', $item); ?>

    <div class="article-info">
        <?php if ($params->get('show_author') && !empty($item->author )) : ?>
            <?php echo LayoutHelper::render('joomla.content.info_block.author', array('item' => $item, 'params' => $params,'articleView'=>'intro')); ?>
        <?php endif; ?>
        <?php if ($params->get('show_publish_date')) : ?>
            <?php echo LayoutHelper::render('joomla.content.info_block.publish_date', array('item' => $item, 'params' => $params,'articleView'=>'intro')); ?>
        <?php endif; ?>
    </div>        
</div>