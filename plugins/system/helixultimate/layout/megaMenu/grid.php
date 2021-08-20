<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use HelixUltimate\Framework\Platform\Helper;
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

/**
 * Get missing menu item and push them to the first row first column.
 *
 */
$missingItems = $builder->getMissingItems();

if (!empty($missingItems) && isset($grid[0]) && isset($grid[0]->attr[0]))
{
    $items = array_merge($grid[0]->attr[0]->items, $missingItems);
    $grid[0]->attr[0]->items = $items;
}

$rowLayout = new FileLayout('megaMenu.row', HELIX_LAYOUT_PATH);
$modules = Helper::getModules();

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
            <span class="fas fa-plus-circle" aria-hidden="true"></span>
            <?php echo Text::_('HELIX_ULTIMATE_MEGAMENU_ADD_NEW_ROW'); ?>
        </a>
    </div>
    <div class="hu-megamenu-add-slots">
        <?php echo (new FileLayout('megaMenu.slots', HELIX_LAYOUT_PATH))->render(); ?>
    </div>
</div>