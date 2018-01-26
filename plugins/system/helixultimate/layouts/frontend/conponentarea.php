<?php
/**
 * @package     Helix
 * @copyright   Copyright (C) 2010 - 2016 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die('Restricted Access');

$data = $displayData;

$output ='';
$output .= '<main id="sp-component" class="' . $data->settings->className . '" role="main">';
$output .= '<div class="sp-column ' . ($data->settings->custom_class) . '">';
$output .= '<jdoc:include type="message" />';
$output .= '<jdoc:include type="component" />';
$output .= '</div>';
$output .= '</main>';

echo $output;
