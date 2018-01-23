<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/

defined ('_JEXEC') or die ('Resticted access');

jimport('joomla.form.formfield');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class JFormFieldHelixheaders extends JFormField
{
    protected $type = 'Helixheaders';

    protected function getInput()
    {
        $html    = '';
        $classes = (!empty($this->element['class'])) ? $this->element['class'] : '';
        $headers = json_decode($this->value);

        $header_style_image_path = JURI::root(true) . '/plugins/system/helixultimate/layouts/frontend/headerlist';
        $header_style = JFolder::folders(JPATH_ROOT .'/plugins/system/helixultimate/layouts/frontend/headerlist');

        $html .= '<div class="clearfix '. $classes .'">';

        $html .= '<div class="header-design-wrap">';
        $html .= '<div><span>Enable Designed Header</span></div>';
        $html .= '<div>';
        $html .= '<select class="choose-desinged-header" data-name="'. $this->name .'">';
        $html .= '<option '. ((isset($headers->header) && $headers->header == 'enable')?'selected':'') .' value="enable">Enable</option>';
        $html .= '<option '. ((isset($headers->header) && $headers->header == 'disable')?'selected':'') .' value="disable">Disable</option>';
        $html .= '</select>';
        $html .= '</div>';

        $html .= '<div><span>Choose Header Style</span></div>';

        $html .= '<ul class="header-design-layout" data-name="'. $this->name .'">';

        foreach($header_style as $style)
        {
            $header_image = $header_style_image_path . '/' . $style . '/image.jpg';
            $html .= '<li class="header-design'.((isset($headers->style) && $headers->style == $style)?' active':'').'" data-style="'.$style.'">';
            $html .= '<span><img style="max-width:100%;" src="'. $header_image .'" alt="'. $style .'"</span>';
            $html .= '</li>';
        }

        $html .= '</ul>';

        $html .= '</div>';

        $html .= '<input type="hidden" name="' . $this->name .'" value=\''. $this->value .'\' class="header-design-'. $this->name .'" id="'. $this->id .'">';
        $html .= '</div>';
        
        return $html;
    }

    protected function getLabel(){
        return;
    }
}