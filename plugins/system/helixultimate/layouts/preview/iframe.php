<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Version;

defined('_JEXEC') or die();

$version = new Version();
$JoomlaVersion = $version->getShortVersion();

if (version_compare($JoomlaVersion, '5.0.0', '<'))
{
	$doc = Factory::getDocument();
	$doc->addStyleSheet(Uri::root(true) . '/media/system/css/joomla-fontawesome.min.css', ['relative' => false, 'version' => 'auto']);
}

extract($displayData);

$style = '';

if (empty($width))
{
	$width = '100%';
}

if (empty($height))
{
	$height = '100%';
}

$style .= "width: {$width}; height: {$height}; box-shadow: rgba(139, 139, 143, 0.56) 3px 0px 10px;";

?>

<iframe id="hu-template-preview" src="<?php echo $url; ?>" frameborder="0" style="<?php echo $style; ?>">
</iframe>