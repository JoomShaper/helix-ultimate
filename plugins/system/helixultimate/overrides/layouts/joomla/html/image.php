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

$displayData['src'] = $img->url;

if (isset($displayData['alt']) && $displayData['alt'] === false) {
    unset($displayData['alt']);
}

if ($img->attributes['width'] > 0 && $img->attributes['height'] > 0) {
    $displayData['width']  = $img->attributes['width'];
    $displayData['height'] = $img->attributes['height'];

    if (empty($displayData['loading'])) {
        $displayData['loading'] = 'lazy';
    }
}

$attributes = [];

foreach ($displayData as $attributeName => $attributeValue) {
    if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_:.-]*$/', $attributeName)) {
        continue;
    }

    $attributes[$attributeName] = htmlspecialchars((string) $attributeValue, ENT_QUOTES, 'UTF-8');
}

echo '<img ' . ArrayHelper::toString($attributes) . '>';
