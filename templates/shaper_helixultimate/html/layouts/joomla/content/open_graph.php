<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('JPATH_BASE') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

extract($displayData);

$doc = Factory::getDocument();
$config = Factory::getConfig();
$sitename = $config->get('sitename');

// Facebook
$doc->addCustomTag('<meta property="og:type" content="article" />');
$doc->addCustomTag('<meta property="og:url" content="'. Uri::current() . '" />');
$doc->addCustomTag('<meta property="og:title" content="'. htmlspecialchars($title ) .'" />');
$doc->addCustomTag('<meta property="og:description" content="'. HTMLHelper::_('string.truncate', (strip_tags($content)), 150) .'" />');

if(isset($image) && $image)
{
    $doc->addCustomTag('<meta property="og:image" content="'. Uri::root().ltrim($image, '/') .'" />');
}

if(isset($fb_app_id) && $fb_app_id)
{
    $doc->addCustomTag('<meta property="fb:app_id" content="'. (int) $fb_app_id . '" />');
}

$doc->addCustomTag('<meta property="og:site_name" content="'. htmlspecialchars($sitename) .'" />');

// Twitter
$doc->addCustomTag('<meta name="twitter:description" content="'. HTMLHelper::_('string.truncate', (strip_tags($content)), 150) .'" />');

if(isset($image) && $image)
{
    $doc->addCustomTag('<meta name="twitter:image:src" content="'. Uri::root().ltrim($image, '/') .'" />');
}

if(isset($twitter_site) && $twitter_site)
{
    $doc->addCustomTag('<meta name="twitter:site" content="@'. htmlspecialchars($twitter_site) .'" />');
}

$doc->addCustomTag('<meta name="twitter:card" content="summary_large_image" />');