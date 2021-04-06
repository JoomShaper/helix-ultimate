<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('_JEXEC') or die();

$settings = $displayData;
$colSettings = 'data-grid_size="12" data-column_type="0" data-name="none"';
if(isset($settings->grid_size) && $settings->grid_size){
    $colSettings = RowColumnSettings::getSettings($settings);
}

$output = '<div class="hu-layout-column col-' . ((isset($settings->grid_size) && $settings->grid_size)? $settings->grid_size :12) .'" ' . $colSettings .'>';
$output .= '<div class="hu-column' . ((isset($settings->column_type) && $settings->column_type) ? ' hu-column-component' : '') . '">';

if (isset($settings->column_type) && $settings->column_type)
{
    $output .= '<span class="hu-column-title">Component</span>';
}
else
{
    if (isset($settings->name))
    {
        $output .= '<span class="hu-column-title">'. $settings->name .'</span>';
    }
    else
    {
        $output .= '<span class="hu-column-title">None</span>';
    }
}

$output .= '<a class="hu-column-options" href="#"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="3" fill="none"><path fill="#020B53" fill-rule="evenodd" d="M3 1.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zm6 0a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM13.5 3a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" clip-rule="evenodd" opacity=".4"/></svg></a>';
$output .= '</div>';
$output .= '</div>';

echo $output;