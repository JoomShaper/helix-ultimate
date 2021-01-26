<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Uri\Uri;

extract($displayData);

$grid = [];

if (!empty($settings) && isset($settings->layout))
{
    $grid = $settings->layout;
}

$rowLayout = new FileLayout('megaMenu.row', HELIX_LAYOUT_PATH);

?>
<div class="hu-megamenu-grid">
    <div class="hu-megamenu-rows-container">
        <?php if (!empty($grid)): ?>
            <?php foreach ($grid as $key => $row): ?>
                <?php
                    echo $rowLayout->render([
                        'itemId' => $itemId,
                        'builder' => $builder,
                        'row' => $row,
                        'rowId' => $key + 1
                    ]);
                ?>
            <?php endforeach ?>
        <?php endif ?>
    </div>
    <div class="hu-megamenu-add-row">
        <a href="#" class="hu-btn hu-btn-primary">
            <span class="fas fa-plus-circle"></span>
            <?php echo Text::_('HELIX_ULTIMATE_MEGAMENU_ADD_NEW_ROW'); ?>
        </a>
    </div>
    <div class="hu-megamenu-add-slots">
        <?php echo (new FileLayout('megaMenu.slots', HELIX_LAYOUT_PATH))->render(); ?>
    </div>

    <div class="hu-megamenu-popover">
        <div class="hu-megamenu-popover-heading">
            <h5 class="title">Select Module</h5>
            <button class="hu-btn hu-btn-link hu-megamenu-popover-close">
                <span class="fas fa-times"></span>
            </button>
        </div>
        <div class="hu-megamenu-popover-body">
            
        </div>
        <div class="hu-megamenu-popover-footer">
            <button class="hu-btn hu-btn-primary hu-megamenu-popover-apply">Add</button>
        </div>
    </div>
</div>