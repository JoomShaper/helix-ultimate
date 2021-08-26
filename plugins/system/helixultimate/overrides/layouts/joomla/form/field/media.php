<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('JPATH_BASE') or die();

if (JVERSION < 4)
{
    require \JPATH_ROOT . '/plugins/system/helixultimate/html/layouts/form/field/media_j3.php';
}
else
{
    require \JPATH_ROOT . '/plugins/system/helixultimate/html/layouts/form/field/media.php';
}
