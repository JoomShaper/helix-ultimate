<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Uri\Uri;

extract($displayData);

$megaFields = new MegaFields($settings, $itemId);
$fields = $megaFields->getSidebarFields();
$item = $builder->getMenuItem();

?>

<div class="hu-megamenu-sidebar">
    <?php if ((int) $item->parent_id === 1): ?>
        <?php echo $builder->renderFieldElement('megamenu', $fields['megamenu']); ?>
    <?php endif ?>

    <div class="hu-megamenu-settings">
        <?php if ((int) $item->parent_id === 1): ?>
            <?php echo $builder->renderFieldElement('width', $fields['width']); ?>
        <?php endif ?>
    </div>

    <?php echo $builder->renderFieldElement('showtitle', $fields['showtitle']); ?>

    <div class="hu-d-flex hu-justify-content-between">
        <?php if ((int) $item->parent_id === 1): ?>
            <div class="hu-megamenu-alignment">
                <?php echo $builder->renderFieldElement('menualign', $fields['menualign']); ?>
            </div>
        <?php endif ?>

        <div class="hu-menuitem-dropdown-position">
            <?php echo $builder->renderFieldElement('dropdown', $fields['dropdown']); ?>
        </div>

        <?php echo $builder->renderFieldElement('faicon', $fields['faicon']); ?>
    </div>
    <?php echo $builder->renderFieldElement('customclass', $fields['customclass']); ?>
    <hr />
    <div class="hu-d-flex hu-justify-content-between">
        <?php echo $builder->renderFieldElement('badge', $fields['badge']); ?>
        <?php echo $builder->renderFieldElement('badge_position', $fields['badge_position']); ?>
    </div>
    <?php echo $builder->renderFieldElement('badge_bg_color', $fields['badge_bg_color']); ?>
    <?php echo $builder->renderFieldElement('badge_text_color', $fields['badge_text_color']); ?>
</div>