<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('JPATH_BASE') or die();

if (JVERSION < 4)
{
    require \JPATH_ROOT . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'helixultimate' . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . 'form' . DIRECTORY_SEPARATOR . 'field' . DIRECTORY_SEPARATOR . 'media_j3.php';
}
else
{
    require \JPATH_ROOT . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'helixultimate' . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . 'form' . DIRECTORY_SEPARATOR . 'field' . DIRECTORY_SEPARATOR . 'media.php';
}
