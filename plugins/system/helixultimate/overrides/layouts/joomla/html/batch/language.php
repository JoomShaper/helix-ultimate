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
<label id="batch-language-lbl" for="batch-language-id">
    <?php echo Text::_('JLIB_HTML_BATCH_LANGUAGE_LABEL'); ?>
</label>
<select name="batch[language_id]" class="form-select" id="batch-language-id">
    <option value=""><?php echo Text::_('JLIB_HTML_BATCH_LANGUAGE_NOCHANGE'); ?></option>
    <?php echo HTMLHelper::_('select.options', HTMLHelper::_('contentlanguage.existing', true, true), 'value', 'text'); ?>
</select>
