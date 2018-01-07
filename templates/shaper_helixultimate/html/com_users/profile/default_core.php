<?php
/**
* @package     Joomla.Site
* @subpackage  com_users
*
* @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

?>

<div class="card" id="users-profile-core">
	<div class="card-header">
		<strong><?php echo JText::_('COM_USERS_PROFILE_CORE_LEGEND'); ?></strong>
	</div>
	<div class="list-group list-group-flush">
		<div class="list-group-item">
			<div class="mb-1">
				<strong><?php echo JText::_('COM_USERS_PROFILE_NAME_LABEL'); ?></strong>
			</div>
			<?php echo $this->data->name; ?>
		</div>

		<div class="list-group-item">
			<div class="mb-1">
				<strong><?php echo JText::_('COM_USERS_PROFILE_USERNAME_LABEL'); ?></strong>
			</div>
			<?php echo htmlspecialchars($this->data->username, ENT_COMPAT, 'UTF-8'); ?>
		</div>

		<div class="list-group-item">
			<div class="mb-1">
				<strong><?php echo JText::_('COM_USERS_PROFILE_REGISTERED_DATE_LABEL'); ?></strong>
			</div>
			<?php echo JHtml::_('date', $this->data->registerDate); ?>
		</div>

		<div class="list-group-item">
			<div class="mb-1">
				<strong><?php echo JText::_('COM_USERS_PROFILE_LAST_VISITED_DATE_LABEL'); ?></strong>
			</div>
			<?php if ($this->data->lastvisitDate != $this->db->getNullDate()) : ?>
				<?php echo JHtml::_('date', $this->data->lastvisitDate); ?>
			<?php else : ?>
				<?php echo JText::_('COM_USERS_PROFILE_NEVER_VISITED'); ?>
			<?php endif; ?>
		</div>
	</div>
	<div class="card-body">
		<?php if (JFactory::getUser()->id == $this->data->id) : ?>
			<a class="btn btn-primary btn-lg" href="<?php echo JRoute::_('index.php?option=com_users&task=profile.edit&user_id=' . (int) $this->data->id); ?>">
				<span class="fa fa-user"></span> <?php echo JText::_('COM_USERS_EDIT_PROFILE'); ?>
			</a>
		<?php endif; ?>
	</div>
</div>
