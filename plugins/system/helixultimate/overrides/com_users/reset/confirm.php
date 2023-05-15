<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die();

use HelixUltimate\Framework\Platform\Settings;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('behavior.formvalidator');
?>
<div class="reset-confirm<?php echo $this->pageclass_sfx; ?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<div class="page-header">
			<h1>
				<?php echo $this->escape($this->params->get('page_heading')); ?>
			</h1>
		</div>
	<?php endif; ?>

	<form action="<?php echo Route::_('index.php?option=com_users&task=reset.confirm'); ?>" method="post" class="form-validate form-horizontal well">
		<?php foreach ($this->form->getFieldsets() as $fieldset) : ?>
			<fieldset>
				<p><?php echo Text::_($fieldset->label); ?></p>
				<?php foreach ($this->form->getFieldset($fieldset->name) as $name => $field) : ?>
					<?php
						$showon = $field->getAttribute('showon');
						$attribs = '';
						if ($showon) 
						{
							$attribs .= ' data-showon=\'' . json_encode(Settings::parseShowOnConditions($showon, $field->formControl)) . '\'';
						}
						// Enable disable on
						$enableOn = $field->getAttribute('enableon', '');
						if ($enableOn)
						{
							$attribs .= ' data-enableon="' . $enableOn . '"';
						}
					?>
					<div class="control-group" <?php echo $attribs; ?>>
						<div class="control-label">
							<?php echo $field->label; ?>
						</div>
						<div class="controls">
							<?php echo $field->input; ?>
							</div>
						</div>
				<?php endforeach; ?>
			</fieldset>
		<?php endforeach; ?>

		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn-primary validate"><?php echo Text::_('JSUBMIT'); ?></button>
			</div>
		</div>
		<?php echo HTMLHelper::_('form.token'); ?>
	</form>
</div>
