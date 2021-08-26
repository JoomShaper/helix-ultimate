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
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('behavior.formvalidator');

?>
<div class="contact-form">
	<form id="contact-form" action="<?php echo Route::_('index.php'); ?>" method="post" class="form-validate">
		<?php foreach ($this->form->getFieldsets() as $fieldset) : ?>
			<?php if ($fieldset->name === 'captcha' && !$this->captchaEnabled) : ?>
				<?php continue; ?>
			<?php endif; ?>
			<?php $fields = $this->form->getFieldset($fieldset->name); ?>
			<?php if (count($fields)) : ?>
				<fieldset>
					<?php foreach ($fields as $field) : ?>
						<?php echo $field->renderField(); ?>
					<?php endforeach; ?>
				</fieldset>
			<?php endif; ?>
		<?php endforeach; ?>
		<div class="control-group">
			<div class="controls">
				<button class="btn btn-primary validate" type="submit"><?php echo Text::_('COM_CONTACT_CONTACT_SEND'); ?></button>
				<input type="hidden" name="option" value="com_contact">
				<input type="hidden" name="task" value="contact.submit">
				<input type="hidden" name="return" value="<?php echo $this->return_page; ?>">
				<?php if(JVERSION >= 4) { ?>
					<input type="hidden" name="id" value="<?php echo $this->item->slug; ?>">
				<?php } else { ?>
					<input type="hidden" name="id" value="<?php echo $this->contact->slug; ?>">
				<?php } ?>
				<?php echo HTMLHelper::_('form.token'); ?>
			</div>
		</div>
	</form>
</div>
