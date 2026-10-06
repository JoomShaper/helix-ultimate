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

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->getDocument()->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate');

?>
<div class="com-users-reset-confirm reset-confirm">
    <?php if ($this->params->get('show_page_heading')) : ?>
        <div class="page-header">
            <h1>
                <?php echo $this->escape($this->params->get('page_heading')); ?>
            </h1>
        </div>
    <?php endif; ?>
    <form action="<?php echo Route::_('index.php?option=com_users&task=reset.confirm'); ?>" method="post" class="com-users-reset-confirm__form form-validate form-horizontal well">
        <?php foreach ($this->form->getFieldsets() as $fieldset) : ?>
            <fieldset>
                <?php $legend = $fieldset->legend ?? $fieldset->label ?? null; ?>
                <?php if ($legend !== null) : ?>
                    <legend><?php echo Text::_($legend); ?></legend>
                <?php endif; ?>
                <?php if (isset($fieldset->description)) : ?>
                    <p><?php echo Text::_($fieldset->description); ?></p>
                <?php endif; ?>
                <?php echo $this->form->renderFieldset($fieldset->name); ?>
            </fieldset>
        <?php endforeach; ?>
        <div class="com-users-reset-confirm__submit control-group">
            <div class="controls">
                <button type="submit" class="btn btn-primary validate">
                    <?php echo Text::_('JSUBMIT'); ?>
                </button>
            </div>
        </div>
        <?php if (version_compare(JVERSION, '6.0.0', '>=')) : ?>
            <?php echo $this->form->renderControlFields(); ?>
        <?php else : ?>
            <?php echo HTMLHelper::_('form.token'); ?>
        <?php endif; ?>
    </form>
</div>
