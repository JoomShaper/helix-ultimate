<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('JPATH_BASE') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

$buttons = $displayData;
?>
<div id="editor-xtd-buttons" role="toolbar" aria-label="<?php echo Text::_('JTOOLBAR'); ?>">
	<?php if ($buttons) : ?>
		<?php foreach ($buttons as $button) : ?>
			<?php echo $this->sublayout('button', $button); ?>
		<?php endforeach; ?>
		<?php foreach ($buttons as $button) : ?>
			<?php echo LayoutHelper::render('joomla.editors.buttons.modal', $button); ?>
		<?php endforeach; ?>
	<?php endif; ?>
</div>
