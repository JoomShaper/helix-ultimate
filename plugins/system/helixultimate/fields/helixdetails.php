<?php
/**
* @package Helix3 Framework
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2015 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die ('Restricted access');

jimport('joomla.form.formfield');

class JFormFieldHelixdetails extends JFormField
{
    protected $type = 'Helixdetails';

    protected function getInput()
    {
        $app = JFactory::getApplication();
        $id  = $app->input->get('id',null,'INT');

        $url = 'index.php?option=com_ajax&preview=theme&view=style&id=' . $id;
        $html  = '';
        $html .= '<div class="">';
        $html .= '<a target="_blank" href="'. $url .'" class="helix-ultimate-options"><i class="icon-options"></i> Template Options</a>';
        $html .= '<style type="text/css">';
        $html .= '.helix-ultimate-options {background: #05D21F;border-radius: 3px;color:#fff;padding:20px 30px;font-size:16px; font-weight: 700; display: inline-block; margin-top: 10px;}';
        $html .= '.helix-ultimate-options:hover {text-decoration: none; color: #fff; background: #05BB1B;}';
        $html .= '</style>';
        $html .= '</div>';

        return $html;
    }

    public function getLabel()
    {
        return false;
    }
}
