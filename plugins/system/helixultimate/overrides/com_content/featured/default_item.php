<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Content\Administrator\Extension\ContentComponent;
use Joomla\Component\Content\Site\Helper\RouteHelper;

// Shortcuts
$params   = $this->item->params;
$images   = json_decode($this->item->images ?? '');
$attribs  = json_decode($this->item->attribs ?? '');
$canEdit  = (bool) $params->get('access-edit');
$info     = (int) $params->get('info_block_position', 0);
$assocParam = (Associations::isEnabled() && $params->get('show_associations'));

// Dates / state
$currentDate       = Factory::getDate()->format('Y-m-d H:i:s');
$isNotPublishedYet = ($this->item->publish_up > $currentDate);
$isExpired         = (!is_null($this->item->publish_down) && $this->item->publish_down < $currentDate);
$isUnpublished     = ($this->item->state == ContentComponent::CONDITION_UNPUBLISHED) || $isNotPublishedYet || $isExpired;

// Helix article format
$article_format = (isset($attribs->helix_ultimate_article_format) && $attribs->helix_ultimate_article_format) ? $attribs->helix_ultimate_article_format : 'standard';

// Deflist decision
$useDefList = (
    $params->get('show_modify_date') ||
    $params->get('show_publish_date') ||
    $params->get('show_create_date')  ||
    $params->get('show_hits')         ||
    $params->get('show_category')     ||
    $params->get('show_parent_category') ||
    $params->get('show_author')       ||
    $assocParam
);
?>

<?php if($article_format == 'gallery') : ?>
	<?php echo LayoutHelper::render('joomla.content.blog.gallery', array('attribs' => $attribs, 'id'=>$this->item->id)); ?>
<?php elseif($article_format == 'video') : ?>
	<?php echo LayoutHelper::render('joomla.content.blog.video', array('attribs' => $attribs)); ?>
<?php elseif($article_format == 'audio') : ?>
	<?php echo LayoutHelper::render('joomla.content.blog.audio', array('attribs' => $attribs)); ?>
<?php else: ?>
	<?php echo LayoutHelper::render('joomla.content.intro_image', $this->item); ?>
<?php endif; ?>

<?php if (!empty($this->item->featured)) : ?>
    <span class="badge bg-danger featured-article-badge"><?php echo Text::_('HELIX_ULTIMATE_FEATURED'); ?></span>
<?php endif; ?>

<div class="item-content articleBody">
    <?php if ($isUnpublished) : ?>
        <div class="system-unpublished">
    <?php endif; ?>

    <?php echo LayoutHelper::render('joomla.content.blog_style_default_item_title', $this->item); ?>
    <?php if ($canEdit) : ?>
        <?php echo LayoutHelper::render('joomla.content.icons', ['params' => $params, 'item' => $this->item]); ?>
    <?php endif; ?>
    <?php echo $this->item->event->afterDisplayTitle; ?>

    <?php if ($useDefList && ($info == 0 || $info == 2)) : ?>
        <?php echo LayoutHelper::render('joomla.content.info_block', ['item' => $this->item, 'params' => $params, 'position' => 'above']); ?>
        <?php if ($info == 0 && $params->get('show_tags', 1) && !empty($this->item->tags->itemTags)) : ?>
            <?php echo LayoutHelper::render('joomla.content.tags', $this->item->tags->itemTags); ?>
        <?php endif; ?>
    <?php endif; ?>

    <?php echo $this->item->event->beforeDisplayContent; ?>

    <?php echo $this->item->introtext; ?>

    <?php if ($useDefList && ($info == 1 || $info == 2)) : ?>
        <?php echo LayoutHelper::render('joomla.content.info_block', ['item' => $this->item, 'params' => $params, 'position' => 'below']); ?>
        <?php if ($params->get('show_tags', 1) && !empty($this->item->tags->itemTags)) : ?>
            <?php echo LayoutHelper::render('joomla.content.tags', $this->item->tags->itemTags); ?>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($params->get('show_readmore') && $this->item->readmore) :
        if ($params->get('access-view')) :
            $link = Route::_(RouteHelper::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language));
        else :
            $menu   = Factory::getApplication()->getMenu();
            $active = $menu->getActive();
            $itemId = $active ? $active->id : 0;
            $link   = new Uri(Route::_('index.php?option=com_users&view=login&Itemid=' . $itemId, false));
            $link->setVar('return', base64_encode(RouteHelper::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language)));
        endif; ?>

        <?php echo LayoutHelper::render('joomla.content.readmore', ['item' => $this->item, 'params' => $params, 'link' => $link]); ?>
    <?php endif; ?>

    <?php if ($isUnpublished) : ?>
        </div>
    <?php endif; ?>
</div>

<?php echo $this->item->event->afterDisplayContent; ?>
