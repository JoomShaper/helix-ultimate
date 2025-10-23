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
use Joomla\CMS\Router\Route;

/** @var Joomla\Component\Users\Site\View\Profile\HtmlView $this */

HTMLHelper::_('bootstrap.tooltip', '.hasTooltip');

// Load user_profile plugin language
$lang = $this->getLanguage();
$lang->load('plg_user_profile', JPATH_ADMINISTRATOR);

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->getDocument()->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate');

?>
<div class="com-users-profile__edit profile-edit">
    <?php if ($this->params->get('show_page_heading')) : ?>
        <div class="page-header">
            <h1>
                <?php echo $this->escape($this->params->get('page_heading')); ?>
            </h1>
        </div>
    <?php endif; ?>

    <form id="member-profile" action="<?php echo Route::_('index.php'); ?>" method="post" class="com-users-profile__edit-form form-validate form-horizontal well" enctype="multipart/form-data">
        <?php // Iterate through the form fieldsets and display each one. ?>
        <?php foreach ($this->form->getFieldsets() as $group => $fieldset) : ?>
            <?php $fields = $this->form->getFieldset($group); ?>
            <?php if (count($fields)) : ?>
                <fieldset>
                    <?php // If the fieldset has a label set, display it as the legend. ?>
                    <?php if (isset($fieldset->label)) : ?>
                        <legend>
                            <?php echo Text::_($fieldset->label); ?>
                        </legend>
                    <?php endif; ?>
                    <?php if (isset($fieldset->description) && trim($fieldset->description)) : ?>
                        <p>
                            <?php echo $this->escape(Text::_($fieldset->description)); ?>
                        </p>
                    <?php endif; ?>
                    <?php // Iterate through the fields in the set and display them. ?>
                    <?php foreach ($fields as $field) : ?>
                        <?php echo $field->renderField(); ?>
                    <?php endforeach; ?>
                </fieldset>
            <?php endif; ?>
        <?php endforeach; ?>

        <?php if ($this->mfaConfigurationUI) : ?>
            <fieldset class="com-users-profile__multifactor">
                <legend><?php echo Text::_('COM_USERS_PROFILE_MULTIFACTOR_AUTH'); ?></legend>
                <?php echo $this->mfaConfigurationUI ?>
            </fieldset>
        <?php endif; ?>

        <div class="com-users-profile__edit-submit control-group">
            <div class="mb-3">
                <button type="submit" class="btn btn-primary validate" name="task" value="profile.save">
                    <span class="icon-check" aria-hidden="true"></span>
                    <?php echo Text::_('JSAVE'); ?>
                </button>
                <button type="submit" class="btn btn-danger" name="task" value="profile.cancel" formnovalidate>
                    <span class="icon-times" aria-hidden="true"></span>
                    <?php echo Text::_('JCANCEL'); ?>
                </button>
                <input type="hidden" name="option" value="com_users">
            </div>
        </div>

        <?php echo $this->form->renderControlFields(); ?>
    </form>
</div>
