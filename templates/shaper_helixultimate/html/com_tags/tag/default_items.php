<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers');

HTMLHelper::_('behavior.core');

// Get the user object.
$user = Factory::getUser();

// Check if user is allowed to add/edit based on tags permissions.
// Do we really have to make it so people can see unpublished tags???
$canEdit      = $user->authorise('core.edit', 'com_tags');
$canCreate    = $user->authorise('core.create', 'com_tags');
$canEditState = $user->authorise('core.edit.state', 'com_tags');
$items        = $this->items;
$n            = count($this->items);

Factory::getDocument()->addScriptDeclaration("
		var resetFilter = function() {
		document.getElementById('filter-search').value = '';
	}
");

?>
<form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm">
	<?php if ($this->params->get('show_headings') || $this->params->get('filter_field') || $this->params->get('show_pagination_limit')) : ?>
		<fieldset class="filters d-flex justify-content-between mb-3">
			<div class="input-group">
				<?php if ($this->params->get('filter_field')) : ?>
					<input type="text" name="filter-search" id="filter-search" value="<?php echo $this->escape($this->state->get('list.filter')); ?>" class="form-control" onchange="document.adminForm.submit();" title="<?php echo Text::_('COM_TAGS_FILTER_SEARCH_DESC'); ?>" placeholder="<?php echo Text::_('COM_TAGS_TITLE_FILTER_LABEL'); ?>">

					<div class="input-group-append">
						<button type="button" name="filter-search-button" title="<?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?>" onclick="document.adminForm.submit();" class="btn btn-secondary">
							<span class="fas fa-search" aria-hidden="true"></span>
						</button>

						<button type="reset" name="filter-clear-button" title="<?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?>" class="btn btn-secondary" onclick="resetFilter(); document.adminForm.submit();">
							<span class="fas fa-times" aria-hidden="true"></span>
						</button>
					</div>
				</div>
			<?php endif; ?>

			<?php if ($this->params->get('show_pagination_limit')) : ?>
				<div class="limit-box mt-3">
					<?php echo $this->pagination->getLimitBox('form-select'); ?>
				</div>
			<?php endif; ?>			

			<input type="hidden" name="filter_order" value="">
			<input type="hidden" name="filter_order_Dir" value="">
			<input type="hidden" name="limitstart" value="">
			<input type="hidden" name="task" value="">
		</fieldset>
	<?php endif; ?>

	<?php if ($this->items === false || $n === 0) : ?>
		<p><?php echo Text::_('COM_TAGS_NO_ITEMS'); ?></p>
	<?php else : ?>
		<ul class="category list-group">
			<?php foreach ($items as $i => $item) : ?>
				<?php if ($item->core_state == 0) : ?>
					<li class="list-group-item-danger">
				<?php else : ?>
					<li class="list-group-item list-group-item-action">
					<?php if (($item->type_alias === 'com_users.category') || ($item->type_alias === 'com_banners.category')) : ?>
						<h3 class="mb-0">
							<?php echo $this->escape($item->core_title); ?>
						</h3>
					<?php else : ?>
						<h3 class="mb-0">
							<a href="<?php echo Route::_(TagsHelperRoute::getItemRoute($item->content_item_id, $item->core_alias, $item->core_catid, $item->core_language, $item->type_alias, $item->router)); ?>">
								<?php echo $this->escape($item->core_title); ?>
							</a>
						</h3>
					<?php endif; ?>
				<?php endif; ?>
				<?php // Content is generated by content plugin event "onContentAfterTitle" ?>
				<?php echo $item->event->afterDisplayTitle; ?>
				<?php $images  = json_decode($item->core_images); ?>
				<?php if ($this->params->get('tag_list_show_item_image', 1) == 1 && !empty($images->image_intro)) : ?>
					<a href="<?php echo Route::_(TagsHelperRoute::getItemRoute($item->content_item_id, $item->core_alias, $item->core_catid, $item->core_language, $item->type_alias, $item->router)); ?>">
						<img src="<?php echo htmlspecialchars($images->image_intro); ?>"
							alt="<?php echo htmlspecialchars($images->image_intro_alt); ?>">
					</a>
				<?php endif; ?>
				<?php if ($this->params->get('tag_list_show_item_description', 1)) : ?>
					<?php // Content is generated by content plugin event "onContentBeforeDisplay" ?>
					<?php echo $item->event->beforeDisplayContent; ?>
					<span class="tag-body">
						<?php echo HTMLHelper::_('string.truncate', $item->core_body, $this->params->get('tag_list_item_maximum_characters')); ?>
					</span>
					<?php // Content is generated by content plugin event "onContentAfterDisplay" ?>
					<?php echo $item->event->afterDisplayContent; ?>
				<?php endif; ?>
					</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</form>
