<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;

$app->getDocument()->getWebAssetManager()
    ->useScript('core')
    ->useScript('keepalive')
    ->useScript('field.passwordview');

Text::script('JSHOWPASSWORD');
Text::script('JHIDEPASSWORD');
?>
<form id="login-form-<?php echo $module->id; ?>"
    class="mod-login form-validate"
    action="<?php echo Route::_('index.php', true, (int) $params->get('usesecure')); ?>"
    method="post">

    <?php if ($params->get('pretext')) : ?>
        <div class="mod-login__pretext pretext mb-2">
            <p><?php echo $params->get('pretext'); ?></p>
        </div>
    <?php endif; ?>

    <div class="mod-login__userdata userdata ">
        <div class="mod-login__username form-group mb-3">
            <label for="modlgn-username-<?php echo $module->id; ?>">
                <?php echo Text::_('MOD_LOGIN_VALUE_USERNAME'); ?>
            </label>
            <div class="input-group">

                <input
                    id="modlgn-username-<?php echo $module->id; ?>"
                    type="text"
                    name="username"
                    class="form-control"
                    required="required"
                    autocomplete="username">
            </div>
        </div>
        <div class="mod-login__password form-group mb-3">
            <label for="modlgn-passwd-<?php echo $module->id; ?>">
                <?php echo Text::_('JGLOBAL_PASSWORD'); ?>
            </label>
            <div class="input-group">

                <input id="modlgn-passwd-<?php echo $module->id; ?>"
                    type="password"
                    name="password"
                    class="form-control input-full"
                    required="required"
                    autocomplete="current-password">
                <button type="button" class="btn btn-secondary input-password-toggle">
                    <span class="icon-eye icon-fw" aria-hidden="true"></span>
                    <span class="visually-hidden"><?php echo Text::_('JSHOWPASSWORD'); ?></span>
                </button>
            </div>
        </div>

        <!-- Remember me -->
        <?php if (PluginHelper::isEnabled('system', 'remember')) : ?>
            <div class="mod-login__remember form-group mb-3">
                <div id="form-login-remember-<?php echo $module->id; ?>" class="form-check">
                    <input type="checkbox"
                        name="remember"
                        class="form-check-input"
                        value="yes"
                        id="form-login-input-remember-<?php echo $module->id; ?>">
                    <label class="form-check-label"
                        for="form-login-input-remember-<?php echo $module->id; ?>">
                        <?php echo Text::_('MOD_LOGIN_REMEMBER_ME'); ?>
                    </label>
                </div>
            </div>
        <?php endif; ?>

        <?php foreach ($extraButtons as $button) :
            $dataAttributeKeys = array_filter(array_keys($button), function ($key) {
                return substr($key, 0, 5) == 'data-';
            });
        ?>
            <div class="mod-login__submit form-group mb-3">
                <button type="button"
                    class="btn btn-secondary btn-lg w-100 <?php echo $button['class'] ?? '' ?>"
                    <?php foreach ($dataAttributeKeys as $key) : ?>
                    <?php echo $key ?>="<?php echo $button[$key] ?>"
                    <?php endforeach; ?>
                    <?php if ($button['onclick']) : ?>
                    onclick="<?php echo $button['onclick'] ?>"
                    <?php endif; ?>
                    title="<?php echo Text::_($button['label']) ?>"
                    id="<?php echo $button['id'] ?>">
                    <?php if (!empty($button['icon'])) : ?>
                        <span class="<?php echo $button['icon'] ?>"></span>
                    <?php elseif (!empty($button['image'])) : ?>
                        <?php echo $button['image']; ?>
                    <?php elseif (!empty($button['svg'])) : ?>
                        <?php echo $button['svg']; ?>
                    <?php endif; ?>
                    <?php echo Text::_($button['label']) ?>
                </button>
            </div>
        <?php endforeach; ?>

        <div class="mod-login__submit form-group mb-3">
            <button type="submit" name="Submit" id="btn-login-submit" class="btn btn-primary w-100 btn-lg"><?php echo Text::_('JLOGIN'); ?></button>
        </div>

        <?php $usersConfig = ComponentHelper::getParams('com_users'); ?>
        <div class="mod-login__options  list-group">
            <a class="mod-login__reset list-group-item" href="<?php echo Route::_('index.php?option=com_users&view=reset'); ?>">
                <?php echo Text::_('MOD_LOGIN_FORGOT_YOUR_PASSWORD'); ?>
            </a>
            <a class="mod-login__remind list-group-item" href="<?php echo Route::_('index.php?option=com_users&view=remind'); ?>">
                <?php echo Text::_('MOD_LOGIN_FORGOT_YOUR_USERNAME'); ?>
            </a>
            <?php if ($usersConfig->get('allowUserRegistration')) : ?>
                <a class="mod-login__register list-group-item" href="<?php echo Route::_('index.php?option=com_users&view=registration'); ?>">
                    <?php echo Text::_('MOD_LOGIN_REGISTER'); ?>	               
                    <span class="icon-register" aria-hidden="true"></span>
                </a>
            <?php endif; ?>
        </div>
        <input type="hidden" name="option" value="com_users">
        <input type="hidden" name="task" value="user.login">
        <input type="hidden" name="return" value="<?php echo $return; ?>">
        <?php echo HTMLHelper::_('form.token'); ?>
    </div>
    <?php if ($params->get('posttext')) : ?>
        <div class="mod-login__posttext posttext">
            <p><?php echo $params->get('posttext'); ?></p>
        </div>
    <?php endif; ?>
</form>
