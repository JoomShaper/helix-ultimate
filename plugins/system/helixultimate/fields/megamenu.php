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

class JFormFieldMegamenu extends JFormField
{
    protected $type = "Megamenu";

    public function getInput()
    {
        $html  = '<div>';
        $html .= $this->getMegaSettings();
        $html .= '<input type="hidden" name="'.$this->name.'" id="'.$this->id.'" value="'.$this->value.'">';
        $html .= '</div>';
        
        return $html;
    }

    public function getMegaSettings()
    {
        $mega_menu_path = \JPATH_SITE.'/plugins/system/helixultimate/fields/';
        $menu_data = json_decode($this->value);
        $menu_item = $this->form->getData()->toObject();

        ob_start();
        include_once $mega_menu_path.'menuhelper.php';
        $html = ob_get_contents();
        ob_clean();

        return $html;
    }
}
