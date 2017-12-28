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
    
    class JFormFieldGroup extends JFormField {
        protected $type = 'Group';
        public function getInput() {
            $text   = (string) $this->element['title'];
            return '<div class="group-label">' . JText::_($text) . '</div>';
        }

        public function getLabel(){
            return false;
        }
    }