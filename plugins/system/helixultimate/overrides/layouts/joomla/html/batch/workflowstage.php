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
<label id="batch-workflowstage-lbl" for="batch-workflowstage-id">
    <?php echo Text::_('JLIB_HTML_BATCH_WORKFLOW_STAGE_LABEL'); ?>
</label>

<?php

$attr = [
    'id'        => 'batch-workflowstage-id',
    'group.label' => 'text',
    'group.items' => null,
    'list.attr' => [
        'class' => 'form-select'
    ]
];

$groups = HTMLHelper::_('workflowstage.existing', ['title' => Text::_('JLIB_HTML_BATCH_WORKFLOW_STAGE_NOCHANGE')]);

echo HTMLHelper::_('select.groupedlist', $groups, 'batch[workflowstage_id]', $attr);
