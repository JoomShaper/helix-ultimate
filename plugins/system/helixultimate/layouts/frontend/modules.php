<?php
/**
 * @package     Helix
 *
 * @copyright   Copyright (C) 2010 - 2016 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die('Restricted Access');

$data = $displayData;

$options = $data->settings;

$output ='';
$grid_size = $options->grid_size;
$col_class_name = 'col-md-' . $grid_size . ' col-lg-' . $grid_size;

$output .= '<div id="sp-' . JFilterOutput::stringURLSafe($options->name) . '" class="'. $col_class_name .'">';
$output .= '<div class="sp-column ' . ($options->custom_class) . '">';
$features = ($data->hasFeature[$options->name])? $data->hasFeature[$options->name] : array();

    foreach ($features as $key => $feature)
    {
        if (isset($feature['feature']) && $feature['load_pos'] == 'before' )
        {
            $output .= $feature['feature'];
        }
    }
    $output .= '<jdoc:include type="modules" name="' . $options->name . '" style="sp_xhtml" />';
    foreach ($features as $key => $feature)
    {
        if (isset($feature['feature']) && $feature['load_pos'] != 'before' )
        {
            $output .= $feature['feature'];
        }
    }

$output .= '</div>';
$output .= '</div>';

echo $output;
