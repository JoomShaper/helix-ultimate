<?php
/**
* @package HelixUltimate Framework
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2017 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

namespace HelixULT;

defined ('_JEXEC') or die ('resticted access');

jimport( 'joomla.filesystem.file' );
jimport('joomla.filesystem.folder');
require_once __DIR__.'/helix-ult-model.php';

use Joomla\CMS\Form as JoomlaForm;
use HelixULT\Model\HelixUltModel as HelixUltModel;

class SPOptions{
    
    public function renderBuilderSidebar()
    {
        
        $input  = \JFactory::getApplication()->input;
        $id = $input->get('id',NULL);

        $tmplStyle = HelixUltModel::getTemplateStyle($id);
        $formData = array();

        if(isset($tmplStyle->params)){
            $formData = json_decode($tmplStyle->params);
        }

        $form = new JoomlaForm\Form('template');
        $form->loadFile( JPATH_PLUGINS.'/system/helix3/templateSettings.xml');
        $form->bind($formData);

        $fieldsets = $form->getFieldsets();

        $raw_html = '<div id="hexli-ult-options">';
        $raw_html .= '<form id="tmpl-style-form" action="#">';

        foreach( $fieldsets as $key => $fieldset ) {

            $raw_html .= $this->renderFieldsetStart($fieldset);
            $fields   = $form->getFieldset($key);

            $fieldArray = array('no-group' => array());

            foreach( $fields as $key => $field ) {
                if ( $field->type == 'Group' ) {
                    if( !in_array( $key, array_keys( $fieldArray ) ) ){
                        $fieldArray[$key] = array(
                            'name' => $field->name,
                            'input' => $field->input,
                            'fields_html' => array() 
                        );
                    }
                } else {
                    $group = $form->getFieldAttribute( $field->name,'group' );
                    $filed_html = $this->renderInputField( $field, $group );

                    if( $group && $filed_html ) {
                        array_push( $fieldArray[$group]['fields_html'], $filed_html );
                    } else {
                        array_push( $fieldArray['no-group'], $filed_html );
                    }
                }
            }

            $raw_html .= $this->renderGroups($fieldArray);
            $raw_html .= $this->renderFieldsetEnd();
        }
        
        $raw_html .= '</form>';
        $raw_html .= '</div>';

        return $raw_html;
    }
    
    private function renderFieldsetStart( $fieldset )
    {

        $html  = '<div class="fieldset-wrap clearfix fieldset-'. $fieldset->name .'">';
        $html .= '<div class="fieldset-toggle-icon"><i class="fa fa-long-arrow-left"></i></div>';
        $html .= '<div class="fieldset-header">';
        $html .= '<span class="fieldset-icon"><i class="'. ( ( isset( $fieldset->icon ) && $fieldset->icon )? $fieldset->icon : 'fa fa-address-book-o' ) .'"></i></span>';
        $html .= '<span class="fieldset-title">'. $fieldset->label .'</span>';
        $html .= '</div>';
        $html .= '<div class="groups-list">';

        return $html;
    }

    private function renderFieldsetEnd()
    {

        return '</div></div>';
    }


    private function renderGroups($groups)
    {
        $html = '';
        foreach( $groups as $key => $group ){
            if($key == 'no-group'){
                $html .= $this->getFields($group);
            } else {
                $html .= $this->renderGroupStart( $key, $group['name'] );
                $html .= $this->getFields($group['fields_html']);
                $html .= $this->renderGroupEnd();
            }
        }

        return $html;
    }

    private function renderGroupStart( $slug, $name )
    {
        $html  = '<div class="group-wrap group-'. $slug .'">';
        $html .= '<div class="group-header-box">';
        $html .= '<span class="group-toggle-icon">';
        $html .= '<i class="fa fa-caret-square-o-down" aria-hidden="true"></i>';
        $html .= '<i class="fa fa-caret-square-o-up" aria-hidden="true"></i>';
        $html .= '</span>';
        $html .= '<span class="group-title">'. $name .'</span>';
        $html .= '<span class="group-more-icon"></span>';
        $html .= '</div>';
        $html .= '<div class="fields-list">';
        
        return $html;
    }

    private function renderGroupEnd()
    {
        return '</div></div>';
    }

    private function getFields( $fields )
    {
        $html = '';
        foreach( $fields as $field ){
            $html .= $field;
        }

        return $html;
    }

    private function renderInputField($field = '', $group = '')
    {
        $field_html = '';
        $field_html .= '<div class="control-group ' . (( $group ) ? 'group-style-'.$group : '') . '">';
        $field_html .= '<div class="control-label">' . $field->label .'</div>';
        $field_html .= '<div class="controls">' . $field->input . '</div>';
        $field_html .= '</div>';

        return $field_html;
    }
}