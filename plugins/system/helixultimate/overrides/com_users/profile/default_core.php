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
<fieldset id="users-profile-core" class="com-users-profile__core">
    <legend>
        <?php echo Text::_('COM_USERS_PROFILE_CORE_LEGEND'); ?>
    </legend>
    <dl class="dl-horizontal">
        <dt>
            <?php echo Text::_('COM_USERS_PROFILE_NAME_LABEL'); ?>
        </dt>
        <dd>
            <?php echo $this->escape($this->data->name); ?>
        </dd>
        <dt>
            <?php echo Text::_('COM_USERS_PROFILE_USERNAME_LABEL'); ?>
        </dt>
        <dd>
            <?php echo $this->escape($this->data->username); ?>
        </dd>
        <dt>
            <?php echo Text::_('COM_USERS_PROFILE_REGISTERED_DATE_LABEL'); ?>
        </dt>
        <dd>
            <?php echo HTMLHelper::_('date', $this->data->registerDate, Text::_('DATE_FORMAT_LC1')); ?>
        </dd>
        <dt>
            <?php echo Text::_('COM_USERS_PROFILE_LAST_VISITED_DATE_LABEL'); ?>
        </dt>
        <?php if ($this->data->lastvisitDate !== null) : ?>
            <dd>
                <?php echo HTMLHelper::_('date', $this->data->lastvisitDate, Text::_('DATE_FORMAT_LC1')); ?>
            </dd>
        <?php else : ?>
            <dd>
                <?php echo Text::_('COM_USERS_PROFILE_NEVER_VISITED'); ?>
            </dd>
        <?php endif; ?>
    </dl>
</fieldset>
