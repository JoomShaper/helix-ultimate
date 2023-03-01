<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('_JEXEC') or die();

use HelixUltimate\Framework\Platform\Helper;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Language\Text;

$data = $displayData;

$options = $data->settings;
$params = Helper::loadTemplateData()->params;
$isHeader = !empty($data->section_sematic) && $data->section_sematic === 'header';
$menuType = $params->get('menu_type', '');
$hasOffcanvas = in_array($menuType, ['mega_offcanvas', 'offcanvas']);
$offcanvasPosition = $params->get('offcanvas_position', 'right');
$columnClass = $isHeader ? ' d-flex align-items-center' : '';

$columnClass .= $isHeader && $options->name === 'menu' ? ' justify-content-end' : '';

$output ='';
$output .= '<'.$data->sematic.' id="sp-' . OutputFilter::stringURLSafe($options->name) . '" class="'. $options->className .'">';
$output .= '<div class="sp-column ' . ($options->custom_class) . $columnClass . '">';
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

if ($isHeader && $hasOffcanvas && $options->name === 'menu')
{
    if ($offcanvasPosition === 'right')
    {
        if ($menuType !== 'offcanvas')
        {
            $output .= '<a id="offcanvas-toggler"  aria-label="'. Text::_('HELIX_ULTIMATE_NAVIGATION') . '" title="'. Text::_('HELIX_ULTIMATE_NAVIGATION') . '"  class="offcanvas-toggler-secondary offcanvas-toggler-right d-flex align-items-center" href="#">';
            $output .= '<div class="burger-icon"><span></span><span></span><span></span></div>';
            $output .= '</a>';
        }
    }
}

$output .= '</div>';
$output .= '</'.$data->sematic.'>';

echo $output;
