<?php

/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Utilities\ArrayHelper;

$img = HTMLHelper::_('cleanImageURL', $displayData['src']);

$displayData['src'] = $this->escape($img->url);

if (isset($displayData['alt'])) {
    if ($displayData['alt'] === false) {
        unset($displayData['alt']);
    } else {
        $displayData['alt'] = $this->escape($displayData['alt']);
    }
}

if ($img->attributes['width'] > 0 && $img->attributes['height'] > 0) {
    $displayData['width']  = $img->attributes['width'];
    $displayData['height'] = $img->attributes['height'];

    if (empty($displayData['loading'])) {
        $displayData['loading'] = 'lazy';
    }
}

echo '<img ' . ArrayHelper::toString($displayData) . '>';
