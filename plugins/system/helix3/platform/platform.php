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
use HelixULT\Model\HelixUltModel as HelixUltModel;

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
        $htmlView  = '<div id="helix-ultimate">';
        $htmlView .= '<div class="helix-ultimate-sidebar">';
        $htmlView .= '<div class="helix-ultimate-logo">';
        $htmlView .= '<img src="'.\JURI::root(true).'/plugins/system/helix3/assets/images/helix-logo.svg" alt="Helix Ultimate by JoomShaper"/>';
        $htmlView .= '</div>';
        $htmlView .= '<div class="helix-ultimate-options-wrap">';

        return $htmlView;
    }

    private function frameworkFormHTMLEnd(){
        $htmlView  = '</div>';

        $htmlView .= '<div class="helix-ultimate-footer clearfix">';
        $htmlView .= '<div class="helix-ultimate-copyright">Helix Ultimate 2.0.1 Beta 1<br />By <a target="_blank" href="https://www.joomshaper.com">JoomShaper</a></div>';
        $htmlView .= '<div class="helix-ultimate-action"><button class="btn btn-primary action-save-template" data-id="'. $this->id .'" data-view="'. $this->view .'"><span class="fa fa-save"></span> Save</button></div>';
        $htmlView .= '</div>';

        $htmlView .= '</div>';

        $htmlView .= '<div class="helix-ultimate-preview">';
        $htmlView .= '<iframe id="helix-ultimate-template-preview" src="'.\JURI::root(true).'" style="width: 100%; height: 100%;"></iframe>';
        $htmlView .= '</div>';
        $htmlView .= '</div>';

        return $htmlView;
    }
    
}
