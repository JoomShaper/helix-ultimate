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
JHtml::register('users.spacer', array('JHtmlUsers', 'spacer'));

$fieldsets = $this->form->getFieldsets();

if (isset($fieldsets['core']))
{
	unset($fieldsets['core']);
}

if (isset($fieldsets['params']))
{
	unset($fieldsets['params']);
}

$tmp          = isset($this->data->jcfields) ? $this->data->jcfields : array();
$customFields = array();

foreach ($tmp as $customField)
{
	$customFields[$customField->name] = $customField;
}
?>
<?php foreach ($fieldsets as $group => $fieldset) : ?>
	<?php $fields = $this->form->getFieldset($group); ?>
	<?php if (count($fields)) : ?>
		<div class="users-profile-custom-<?php echo $group; ?>" id="users-profile-custom-<?php echo $group; ?>">
			<div class="sp-fieldset">
				<div class="sp-fieldset-title">
					<?php if (isset($fieldset->label) && ($legend = trim(JText::_($fieldset->label))) !== '') : ?>
						<?php echo $legend; ?>
					<?php endif; ?>
					<?php if (isset($fieldset->description) && trim($fieldset->description)) : ?>
						<div><?php echo $this->escape(JText::_($fieldset->description)); ?></span>
					<?php endif; ?>
				</div>

				<div class="sp-fields">
				<?php foreach ($fields as $field) : ?>
						<?php if (!$field->hidden && $field->type !== 'Spacer') : ?>
				<div class="sp-field">
						<span class="sp-field-label">
							<?php echo $field->title; ?>
						</span>
						<span class="sp-field-content">
						<?php if (key_exists($field->fieldname, $customFields)) : ?>
						<?php echo $customFields[$field->fieldname]->value ?: JText::_('COM_USERS_PROFILE_VALUE_NOT_FOUND'); ?>
					<?php elseif (JHtml::isRegistered('users.' . $field->id)) : ?>
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
<?php endforeach; ?>
