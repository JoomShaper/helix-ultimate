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

class Platform
{

    protected $app;

    protected $option;

    protected $preview;

    protected $view;

    protected $id;

    protected $request;

    protected $user = array();

    protected $permission = false;

    public function __construct()
    {
        $this->user = \JFactory::getUser();
        $this->app  = \JFactory::getApplication();
        $input = $this->app->input;

        $this->option     = $input->get('option','');
        $this->preview    = $input->get('preview','');
        $this->view       = $input->get('view','');
        $this->id         = $input->get('id',NULL);
        $this->request    = $input->get('request','');
        $this->userTmplEditPermission();
    }


    public function initialize()
    {
      if( $this->option == 'com_ajax' && $this->preview == 'theme' && $this->view == 'style' && $this->request == 'ajaxHelix' && $this->id )
      {
          if (!$this->permission)
          {
              throw new \Exception("Permission Denied",403);
          }
          $request = new Request;
          $request->initialize();
      }
      else if( $this->option == 'com_ajax' && $this->preview == 'theme' && $this->view == 'style' && $this->id && $this->permission)
      {
          $frmkHTML    = $this->frameworkFormHTMLStart();
          $frmkOptions = new SPOptions();
          $frmkHTML    .= $frmkOptions->renderBuilderSidebar();
          $frmkHTML    .= $this->frameworkFormHTMLEnd();

          echo $frmkHTML;
      }
    }

    private function frameworkFormHTMLStart()
    {
            $htmlView  = '<div id="helix-ultimate">';
            $htmlView .= '<div class="helix-ultimate-sidebar">';
            $htmlView .= '<div class="helix-ultimate-logo">';
            $htmlView .= '<img src="'.\JURI::root(true).'/plugins/system/helixultimate/assets/images/helix-logo.svg" alt="Helix Ultimate by JoomShaper"/>';
            $htmlView .= '</div>';
            $htmlView .= '<div class="helix-ultimate-options-wrap">';

            return $htmlView;
    }

    private function frameworkFormHTMLEnd()
    {
        $htmlView  = '</div>';

        $htmlView .= '<div class="helix-ultimate-footer clearfix">';
        $htmlView .= '<div class="helix-ultimate-copyright">Helix Ultimate 1.0 Beta<br />By <a target="_blank" href="https://www.joomshaper.com">JoomShaper</a></div>';
        $htmlView .= '<div class="helix-ultimate-action"><button class="btn btn-primary action-save-template" data-id="'. $this->id .'" data-view="'. $this->view .'"><span class="fa fa-save"></span> Save</button></div>';
        $htmlView .= '</div>';

        $htmlView .= '</div>';

        $htmlView .= '<div class="helix-ultimate-preview">';
        $htmlView .= '<iframe id="helix-ultimate-template-preview" src="'.\JURI::root(true).'" style="width: 100%; height: 100%;"></iframe>';
        $htmlView .= '</div>';
        $htmlView .= '</div>';

        return $htmlView;
    }


    private function userTmplEditPermission()
    {
        if ($this->user->id)
        {
            if ($this->user->authorise('core.edit','com_templates'))
            {
                $this->permission = true;
            }
        }
    }

    public static function loadFrameworkSystem()
    {
        \JFactory::getLanguage()->load('tpl_shaper_helixultimate', JPATH_SITE, null, true);;

        $app = \JFactory::getApplication();
        $templateID = $app->input->get('id',NULL);

        $doc = \JFactory::getDocument();
        $doc->setTitle("Helix Template Framework by JoomShaper");

        $helix_plg_url = \JURI::root(true).'/plugins/system/helixultimate';
        $doc->addScriptdeclaration('var layoutbuilder_base="' . \JURI::root() . '";');
        $doc->addScriptDeclaration("var basepath = '{$helix_plg_url}';");
        $doc->addScriptDeclaration("var templateID = '{$templateID}';");
        $doc->addScriptDeclaration("var pluginVersion = 1;");

        \JHtml::_('jquery.ui', array('core', 'sortable'));
        \JHtml::_('bootstrap.framework');
        \JHtml::_('behavior.formvalidator');
        \JHtml::_('behavior.keepalive');
        \JHtml::_('formbehavior.chosen', 'select');
        \JHtml::_('behavior.colorpicker');

        $doc->addScript($helix_plg_url.'/assets/js/helper.js');
        $doc->addScript($helix_plg_url.'/assets/js/webfont.js');
        $doc->addScript($helix_plg_url.'/assets/js/modal.js');
        $doc->addScript($helix_plg_url.'/assets/js/admin.general.js');
        $doc->addScript($helix_plg_url.'/assets/js/admin.layout.js');

        $doc->addStyleSheet($helix_plg_url.'/assets/css/helix-ultimate.css');
        $doc->addStyleSheet($helix_plg_url.'/assets/css/font-awesome.min.css');
        $doc->addStyleSheet($helix_plg_url.'/assets/css/admin.general.css');

        $doc->addScript( $helix_plg_url. '/assets/js/media.js' );
        $doc->addScript( $helix_plg_url. '/assets/js/admin.helix-ultimate.js' );

        echo $doc->render(false,[
            'file' => 'component.php',
            'template' => 'HelixUltimate',
        ]);
    }
}
