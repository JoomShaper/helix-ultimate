<?php
/**
* @package HelixUltimate Framework
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2017 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

//no direct accees
defined ('_JEXEC') or die ('resticted aceess');

class OptionBase{
    public static $fieldset = array();

    public function __construct(){
        $this->getDefaultSettings();
    }

    protected static function setFieldset( $name = '', $params = array() ){
        self::$fieldset[$name] = $params;
    }

    protected static function getFieldset( $name = '' ){
        if( !empty($name) || isset(self::$fieldset[$name]) ) return self::$fieldset[$name];
        return;
    }

    public static function getAllFieldset(){
        return self::$fieldset;
    }

    public static function removeFieldSet( $name = '' ){
        if( empty($name) && isset(self::$fieldset[$name]) ){
            unset(self::$fieldset[$name]);
        }
    }

    public static function addGroup( $fieldset = '', $group_name = '', $params = array() ){
        if( empty($fieldset) || !isset(self::$fieldsets[$fieldset]) ) return;

        $fieldset_params = self::getFieldset( $fieldset );
        $fieldset_params['group'][$group_name] = $params;
        self::setFieldset( $fieldset, $fieldset_params );
    }

    private function getDefaultSettings(){
        $setting_folder_path = __DIR__.'/settings/';
        $files = JFolder::files($setting_folder_path, $filter = '.');
        $number_of_files = count($files);

        if( $number_of_files < 1 ) return;

        $settings = array();
        foreach ( $files as $key => $file_name ) {
            $file_path = $setting_folder_path . $file_name;
            $fieldset = require_once $file_path;
            if(isset($fieldset['fieldset']) && $fieldset['fieldset']){
                $fieldset_name = $fieldset['fieldset'];
                self::setFieldset( $fieldset_name, $fieldset );
            }
        }
    }

    public static function addField( $fieldset = '', $field_name = '', $params = array() ){
        if( empty($fieldset) || !isset(self::$fieldsets[$fieldset]) || empty($field_name) ) return;
    }
}