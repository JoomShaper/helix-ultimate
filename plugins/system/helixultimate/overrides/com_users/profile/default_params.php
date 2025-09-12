<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

?>
<?php $fields = $this->form->getFieldset('params'); ?>
<?php if (count($fields)) : ?>
    <fieldset id="users-profile-custom" class="com-users-profile__params">
        <legend><?php echo Text::_('COM_USERS_SETTINGS_FIELDSET_LABEL'); ?></legend>
        <dl class="dl-horizontal">
            <?php foreach ($fields as $field) : ?>
                <?php if (!$field->hidden) : ?>
                    <dt>
                        <?php echo $field->title; ?>
                    </dt>
                    <dd>
                        <?php if (HTMLHelper::isRegistered('users.' . $field->id)) : ?>
                            <?php echo HTMLHelper::_('users.' . $field->id, $field->value); ?>
                        <?php elseif (HTMLHelper::isRegistered('users.' . $field->fieldname)) : ?>
                            <?php echo HTMLHelper::_('users.' . $field->fieldname, $field->value); ?>
                        <?php elseif (HTMLHelper::isRegistered('users.' . $field->type)) : ?>
                            <?php echo HTMLHelper::_('users.' . $field->type, $field->value); ?>
                        <?php else : ?>
                            <?php echo HTMLHelper::_('users.value', $field->value); ?>
                        <?php endif; ?>
                    </dd>
                <?php endif; ?>
            <?php endforeach; ?>
        </dl>
    </fieldset>
<?php endif; ?>
