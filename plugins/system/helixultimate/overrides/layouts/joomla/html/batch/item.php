<?php

/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

extract($displayData);

/**
 * Layout variables
 * -----------------
 * @var   string  $extension  The extension name
 */

// Create the copy/move options.
$options = [
    HTMLHelper::_('select.option', 'c', Text::_('JLIB_HTML_BATCH_COPY')),
    HTMLHelper::_('select.option', 'm', Text::_('JLIB_HTML_BATCH_MOVE'))
];

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->useScript('joomla.batch-copymove');

?>
<label id="batch-choose-action-lbl" for="batch-category-id">
    <?php echo Text::_('JLIB_HTML_BATCH_MENU_LABEL'); ?>
</label>
<div id="batch-choose-action" class="control-group">
    <select name="batch[category_id]" class="form-select" id="batch-category-id">
        <option value=""><?php echo Text::_('JLIB_HTML_BATCH_NO_CATEGORY'); ?></option>
        <?php if (isset($addRoot) && $addRoot) : ?>
            <?php echo HTMLHelper::_('select.options', HTMLHelper::_('category.categories', $extension)); ?>
        <?php else : ?>
            <?php echo HTMLHelper::_('select.options', HTMLHelper::_('category.options', $extension)); ?>
        <?php endif; ?>
    </select>
</div>
<div id="batch-copy-move" class="control-group radio">
    <fieldset id="batch-copy-move-id">
        <legend>
            <?php echo Text::_('JLIB_HTML_BATCH_MOVE_QUESTION'); ?>
        </legend>
        <?php echo HTMLHelper::_('select.radiolist', $options, 'batch[move_copy]', '', 'value', 'text', 'm'); ?>
    </fieldset>
</div>
