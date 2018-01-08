<?php
/**
* @package Helix3 Framework
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2015 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die ('Restricted access');

jimport('joomla.form.formfield');

class JFormFieldTypography extends JFormField
{
    protected $type = 'Typography';

    protected function getInput()
    {
        $template_path = \JPATH_SITE . '/templates/' . self::getTemplate() . '/webfonts/webfonts.json';
        $plugin_path   = \JPATH_PLUGINS . '/system/helixultimate/assets/webfonts/webfonts.json';

        if(file_exists( $template_path ))
        {
            $json = JFile::read( $template_path );
        }
        else
        {
            $json = JFile::read( $plugin_path );
        }

        $webfonts   = json_decode($json);
        $items      = $webfonts->items;
        $value      = json_decode($this->value);

        if (isset($value->fontFamily))
        {
            $font = self::filterArray($items, $value->fontFamily);
        }

        $html  = '';
        $classes = (!empty($this->element['class'])) ? $this->element['class'] : '';

        //Font Family
        $html .= '<div class="helix-ultimate-field-webfont '. $classes .'">';
        $html .= '<div class="row">';

        $html .= '<div class="col-6 font-families">';
        $html .= '<label><small>'. \JText::_('HELIX_ULTIMATE_FONT_FAMILY') .'</small></label>';
        $html .= '<select class="list-font-families">';

        foreach ($items as $item)
        {
            $html .= '<option '. ((isset($value->fontFamily) && $item->family == $value->fontFamily)?'selected="selected"':'') .' value="'. $item->family .'">'. $item->family .'</option>';
        }

        $html .= '</select>';
        $html .= '</div>';

        //Font Weight
        $html .= '<div class="col-6 font-weight">';
        $html .= '<label><small>'. \JText::_('HELIX_ULTIMATE_FONT_WEIGHT') .'</small></label>';
        $html .= '<select class="list-font-weight">';

        if (isset($value->fontFamily))
        {
            $html .= $this->generateSelectOptions($font->variants, $value->fontWeight);
        }
        else
        {
            $html .= $this->generateSelectOptions($items[0]->variants, 'no-selection');
        }

        $html .= '</select>';
        $html .= '</div>';

        //Font Size
        $fontSize = (isset($value->fontSize))?$value->fontSize:'';
        $html .= '<div class="col-6 font-size">';
        $html .= '<p></p><label><small>'. \JText::_('HELIX_ULTIMATE_FONT_SIZE') .'</small></label>';
        $html .= '<input type="number" value="'. $fontSize .'" class="webfont-size" min="1" placeholder="14">';
        $html .= '</div>';

        //Font Subsets
        $html .= '<div class="col-6 font-subsets">';
        $html .= '<p></p><label><small>'. \JText::_('HELIX_ULTIMATE_FONT_SUBSET') .'</small></label>';
        $html .= '<select class="list-font-subset">';

        if(isset($value->fontFamily))
        {
            $html .= $this->generateSelectOptions($font->subsets, $value->fontSubset);
        }
        else
        {
            $html .= $this->generateSelectOptions($items[0]->subsets, 'no-selection');
        }

        $html .= '</select>';
        $html .= '</div>';

        $html .= '</div>';

        //Preview
        $html .= '<p style="display:none" class="webfont-preview">1 2 3 4 5 6 7 8 9 0 Grumpy wizards make toxic brew for the evil Queen and Jack.</p>';
        $html .= '<input type="hidden" name="' . $this->name .'" value="'. $this->value .'" class="input-webfont" id="'. $this->id .'">';
        $html .= '</div>';

        return $html;
    }

    private function generateSelectOptions( $items = array(), $selected = '' )
    {
        $html = '';
        foreach ($items as $item)
        {
            $html .= '<option ' . (($selected !== 'no-selection' && $item == $selected) ? 'selected="selected"' : '') . ' value="'. $item .'">'. $item .'</option>';
        }
        return $html;
    }

    // Get current font
    private static function filterArray($items, $key)
    {
        foreach ($items as $item)
        {
            if ($item->family == $key) return $item;
        }
        return false;
    }

    private static function getTemplate()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->quoteName(array('template')));
        $query->from($db->quoteName('#__template_styles'));
        $query->where($db->quoteName('id') . ' = '. $db->quote( JRequest::getVar('id') ));
        $db->setQuery($query);

        return $db->loadResult();
    }

}
