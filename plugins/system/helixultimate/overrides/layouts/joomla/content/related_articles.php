
<?php 
defined ('JPATH_BASE') or die();

use Joomla\CMS\Layout\LayoutHelper;
$articles = $displayData['articles'];
$mainItem = $displayData['item'];
$template = HelixUltimate\Framework\Platform\Helper::loadTemplateData();
$tmpl_params = $template->params;
?>
<div class="related-article-list-container">
	<h3 class="related-article-title"> <?php echo $tmpl_params->get('related_article_title'); ?> </h3>

	<?php if( $tmpl_params->get('related_article_view_type') === 'thumb' ): ?> 
		<div class="article-list related-article-list">
			<div class="row">
				<?php foreach( $articles as $item ): ?> 
					<div class="col-lg-<?php echo round(12 / $mainItem->params->get('num_columns')); ?>">            
						<?php echo LayoutHelper::render('joomla.content.related_article', $item); ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>

	<?php if( $tmpl_params->get('related_article_view_type') === 'list' ): ?> 
		<ul class="article-list related-article-list">
			<?php foreach( $articles as $item ): ?> 
				<li class="related-article-list-item">     
					<?php echo LayoutHelper::render('joomla.content.blog_style_default_item_title', $item); ?>
					<?php echo LayoutHelper::render('joomla.content.info_block.publish_date', array('item' => $item, 'params' => $item->params,'articleView'=>'intro')); ?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<?php if( $tmpl_params->get('related_article_view_type') === 'large' ): ?> 
		<div class="article-list related-article-list">
			<?php foreach( $articles as $item ): ?> 
				<div class="row">
					<div class="col-12">
						<?php echo LayoutHelper::render('joomla.content.related_article_large', $item); ?>
					</div>  
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>