<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;

// Note that there are certain parts of this layout used only when there is exactly one tag.
HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers');
$description      = $this->params->get('all_tags_description');
$descriptionImage = $this->params->get('all_tags_description_image');

?>
<div class="tag-category<?php echo $this->pageclass_sfx; ?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<h1>
			<?php echo $this->escape($this->params->get('page_heading')); ?>
		</h1>
	<?php endif; ?>
	<?php if ($this->params->get('all_tags_show_description_image') && !empty($descriptionImage)) : ?>
		<?php $alt = empty($this->params->get('all_tags_description_image_alt')) && empty($this->params->get('all_tags_description_image_alt_empty'))
			? ''
			: 'alt="' . htmlspecialchars($this->params->get('all_tags_description_image_alt') ?? "", ENT_COMPAT, 'UTF-8') . '"'; ?>
		<div>
			<img src="<?php echo htmlspecialchars($descriptionImage ?? "", ENT_QUOTES, 'UTF-8'); ?>" <?php echo $alt; ?>>
		</div>
	<?php endif; ?>
	<?php if (!empty($description)) : ?>
		<div>
			<?php echo $description; ?>
		</div>
	<?php endif; ?>
	<?php echo $this->loadTemplate('items'); ?>
</div>
