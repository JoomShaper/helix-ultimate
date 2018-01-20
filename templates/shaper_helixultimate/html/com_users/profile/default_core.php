<?php

defined('_JEXEC') or die;

?>

<div id="users-profile-core">
	<div class="sp-fieldset">
		<div class="sp-fieldset-title d-flex">
			<div class="mr-auto">
				<?php echo JText::_('COM_USERS_PROFILE_CORE_LEGEND'); ?>
			</div>
			<div>
				<?php if (JFactory::getUser()->id == $this->data->id): ?>
					<a href="<?php echo JRoute::_('index.php?option=com_users&task=profile.edit&user_id=' . (int) $this->data->id); ?>">
						<span class="fa fa-user"></span> <?php echo JText::_('COM_USERS_EDIT_PROFILE'); ?>
					</a>
				<?php endif;?>
			</div>
		</div>
		<div class="sp-fields">
			<div class="sp-field">
				<span class="sp-field-label">
					<?php echo JText::_('COM_USERS_PROFILE_NAME_LABEL'); ?>
				</span>
				<span class="sp-field-content">
					<?php echo $this->data->name; ?>
				</span>
			</div>
			<div class="sp-field">
				<span class="sp-field-label">
					<?php echo JText::_('COM_USERS_PROFILE_USERNAME_LABEL'); ?>
				</span>
				<span class="sp-field-content">
					<?php echo htmlspecialchars($this->data->username, ENT_COMPAT, 'UTF-8'); ?>
				</span>
			</div>
			<div class="sp-field">
				<span class="sp-field-label">
					<?php echo JText::_('COM_USERS_PROFILE_REGISTERED_DATE_LABEL'); ?>
				</span>
				<span class="sp-field-content">
					<?php echo JHtml::_('date', $this->data->registerDate); ?>
				</span>
			</div>
			<div class="sp-field">
				<span class="sp-field-label"><?php echo JText::_('COM_USERS_PROFILE_LAST_VISITED_DATE_LABEL'); ?></span>
				<span class="sp-field-content">
				<?php if ($this->data->lastvisitDate != $this->db->getNullDate()): ?>
					<?php echo JHtml::_('date', $this->data->lastvisitDate); ?>
				<?php else: ?>
					<?php echo JText::_('COM_USERS_PROFILE_NEVER_VISITED'); ?>
				<?php endif;?>
				</span>
			</div>
		</div>
	</div>
</div>
