<?php

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

?>

<?php $fields = $this->form->getFieldset('params'); ?>
<?php if (count($fields)) : ?>
	<div id="users-profile-params">
		<div class="sp-fieldset">
			<div class="sp-fieldset-title">
				<?php echo JText::_('COM_USERS_SETTINGS_FIELDSET_LABEL'); ?>
			</div>
			<div class="sp-fields">
				<?php foreach ($fields as $field) : ?>
					<?php if (!$field->hidden) : ?>
					<div class="sp-field">
						<span class="sp-field-label">
							<?php echo $field->title; ?>
						</span>
						<span class="sp-field-content">
							<?php if (JHtml::isRegistered('users.' . $field->id)) : ?>
								<?php echo JHtml::_('users.' . $field->id, $field->value); ?>
							<?php elseif (JHtml::isRegistered('users.' . $field->fieldname)) : ?>
								<?php echo JHtml::_('users.' . $field->fieldname, $field->value); ?>
							<?php elseif (JHtml::isRegistered('users.' . $field->type)) : ?>
								<?php echo JHtml::_('users.' . $field->type, $field->value); ?>
							<?php else : ?>
								<?php echo JHtml::_('users.value', $field->value); ?>
							<?php endif; ?>
						</span>
					</div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
<?php endif; ?>
