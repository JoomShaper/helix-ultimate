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

// Create the add/remove tag options.
$options = [
    HTMLHelper::_('select.option', 'a', Text::_('JLIB_HTML_BATCH_TAG_ADD')),
    HTMLHelper::_('select.option', 'r', Text::_('JLIB_HTML_BATCH_TAG_REMOVE'))
];

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->useScript('joomla.batch-tag-addremove');

?>
<label id="batch-tag-choose-action-lbl" for="batch-tag-id">
    <?php echo Text::_('JLIB_HTML_BATCH_TAG_LABEL'); ?>
</label>
<div id="batch-tag-choose-action" class="control-group">
    <select name="batch[tag]" class="form-select" id="batch-tag-id">
        <option value=""><?php echo Text::_('JLIB_HTML_BATCH_TAG_NOCHANGE'); ?></option>
        <?php echo HTMLHelper::_('select.options', HTMLHelper::_('tag.tags', ['filter.published' => [1]]), 'value', 'text'); ?>
    </select>
</div>
<div id="batch-tag-addremove" class="control-group radio">
    <fieldset id="batch-tag-addremove-id">
        <legend>
            <?php echo Text::_('JLIB_HTML_BATCH_TAG_ADDREMOVE_QUESTION'); ?>
        </legend>
        <?php echo HTMLHelper::_('select.radiolist', $options, 'batch[tag_addremove]', '', 'value', 'text', 'a'); ?>
    </fieldset>
</div>
