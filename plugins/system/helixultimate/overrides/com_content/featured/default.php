<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('_JEXEC') or die();

use HelixUltimate\Framework\Platform\Helper;
use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers');

?>
<div class="container-fluid blog-featured<?php echo $this->pageclass_sfx; ?>" itemscope itemtype="https://schema.org/Blog">
    <?php if ((int) $this->params->get('show_page_heading') !== 0) : ?>
        <div class="page-header">
            <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
        </div>
    <?php endif; ?>

    <?php $leadingcount = 0; ?>
    <?php if (!empty($this->lead_items)) : ?>
        <div class="article-list">
            <div class="blog-items items-leading <?php echo $this->params->get('blog_class_leading'); ?>">
                <?php foreach ($this->lead_items as &$item) : ?>
                    <div class="leading-<?php echo (int) $leadingcount; ?>">
                        <div class="blog-item article" itemprop="blogPost" itemscope itemtype="https://schema.org/BlogPosting">
                            <?php
                                $this->item = &$item;
                                $this->item->leading = true;
                                echo $this->loadTemplate('item');
                                $leadingcount++;
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php
        $counter      = 0;
        $numColumns   = (int) $this->params->get('num_columns', 1);
        $blogClass    = trim($this->params->get('blog_class', ''));
        if ($numColumns > 1) {
            $blogClass .= ($blogClass ? ' ' : '') . 'cols-' . $numColumns;
        }
    ?>

    <?php if (!empty($this->intro_items)) : ?>
        <div class="article-list">
            <div class="row row-<?php echo $counter + 1; ?> <?php echo $blogClass; ?>">
                <?php foreach ($this->intro_items as $key => &$item) : ?>
                    <div class="col-lg-<?php echo (int) round(12 / Helper::SetColumn($numColumns, 3)); ?>">
                        <div class="article blog-items <?php echo $blogClass; ?>" itemprop="blogPost" itemscope itemtype="https://schema.org/BlogPosting">
                            <?php
                                $this->item = &$item;
                                echo $this->loadTemplate('item');
                                $counter++;
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($this->link_items)) : ?>
        <div class="items-more articles-more mb-4">
            <?php echo $this->loadTemplate('links'); ?>
        </div>
    <?php endif; ?>

    <?php if ($this->params->def('show_pagination', 2) == 1 || ($this->params->get('show_pagination') == 2 && $this->pagination->pagesTotal > 1)) : ?>
        <nav class="pagination-wrapper d-lg-flex justify-content-between w-100">
            <?php echo $this->pagination->getPagesLinks(); ?>
            <?php if ($this->params->def('show_pagination_results', 1)) : ?>
                <div class="pagination-counter text-muted mb-4">
                    <?php echo $this->pagination->getPagesCounter(); ?>
                </div>
            <?php endif; ?>
        </nav>
    <?php endif; ?>
</div>
