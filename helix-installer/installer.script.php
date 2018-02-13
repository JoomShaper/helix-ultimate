<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die();

class plgSystemTmplHelixUltimateInstallerScript {


    public function preflight($type, $parent) {
        
        $db = JFactory::getDBO();

        $src = $parent->getParent()->getPath('source');
        $manifest = $parent->getParent()->manifest;
        $plugins = $manifest->xpath('plugins/plugin');

        foreach ($plugins as $key => $plugin) {
            $name = (string)$plugin->attributes()->plugin;
            $group = (string)$plugin->attributes()->group;
            $path = $src.'/plugins/'.$group;

            if (JFolder::exists($src.'/plugins/'.$group.'/'.$name))
            {
                $path = $src.'/plugins/'.$group.'/'.$name;
            }

            $installer = new JInstaller;
            $result = $installer->install($path);

            if ($result) {
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

        $template_path = $src.'/template';
        if (JFolder::exists( $template_path ))
        {
            $installer = new JInstaller;
            $result = $installer->install($template_path);
        }

        $conf = JFactory::getConfig();
        $conf->set('debug', false);
        $parent->getParent()->abort();

    }

    public function install($parent) {

        $manifest = $parent->getParent()->manifest;
        $src = $parent->getParent()->getPath('source');
        $templates = $manifest->xpath('template');

        foreach($templates as $key => $template)
        {
            $tmpl_name = (string)$template->attributes()->name;
            $template_path = $src.'/template';
            $options_default = file_get_contents($template_path .'/layout/default.json');

            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $fields = array(
                $db->quoteName('params') . ' = ' . $db->quote($options_default)
            );

            $conditions = array(
                $db->quoteName('client_id') . ' = 0', 
                $db->quoteName('template') . ' = ' . $db->quote($tmpl_name)
            );

            $query->update($db->quoteName('#__template_styles'))->set($fields)->where($conditions);
            $db->setQuery($query);
            $db->execute();
        }
    }

    public function abort($msg = null, $type = null){
        if ($msg) {
            JError::raiseWarning(100, $msg);
        }
        foreach ($this->packages as $package) {
            $package['installer']->abort(null, $type);
        }
    }
}
