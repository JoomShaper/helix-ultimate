<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die();

$data = $displayData;

$feature_folder_path     = JPATH_THEMES . '/' . $data->template->template . '/features/';

include_once $feature_folder_path.'logo.php';
include_once $feature_folder_path.'menu.php';

$output  = '';

$output .= '<header id="sp-header">';
$output .= '<div class="container">';
$output .= '<div class="container-inner">';
$output .= '<div class="row">';

$output .= '<div id="sp-logo" class="col-8 col-lg-4">';
$output .= '<div class="sp-column">';
$logo    = new HelixUltimateFeatureLogo($data->params);
if(isset($logo->load_pos) && $logo->load_pos == 'before')
{
    $output .= $logo->renderFeature();
    $output .= '<jdoc:include type="modules" name="logo" style="sp_xhtml" />';
}
else
{
    $output .= '<jdoc:include type="modules" name="logo" style="sp_xhtml" />';
    $output .= $logo->renderFeature();
}
$output .= '</div>';
$output .= '</div>';

$output .= '<div id="sp-menu" class="col-4 col-lg-8">';
$output .= '<div class="sp-column">';
$menu    = new HelixUltimateFeatureMenu($data->params);
if(isset($menu->load_pos) && $menu->load_pos == 'before')
{
    $output .= $menu->renderFeature();
    $output .= '<jdoc:include type="modules" name="menu" style="sp_xhtml" />';
}
else
{
    $output .= '<jdoc:include type="modules" name="menu" style="sp_xhtml" />';
    $output .= $menu->renderFeature();
}
$output .= '</div>';
$output .= '</div>';

$output .= '</div>';
$output .= '</div>';
$output .= '</div>';
$output .= '</header>';

echo $output;