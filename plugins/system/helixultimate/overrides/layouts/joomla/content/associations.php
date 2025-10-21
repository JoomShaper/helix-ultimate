<?php

/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('_JEXEC') or die;

$items = $displayData;

if (!empty($items)) : ?>
    <ul class="item-associations">
        <?php foreach ($items as $id => $item) : ?>
            <?php if (is_array($item) && isset($item['link'])) : ?>
                <li>
                    <?php echo $item['link']; ?>
                </li>
            <?php elseif (isset($item->link)) : ?>
                <li>
                    <?php echo $item->link; ?>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
