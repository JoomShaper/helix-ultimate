<?php
/**
* @package Helix3 Framework
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2015 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/  

defined ('_JEXEC') or die ('resticted aceess');

jimport('joomla.form.formfield');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class JFormFieldPresets extends JFormField
{

    protected $type = 'Presets';

    protected $presetfiled = '';
    protected $presetList ='';

    protected function getInput()
    {
        $children = $this->element->children();
        $html = '<div class="presets">';
    
        foreach ($children as $child)
        {
            $childName = $child->getName();

            if ($childName == 'preset')
            {
                $html_data_attr = 'data-preset="'. $child['name'] .'"';
                foreach ( $child->children() as $preset )
                {
                    $html_data_attr .= ' data-'. $preset['name'] .'="'. $preset['value'] .'"';
                }

                $html .='<div style="background-color: '. $child['default'] .'" '. $html_data_attr .'  class="preset">';
                $html .='<div class="preset-title">'. $child['label'] .'</div>';
                $html .='<div class="preset-contents">';
                $html .='</div>';
                $html .='</div>';
            }
            else
            {
                throw new UnexpectedValueException(sprintf('Unsupported element %s in JFormFieldGroupedList', $child->getName()), 500);
            }
        }

        $html .= '<input id="template-preset" type="hidden" name="'. $this->name .'" class="preset-input" value="'. $this->value .'" />';
        $html .= '</div>';
            
        return $html; 
    }

    public function getLabel()
    {
        return false;
    }
}
