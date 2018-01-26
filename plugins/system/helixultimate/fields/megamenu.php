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

    private function getModuleNameById($id = 'all')
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select($db->quoteName(array('id','title')));
        $query->from($db->quoteName('#__modules'));
        $query->where($db->quoteName('published').'= 1');
        $query->where($db->quoteName('client_id').'= 0');

        if($id != 'all')
        {
            $query->where($db->quoteName('id').'=' . $db->quote($id));
        }
        $db->setQuery($query);

        if($id != 'all')
        {
            return $db->loadObject();
        }

        return $db->loadObjectList();
    }

    private function uniqueMenuItems($current_menu_id, $layout)
    {
        $saved_menu_items = array();
        if (! empty($layout) && count($layout))
        {
            foreach ($layout as $key => $row)
            {
                if (! empty($row->attr) && count($row->attr))
                {
                    foreach ($row->attr as $col_key => $col)
                    {
                        if ( ! empty($col->items) && count($col->items))
                        {
                            foreach ($col->items as $item)
                            {
                                if ($item->type === 'menu_item')
                                {
                                    $saved_menu_items[] = $item->item_id;
                                }
                            }
                        }
                    }
                }
            }
        }

        $items = $this->menuItems();
        $menus = new JMenuSite;

        $unique_item_id = array();
        if (isset($items[$current_menu_id]))
        {
            $items = $items[$current_menu_id];
            foreach ($items as $key => $item_id)
            {
                if ( ! in_array($item_id,$saved_menu_items))
                {
                    $unique_item_id[] = $item_id;
                }
            }
        }

        return $unique_item_id;
    }

    private function menuItems()
    {
        $menus = new JMenuSite;
        $menus = $menus->getMenu();
        $new = array();
        foreach ($menus as $item)
        {
            $new[$item->parent_id][] = $item->id;
        }
        return $new;
    }
}
