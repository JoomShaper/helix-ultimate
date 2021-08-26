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
use Joomla\CMS\Layout\LayoutHelper;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers');
// HTMLHelper::_('behavior.caption');

Factory::getDocument()->addScriptDeclaration("
jQuery(function($) {
	$('.categories-list').find('[id^=category-btn-]').each(function(index, btn) {
		var btn = $(btn);
		btn.on('click', function() {
			btn.find('span').toggleClass('icon-plus');
			btn.find('span').toggleClass('icon-minus');
		});
	});
});");
?>
<div class="categories-list<?php echo $this->pageclass_sfx; ?> list-group">
	<?php
		echo LayoutHelper::render('joomla.content.categories_default', $this);
		echo $this->loadTemplate('items');
	?>
</div>
