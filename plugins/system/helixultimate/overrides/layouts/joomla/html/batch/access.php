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
<label id="batch-access-lbl" for="batch-access">
    <?php echo Text::_('JLIB_HTML_BATCH_ACCESS_LABEL'); ?>
</label>
    <?php echo HTMLHelper::_(
        'access.assetgrouplist',
        'batch[assetgroup_id]',
        '',
        'class="form-select"',
        [
            'title' => Text::_('JLIB_HTML_BATCH_NOCHANGE'),
            'id'    => 'batch-access'
        ]
    );
