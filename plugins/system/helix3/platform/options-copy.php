<?php
/**
* @package HelixUltimate Framework
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2017 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

//no direct accees
defined ('_JEXEC') or die ('resticted aceess');
jimport( 'joomla.filesystem.file' );
jimport('joomla.filesystem.folder');
require_once __DIR__.'/optionBase.php';

use Joomla\CMS\Form as joomlaForm;

class SPOptions extends OptionBase{
    
    public function __construct(){
        parent::__construct();
    }

    public function renderBuilderSidebar(){
        $options = parent::$fieldset;

        $form = new joomlaForm\Form('template');
        $form->loadFile( JPATH_PLUGINS.'/system/helix3/templateSettings.xml');

        $fieldsets = $form->getFieldsets();

        $raw_html = '<div id="hexli-ult-options">';
        foreach( $fieldsets as $key => $fieldset ) {

            $raw_html .= $this->renderFieldsetStart($fieldset);
            $fields   = $form->getFieldset($key);

            foreach( $fields as $key => $field ) {
                if ( $field->type == 'Group' ) {
                    $raw_html .= '<div class="field-group" data-group="'. $field->name .'">';
                    $raw_html .= '<div class="control-label">' . $key . $field->input .'</div>';
                    $raw_html .= '</div>';
                } else {
                    $group_name = $form->getFieldAttribute($field->name,'group');
                    $raw_html .= '<div class="control-group ' . (( $group_name ) ? 'group-style-'.$group_name : '') . '">';
                    $raw_html .= '<div class="control-label">' . $field->label .'</div>';
                    $raw_html .= '<div class="controls">' . $field->input . '</div>';
                    $raw_html .= '</div>';
                }
            }

            $raw_html .= $this->renderFieldsetEnd();
        }
        $raw_html .= '</div>';

        return $raw_html;
    }
    
    private function renderFieldsetStart( $fieldset ) {

        $html  = '<div class="fieldset-wrap clearfix fieldset-'. $fieldset->name .'">';
        $html .= '<div class="fieldset-toggle-icon"><i class="fa fa-long-arrow-left"></i></div>';
        $html .= '<div class="fieldset-header">';
        $html .= '<span class="fieldset-icon"><i class="'. ( ( isset( $fieldset->icon ) && $fieldset->icon )? $fieldset->icon : 'fa fa-address-book-o' ) .'"></i></span>';
        $html .= '<span class="fieldset-title">'. $fieldset->label .'</span>';
        $html .= '</div>';
        $html .= '<div class="groups-list">';

        return $html;
    }

    private function renderFieldsetEnd(){

        return '</div></div>';
    }

    private function renderGroupStart($slug, $params){
        $html  = '<div class="group-wrap group-'. $slug .'">';
        $html .= '<div class="group-header-box">';
        $html .= '<span class="group-toggle-icon">';
        $html .= '<i class="fa fa-caret-square-o-down" aria-hidden="true"></i>';
        $html .= '<i class="fa fa-caret-square-o-up" aria-hidden="true"></i>';
        $html .= '</span>';
        $html .= '<span class="group-title">'. $params['name'] .'</span>';
        $html .= '<span class="group-more-icon"></span>';
        $html .= '</div>';
        $html .= '<div class="fields-list">';
        
        return $html;
    }

    private function renderGroupEnd(){
        return '</div></div>';
    }

    private function renderInputField(){
       
        return '<input type="text" name="" id="jform_params_facebook" value="">';
    }
}