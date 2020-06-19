<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('_JEXEC') or die();

$colGrid = array('12', '6+6', '4+4+4', '3+3+3+3', '4+8', '3+9', '3+6+3', '2+6+4', '2+10', '5+7', '2+3+7', '2+5+5', '2+8+2', '2+4+4+2');
$row = $displayData;

$rowSettings = '';
if(isset($row->settings))
{
    $rowSettings = RowColumnSettings::getSettings($row->settings);
}

$name = JText::_('HELIX_ULTIMATE_SECTION_TITLE');
if (isset($row->settings->name))
{
    $name = $row->settings->name;
}

$layout_path  = JPATH_ROOT .'/plugins/system/helixultimate/layouts';
$layout_column = new JLayoutFile('backend.column', $layout_path );

$output = '';
$output .= '<div '.((isset($row->sectionID) && $row->sectionID)?'id="hu-layout-section"':'').' class="hu-layout-section" ' . $rowSettings .'>';
$output .= '<div class="hu-section-settings clearfix">';
$output .= '<div class="pull-left">';
$output .= '<a class="hu-move-row" href="#"><i class="fas fa-arrows-alt"></i></a>';
$output .= '<strong class="hu-section-title">' . $name . '</strong>';
$output .= '</div>';
$output .= '<div class="pull-right">';
$output .= '<ul class="hu-row-option-list">';
$output .= '<li>';
$output .= '<a class="hu-add-columns" href="#"><span class="fas fa-columns"></span></a>';
$output .= '<ul class="hu-column-list">';

if(!isset($row->layout)){
    $row->layout =  12;
}

foreach ($colGrid as $grid)
{
    $cols = explode('+', $grid);
    $output .= '<li><a href="#" class="hu-column-layout '.(($grid == $row->layout)? 'active' : '' ).'" data-layout="'.$grid.'">';

    foreach ($cols as $col)
    {
        $output .= '<span class="hu-column-layout-col-'. $col .'"><span>'. $col .'</span></span>';
    }
    $output .= '</a></li>';
}

$output .= '<li><a href="#" class="hu-column-layout-custom hu-column-layout hu-custom ' . ((isset($row->layout) && !in_array($row->layout, $colGrid)) ? 'active' : '' ) .'" data-layout="'. $row->layout .'" data-type="custom" title="Custom Layout"><span class="hu-column-layout-col-3"><span>[</span></span><span class="hu-column-layout-col-6"><span>+</span></span><span class="hu-column-layout-col-3"><span>]</span></span></a></li>';
$output .= '</ul>';
$output .= '</li>';
$output .= '<li><a class="hu-row-options" href="#"><i class="fas fa-cogs fa-fw"></i></a></li>';
$output .= '<li><a class="hu-remove-row" href="#"><i class="fas fa-trash fa-fw"></i></a></li>';
$output .= '</ul>';
$output .= '</div>';
$output .= '</div>';

$output .= '<div class="hu-row-container ui-sortable">';
$output .= '<div class="row ui-sortable">';

if(isset($row->attr) && $row->attr)
{
    foreach ($row->attr as $column)
    {
        $output .= $layout_column->render($column->settings);
    }
}
else
{
    $output .= $layout_column->render(new stdClass);
}

$output .= '</div>';
$output .= '</div>';
$output .= '<a class="hu-add-row" href="#"><i class="fas fa-plus"></i></a>';
$output .= '</div>';

echo $output;