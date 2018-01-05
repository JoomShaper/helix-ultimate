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

    private function frameworkFormHTMLEnd()
    {
        $htmlView  = '</div>';
        $htmlView .= '</div>';
        $htmlView .= '<div class="preview-container">';
        $htmlView .= '<iframe id="theme-preview" src="'.\JURI::root(true).'" width="100%" height="100%"></iframe>';
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
        \JFactory::getLanguage()->load('tpl_shaper_helix3', JPATH_SITE, null, true);
        $doc = \JFactory::getDocument();
        
        $doc->setTitle("Helix Template Framework by JoomShaper");

        $helix_plg_url = \JURI::root(true).'/plugins/system/helix3';
        $doc->addScriptdeclaration('var layoutbuilder_base="' . \JURI::root() . '";');
        $doc->addScriptDeclaration("var basepath = '{$helix_plg_url}';");
        $doc->addScriptDeclaration("var pluginVersion = 34;");
        //  $doc->addFavicon(JUri::root(true).'/administrator/templates/isis/favicon.ico');

        \JHtml::_('jquery.ui', array('core', 'sortable'));
        \JHtml::_('bootstrap.framework');
        \JHtml::_('behavior.formvalidator');
        \JHtml::_('behavior.keepalive');
        \JHtml::_('formbehavior.chosen', 'select');
        \JHtml::_('behavior.colorpicker');

        $doc->addScript($helix_plg_url .'/assets/js/helper.js');
        $doc->addScript($helix_plg_url .'/assets/js/webfont.js');
        $doc->addScript($helix_plg_url . '/assets/js/modal.js');
        $doc->addScript($helix_plg_url . '/assets/js/admin.general.js');
        $doc->addScript($helix_plg_url . '/assets/js/admin.layout.js');
        $doc->addScript(\JURI::root(true) . '/media/media/js/mediafield.min.js');

        //CSS
        $doc->addStyleSheet($helix_plg_url.'/assets/css/bootstrap.css');
        $doc->addStyleSheet($helix_plg_url.'/assets/css/modal.css');
        $doc->addStyleSheet(\JURI::root(true) . '/administrator/templates/isis/css/template.css');
        $doc->addStyleSheet(\JURI::root(true) . '/media/system/css/modal.css');
        $doc->addStyleSheet($helix_plg_url.'/assets/css/font-awesome.min.css');
        $doc->addStyleSheet($helix_plg_url.'/assets/css/admin.general.css');
        $doc->addScript( $helix_plg_url. '/assets/js/admin.helix-ultimate.js' );

        echo $doc->render(false,[
            'file' => 'component.php',
            'template' => 'HelixUltimate',
        ]);
    }
}