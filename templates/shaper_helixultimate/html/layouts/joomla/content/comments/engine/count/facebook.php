<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('JPATH_BASE') or die();

if( $displayData['params']->get('fb_appID') != '' ) {

	$doc = JFactory::getDocument();

	if(!defined('HELIX_COMMENTS_FACEBOOK_COUNT')) {

		$doc->addScript( '//connect.facebook.net/en-GB/all.js#xfbml=1&appId=' . $displayData['params']->get('fb_appID') . '&version=v2.0' );

		define('HELIX_COMMENTS_FACEBOOK_COUNT', 1);

	}

	?>

	<span class="comments-anchor">
		<a href="<?php echo $displayData['url']; ?>#sp-comments"><?php echo JText::_('COMMENTS'); ?> <fb:comments-count href=<?php echo $displayData['url']; ?>></fb:comments-count></a>
	</span>

	<?php

}