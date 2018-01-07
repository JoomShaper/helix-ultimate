<?php
/**
* @package Helix3 Framework
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2015 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/  

//no direct accees
defined ('_JEXEC') or die ('resticted aceess');

jimport('joomla.form.formfield');

class JFormFieldHeader extends JFormField
{
    protected $type = 'Header';

    protected function getInput()
    {

        $html  = '';
        $classes = (!empty($this->element['class'])) ? $this->element['class'] : '';

        //Font Family
        $html .= '<div class="clearfix '. $classes .'">';
        
        $html .= '<div class="header-design-wrap">';

        $html .= '<div><span>Enable Designed Header</span></div>';
        $html .= '<div>';
        $html .= '<select class="choose-desinged-header" data-name="'. $this->name .'">';
        $html .= '<option value="enable">Enable</option>';
        $html .= '<option value="disable">Disable</option>';
        $html .= '</select>';
        $html .= '</div>';

        $html .= '<div><span>Choose Header Style</span></div>';
        $html .= '<ul class="header-design-layout" data-name="'. $this->name .'">';
        $html .= '<li class="header-design"data-style="layout-design-1">';
        $html .= '<span></span>';
        $html .= '</li>';
        $html .= '<li class="header-design" data-style="layout-design-2">';
        $html .= '<span></span>';
        $html .= '</li>';
        $html .= '<li class="header-design" data-style="layout-design-3">';
        $html .= '<span></span>';
        $html .= '</li>';
        $html .= '<li class="header-design" data-style="layout-design-4">';
        $html .= '<span></span>';
        $html .= '</li>';
        $html .= '</ul>';
        $html .= '</div>';
        $html .= '<input type="hidden" name="' . $this->name .'" value="'. $this->value .'" class="header-design-'. $this->name .'" id="'. $this->id .'">';

        $html .= '</div>';
        
        return $html;
    }
}