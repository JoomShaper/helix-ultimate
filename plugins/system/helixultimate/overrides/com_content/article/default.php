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

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers');

$tmplParams = null;
if (class_exists('HelixUltimate\\Framework\\Platform\\Helper')) {
    $template   = HelixUltimate\Framework\Platform\Helper::loadTemplateData();
    $tmplParams = $template ? ($template->params ?? null) : null;
}

$relatedArticles = [];
if ($tmplParams && $tmplParams->get('related_article')) {
    $args = [
        'catId'    => $this->item->catid,
        'maximum'  => (int) $tmplParams->get('related_article_limit'),
        'itemTags' => $this->item->tags->itemTags ?? [],
        'item_id'  => $this->item->id,
    ];
    if (class_exists('HelixUltimate\\Framework\\Core\\HelixUltimate')) {
        $relatedArticles = HelixUltimate\Framework\Core\HelixUltimate::getRelatedArticles($args);
    }
}

// Shortcuts
$params         = $this->item->params;
$images         = json_decode($this->item->images ?? '');
$urls           = json_decode($this->item->urls ?? '');
$attribs        = json_decode($this->item->attribs ?? '');
$canEdit        = (bool) $params->get('access-edit');
$user           = Factory::getUser();
$currentDate    = Factory::getDate()->format('Y-m-d H:i:s');
$info           = (int) $params->get('info_block_position', 0);
$pageHeaderTag  = $this->params->get('show_page_heading') ? 'h2' : 'h1';

$articleFormat = (!empty($attribs->helix_ultimate_article_format)) ? $attribs->helix_ultimate_article_format : 'standard';

$assocParam = (Associations::isEnabled() && $params->get('show_associations'));

// State badges
$isUnpublished     = ($this->item->state == ContentComponent::CONDITION_UNPUBLISHED);
$isNotPublishedYet = ($this->item->publish_up > $currentDate);
$isExpired         = !is_null($this->item->publish_down) && ($this->item->publish_down < $currentDate);

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
<div class="article-details <?php echo $this->pageclass_sfx; ?>" itemscope itemtype="https://schema.org/Article">
    <meta itemprop="inLanguage" content="<?php echo ($this->item->language === '*') ? Factory::getConfig()->get('language') : $this->item->language; ?>">

    <?php if ($this->params->get('show_page_heading')) : ?>
        <div class="page-header">
            <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
        </div>
        <?php $pageHeaderTag = 'h2'; ?>
    <?php endif; ?>

    <?php
    if (!empty($this->item->pagination) && $this->item->pagination && !$this->item->paginationposition && $this->item->paginationrelative) {
        echo $this->item->pagination;
    }
    ?>

    <?php
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
            echo LayoutHelper::render('joomla.content.full_image', $this->item);
            break;
    }
    ?>

    <?php if ($this->item->featured) : ?>
        <span class="badge bg-danger featured-article-badge"><?php echo Text::_('HELIX_ULTIMATE_FEATURED'); ?></span>
    <?php endif; ?>

    <?php if ($params->get('show_title') || $params->get('show_author')) : ?>
        <div class="article-header">
            <?php if ($params->get('show_title')) : ?>
                <<?php echo $pageHeaderTag; ?> itemprop="headline">
                    <?php echo $this->escape($this->item->title); ?>
                </<?php echo $pageHeaderTag; ?>>
            <?php endif; ?>

            <?php if ($isUnpublished) : ?>
                <span class="badge bg-warning text-dark"><?php echo Text::_('JUNPUBLISHED'); ?></span>
            <?php endif; ?>

            <?php if ($isNotPublishedYet) : ?>
                <span class="badge bg-warning text-dark"><?php echo Text::_('JNOTPUBLISHEDYET'); ?></span>
            <?php endif; ?>

            <?php if ($isExpired) : ?>
                <span class="badge bg-warning text-dark mb-2"><?php echo Text::_('JEXPIRED'); ?></span>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="article-can-edit d-flex flex-wrap justify-content-between">
        <?php ?>
        <?php echo $this->item->event->afterDisplayTitle; ?>

        <?php if ($canEdit && empty($this->print)) : ?>
            <?php echo HTMLHelper::_('icon.edit', $this->item, $params); ?>
        <?php endif; ?>
    </div>

    <?php if ($useDefList && ($info == 0 || $info == 2)) : ?>
        <?php echo LayoutHelper::render('joomla.content.info_block', ['item' => $this->item, 'params' => $params, 'position' => 'above']); ?>
    <?php endif; ?>

    <?php ?>
    <?php echo $this->item->event->beforeDisplayContent; ?>

    <?php
    // Links (position=0)
    $urlsPos0 = (isset($urls) && ((!empty($urls->urls_position) && ($urls->urls_position == '0')) || ($params->get('urls_position') == '0' && empty($urls->urls_position))))
                || (empty($urls->urls_position) && (!$params->get('urls_position')));
    if ($urlsPos0) : ?>
        <?php echo $this->loadTemplate('links'); ?>
    <?php endif; ?>

    <?php if ($params->get('access-view')) : ?>

        <?php

        if (!empty($this->item->pagination) && $this->item->pagination && !$this->item->paginationposition && !$this->item->paginationrelative) {
            echo $this->item->pagination;
        }
        ?>

        <?php if (isset($this->item->toc)) : ?>
            <?php echo $this->item->toc; ?>
        <?php endif; ?>

        <?php if ( ($tmplParams && ($tmplParams->get('social_share') || $params->get('show_vote'))) && empty($this->print) ) : ?>
            <div class="article-ratings-social-share d-flex justify-content-end">
                <div class="me-auto align-self-center">
                    <?php if ($params->get('show_vote')) : ?>
                        <?php HTMLHelper::_('jquery.token'); ?>
                        <?php echo LayoutHelper::render('joomla.content.rating', ['item' => $this->item, 'params' => $params]); ?>
                    <?php endif; ?>
                </div>
                <div class="social-share-block">
                    <?php echo LayoutHelper::render('joomla.content.social_share', $this->item); ?>
                </div>
            </div>
        <?php endif; ?>

        <div itemprop="articleBody">
            <?php echo $this->item->text; ?>
        </div>

        <?php if ($info == 1 || $info == 2) : ?>
            <?php if ($useDefList) : ?>
                <?php echo LayoutHelper::render('joomla.content.info_block', ['item' => $this->item, 'params' => $params, 'position' => 'below']); ?>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($info == 0 && $params->get('show_tags', 1) && !empty($this->item->tags->itemTags)) : ?>
            <?php $this->item->tagLayout = new FileLayout('joomla.content.tags'); ?>
            <?php echo $this->item->tagLayout->render($this->item->tags->itemTags); ?>
        <?php endif; ?>

        <?php
        $urlsPos1 = (isset($urls) && ((!empty($urls->urls_position) && ($urls->urls_position == '1')) || ($params->get('urls_position') == '1')));
        if ($urlsPos1) : ?>
            <?php echo $this->loadTemplate('links'); ?>
        <?php endif; ?>

    <?php elseif ($params->get('show_noauth') == true && $user->get('guest')) : ?>
        <?php ?>
        <?php echo LayoutHelper::render('joomla.content.intro_image', $this->item); ?>
        <?php echo HTMLHelper::_('content.prepare', $this->item->introtext); ?>

        <?php if ($params->get('show_readmore') && !empty($this->item->fulltext)) : ?>
            <?php
            $menu   = Factory::getApplication()->getMenu();
            $active = $menu->getActive();
            $itemId = $active ? $active->id : 0;
            $link   = new Uri(Route::_('index.php?option=com_users&view=login&Itemid=' . $itemId, false));
            $link->setVar('return', base64_encode(RouteHelper::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language)));
            ?>
            <p class="readmore">
                <a class="register" href="<?php echo $link; ?>">
                    <?php
                    if (empty($attribs->alternative_readmore)) {
                        echo Text::_('COM_CONTENT_REGISTER_TO_READ_MORE');
                    } elseif ($readmore = $attribs->alternative_readmore) {
                        echo $readmore;
                        if ((int) $params->get('show_readmore_title', 0) !== 0) {
                            echo HTMLHelper::_('string.truncate', $this->item->title, (int) $params->get('readmore_limit'));
                        }
                    } elseif ((int) $params->get('show_readmore_title', 0) === 0) {
                        echo Text::sprintf('COM_CONTENT_READ_MORE_TITLE');
                    } else {
                        echo Text::_('COM_CONTENT_READ_MORE');
                        echo HTMLHelper::_('string.truncate', $this->item->title, (int) $params->get('readmore_limit'));
                    }
                    ?>
                </a>
            </p>
        <?php endif; ?>
    <?php endif; ?>


    <?php ?>
    <?php echo $this->item->event->afterDisplayContent; ?>

    <?php echo LayoutHelper::render('joomla.content.blog.author_info', $this->item); ?>

   <?php
	if (!empty($this->item->pagination) && $this->item->pagination && $this->item->paginationposition) :
		echo $this->item->pagination;
	?>
	<?php endif; ?>

    <?php if (empty($this->print)) : ?>
        <?php echo LayoutHelper::render('joomla.content.blog.comments.comments', $this->item); ?>
    <?php endif; ?>
</div>

<?php if ($tmplParams && $tmplParams->get('related_article') && count($relatedArticles) > 0) : ?>
    <?php echo LayoutHelper::render('joomla.content.related_articles', ['articles' => $relatedArticles, 'item' => $this->item]); ?>
<?php endif; ?>
