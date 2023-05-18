<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');

// Create some shortcuts.
$n         = count($this->items);
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

// Check for at least one editable article
$isEditable = false;

if (!empty($this->items))
{
	foreach ($this->items as $article)
	{
		if ($article->params->get('access-edit'))
		{
			$isEditable = true;
			break;
		}
	}
}
$currentDate = Factory::getDate()->format('Y-m-d H:i:s');
?>

<form action="<?php echo htmlspecialchars(Uri::getInstance()->toString() ?? ""); ?>" method="post" name="adminForm" id="adminForm">

<?php if ($this->params->get('filter_field') !== 'hide' || $this->params->get('show_pagination_limit')) : ?>
	<div class="d-flex justify-content-between align-items-centerd-flex mb-4">
		<div class="me-auto align-self-center">
			<strong><?php echo Text::_('COM_CONTENT_FORM_FILTER_LEGEND'); ?></strong>
		</div>
		
		<div>
			<div class="filters row gx-3">
				<?php if ($this->params->get('filter_field') !== 'hide') : ?>			
					<?php if ($this->params->get('filter_field') !== 'tag') : ?>
						<div class="col">
							<label class="filter-search-lbl visually-hidden" for="filter-search">
								<?php echo Text::_('COM_CONTENT_' . $this->params->get('filter_field') . '_FILTER_LABEL') . '&#160;'; ?>
							</label>
							<input type="text" name="filter-search" id="filter-search" value="<?php echo $this->escape($this->state->get('list.filter')); ?>" class="form-control" onchange="document.adminForm.submit();" title="<?php echo Text::_('COM_CONTENT_FILTER_SEARCH_DESC'); ?>" placeholder="<?php echo Text::_('COM_CONTENT_' . $this->params->get('filter_field') . '_FILTER_LABEL'); ?>">
						</div>
					<?php else : ?>
						<div class="col">
							<select name="filter_tag" id="filter_tag" onchange="document.adminForm.submit();" >
								<option value=""><?php echo Text::_('JOPTION_SELECT_TAG'); ?></option>
								<?php echo HTMLHelper::_('select.options', HTMLHelper::_('tag.options', true, true), 'value', 'text', $this->state->get('filter.tag')); ?>
							</select>
						</div>
					<?php endif; ?>
				<?php endif; ?>
				<?php if ($this->params->get('show_pagination_limit')) : ?>
					<div class="col">
						<label for="limit" class="visually-hidden">
							<?php echo Text::_('JGLOBAL_DISPLAY_NUM'); ?>
						</label>
						<?php echo $this->pagination->getLimitBox(); ?>
					</div>
				<?php endif; ?>
				<div class="col-auto">
					<input type="hidden" name="filter_order" value="">
					<input type="hidden" name="filter_order_Dir" value="">
					<input type="hidden" name="limitstart" value="">
					<input type="hidden" name="task" value="">
					<button type="submit" name="filter_submit" class="btn btn-secondary"><?php echo Text::_('COM_CONTENT_FORM_FILTER_SUBMIT'); ?></button>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>

<?php if (empty($this->items)) : ?>
	<?php if ($this->params->get('show_no_articles', 1)) : ?>
		<p><?php echo Text::_('COM_CONTENT_NO_ARTICLES'); ?></p>
	<?php endif; ?>
<?php else : ?>

	<table class="category table table-bordered">
		<?php
		$headerTitle    = '';
		$headerDate     = '';
		$headerAuthor   = '';
		$headerHits     = '';
		$headerVotes    = '';
		$headerRatings  = '';
		$headerEdit     = '';
		?>
		<?php if ($this->params->get('show_headings')) : ?>
			<?php
			$headerTitle    = 'headers="categorylist_header_title"';
			$headerDate     = 'headers="categorylist_header_date"';
			$headerAuthor   = 'headers="categorylist_header_author"';
			$headerHits     = 'headers="categorylist_header_hits"';
			$headerVotes    = 'headers="categorylist_header_votes"';
			$headerRatings  = 'headers="categorylist_header_ratings"';
			$headerEdit     = 'headers="categorylist_header_edit"';
			?>
			<thead>
			<tr>
				<th scope="col" id="categorylist_header_title">
					<?php echo HTMLHelper::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder, null, 'asc', '', 'adminForm'); ?>
				</th>
				<?php if ($date = $this->params->get('list_show_date')) : ?>
					<th scope="col" id="categorylist_header_date">
						<?php if ($date === 'created') : ?>
							<?php echo HTMLHelper::_('grid.sort', 'COM_CONTENT_' . $date . '_DATE', 'a.created', $listDirn, $listOrder); ?>
						<?php elseif ($date === 'modified') : ?>
							<?php echo HTMLHelper::_('grid.sort', 'COM_CONTENT_' . $date . '_DATE', 'a.modified', $listDirn, $listOrder); ?>
						<?php elseif ($date === 'published') : ?>
							<?php echo HTMLHelper::_('grid.sort', 'COM_CONTENT_' . $date . '_DATE', 'a.publish_up', $listDirn, $listOrder); ?>
						<?php endif; ?>
					</th>
				<?php endif; ?>
				<?php if ($this->params->get('list_show_author')) : ?>
					<th scope="col" id="categorylist_header_author">
						<?php echo HTMLHelper::_('grid.sort', 'JAUTHOR', 'author', $listDirn, $listOrder); ?>
					</th>
				<?php endif; ?>
				<?php if ($this->params->get('list_show_hits')) : ?>
					<th scope="col" id="categorylist_header_hits">
						<?php echo HTMLHelper::_('grid.sort', 'JGLOBAL_HITS', 'a.hits', $listDirn, $listOrder); ?>
					</th>
				<?php endif; ?>
				<?php if ($this->params->get('list_show_votes', 0) && $this->vote) : ?>
					<th scope="col" id="categorylist_header_votes">
						<?php echo HTMLHelper::_('grid.sort', 'COM_CONTENT_VOTES', 'rating_count', $listDirn, $listOrder); ?>
					</th>
				<?php endif; ?>
				<?php if ($this->params->get('list_show_ratings', 0) && $this->vote) : ?>
					<th scope="col" id="categorylist_header_ratings">
						<?php echo HTMLHelper::_('grid.sort', 'COM_CONTENT_RATINGS', 'rating', $listDirn, $listOrder); ?>
					</th>
				<?php endif; ?>
				<?php if ($isEditable) : ?>
					<th scope="col" id="categorylist_header_edit"><?php echo Text::_('COM_CONTENT_EDIT_ITEM'); ?></th>
				<?php endif; ?>
			</tr>
			</thead>
		<?php endif; ?>
		<tbody>
		<?php foreach ($this->items as $i => $article) : ?>
			<?php if ($this->items[$i]->state == 0) : ?>
				<tr class="system-unpublished cat-list-row<?php echo $i % 2; ?>">
			<?php else : ?>
				<tr class="cat-list-row<?php echo $i % 2; ?>" >
			<?php endif; ?>
			<td headers="categorylist_header_title" class="list-title">
				<?php if (in_array($article->access, $this->user->getAuthorisedViewLevels())) : ?>
					<a href="<?php echo Route::_(ContentHelperRoute::getArticleRoute($article->slug, $article->catid, $article->language)); ?>">
						<?php echo $this->escape($article->title); ?>
					</a>
					<?php if (Associations::isEnabled() && $this->params->get('show_associations')) : ?>
						<?php $associations = ContentHelperAssociation::displayAssociations($article->id); ?>
						<?php foreach ($associations as $association) : ?>
							<?php if ($this->params->get('flags', 1)) : ?>
								<?php $flag = HTMLHelper::_('image', 'mod_languages/' . $association['language']->image . '.gif', $association['language']->title_native, array('title' => $association['language']->title_native), true); ?>
								&nbsp;<a href="<?php echo Route::_($association['item']); ?>"><?php echo $flag; ?></a>&nbsp;
							<?php else : ?>
								<?php $class = 'label label-association label-' . $association['language']->sef; ?>
								&nbsp;<a class="' . <?php echo $class; ?> . '" href="<?php echo Route::_($association['item']); ?>"><?php echo strtoupper($association['language']->sef); ?></a>&nbsp;
							<?php endif; ?>
						<?php endforeach; ?>
					<?php endif; ?>
				<?php else : ?>
					<?php
					echo $this->escape($article->title) . ' : ';
					$itemId = Factory::getApplication()->getMenu()->getActive()->id;
					$link   = new Uri(Route::_('index.php?option=com_users&view=login&Itemid=' . $itemId, false));
					$link->setVar('return', base64_encode(ContentHelperRoute::getArticleRoute($article->slug, $article->catid, $article->language)));
					?>
					<a href="<?php echo $link; ?>" class="register">
						<?php echo Text::_('COM_CONTENT_REGISTER_TO_READ_MORE'); ?>
					</a>
					<?php if (Associations::isEnabled() && $this->params->get('show_associations')) : ?>
						<?php $associations = ContentHelperAssociation::displayAssociations($article->id); ?>
						<?php foreach ($associations as $association) : ?>
							<?php if ($this->params->get('flags', 1)) : ?>
								<?php $flag = HTMLHelper::_('image', 'mod_languages/' . $association['language']->image . '.gif', $association['language']->title_native, array('title' => $association['language']->title_native), true); ?>
								&nbsp;<a href="<?php echo Route::_($association['item']); ?>"><?php echo $flag; ?></a>&nbsp;
							<?php else : ?>
								<?php $class = 'label label-association label-' . $association['language']->sef; ?>
								&nbsp;<a class="' . <?php echo $class; ?> . '" href="<?php echo Route::_($association['item']); ?>"><?php echo strtoupper($association['language']->sef); ?></a>&nbsp;
							<?php endif; ?>
						<?php endforeach; ?>
					<?php endif; ?>
				<?php endif; ?>

				<!-- check for the Joomla version  -->
				<?php if (JVERSION < 4): ?>
					<?php if ($article->state == 0) : ?>
						<span class="list-published badge bg-warning text-dark">
							<?php echo Text::_('JUNPUBLISHED'); ?>
						</span>
					<?php endif; ?>
					<?php if (strtotime($article->publish_up) > strtotime(Factory::getDate())) : ?>
						<span class="list-published badge bg-warning text-dark">
							<?php echo Text::_('JNOTPUBLISHEDYET'); ?>
						</span>
					<?php endif; ?>
					<?php if ((strtotime($article->publish_down) < strtotime(Factory::getDate())) && $article->publish_down != Factory::getDbo()->getNullDate()) : ?>
						<span class="list-published badge bg-warning text-dark">
							<?php echo Text::_('JEXPIRED'); ?>
						</span>
					<?php endif; ?>
				<?php else: ?>
					<?php if ($article->state == Joomla\Component\Content\Administrator\Extension\ContentComponent::CONDITION_UNPUBLISHED) : ?>
						<div>
							<span class="list-published badge bg-warning text-dark">
								<?php echo Text::_('JUNPUBLISHED'); ?>
							</span>
						</div>
					<?php endif; ?>
					<?php if ($article->publish_up > $currentDate) : ?>
						<div>
							<span class="list-published badge bg-warning text-dark">
								<?php echo Text::_('JNOTPUBLISHEDYET'); ?>
							</span>
						</div>
					<?php endif; ?>
					<?php if (!is_null($article->publish_down) && $article->publish_down < $currentDate) : ?>
						<div>
							<span class="list-published badge bg-warning text-dark">
								<?php echo Text::_('JEXPIRED'); ?>
							</span>
						</div>
					<?php endif; ?>
				<?php endif ?>

			</td>
			<?php if ($this->params->get('list_show_date')) : ?>
				<td headers="categorylist_header_date" class="list-date small">
					<?php
					echo HTMLHelper::_(
						'date', $article->displayDate,
						$this->escape($this->params->get('date_format', Text::_('DATE_FORMAT_LC3')))
					); ?>
				</td>
			<?php endif; ?>
			<?php if ($this->params->get('list_show_author', 1)) : ?>
				<td headers="categorylist_header_author" class="list-author">
					<?php if (!empty($article->author) || !empty($article->created_by_alias)) : ?>
						<?php $author = $article->author ?>
						<?php $author = $article->created_by_alias ?: $author; ?>
						<?php if (!empty($article->contact_link) && $this->params->get('link_author') == true) : ?>
							<?php echo Text::sprintf('COM_CONTENT_WRITTEN_BY', HTMLHelper::_('link', $article->contact_link, $author)); ?>
						<?php else : ?>
							<?php echo Text::sprintf('COM_CONTENT_WRITTEN_BY', $author); ?>
						<?php endif; ?>
					<?php endif; ?>
				</td>
			<?php endif; ?>
			<?php if ($this->params->get('list_show_hits', 1)) : ?>
				<td headers="categorylist_header_hits" class="list-hits">
					<span class="badge bg-primary">
						<?php echo Text::sprintf('JGLOBAL_HITS_COUNT', $article->hits); ?>
					</span>
				</td>
			<?php endif; ?>
			<?php if ($this->params->get('list_show_votes', 0) && $this->vote) : ?>
				<td headers="categorylist_header_votes" class="list-votes">
					<span class="badge bg-success">
						<?php echo Text::sprintf('COM_CONTENT_VOTES_COUNT', $article->rating_count); ?>
					</span>
				</td>
			<?php endif; ?>
			<?php if ($this->params->get('list_show_ratings', 0) && $this->vote) : ?>
				<td headers="categorylist_header_ratings" class="list-ratings">
					<span class="badge bg-warning">
						<?php echo Text::sprintf('COM_CONTENT_RATINGS_COUNT', $article->rating); ?>
					</span>
				</td>
			<?php endif; ?>
			<?php if ($isEditable) : ?>
				<td headers="categorylist_header_edit" class="list-edit">
					<?php if ($article->params->get('access-edit')) : ?>
						<?php echo HTMLHelper::_('icon.edit', $article, $article->params); ?>
					<?php endif; ?>
				</td>
			<?php endif; ?>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>

<?php if (!empty($this->items)) : ?>
	<?php if (($this->params->def('show_pagination', 2) == 1  || ($this->params->get('show_pagination') == 2)) && ($this->pagination->pagesTotal > 1)) : ?>
		<nav class="d-flex pagination-wrapper">
			<?php if ($this->params->def('show_pagination_results', 1)) : ?>
				<div class="me-auto">
					<?php echo $this->pagination->getPagesLinks(); ?>
				</div>
				<div class="pagination-counter">
					<?php echo $this->pagination->getPagesCounter(); ?>
				</div>
			<?php endif; ?>
		</nav>
	<?php endif; ?>
<?php endif; ?>
</form>
