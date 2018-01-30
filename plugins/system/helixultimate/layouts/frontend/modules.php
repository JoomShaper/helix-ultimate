<?php
/**
 * @package     Helix
 * @copyright   Copyright (C) 2010 - 2016 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die('Restricted Access');

$data = $displayData;
$options = $data->settings;

$output ='';
$output .= '<'.$data->sematic.' id="sp-' . JFilterOutput::stringURLSafe($options->name) . '" class="'. $options->className .'">';
$output .= '<div class="sp-column ' . ($options->custom_class) . '">';
$features = (isset($data->hasFeature[$options->name]) && $data->hasFeature[$options->name])? $data->hasFeature[$options->name] : array();

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
$output .= '</'.$data->sematic.'>';

echo $output;
