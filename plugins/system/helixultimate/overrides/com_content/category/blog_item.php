<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Content\Administrator\Extension\ContentComponent;
use Joomla\Component\Content\Site\Helper\RouteHelper;

$params    = $this->item->params ?? null;
$attribs   = json_decode($this->item->attribs ?? '');
$canEdit   = $params ? (bool) $params->get('access-edit') : false;
$info      = $params ? (int) $params->get('info_block_position', 0) : 0;

// Helix template params
$tmplParams = null;
if (class_exists('HelixUltimate\\Framework\\Platform\\Helper')) {
    $template   = HelixUltimate\Framework\Platform\Helper::loadTemplateData();
    $tmplParams = $template ? ($template->params ?? null) : null;
}

$assocParam = ($params && Associations::isEnabled() && $params->get('show_associations'));

$currentDate       = Factory::getDate()->format('Y-m-d H:i:s');
$isNotPublishedYet = (!empty($this->item->publish_up) && $this->item->publish_up > $currentDate);
$isExpired         = (!empty($this->item->publish_down) && $this->item->publish_down < $currentDate);
$isUnpublished     = ($this->item->state == ContentComponent::CONDITION_UNPUBLISHED) || $isNotPublishedYet || $isExpired;

$articleFormat = !empty($attribs->helix_ultimate_article_format) ? $attribs->helix_ultimate_article_format : 'standard';

$useDefList = ($params && (
    $params->get('show_modify_date') ||
    $params->get('show_publish_date') ||
    $params->get('show_create_date')  ||
    $params->get('show_hits')         ||
    $params->get('show_category')     ||
    $params->get('show_parent_category') ||
    $params->get('show_author')       ||
    $assocParam
));

switch ($articleFormat) {
    case 'gallery':
        echo LayoutHelper::render('joomla.content.blog.gallery', ['attribs' => $attribs, 'id' => $this->item->id]);
        break;
    case 'video':
        echo LayoutHelper::render('joomla.content.blog.video', ['attribs' => $attribs]);
        break;
    case 'audio':
        echo LayoutHelper::render('joomla.content.blog.audio', ['attribs' => $attribs]);
        break;
    default:
        echo LayoutHelper::render('joomla.content.intro_image', $this->item);
        break;
}

if (!empty($this->item->featured)) : ?>
    <span class="badge bg-danger featured-article-badge"><?php echo Text::_('HELIX_ULTIMATE_FEATURED'); ?></span>
<?php endif; ?>

<div class="article-body">
    <?php if ($isUnpublished) : ?>
        <div class="system-unpublished">
    <?php endif; ?>

    <?php echo LayoutHelper::render('joomla.content.blog_style_default_item_title', $this->item); ?>

    <?php if ($useDefList && ($info == 0 || $info == 2)) : ?>
        <?php echo LayoutHelper::render('joomla.content.info_block', ['item' => $this->item, 'params' => $params, 'position' => 'above', 'intro' => true]); ?>
        <?php if ($params->get('show_tags', 1) && !empty($this->item->tags->itemTags)) : ?>
            <?php if (!($tmplParams && $tmplParams->get('show_list_tags', 0))) : ?>
                <?php $this->item->tagLayout = new FileLayout('joomla.content.tags'); ?>
                <?php echo $this->item->tagLayout->render($this->item->tags->itemTags); ?>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($params && !$params->get('show_intro')) : ?>
        <?php echo $this->item->event->afterDisplayTitle; ?>
    <?php endif; ?>

    <?php echo $this->item->event->beforeDisplayContent; ?>

    <div class="article-introtext">
        <?php echo $this->item->introtext; ?>

        <?php if ($useDefList && ($info == 1)) : ?>
            <?php echo LayoutHelper::render('joomla.content.info_block', ['item' => $this->item, 'params' => $params, 'position' => 'below', 'intro' => true]); ?>
        <?php endif; ?>

        <?php if ($params && $params->get('show_readmore') && $this->item->readmore) :
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
    </div>

    <?php if ($isUnpublished) : ?>
        </div>
    <?php endif; ?>
</div>

<?php echo $this->item->event->afterDisplayContent; ?>

