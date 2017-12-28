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

class JFormFieldAsset extends JFormField
{
    protected	$type = 'Asset';

    protected function getInput() {

        $helix_plg_url = JURI::root(true).'/plugins/system/helix3';
        $doc = JFactory::getDocument();
        $doc->addScriptdeclaration('var layoutbuilder_base="' . JURI::root() . '";');
        $doc->addScriptDeclaration("var basepath = '{$helix_plg_url}';");
        $doc->addScriptDeclaration("var pluginVersion = '{$this->getVersion()}';");

        //Core scripts
        JHtml::_('jquery.ui', array('core', 'sortable'));
        JHtml::_('formbehavior.chosen', 'select');

        $doc->addScript($helix_plg_url.'/assets/js/helper.js');
        $doc->addScript($helix_plg_url.'/assets/js/webfont.js');
        $doc->addScript($helix_plg_url.'/assets/js/modal.js');
        $doc->addScript($helix_plg_url.'/assets/js/admin.general.js');
        $doc->addScript($helix_plg_url.'/assets/js/admin.layout.js');

        //CSS
        $doc->addStyleSheet($helix_plg_url.'/assets/css/bootstrap.css');
        $doc->addStyleSheet($helix_plg_url.'/assets/css/modal.css');
        $doc->addStyleSheet($helix_plg_url.'/assets/css/font-awesome.min.css');
        $doc->addStyleSheet($helix_plg_url.'/assets/css/admin.general.css');

        $doc->addScript($helix_plg_url.'/assets/js/custom_builder.js');
        $doc->addStyleSheet($helix_plg_url.'/assets/css/custom_builder.css');



        //Required for live header footer css
        $helixfw_helper_file = '/plugins/system/helix3/layout/layout-settings/helix4-helpers.php';
        include_once JPATH_ROOT.$helixfw_helper_file;

        $template= helixfw_get_template()->template;
        $header_footer_css_path     = JPATH_SITE . '/templates/' . $template . '/layout/inc/header_footer.css';
        $header_footer_css_uri     = JURI::root(true) . '/templates/' . $template . '/layout/inc/header_footer.css';
        if (file_exists($header_footer_css_path)){
            $doc->addStyleSheet($header_footer_css_uri);
        }

        $doc->setMetaData('custom_helix4_token', JSession::getFormToken() );
    }

    private function getVersion() {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query
            ->select(array('*'))
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('type').' = '.$db->quote('plugin'))
            ->where($db->quoteName('element').' = '.$db->quote('helix3'))
            ->where($db->quoteName('folder').' = '.$db->quote('system'));
        $db->setQuery($query);
        $result = $db->loadObject();
        $manifest_cache = json_decode($result->manifest_cache);
        if (isset($manifest_cache->version)) {
            return $manifest_cache->version;
        }
        return;
    }
}
