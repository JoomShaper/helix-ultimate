<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2020 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

$templatePath = JPATH_BASE . '/plugins/system/helixultimate/overrides/com_content/form/edit.php';

if (\file_exists($templatePath))
{
	require_once $templatePath;
}
