<?php

/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('_JEXEC') or die;

extract($displayData);

/**
 * Layout variables
 * -----------------
 * @var   integer  $level  The level of the item in the tree like structure.
 *
 * @since  3.6.0
 */

if ($level > 1) {
    echo '<span class="text-muted">' . str_repeat('&#8942;&nbsp;&nbsp;&nbsp;', (int) $level - 2) . '</span>&ndash;&nbsp;';
}
