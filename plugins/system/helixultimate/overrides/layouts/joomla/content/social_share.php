<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('JPATH_BASE') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Version;

$version = new Version();
$JoomlaVersion = $version->getShortVersion();

$url = Route::_(version_compare($JoomlaVersion, '4.0.0', '>=') ? Joomla\Component\Content\Site\Helper\RouteHelper::getArticleRoute($displayData->id . ':' . $displayData->alias, $displayData->catid, $displayData->language) : ContentHelperRoute::getArticleRoute($displayData->id . ':' . $displayData->alias, $displayData->catid, $displayData->language));
$root = Uri::base();
$root = new Uri($root);
$url = $root->getScheme() . '://' . $root->getHost() . $url;
$template = HelixUltimate\Framework\Platform\Helper::loadTemplateData();
$params = $template->params;
$tmpl_params = $template->params;
$socialShares = $tmpl_params->get("social_share_lists");

if( is_array($socialShares) && $params->get('social_share') ) : ?>
<div class="article-social-share">
	<div class="social-share-icon">
		<ul>
			<?php foreach( $socialShares as $socialSite ): ?>
				<?php if( $socialSite == 'facebook'): ?>
				<li>
					<a class="facebook" onClick="window.open('https://www.facebook.com/sharer.php?u=<?php echo $url; ?>','Facebook','width=600,height=300,left='+(screen.availWidth/2-300)+',top='+(screen.availHeight/2-150)+''); return false;" href="https://www.facebook.com/sharer.php?u=<?php echo $url; ?>" title="<?php echo Text::_('HELIX_ULTIMATE_SHARE_FACEBOOK'); ?>">
						<span class="fab fa-facebook" aria-hidden="true"></span>
					</a>
				</li>
				<?php endif; ?>
				<?php if( $socialSite == 'twitter'): ?>
				<li>
					<a class="twitter" title="<?php echo Text::_('HELIX_ULTIMATE_SHARE_TWITTER'); ?>" onClick="window.open('https://twitter.com/share?url=<?php echo $url; ?>&amp;text=<?php echo str_replace(" ", "%20", $displayData->title); ?>','Twitter share','width=600,height=300,left='+(screen.availWidth/2-300)+',top='+(screen.availHeight/2-150)+''); return false;" href="https://twitter.com/share?url=<?php echo $url; ?>&amp;text=<?php echo str_replace(" ", "%20", $displayData->title); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor" style="width: 13.56px;position: relative;top: -1.5px;"><path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"/></svg>
					</a>
				</li>
				<?php endif; ?>
				<?php if( $socialSite == 'linkedin'): ?>
					<li>
						<a class="linkedin" title="<?php echo Text::_('HELIX_ULTIMATE_SHARE_LINKEDIN'); ?>" onClick="window.open('https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $url; ?>','Linkedin','width=585,height=666,left='+(screen.availWidth/2-292)+',top='+(screen.availHeight/2-333)+''); return false;" href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $url; ?>" >
							<span class="fab fa-linkedin" aria-hidden="true"></span>
						</a>
					</li>
				<?php endif; ?>
			<?php endforeach; ?>
			</ul>
		</div>
	</div>
<?php endif; ?>
