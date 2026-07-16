<?php
/**
 * Helix Ultimate Joomla 3 security patch installer.
 *
 * @package HelixUltimateJ3Security
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\Installer;

class Helixultimatej3securityfixesInstallerScript
{
    const PACKAGE_VERSION = '1.0.0';

    const HELIX_BASELINE = '2.1.4-j3sec';

    const SUPPORTED_HELIX_MAX = '2.1.4-j3sec';

    /** @var array<int, string> */
    private $patchedFiles = [
        'plugins/system/helixultimate/helixultimate.php',
        'plugins/system/helixultimate/helixultimate.xml',
        'plugins/system/helixultimate/script.php',
        'plugins/system/helixultimate/src/Platform/Helper.php',
        'plugins/system/helixultimate/src/Platform/Platform.php',
        'plugins/system/helixultimate/src/Platform/Request.php',
        'plugins/system/helixultimate/src/Platform/Media.php',
        'plugins/system/helixultimate/src/Platform/Blog.php',
        'plugins/system/helixultimate/src/HttpResponse/Response.php',
        'plugins/system/helixultimate/src/Core/Classes/HelixultimateMenu.php',
        'plugins/system/helixultimate/overrides/layouts/joomla/content/blog/audio.php',
        'plugins/system/helixultimate/overrides/layouts/joomla/content/blog/gallery.php',
        'plugins/system/helixultimate/overrides/layouts/joomla/content/blog/video.php',
        'plugins/system/helixultimate/overrides/mod_menu/default.php',
        'templates/shaper_helixultimate/templateDetails.xml',
    ];

    public function preflight($type, $parent)
    {
        if (version_compare(JVERSION, '3.10', '<') || version_compare(JVERSION, '4.0', '>=')) {
            Factory::getApplication()->enqueueMessage(
                'Helix Ultimate J3 Security Fixes requires Joomla 3.10.x. Upgrade Joomla or use Helix 2.2.x on Joomla 4+.',
                'error'
            );

            return false;
        }

        if (version_compare(PHP_VERSION, '7.2.5', '<')) {
            Factory::getApplication()->enqueueMessage(
                'Helix Ultimate J3 Security Fixes requires PHP 7.2.5 or later.',
                'error'
            );

            return false;
        }

        $helixVersion = $this->getHelixPluginVersion();

        if ($helixVersion === null) {
            Factory::getApplication()->enqueueMessage(
                'Helix Ultimate system plugin (plg_system_helixultimate) is not installed.',
                'error'
            );

            return false;
        }

        if (version_compare($helixVersion, self::SUPPORTED_HELIX_MAX, '>')) {
            Factory::getApplication()->enqueueMessage(
                sprintf(
                    'Unsupported Helix Ultimate version %s. Supported version: up to %s.',
                    $helixVersion,
                    self::SUPPORTED_HELIX_MAX
                ),
                'error'
            );

            return false;
        }

        if (! $this->isTemplatePresent()) {
            Factory::getApplication()->enqueueMessage(
                'Warning: shaper_helixultimate template was not found. Plugin security files will still be patched.',
                'warning'
            );
        }

        foreach ($this->patchedFiles as $relativePath) {
            $absolutePath = JPATH_ROOT . '/' . $relativePath;

            if (file_exists($absolutePath) && ! is_writable($absolutePath)) {
                Factory::getApplication()->enqueueMessage(
                    'Cannot write to ' . $relativePath . '. Check file permissions.',
                    'error'
                );

                return false;
            }

            $parentDir = dirname($absolutePath);

            if (is_dir($parentDir) && ! is_writable($parentDir)) {
                Factory::getApplication()->enqueueMessage(
                    'Cannot write to directory for ' . $relativePath . '. Check folder permissions.',
                    'error'
                );

                return false;
            }
        }

        return true;
    }

    public function postflight($type, $parent)
    {
        $this->writeAuditLog();
        $this->enableHelixPlugin();

        Factory::getApplication()->enqueueMessage(
            '<strong>Helix Ultimate hardened successfully (v' . self::PACKAGE_VERSION . ').</strong> '
            . 'Security fixes from Helix ' . self::HELIX_BASELINE . ' have been applied '
            . '(CSRF/ACL, path traversal, upload hardening, XSS sanitization, open redirect protection). '
            . '<strong>Backup recommended.</strong> Plan migration to Joomla 4+ and Helix 2.2.x.',
            'message'
        );

        $this->uninstallPackage();
    }

    private function enableHelixPlugin()
    {
        $db     = Factory::getDBO();
        $query  = $db->getQuery(true);
        $fields = [
            $db->quoteName('enabled') . ' = 1',
        ];

        $conditions = [
            $db->quoteName('type') . ' = ' . $db->quote('plugin'),
            $db->quoteName('element') . ' = ' . $db->quote('helixultimate'),
            $db->quoteName('folder') . ' = ' . $db->quote('system'),
        ];

        $query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);
        $db->setQuery($query);

        try
        {
            $db->execute();
        } catch (\Exception $e) {
            // Ignore database errors during execution
        }
    }

    private function writeAuditLog()
    {
        $checksums = [];

        foreach ($this->patchedFiles as $relativePath) {
            $absolutePath = JPATH_ROOT . '/' . $relativePath;

            if (is_file($absolutePath)) {
                $checksums[$relativePath] = sha1_file($absolutePath);
            }
        }

        $payload = [
            'package'        => 'helixultimatej3securityfixes',
            'packageVersion' => self::PACKAGE_VERSION,
            'helixBaseline'  => self::HELIX_BASELINE,
            'appliedAt'      => gmdate('c'),
            'joomlaVersion'  => JVERSION,
            'phpVersion'     => PHP_VERSION,
            'files'          => $checksums,
        ];

        $logDir = JPATH_ADMINISTRATOR . '/logs';

        if (! is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        @file_put_contents(
            $logDir . '/helix_j3_security_applied.json',
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }

    private function uninstallPackage()
    {
        $db    = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select(['extension_id', 'type', 'name'])
            ->from('#__extensions')
            ->where($db->quoteName('element') . ' = ' . $db->quote('helixultimatej3securityfixes'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('file'));
        $db->setQuery($query);
        $extensions = $db->loadObjectList();

        foreach ($extensions as $extension) {
            if (Installer::getInstance()->uninstall($extension->type, $extension->extension_id)) {
                Factory::getApplication()->enqueueMessage(
                    'Helix Ultimate J3 Security Fixes package removed (no permanent extension left behind).',
                    'message'
                );
            } else {
                Factory::getApplication()->enqueueMessage(
                    'Could not auto-remove the patch package. Uninstall "Helix Ultimate J3 Security Fixes" manually from Extensions.',
                    'warning'
                );
            }
        }
    }

    private function getHelixPluginVersion()
    {
        $db    = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select($db->quoteName('manifest_cache'))
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
            ->where($db->quoteName('folder') . ' = ' . $db->quote('system'))
            ->where($db->quoteName('element') . ' = ' . $db->quote('helixultimate'));
        $db->setQuery($query);
        $manifestCache = $db->loadResult();

        if (empty($manifestCache)) {
            return null;
        }

        $data = json_decode($manifestCache, true);

        return isset($data['version']) ? $data['version'] : null;
    }

    private function isTemplatePresent()
    {
        $db    = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('template'))
            ->where($db->quoteName('element') . ' = ' . $db->quote('shaper_helixultimate'));
        $db->setQuery($query);

        return (int) $db->loadResult() > 0;
    }
}
