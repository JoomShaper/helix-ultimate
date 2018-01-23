<?php
/**
* @package Helix Ultimate Framework
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2017 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('_JEXEC') or die('Restricted Access');

$data = $displayData;

$feature_folder_path     = JPATH_THEMES . '/' . $data->template->template . '/features/';

include_once $feature_folder_path.'logo.php';
include_once $feature_folder_path.'social.php';
include_once $feature_folder_path.'menu.php';

$output  = '';
$output .= '<header id="sp-header">';
$output .= '<div class="container">';
$output .= '<div class="container-inner">';
$output .= '<div class="row" style="position:relative;">';

$output .= '<div id="sp-logo" class="col-lg-3">';
$output .= '<div class="sp-column">';
$logo_obj = new HelixUltimateFeatureLogo($data->params);
$output .= $logo_obj->renderFeature();
$output .= '</div>';
$output .= '</div>';

$output .= '<div id="sp-menu" class="col-lg-9" style="position: static;">';
$output .= '<div class="sp-column">';
$menu_obj = new HelixUltimateFeatureMenu($data->params);
$output .= $menu_obj->renderFeature();
$output .= '</div>';
$output .= '</div>';

$output .= '</div>';
$output .= '</div>';
$output .= '</div>';
$output .= '</header>';

echo $output;