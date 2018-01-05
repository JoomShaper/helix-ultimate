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

        $raw_html = '<div id="helix-ultimate-options">';
        $raw_html .= '<form id="helix-ultimate-style-form" action="index.php">';

        foreach( $fieldsets as $key => $fieldset ) {

            $raw_html .= $this->renderFieldsetStart($fieldset);
            $fields = $form->getFieldset($key);

            $fieldArray = array();

            foreach( $fields as $key => $field ) {
              $group = $field->getAttribute('helixgroup') ? $field->getAttribute('helixgroup') : 'no-group';
              $filed_html = $this->renderInputField( $field, $group );
              $fieldArray[$group]['fields_html'][] = $filed_html;
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

        $html  = '<div class="helix-ultimate-fieldset helix-ultimate-fieldset-'. $fieldset->name .' clearfix">';
        $html .= '<div class="helix-ultimate-fieldset-header">';
        $html .= '<div class="helix-ultimate-fieldset-toggle-icon"><i class="fa fa-long-arrow-left"></i></div>';
        $html .= '<div class="helix-ultimate-fieldset-header-inner" data-fieldset="'. $fieldset->name .'">';
        $html .= '<span class="helix-ultimate-fieldset-icon"><i class="'. ( ( isset( $fieldset->icon ) && $fieldset->icon )? $fieldset->icon : 'fa fa-address-book-o' ) .'"></i></span>';
        $html .= '<span class="helix-ultimate-fieldset-title">'. $fieldset->label .'</span>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="helix-ultimate-group-list">';

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
          if($key != 'no-group') {
            $html .= $this->renderGroupStart( $key );
          }

          $html .= $this->getFields($group['fields_html']);

          if($key != 'no-group') {
            $html .= $this->renderGroupEnd();
          }
        }

        return $html;
    }

    private function renderGroupStart( $group )
    {
        $html  = '<div class="helix-ultimate-group-wrap helix-ultimate-group-'. $group .'">';
        $html .= '<div class="helix-ultimate-group-header-box">';
        $html .= '<span class="helix-ultimate-group-toggle-icon">';
        $html .= '<i class="fa fa-angle-down" aria-hidden="true"></i>';
        $html .= '<i class="fa fa-angle-up" aria-hidden="true"></i>';
        $html .= '</span>';
        $html .= '<span class="helix-ultimate-group-title">'. \JText::_('HELIX_ULTIMATE_GROUP_' . strtoupper($group)) .'</span>';
        $html .= '<span class="helix-ultimate-group-more-icon"></span>';
        $html .= '</div>';
        $html .= '<div class="helix-ultimate-field-list">';

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
        $field_html .= '<div class="controls">';
        $field_html .= $field->input;
        if($field->getAttribute('description') != '') {
            $field_html .= '<div class="control-help">' . \JText::_($field->getAttribute('description')) . '</div>';
        }
        $field_html .= '</div>';
        $field_html .= '</div>';

        return $field_html;
    }
}
