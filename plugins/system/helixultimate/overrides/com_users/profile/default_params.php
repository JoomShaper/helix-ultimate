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

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
?>

<?php $fields = $this->form->getFieldset('params'); ?>
<?php if (count($fields)) : ?>
	<div id="users-profile-params">
		<div class="mb-3">
			<strong><?php echo Text::_('COM_USERS_SETTINGS_FIELDSET_LABEL'); ?></strong>
		</div>
		<ul class="list-group">
			<?php foreach ($fields as $field) : ?>
					<?php if (!$field->hidden) : ?>
						<li class="list-group-item">
							<strong><?php echo $field->title; ?></strong>:
							<?php if (HTMLHelper::isRegistered('users.' . $field->id)) : ?>
								<?php echo HTMLHelper::_('users.' . $field->id, $field->value); ?>
							<?php elseif (HTMLHelper::isRegistered('users.' . $field->fieldname)) : ?>
								<?php echo HTMLHelper::_('users.' . $field->fieldname, $field->value); ?>
							<?php elseif (HTMLHelper::isRegistered('users.' . $field->type)) : ?>
								<?php echo HTMLHelper::_('users.' . $field->type, $field->value); ?>
							<?php else : ?>
								<?php echo HTMLHelper::_('users.value', $field->value); ?>
							<?php endif; ?>
						</li>
					<?php endif; ?>
			<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>
