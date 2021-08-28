<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

?>

<div id="users-profile-core">
	<div class="d-flex mb-3">
		<div class="me-auto">
			<strong><?php echo Text::_('COM_USERS_PROFILE_CORE_LEGEND'); ?></strong>
		</div>
		<div>
			<?php if (Factory::getUser()->id == $this->data->id): ?>
				<a href="<?php echo Route::_('index.php?option=com_users&task=profile.edit&user_id=' . (int) $this->data->id); ?>">
					<span class="fas fa-user-edit" aria-hidden="true"></span> <?php echo Text::_('COM_USERS_EDIT_PROFILE'); ?>
				</a>
			<?php endif;?>
		</div>
	</div>
	<ul class="list-group">
		<li class="list-group-item">
			<strong><?php echo Text::_('COM_USERS_PROFILE_NAME_LABEL'); ?></strong>:
			<?php echo $this->data->name; ?>
		</li>
		<li class="list-group-item">
			<strong><?php echo Text::_('COM_USERS_PROFILE_USERNAME_LABEL'); ?></strong>:
			<?php echo htmlspecialchars($this->data->username, ENT_COMPAT, 'UTF-8'); ?>
		</li>
		<li class="list-group-item">
			<strong><?php echo Text::_('COM_USERS_PROFILE_REGISTERED_DATE_LABEL'); ?></strong>:
			<?php echo HTMLHelper::_('date', $this->data->registerDate); ?>
		</li>
		<li class="list-group-item">
			<strong><?php echo Text::_('COM_USERS_PROFILE_LAST_VISITED_DATE_LABEL'); ?></strong>:
			<?php if ($this->data->lastvisitDate != $this->db->getNullDate()): ?>
				<?php echo HTMLHelper::_('date', $this->data->lastvisitDate); ?>
			<?php else: ?>
				<?php echo Text::_('COM_USERS_PROFILE_NEVER_VISITED'); ?>
			<?php endif;?>
		</li>
	</ul>
</div>
