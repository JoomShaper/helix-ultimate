<?php
/**
* @package Helix3 Framework
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2015 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die ('Restricted access');

jimport('joomla.form.formfield');

class JFormFieldDetails extends JFormField
{
    protected $type = 'Details';

    protected function getInput()
    {
        $app = JFactory::getApplication();
        $id  = $app->input->get('id',null,'INT');

        $url = 'index.php?option=com_ajax&preview=theme&view=style&id=' . $id;
        $html  = '';
        $html .= '<div class="">';
        $html .= '<a target="_blank" href="'. $url .'">Go To Builder</a>';
        $html .= '</div>';

        return $html;
    }

    public function getLabel()
    {
        return false;
    }
}
