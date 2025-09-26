<?php

/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;

$canEdit   = $displayData['params']->get('access-edit');
$articleId = $displayData['item']->id;
?>

<?php if ($canEdit) : ?>
    <div class="icons">
        <div class="float-end">
            <div>
                <?php echo HTMLHelper::_('icon.edit', $displayData['item'], $displayData['params']); ?>
            </div>
        </div>
    </div>
<?php endif; ?>
