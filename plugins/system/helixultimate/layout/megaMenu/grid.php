<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
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
            <span class="fas fa-plus-circle"></span>
            <?php echo Text::_('HELIX_ULTIMATE_MEGAMENU_ADD_NEW_ROW'); ?>
        </a>
    </div>
    <div class="hu-megamenu-add-slots">
        <?php echo (new FileLayout('megaMenu.slots', HELIX_LAYOUT_PATH))->render(); ?>
    </div>

    <div class="hu-megamenu-popover">
        <div class="hu-megamenu-popover-heading">
            <h5 class="title"><?php echo Text::_('HELIX_ULTIMATE_MENU_MODULE_LIST'); ?></h5>
            <button class="hu-btn hu-btn-link hu-megamenu-popover-close">
                <span class="fas fa-times"></span>
            </button>
        </div>
        <div class="hu-megamenu-popover-body">
            <div class="hu-megamenu-search-wrapper">
                <span class="fas fa-search"></span>
                <input type="search" class="hu-input hu-megamenu-module-search" placeholder="<?php echo Text::_('HELIX_ULTIMATE_SEARCH_MODULE_HINT'); ?>" />
            </div>

            <div class="hu-megamenu-modules-container">
                <?php if (!empty($modules)): ?>
                    <div class="row">
                        <?php foreach ($modules as $module): ?>
                            <div class="col-4 hu-megamenu-column">
                                <div class="hu-megamenu-module-item">
                                    <strong class="hu-megamenu-module-title"><?php echo $module->title; ?></strong>
                                    <p class="hu-megamenu-module-desc"><?php echo (strlen($module->desc) > 80 ? substr($module->desc, 0, 80) . '...' : $module->desc); ?></p>
                                    <button type="button" role="button" class="hu-btn hu-btn-default hu-megamenu-insert-module" data-module="<?php echo $module->id; ?>"><?php echo Text::_('HELIX_ULTIMATE_MODULE_INSERT'); ?></button>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>