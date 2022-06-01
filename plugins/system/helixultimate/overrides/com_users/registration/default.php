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
<div class="registration<?php echo $this->pageclass_sfx; ?>">
	<div class="row justify-content-center">
		<div class="col-lg-9 col-xl-6">
			<?php if ($this->params->get('show_page_heading')) : ?>
				<div class="page-header">
					<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
				</div>
			<?php endif; ?>

			<form id="member-registration" action="<?php echo Route::_('index.php?option=com_users&task=registration.register'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
				<?php foreach ($this->form->getFieldsets() as $fieldset) : ?>
					<?php $fields = $this->form->getFieldset($fieldset->name); ?>
					<?php if (count($fields)) : ?>
						<fieldset>
							<?php if (isset($fieldset->label)) : ?>
								<legend><?php echo Text::_($fieldset->label); ?></legend>
							<?php endif; ?>
							<div class="row">
								<?php foreach ($fields as $field) : ?>
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
									<?php if ($field->hidden) : ?>
										<?php echo $field->input; ?>
									<?php else : ?>
										<?php $fieldName = $field->getAttribute('name'); ?>
										<?php if(($fieldName == 'password1') || ($fieldName == 'password2') || ($fieldName == 'email1') || ($fieldName == 'email2')) : ?>
											<div class="col-lg-6" <?php echo $attribs; ?>>
										<?php else: ?>
											<div class="col-xl-12" <?php echo $attribs; ?>>
										<?php endif; ?>
										<div class="mb-3">
											<?php echo $field->label; ?>
											<?php if (!$field->required && $field->type !== 'Spacer') : ?>
												<span class="optional"><?php echo Text::_('COM_USERS_OPTIONAL'); ?></span>
											<?php endif; ?>
											<?php echo $field->input; ?>
										</div>
									</div>
								<?php endif; ?>
							<?php endforeach; ?>
						</div>
					</fieldset>
				<?php endif; ?>
			<?php endforeach; ?>
			<div>
				<button type="submit" class="btn btn-primary validate"><?php echo Text::_('JREGISTER'); ?></button>
				<a class="btn btn-secondary" href="<?php echo Route::_(''); ?>" title="<?php echo Text::_('JCANCEL'); ?>"><?php echo Text::_('JCANCEL'); ?></a>
				<input type="hidden" name="option" value="com_users">
				<input type="hidden" name="task" value="registration.register">
			</div>
			<?php echo HTMLHelper::_('form.token'); ?>
			</form>
		</div>
	</div>
</div>
