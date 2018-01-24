<?php
/**
* @package Helix3 Framework
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2015 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

//no direct accees
defined ('_JEXEC') or die ('resticted aceess');

jimport('joomla.plugin.plugin');
jimport( 'joomla.event.plugin' );
jimport('joomla.registry.registry');

require_once __DIR__.'/platform/platform.php';

use HelixULT\Platform as Platform;

class  plgSystemHelixultimate extends JPlugin
{
    protected $autoloadLanguage = true;

    protected $app;

    public function onAfterDispatch()
    {
        if (!$this->app->isAdmin())
        {
            $activeMenu = $this->app->getMenu()->getActive();

            if (is_null($activeMenu)) $template_style_id = 0;
            else $template_style_id = (int) $activeMenu->template_style_id;

            if ( $template_style_id > 0 )
            {
                JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_templates/tables');
                $style = JTable::getInstance('Style', 'TemplatesTable');
                $style->load($template_style_id);
                if ( !empty($style->template) ) $this->app->setTemplate($style->template, $style->params);
            }
        }
    }

    public function onContentPrepareForm($form, $data)
    {
        $doc = JFactory::getDocument();
        $plg_path = JURI::root(true).'/plugins/system/helixultimate';
        JForm::addFormPath(JPATH_PLUGINS.'/system/helixultimate/params');

        if ($form->getName() == 'com_menus.item')
        {
            JHtml::_('jquery.framework');
            JHtml::_('jquery.ui', array('core', 'more', 'sortable'));

            $doc->addStyleSheet($plg_path.'/assets/css/bootstrap.css');
            $doc->addStyleSheet($plg_path.'/assets/css/font-awesome.min.css');
            $doc->addStyleSheet($plg_path.'/assets/css/modal.css');
            $doc->addStyleSheet($plg_path.'/assets/css/menu.generator.css');
            $doc->addScript($plg_path.'/assets/js/modal.js');
            $doc->addScript( $plg_path. '/assets/js/menu.generator.js' );

            $form->loadFile('menu-parent', false);
        }

        //Article Post format
        if ($form->getName()=='com_content.article')
        {
            $doc->addStyleSheet($plg_path.'/assets/css/font-awesome.min.css');
            $tpl_path = JPATH_ROOT . '/templates/' . $this->getTemplateName();

            if (JFile::exists( $tpl_path . '/article-formats.xml' ))
            {
                JForm::addFormPath($tpl_path);
            }
            else
            {
                JForm::addFormPath(JPATH_PLUGINS . '/system/helixultimate/params');
            }
            $form->loadFile('article-formats', false);
        }
    }

    // Live Update system
    public function onExtensionAfterSave($option, $data)
    {
        if ($option == 'com_templates.style' && !empty($data->id))
        {
            $params = new JRegistry;
            $params->loadString($data->params);

            $email       = $params->get('joomshaper_email');
            $license_key = $params->get('joomshaper_license_key');
            $template    = trim($data->template);

            if(!empty($email) and !empty($license_key) )
            {
                $extra_query = 'joomshaper_email=' . urlencode($email);
                $extra_query .='&amp;joomshaper_license_key=' . urlencode($license_key);

                $db = JFactory::getDbo();
                $fields = array(
                    $db->quoteName('extra_query') . '=' . $db->quote($extra_query),
                    $db->quoteName('last_check_timestamp') . '=0'
                );

                $query = $db->getQuery(true)
                    ->update($db->quoteName('#__update_sites'))
                    ->set($fields)
                    ->where($db->quoteName('name').'='.$db->quote($template));
                $db->setQuery($query);
                $db->execute();
            }
        }
    }

    public function onAfterRoute()
    {
        $option     = $this->app->input->get('option','');
        $preview    = $this->app->input->get('preview','');
        $view       = $this->app->input->get('view','');
        $action     = $this->app->input->get('action', '');;

        $doc = JFactory::getDocument();
        if($this->app->isAdmin())
        {
            if($option == 'com_ajax' && $preview == 'theme' && $view == 'style')
            {
                Platform::loadFrameworkSystem();
                JEventDispatcher::getInstance()->trigger('onAfterRespond');
                die;
            }
        }
    }

    public function onAfterRespond()
    {
        if($this->app->isAdmin()){
            $platform = new Platform;
            $platform->initialize();
        }
    }

    private function updateTemplateStyle($data = '', $id = 0)
    {
        $db = JFactory::getDbo();
        if(empty($data)) return false;

        $data = json_encode($data);

        $query = $db->getQuery(true);
        $fields = array($db->quoteName('params') . '=' . $db->quote($data));
        $conditions = array(
                    $db->quoteName('id') .'='. $db->quote($id),
                    $db->quoteName('client_id') .'= 0'
                );

        $query->update($db->quoteName('#__template_styles'))->set($fields)->where($conditions);
        $db->setQuery($query);
        $result = $db->execute();
    }

    private function getTemplateName()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->quoteName(array('template')));
        $query->from($db->quoteName('#__template_styles'));
        $query->where($db->quoteName('client_id') . ' = 0');
        $query->where($db->quoteName('home') . ' = 1');
        $db->setQuery($query);

        return $db->loadObject()->template;
    }
}
