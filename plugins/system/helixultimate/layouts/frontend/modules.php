<?php
/**
 * @package     Helix
 *
 * @copyright   Copyright (C) 2010 - 2016 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */
defined('_JEXEC') or die('Restricted Access');

//helper & model

$data = $displayData;

$output ='';
$grid_size = $data->settings->grid_size;
$col_class_name = 'col-md-' . $grid_size . ' col-lg-' . $grid_size;

$output .= '<div id="sp-' . JFilterOutput::stringURLSafe($data->settings->name) . '" class="'. $col_class_name .'">';
    $output .= '<div class="sp-column ' . ($data->settings->custom_class) . '">';
    $features = (HelixUltimate::hasFeature($data->settings->name))? helixUltimate::getInstance()->loadFeature[$data->settings->name] : array();
        foreach ($features as $key => $feature)
        {
            if (isset($feature['feature']) && $feature['load_pos'] == 'before' )
            {
                $output .= $feature['feature'];
            }
        }
        $output .= '<jdoc:include type="modules" name="' . $data->settings->name . '" style="sp_xhtml" />';
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
