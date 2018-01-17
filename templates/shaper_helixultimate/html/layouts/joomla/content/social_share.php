<?php
/**
* @package     Joomla.Site
* @subpackage  Layout
*
* @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('JPATH_BASE') or die;

$url = JRoute::_(ContentHelperRoute::getArticleRoute($displayData->id . ':' . $displayData->alias, $displayData->catid, $displayData->language));
$root = JURI::base();
$root = new JURI($root);
$url = $root->getScheme() . '://' . $root->getHost() . $url;
$params = JFactory::getApplication()->getTemplate(true)->params;

if( $params->get('social_share') ) : ?>
<div class="article-social-share">
	<div class="social-share-icon">
		<ul>
			<li>
				<a class="facebook" onClick="window.open('http://www.facebook.com/sharer.php?u=<?php echo $url; ?>','Facebook','width=600,height=300,left='+(screen.availWidth/2-300)+',top='+(screen.availHeight/2-150)+''); return false;" href="http://www.facebook.com/sharer.php?u=<?php echo $url; ?>" title="<?php echo JText::_('HELIX_ULTIMATE_SHARE_FACEBOOK'); ?>">
					<span class="fa fa-facebook"></span>
				</a>
			</li>
			<li>
				<a class="twitter" title="<?php echo JText::_('HELIX_ULTIMATE_SHARE_TWITTER'); ?>" onClick="window.open('http://twitter.com/share?url=<?php echo $url; ?>&amp;text=<?php echo str_replace(" ", "%20", $displayData->title); ?>','Twitter share','width=600,height=300,left='+(screen.availWidth/2-300)+',top='+(screen.availHeight/2-150)+''); return false;" href="http://twitter.com/share?url=<?php echo $url; ?>&amp;text=<?php echo str_replace(" ", "%20", $displayData->title); ?>">
					<span class="fa fa-twitter"></span>
				</a>
			</li>
			<li>
				<a class="gplus" title="<?php echo JText::_('HELIX_ULTIMATE_SHARE_GOOGLE_PLUS'); ?>" onClick="window.open('https://plus.google.com/share?url=<?php echo $url; ?>','Google plus','width=585,height=666,left='+(screen.availWidth/2-292)+',top='+(screen.availHeight/2-333)+''); return false;" href="https://plus.google.com/share?url=<?php echo $url; ?>" >
					<span class="fa fa-google-plus"></span></a>
				</li>
				<li>
					<a class="linkedin" title="<?php echo JText::_('HELIX_ULTIMATE_SHARE_LINKEDIN'); ?>" onClick="window.open('http://www.linkedin.com/shareArticle?mini=true&url=<?php echo $url; ?>','Linkedin','width=585,height=666,left='+(screen.availWidth/2-292)+',top='+(screen.availHeight/2-333)+''); return false;" href="http://www.linkedin.com/shareArticle?mini=true&url=<?php echo $url; ?>" >
						<span class="fa fa-linkedin-square"></span>
					</a>
				</li>
			</ul>
		</div>
	</div>
<?php endif; ?>
