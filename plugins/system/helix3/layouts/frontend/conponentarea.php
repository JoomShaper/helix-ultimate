<?php
/**
 * @package     Helix
 *
 * @copyright   Copyright (C) 2010 - 2016 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */
defined('_JEXEC') or die('Restricted Access');

//Helix3
helix3::addLess('frontend-edit', 'frontend-edit');
helix3::addJS('frontend-edit.js');

$data = $displayData;

$output ='';

$output .= '<div id="sp-component" class="' . $data->className . '">';

$output .= '<div class="sp-column ' . ($data->settings->custom_class) . '">';
$output .= '<jdoc:include type="message" />';
$output .= '<jdoc:include type="component" />';
$output .= '</div>';

$output .= '</div>';


echo $output;

