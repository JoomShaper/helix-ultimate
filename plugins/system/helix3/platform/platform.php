<?php
/**
* @package HelixUltimate Framework
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2017 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

namespace HelixULT;

defined ('_JEXEC') or die ('resticted access');

require_once __DIR__.'/options.php';
require_once __DIR__.'/request.php';

use HelixULT\SPOptions as SPOptions;

class Platform{
    
    protected $app;

    protected $option;

    protected $preview;

    protected $view;

    protected $id;

    protected $request;


    public function __construct(){
        $this->app = \JFactory::getApplication();
        $input = $this->app->input;

        $this->option     = $input->get('option','');
        $this->preview    = $input->get('preview','');
        $this->view       = $input->get('view','');
        $this->id         = $input->get('id',NULL);
        $this->request    = $input->get('request','');
    }

    public function initialize(){
        if( $this->option == 'com_ajax' && $this->preview == 'theme' && $this->view == 'style' && $this->request == 'ajaxHelix' && $this->id ){

            $request = new Request;
            $request->initialize();

        } else if( $this->option == 'com_ajax' && $this->preview == 'theme' && $this->view == 'style' && $this->id ) {
            $frmkHTML  = $this->frameworkFormHTMLStart();

            $frmkOptions = new SPOptions();
            $frmkHTML .= $frmkOptions->renderBuilderSidebar();

            $frmkHTML .= $this->frameworkFormHTMLEnd();

            echo $frmkHTML;
        }
    }

    private function frameworkFormHTMLStart(){
        $htmlView  = '<div id="sp-helix-container">';
        $htmlView .= '<div class="sidebar-container">';
        $htmlView .= '<div class="helix-logo">';
        $htmlView .= '<img src="'.\JURI::root(true).'/plugins/system/helix3/assets/images/helix-ultimate-final-logo.svg" alt="Helix Ultimate Template"/>';
        $htmlView .= '</div>';
        $htmlView .= '<div style="margin-top: 15px; margin-left: 10px;">';
        $htmlView .= '<button class="btn btn-success btn-lg tmpl-style-save" data-tmplID="'. $this->id .'" data-tmplView="'. $this->view .'">Save Settings</button>';
        $htmlView .= '</div>';
        $htmlView .= '<div>';

        return $htmlView;
    }

    private function frameworkFormHTMLEnd(){
        $htmlView  = '</div>';
        $htmlView .= '</div>';
        $htmlView .= '<div class="preview-container">';
        $htmlView .= '<iframe id="theme-preview" src="'.\JURI::root(true).'" width="100%" height="100%"></iframe>';
        $htmlView .= '</div>';
        $htmlView .= '</div>';

        return $htmlView;
    }

}