<?php
/**
 * @package     Helix
 * @copyright   Copyright (C) 2010 - 2016 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die('Restricted Access');

$doc = \JFactory::getDocument();

$data = $displayData;

$output ='';
$output .= '<div id="sp-component" class="' . $data->settings->className . '">';
$output .= '<div class="sp-column ' . ($data->settings->custom_class) . '">';
$output .= '<jdoc:include type="message" />';

if($doc->countModules('content-top'))
{
    $output .= '<div class="sp-module-content-top clearfix">';
    $output .= '<jdoc:include type="modules" name="content-top" style="sp_xhtml" />';
    $output .= '</div>';
}

$output .= '<jdoc:include type="component" />';

if($doc->countModules('content-bottom'))
{
    $output .= '<div class="sp-module-content-top clearfix">';
    $output .= '<jdoc:include type="modules" name="content-bottom" style="sp_xhtml" />';
    $output .= '</div>';
}

$output .= '</div>';
$output .= '</div>';

echo $output;
