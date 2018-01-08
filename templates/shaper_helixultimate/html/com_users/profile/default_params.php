<?php
/**
* @package     Joomla.Site
* @subpackage  com_users
*
* @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

?>
<?php $fields = $this->form->getFieldset('params'); ?>
<?php if (count($fields)) : ?>
	<div class="card" id="users-profile-custom">
		<div class="card-header">
			<strong><?php echo JText::_('COM_USERS_SETTINGS_FIELDSET_LABEL'); ?></strong>
		</div>
		<div class="list-group list-group-flush">
			<?php foreach ($fields as $field) : ?>
				<?php if (!$field->hidden) : ?>
					<div class="list-group-item">
						<div class="mb-1">
							<strong><?php echo $field->title; ?></strong>
						</div>
						<?php if (JHtml::isRegistered('users.' . $field->id)) : ?>
							<?php echo JHtml::_('users.' . $field->id, $field->value); ?>
						<?php elseif (JHtml::isRegistered('users.' . $field->fieldname)) : ?>
							<?php echo JHtml::_('users.' . $field->fieldname, $field->value); ?>
						<?php elseif (JHtml::isRegistered('users.' . $field->type)) : ?>
							<?php echo JHtml::_('users.' . $field->type, $field->value); ?>
						<?php else : ?>
							<?php echo JHtml::_('users.value', $field->value); ?>
						<?php endif; ?>
						<?php echo $this->data->name; ?>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
	</div>
<?php endif; ?>
