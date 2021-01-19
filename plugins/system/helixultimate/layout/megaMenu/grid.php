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
    <?php if (!empty($grid)): ?>
        <?php foreach ($grid as $row): ?>
            <?php echo $rowLayout->render(['itemId' => $itemId, 'builder' => $builder, 'row' => $row]); ?>
        <?php endforeach ?>
    <?php endif ?>
    <a href="#" class="hu-btn hu-btn-primary">
        <span class="fas fa-plus-circle"></span>
        <?php echo Text::_('HELIX_ULTIMATE_MEGAMENU_ADD_NEW_ROW'); ?>
    </a>
</div>