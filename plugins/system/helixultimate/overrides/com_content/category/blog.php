<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use HelixUltimate\Framework\Platform\Helper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Layout\LayoutHelper;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers');

$app = Factory::getApplication();

// Content events
$this->category->text = $this->category->description;
$app->triggerEvent('onContentPrepare', array($this->category->extension . '.categories', &$this->category, &$this->params, 0));
$this->category->description = $this->category->text;

$results = $app->triggerEvent('onContentAfterTitle', array($this->category->extension . '.categories', &$this->category, &$this->params, 0));
$afterDisplayTitle = trim(implode("\n", $results));

$results = $app->triggerEvent('onContentBeforeDisplay', array($this->category->extension . '.categories', &$this->category, &$this->params, 0));
$beforeDisplayContent = trim(implode("\n", $results));

$results = $app->triggerEvent('onContentAfterDisplay', array($this->category->extension . '.categories', &$this->category, &$this->params, 0));
$afterDisplayContent = trim(implode("\n", $results));

// Columns + Helix blog list type
$columns = !empty((int) $this->params->get('num_columns')) ? (int) $this->params->get('num_columns') : 3;

$template     = HelixUltimate\Framework\Platform\Helper::loadTemplateData();
$blogListType = $template && isset($template->params) ? ($template->params->get('blog_list_type') ?? 'default') : 'default';

// Choose H tag
$htag = $this->params->get('show_page_heading') ? 'h2' : 'h1';
?>
<style>
	.article-list.grid {
		--columns: <?php echo (int) $columns; ?>;
	}
</style>

<div class="blog<?php echo $this->pageclass_sfx; ?> com-content-category-blog">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<div class="page-header">
			<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
		</div>
	<?php endif; ?>

	<?php if ($this->params->get('show_category_title', 1)) : ?>
		<<?php echo $htag; ?>>
			<?php echo $this->category->title; ?>
		</<?php echo $htag; ?>>
	<?php endif; ?>

	<?php echo $afterDisplayTitle; ?>

	<?php if ($this->params->get('show_cat_tags', 1) && !empty($this->category->tags->itemTags)) : ?>
		<?php $this->category->tagLayout = new FileLayout('joomla.content.tags'); ?>
		<?php echo $this->category->tagLayout->render($this->category->tags->itemTags); ?>
	<?php endif; ?>

	<?php if ($beforeDisplayContent || $afterDisplayContent || $this->params->get('show_description', 1) || $this->params->def('show_description_image', 1)) : ?>
		<div class="category-desc clearfix">
			<?php if ($this->params->get('show_description_image') && $this->category->getParams()->get('image')) : ?>
                 <?php echo LayoutHelper::render(
                    'joomla.html.image',
                    [
                        'src' => $this->category->getParams()->get('image'),
                        'alt' => empty($this->category->getParams()->get('image_alt')) && empty($this->category->getParams()->get('image_alt_empty')) ? false : $this->category->getParams()->get('image_alt'),
                    ]
                ); ?>
			<?php endif; ?>
			<?php echo $beforeDisplayContent; ?>
			<?php if ($this->params->get('show_description') && $this->category->description) : ?>
				<?php echo HTMLHelper::_('content.prepare', $this->category->description, '', 'com_content.category'); ?>
			<?php endif; ?>
			<?php echo $afterDisplayContent; ?>
		</div>
	<?php endif; ?>

	<?php if (empty($this->lead_items) && empty($this->link_items) && empty($this->intro_items)) : ?>
		<?php if ($this->params->get('show_no_articles', 1)) : ?>
			<div class="alert alert-info">
				<span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
				<?php echo Text::_('COM_CONTENT_NO_ARTICLES'); ?>
			</div>
		<?php endif; ?>
	<?php endif; ?>

	<?php if (!empty($this->lead_items)) : ?>
		<div class="com-content-category-blog__items blog-items items-leading article-list articles-leading<?php echo $this->params->get('blog_class_leading'); ?>">
			<?php foreach ($this->lead_items as &$item) : ?>
				<div class="com-content-category-blog__item blog-item article<?php echo $item->state == 0 ? ' system-unpublished' : null; ?>"
					itemprop="blogPost" itemscope itemtype="https://schema.org/BlogPosting">
					<?php
						$this->item = &$item;
						$this->item->leading = true; // flag for intro image size logic in your item layout
						echo $this->loadTemplate('item');
					?>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<?php
	$introcount = count($this->intro_items);
	?>

	<?php if (!empty($this->intro_items)) : ?>
		<?php $blogClass = $this->params->get('blog_class', ''); ?>
		<?php if ((int) $this->params->get('num_columns') > 1) : ?>
			<?php $blogClass .= ' cols-' . (int) $this->params->get('num_columns'); ?>
		<?php endif; ?>

		<?php if ($blogListType === 'masonry') : ?>
			<?php
			$numCols   = (int) $this->params->get('num_columns', 1);
			$orderDown = (int) $this->params->get('multi_column_order', 1); // 1 = across, 0 = down
			$introcount = count($this->intro_items);
			$numRows   = (int) ceil($introcount / max(1, $numCols));
			?>
			<div class="article-list grid <?php echo $blogClass; ?>">

				<?php for ($col = 0; $col < $numCols; $col++) : ?>
					<?php for ($row = 0; $row < $numRows; $row++) :
						// Index calc for masonry style
						$index = $orderDown ? ($col + $row * $numCols) : ($col * $numRows + $row);
						if ($index >= $introcount) {
							continue;
						}
						$item = &$this->intro_items[$index];
					?>
						<div class="article flow" itemprop="blogPost" itemscope itemtype="https://schema.org/BlogPosting">
							<?php
							$this->item = &$item;
							echo LayoutHelper::render('masonry.bloglist', array($item, ($index + 1)), defined('HELIX_LAYOUTS_PATH') ? HELIX_LAYOUTS_PATH : null);
							?>
						</div>
					<?php endfor; ?>
				<?php endfor; ?>
			</div>
		<?php else : ?>
			<div class="article-list <?php echo $blogClass; ?>">
				<?php
				$numCols   = (int) $this->params->get('num_columns', 1);
				$orderDown = (int) $this->params->get('multi_column_order', 1); // 1 = across, 0 = down
				$introcount = count($this->intro_items);
				$numRows   = (int) ceil($introcount / max(1, $numCols));
				$columnClass = 'col-lg-' . max(1, (12 / max(1, $numCols)));

				for ($row = 0; $row < $numRows; $row++) : ?>
					<div class="row">
						<?php for ($col = 0; $col < $numCols; $col++) :
							// Index calc for grid style
							$index = $orderDown ? ($row * $numCols + $col) : ($row + $col * $numRows);
							if ($index >= $introcount) {
								continue;
							}
							$item = &$this->intro_items[$index];
						?>
							<div class="<?php echo $columnClass; ?>">
								<div class="article" itemprop="blogPost" itemscope itemtype="https://schema.org/BlogPosting">
									<?php
										$this->item = &$item;
										echo $this->loadTemplate('item');
									?>
								</div>
							</div>
						<?php endfor; ?>
					</div>
				<?php endfor; ?>
			</div>
		<?php endif; ?>
	<?php endif; ?>

	<?php if (!empty($this->link_items)) : ?>
		<div class="items-more articles-more mb-4">
			<?php echo $this->loadTemplate('links'); ?>
		</div>
	<?php endif; ?>

    <?php if ($this->maxLevel != 0 && !empty($this->children[$this->category->id])) : ?>
        <div class="com-content-category-blog__children cat-children">
            <?php if ($this->params->get('show_category_heading_title_text', 1) == 1) : ?>
                <h3> <?php echo Text::_('JGLOBAL_SUBCATEGORIES'); ?> </h3>
            <?php endif; ?>
            <?php echo $this->loadTemplate('children'); ?> </div>
    <?php endif; ?>
    <?php if (($this->params->def('show_pagination', 1) == 1 || ($this->params->get('show_pagination') == 2)) && ($this->pagination->pagesTotal > 1)) : ?>
        <div class="com-content-category-blog__navigation w-100">
            <?php if ($this->params->def('show_pagination_results', 1)) : ?>
                <p class="com-content-category-blog__counter counter float-md-end pt-3 pe-2">
                    <?php echo $this->pagination->getPagesCounter(); ?>
                </p>
            <?php endif; ?>
            <div class="com-content-category-blog__pagination">
                <?php echo $this->pagination->getPagesLinks(); ?>
            </div>
        </div>
    <?php endif; ?>
</div>
