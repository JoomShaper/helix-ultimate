<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/
defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Uri\Uri;

$params = ComponentHelper::getParams('com_media');
$input  = Factory::getApplication()->input;

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa
	->useScript('keepalive')
	->useStyle('com_media.mediamanager')
	->useScript('com_media.mediamanager')
	->useStyle('webcomponent.joomla-alert')
	->useScript('messages');

// Populate the language
$this->loadTemplate('texts');

$tmpl = $input->getCmd('tmpl');

// Load the toolbar when we are in an iframe
if ($tmpl === 'component')
{
	echo '<div class="subhead noshadow">';
	echo Toolbar::getInstance('toolbar')->render();
	echo '</div>';
}

$mediaTypes = '&mediatypes=' . $input->getString('mediatypes', '0,1,2,3');

// Populate the media config
$config = array(
	'apiBaseUrl'          => Uri::base() . 'index.php?option=com_media&format=json' . $mediaTypes,
	'csrfToken'           => Session::getFormToken(),
	'filePath'            => $params->get('file_path', 'images'),
	'fileBaseUrl'         => Uri::root() . $params->get('file_path', 'images'),
	'fileBaseRelativeUrl' => $params->get('file_path', 'images'),
	'editViewUrl'         => Uri::base() . 'index.php?option=com_media&view=file' . ($tmpl ? '&tmpl=' . $tmpl : '')  . $mediaTypes,
	'imagesExtensions'    => explode(',', $params->get('image_extensions', 'bmp,gif,jpg,jpeg,png,webp')),
	'audioExtensions'     => explode(',', $params->get('audio_extensions', 'mp3,m4a,mp4a,ogg')),
	'videoExtensions'     => explode(',', $params->get('video_extensions', 'mp4,mp4v,mpeg,mov,webm')),
	'documentExtensions'  => explode(',', $params->get('doc_extensions', 'doc,odg,odp,ods,odt,pdf,ppt,txt,xcf,xls,csv')),
	'maxUploadSizeMb'     => $params->get('upload_maxsize', 10),
	'providers'           => (array) $this->providers,
	'currentPath'         => $this->currentPath,
	'isModal'             => $tmpl === 'component',
);
$this->document->addScriptOptions('com_media', $config);
$app = Factory::getApplication();
/**
 * Add system-message-container above subhead
 */
$app->getDocument()->addScriptDeclaration(
	"
		jQuery(function($) {
			let element = '<div id=\"system-message-container\" aria-live=\"polite\"></div>';
			$( document ).ready(function() {
				$('body.com-media').prepend(element);
			});
		});
	"
);
?>

<div id="com-media"></div>
