<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die();

class plgSystemTmplHelixUltimateInstallerScript {

    function postflight($type, $parent) {
        $db = \JFactory::getDBO();
        $status = new stdClass;
        $status->plugins = array();

        $src = $parent->getParent()->getPath('source');
        $manifest = $parent->getParent()->manifest;
        $plugins = $manifest->xpath('plugins/plugin');

        foreach ($plugins as $key => $plugin) {
            $name = (string)$plugin->attributes()->plugin;
            $group = (string)$plugin->attributes()->group;
            $path = $src.'/plugins/'.$group;

            if (\JFolder::exists($src.'/plugins/'.$group.'/'.$name))
            {
                $path = $src.'/plugins/'.$group.'/'.$name;
            }

            $installer = \JInstaller::getInstance();
            $result = $installer->install($path);

            if ($result) {
                $db = \JFactory::getDbo();
                $query = $db->getQuery(true);
                
                $fields = array(
                    $db->quoteName('enabled') . ' = 1'
                );
                
                $conditions = array(
                    $db->quoteName('type') . ' = ' . $db->quote('plugin'),
                    $db->quoteName('element') . ' = ' . $db->quote($name),
                    $db->quoteName('folder') . ' = ' . $db->quote($group)
                );
                
                $query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);
                $db->setQuery($query);
                $db->execute();
            }
        }

        $template_path = $src . '/template';

        if (\JFolder::exists( $template_path )) {
            $installer = \JInstaller::getInstance();
            $installer->install($template_path);
        }

        $conf = \JFactory::getConfig();
        $conf->set('debug', false);
        $parent->getParent()->abort();
    }

    public function abort($msg = null, $type = null){
        if ($msg) {
            \JError::raiseWarning(100, $msg);
        }
        foreach ($this->packages as $package) {
            $package['installer']->abort(null, $type);
        }
    }
}
