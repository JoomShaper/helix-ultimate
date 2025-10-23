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

extract($displayData);

/**
 * Layout variables
 * -----------------
 * @var   boolean  $noUser  Inject an option for no user?
 */

$optionNo = '';

if ($noUser) {
    $optionNo = '<option value="0">' . Text::_('JLIB_HTML_BATCH_USER_NOUSER') . '</option>';
}
?>
<label id="batch-user-lbl" for="batch-user-id">
    <?php echo Text::_('JLIB_HTML_BATCH_USER_LABEL'); ?>
</label>
<select name="batch[user_id]" class="form-select" id="batch-user-id">
    <option value=""><?php echo Text::_('JLIB_HTML_BATCH_USER_NOCHANGE'); ?></option>
    <?php echo $optionNo; ?>
    <?php echo HTMLHelper::_('select.options', HTMLHelper::_('user.userlist'), 'value', 'text'); ?>
</select>
