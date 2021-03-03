<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2020 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('JPATH_BASE') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

$button = $displayData;

if (!$button->get('modal'))
{
	return;
}

$doc = Factory::getDocument();
$doc->addScriptDeclaration(
	<<<JS
	jQuery(function($) {
		let openedModal = $('.modal.show');
		if (openedModal.length) {
			dismissButton = openedModal.find('button[data-dismiss=modal]');
			console.log(dismissButton);
			if (dismissButton.length) {
				dismissButton.on('click', function(e) {
					e.preventDefault();
					alert($(this).data('dismiss'));
				});
			}
		}
	});
	JS
);

$fontAwesomePath = JPATH_THEMES . '/shaper_helixultimate/css/font-awesome.min.css';
Factory::getDocument()->addStylesheet(Uri::root(true) . '/templates/shaper_helixultimate/css/font-awesome.min.css');

$class 		= $button->get('class', '');
$class 		.= $button->get('modal', '');
$link 		= $button->get('link', '');
$onclick 	= !empty($button->get('onclick', '')) ? ' onclick="' . $button->get('onclick') . '"' : '';
$title 		= $button->get('title', $button->get('text', ''));
$options 	= $button->get('options', array());
$selector   = str_replace(' ', '', $button->get('text')) . 'Modal';
$options 	= is_array($options) ? $options : array();

// Create the modal
echo HTMLHelper::_(
	'bootstrap.renderModal',
	$selector,
	array(
		'url'    => Route::_($link),
		'title'  => $title,
		'height' => array_key_exists('height', $options) ? $options['height'] : '400px',
		'width'  => array_key_exists('width', $options) ? $options['width'] : '800px',
		'bodyHeight'  => array_key_exists('bodyHeight', $options) ? $options['bodyHeight'] : '70',
		'modalWidth'  => array_key_exists('modalWidth', $options) ? $options['modalWidth'] : '80',
		'footer' => '<button type="button" role="button" class="btn btn-secondary" data-dismiss="modal" aria-hidden="true">'
			. Text::_("JLIB_HTML_BEHAVIOR_CLOSE") . '</button>'
	)
);
