<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;


if(JVERSION < 4)
{
	HTMLHelper::_('behavior.core');
	HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
	
	HTMLHelper::_('stylesheet', 'com_finder/finder.css', array('version' => 'auto', 'relative' => true));
	HTMLHelper::_('stylesheet', 'vendor/awesomplete/awesomplete.css', array('version' => 'auto', 'relative' => true));
	
	Text::script('MOD_FINDER_SEARCH_VALUE', true);
	
	HTMLHelper::_('script', 'com_finder/finder.js', array('version' => 'auto', 'relative' => true));
}
else
{
	$this->document->getWebAssetManager()
		->useStyle('com_finder.finder')
		->useScript('com_finder.finder');
}
?>
<div class="finder">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<h1>
			<?php if ($this ->escape($this->params->get('page_heading'))) : ?>
				<?php echo $this->escape($this->params->get('page_heading')); ?>
			<?php else : ?>
				<?php echo $this->escape($this->params->get('page_title')); ?>
			<?php endif; ?>
		</h1>
	<?php endif; ?>
	<?php if ($this->params->get('show_search_form', 1)) : ?>
		<div id="search-form">
			<?php echo $this->loadTemplate('form'); ?>
		</div>
	<?php endif; ?>
	<?php // Load the search results layout if we are performing a search. ?>
	<?php if ($this->query->search === true) : ?>
		<div id="search-results">
			<?php echo $this->loadTemplate('results'); ?>
		</div>
	<?php endif; ?>
</div>
