<?php
/**
* @package Helix Ultimate Framework
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2018 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

//no direct accees
namespace HelixULT\Model;

class HelixUltModel
{
    public static function getTemplateStyle($id = 0)
    {
        $db = \JFactory::getDbo();

        $query = $db->getQuery(true);
        $query->select(array('*'));
        $query->from( $db->quoteName('#__template_styles') );
        $query->where( $db->quoteName('client_id') . ' = 0' );
        $query->where( $db->quoteName('id') . ' = ' . $db->quote($id) );

        $db->setQuery($query);

        return $db->loadObject();
    }

    public static function updateTemplateStyle($id = 0, $data = '')
    {
        $db = \JFactory::getDbo();

        if(empty($data)) return;
        $data = json_encode($data);

        $query = $db->getQuery(true);
        $fields = array($db->quoteName('params') . '=' . $db->quote($data));
        $conditions = array(
                $db->quoteName('id') .'='. $db->quote($id),
                $db->quoteName('client_id') .'= 0'
            );

        $query->update($db->quoteName('#__template_styles'))->set($fields)->where($conditions);
        $db->setQuery($query);
        return $db->execute();
    }
}
