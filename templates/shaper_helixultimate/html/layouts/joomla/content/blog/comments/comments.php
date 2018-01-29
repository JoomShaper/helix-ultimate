<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('JPATH_BASE') or die();

$params = JFactory::getApplication()->getTemplate(true)->params;

if( $params->get('commenting_engine') != 'disabled' )
{
	$url = \JRoute::_(\ContentHelperRoute::getArticleRoute($displayData->id . ':' . $displayData->alias, $displayData->catid, $displayData->language));
	$root = \JURI::base();
	$root = new \JURI($root);
	$url = $root->getScheme() . '://' . $root->getHost() . $url;

	echo '<div id="article-comments">';
	echo \JLayoutHelper::render( 'joomla.content.blog.comments.comments.' . $params->get('commenting_engine'), array( 'item'=>$displayData, 'params'=>$params, 'url'=>$url ) );
	echo '</div>';
}
