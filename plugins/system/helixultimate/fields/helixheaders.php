<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die ();

jimport('joomla.form.formfield');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class JFormFieldHelixheaders extends JFormField
{
    protected $type = 'Helixheaders';

    protected function getInput()
    {
        $input  = JFactory::getApplication()->input;
        $id     = $input->get('id',NULL,'INT');

        $header_image_path = JURI::root() . 'plugins/system/helixultimate/layouts/frontend/headerlist/';
        $header_style = JFolder::folders(JPATH_ROOT .'/plugins/system/helixultimate/layouts/frontend/headerlist');

        // Template header list
        $template = $this->getTemplateName($id);
        $tmpl_header_image_path = JURI::root() .'templates/'. $template .'/headerlist/';
        if($template)
        {
            $tmpl_header_path = JPATH_ROOT .'/templates/'. $template .'/headerlist';
            if(JFolder::exists($tmpl_header_path))
            {
                $tmpl_header_style = JFolder::folders($tmpl_header_path);
                $header_style = array_merge($header_style,$tmpl_header_style);
            }
        }

        $html = '<div class="helix-ultimate-predefined-headers">';
        $html .= '<ul class="helix-ultimate-header-list clearfix" data-name="'. $this->name .'">';

        foreach($header_style as $key => $style)
        {
            if(JFile::exists($tmpl_header_path .'/'. $style . '/thumb.jpg'))
            {
                $header_image = $tmpl_header_image_path . $style . '/thumb.jpg';
            }
            else
            {
                $header_image = $header_image_path . $style . '/thumb.jpg';
            }
            $html .= '<li class="helix-ultimate-header-item'.(($this->value == $style)?' active':'').'" data-style="'.$style.'">';
            $html .= '<span><img src="'. $header_image .'" alt="'. $style .'"</span>';
            $html .= '</li>';
        }

        $html .= '</ul>';

        $html .= '<input type="hidden" name="' . $this->name .'" value=\''. $this->value .'\' id="'. $this->id .'">';
        $html .= '</div>';
        
        return $html;
    }

    private function getTemplateName($id = 0)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('#__template_styles'));
        $query->where($db->quoteName('client_id') . ' = 0');
        $query->where($db->quoteName('id') . ' = ' . $db->quote( $id ));
        $db->setQuery($query);
        $result = $db->loadObject();

        if($result){
            return $result->template;
        }

        return;
    }
}