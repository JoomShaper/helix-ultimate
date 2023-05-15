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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

if (JVERSION < 4)
{
	if ($this->params->get('show_advanced', 1) || $this->params->get('show_autosuggest', 1))
	{
		HTMLHelper::_('jquery.framework');
	
		$script = "
		jQuery(function() {";
	
		if ($this->params->get('show_advanced', 1))
		{
			/*
			* This segment of code disables select boxes that have no value when the
			* form is submitted so that the URL doesn't get blown up with null values.
			*/
			$script .= "
			jQuery('#finder-search').on('submit', function(e){
				e.stopPropagation();
				// Disable select boxes with no value selected.
				jQuery('#advancedSearch').find('select').each(function(index, el) {
					var el = jQuery(el);
					if(!el.val()){
						el.attr('disabled', 'disabled');
					}
				});
			});";
		}
	
		/*
		* This segment of code sets up the autocompleter.
		*/
		if ($this->params->get('show_autosuggest', 1))
		{
			HTMLHelper::_('script', 'jui/jquery.autocomplete.min.js', array('version' => 'auto', 'relative' => true));
	
			$script .= "
			var suggest = jQuery('#q').autocomplete({
				serviceUrl: '" . Route::_('index.php?option=com_finder&task=suggestions.suggest&format=json&tmpl=component') . "',
				paramName: 'q',
				minChars: 1,
				maxHeight: 400,
				width: 300,
				zIndex: 9999,
				deferRequestBy: 500
			});";
		}
	
		$script .= "
		});";
	
		Factory::getDocument()->addScriptDeclaration($script);
	}
}
else
{
	HTMLHelper::_('jquery.framework');
	
	$script = "
	jQuery(function() {";

	$script .= "
		jQuery('.ads').on('click', function(e){
			if(jQuery('#advancedSearch').hasClass('hide')) {
				jQuery('#advancedSearch').removeClass('hide');
				jQuery('#advancedSearch').slideDown(300);
				
			} else {
				jQuery('#advancedSearch').addClass('hide');
				jQuery('#advancedSearch').slideUp(300);
			}
		});";

	$script .= "
	});";

	Factory::getDocument()->addScriptDeclaration($script);
	
	if ($this->params->get('show_autosuggest', 1))
	{
		$this->document->getWebAssetManager()->usePreset('awesomplete');
		$this->document->addScriptOptions('finder-search', array('url' => Route::_('index.php?option=com_finder&task=suggestions.suggest&format=json&tmpl=component')));
	}
}
?>

<form action="<?php echo Route::_($this->query->toUri()); ?>" id="finder-search" method="get" class="js-finder-searchform">
	<?php echo $this->getFields(); ?>

	<?php //DISABLED UNTIL WEIRD VALUES CAN BE TRACKED DOWN. ?>
	<?php if (false && $this->state->get('list.ordering') !== 'relevance_dsc') : ?>
		<input type="hidden" name="o" value="<?php echo $this->escape($this->state->get('list.ordering')); ?>">
	<?php endif; ?>
	<fieldset class="word mb-3">
		<label for="q" class="form-label">
			<?php echo Text::_('COM_FINDER_SEARCH_TERMS'); ?>
		</label>
		<div class="input-group">
			<input type="text" id="q" name="q" class="js-finder-search-query form-control" value="<?php echo $this->escape($this->query->input); ?>">
			<?php if ($this->escape($this->query->input) != '' || $this->params->get('allow_empty_query')) : ?>
				<button name="Search" type="submit" class="btn btn-primary">
					<span class="fas fa-search icon-white" aria-hidden="true"></span>
					<?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?>
				</button>
			<?php else : ?>
				<button name="Search" type="submit" class="btn btn-primary disabled">
					<span class="fas fa-search icon-white" aria-hidden="true"></span>
					<?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?>
				</button>
			<?php endif; ?>
			<?php if ($this->params->get('show_advanced', 1)) : ?>
				<?php if (JVERSION < 4) : ?>
					<a class="btn btn-secondary ads" data-bs-toggle="collapse" href="#advancedSearch" role="button" aria-expanded="false" aria-controls="advancedSearch">
						<span class="fas fa-search-plus" aria-hidden="true"></span>
						<?php echo Text::_('COM_FINDER_ADVANCED_SEARCH_TOGGLE'); ?>
					</a>
				<?php else : ?>
					<a class="btn btn-secondary ads" role="button">
						<span class="fas fa-search-plus" aria-hidden="true"></span>
						<?php echo Text::_('COM_FINDER_ADVANCED_SEARCH_TOGGLE'); ?>
					</a>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</fieldset>

	<?php if ($this->params->get('show_advanced', 1)) : ?>
		<?php if (JVERSION < 4) : ?>
			<div id="advancedSearch" class="js-finder-advanced collapse<?php if ($this->params->get('expand_advanced', 0)) echo ' show'; ?>">
				<?php if ($this->params->get('show_advanced_tips', 1)) : ?>
					<div class="card card-outline-secondary mb-3">
						<div class="card-body">
							<?php echo Text::_('COM_FINDER_ADVANCED_TIPS'); ?>
						</div>
					</div>
				<?php endif; ?>
				<div id="finder-filter-window">
					<?php echo HTMLHelper::_('filter.select', $this->query, $this->params); ?>
				</div>
			</div>
		<?php else : ?>
			<div id="advancedSearch" class="js-finder-advanced<?php echo ($this->params->get('expand_advanced', 0)) ? '' : ' hide'; ?>" style="<?php if(!$this->params->get('expand_advanced', 0)) { echo 'display:none'; }?>">
				<?php if ($this->params->get('show_advanced_tips', 1)) : ?>
					<div class="card card-outline-secondary mb-3">
						<div class="card-body">
							<?php echo Text::_('COM_FINDER_ADVANCED_TIPS'); ?>
						</div>
					</div>
				<?php endif; ?>
				<div id="finder-filter-window">
					<?php echo HTMLHelper::_('filter.select', $this->query, $this->params); ?>
				</div>
			</div>
		<?php endif; ?>
	<?php endif; ?>
</form>