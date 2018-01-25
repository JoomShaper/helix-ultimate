<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('JPATH_BASE') or die();

$params 	= JFactory::getApplication()->getTemplate(true)->params;

if( ( $params->get('commenting_engine') != 'disabled' ) && ( $params->get('comments_count') ) ) {
	$url = JRoute::_(ContentHelperRoute::getArticleRoute($displayData['item']->id . ':' . $displayData['item']->alias, $displayData['item']->catid, $displayData['item']->language));
	$root = JURI::base();
	$root = new JURI($root);
	$url = $root->getScheme() . '://' . $root->getHost() . $url;

	?>
	<span class="comments">
		<i class="fa fa-comments-o"></i>
		<?php echo JLayoutHelper::render( 'joomla.content.comments.engine.count.' . $params->get('commenting_engine'), array( 'item'=>$displayData, 'params'=>$params, 'url'=>$url ) ); ?>
	</span>
	<?php
}
