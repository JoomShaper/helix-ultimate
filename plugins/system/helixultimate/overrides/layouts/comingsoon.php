<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use HelixUltimate\Framework\Core\HelixUltimate;

extract($displayData);

$app = Factory::getApplication();
$doc = Factory::getDocument();

$title_status = $params->get('comingsoon_title_status');
$content_status = $params->get('comingsoon_content_status');
$countdown = $params->get('comingsoon_countdown');

/**
 * Load the bootstrap file for enabling the HelixUltimate\Framework namespacing.
 *
 * @since	2.0.0
 */
$bootstrap_path = JPATH_PLUGINS . '/system/helixultimate/bootstrap.php';

if (file_exists($bootstrap_path))
{
	require_once $bootstrap_path;
}
else
{
	die('Install and activate <a target="_blank" rel="noopener noreferrer" href="https://www.joomshaper.com/helix">Helix Ultimate Framework</a>.');
}

$theme = new HelixUltimate;
$site_title = $app->get('sitename');
?>

<!doctype html>
<html class="coming-soon" lang="<?php echo $language; ?>" dir="<?php echo $direction; ?>">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<?php
			$theme->head();
			$theme->add_js('jquery.countdown.min.js');
			$theme->add_js('custom.js');
			$theme->add_css('font-awesome.min.css, template.css');
			$theme->add_css('presets/' . $params->get('preset', 'preset1') . '.css');
			$theme->add_css('custom.css');

			//Custom CSS
			if ($custom_css = $params->get('custom_css'))
			{
				$doc->addStyledeclaration($custom_css);
			}

			//Custom JS
			if ($custom_js = $params->get('custom_js'))
			{
				$doc->addScriptdeclaration($custom_js);
			}
		?>
	</head>
	<body>
		<div class="container">

			<jdoc:include type="message" />

			<?php if($params->get('comingsoon_logo')) : ?>
				<img class="coming-soon-logo" src="<?php echo $params->get('comingsoon_logo'); ?>" alt="<?php echo htmlspecialchars($site_title ?? ""); ?>">
			<?php endif; ?>
			<?php if ($title_status) :?>
				<?php if($params->get('comingsoon_title')) : ?>
					<h1 class="coming-soon-title"><?php echo htmlspecialchars($params->get('comingsoon_title') ?? ""); ?></h1>
				<?php else: ?>
					<h1 class="coming-soon-title"><?php echo htmlspecialchars($site_title ?? ""); ?></h1>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ($content_status) :?>				
				<?php if($params->get('comingsoon_content')) : ?>
					<div class="row justify-content-center">
						<div class="col-lg-8">
							<div class="coming-soon-content">
								<?php echo $params->get('comingsoon_content'); ?>
							</div>
						</div>
					</div>
				<?php else: ?>
					<?php if ($app->get('display_offline_message', 1) == 1 && str_replace(' ', '', $app->get('offline_message')) != '') : ?>
						<div class="row justify-content-center">
							<div class="col-lg-8">
								<div class="coming-soon-content">
									<?php echo $app->get('offline_message'); ?>
								</div>
							</div>
						</div>
					<?php elseif ($app->get('display_offline_message', 1) == 2) : ?>
						<div class="row justify-content-center">
							<div class="col-lg-8">
								<div class="coming-soon-content">
									<?php echo Text::_('JOFFLINE_MESSAGE'); ?>
								</div>
							</div>
						</div>
					<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?>

			<?php if($countdown && $params->get('comingsoon_date')) : ?>
				<?php $comingsoon_date = explode('-', $params->get("comingsoon_date")); ?>
				<div id="coming-soon-countdown" class="clearfix"></div>
				<script type="text/javascript">
					jQuery(function($) {
						$('#coming-soon-countdown').countdown('<?php echo trim($comingsoon_date[0]); ?>/<?php echo trim($comingsoon_date[1]); ?>/<?php echo trim($comingsoon_date[2]); ?>', function(event) {
							$(this).html(event.strftime('<div class="coming-soon-days"><span class="coming-soon-number">%-D</span><span class="coming-soon-string">%!D:<?php echo Text::_("HELIX_ULTIMATE_DAY"); ?>,<?php echo Text::_("HELIX_ULTIMATE_DAYS"); ?>;</span></div><div class="coming-soon-hours"><span class="coming-soon-number">%H</span><span class="coming-soon-string">%!H:<?php echo Text::_("HELIX_ULTIMATE_HOUR"); ?>,<?php echo Text::_("HELIX_ULTIMATE_HOURS"); ?>;</span></div><div class="coming-soon-minutes"><span class="coming-soon-number">%M</span><span class="coming-soon-string">%!M:<?php echo Text::_("HELIX_ULTIMATE_MINUTE"); ?>,<?php echo Text::_("HELIX_ULTIMATE_MINUTES"); ?>;</span></div><div class="coming-soon-seconds"><span class="coming-soon-number">%S</span><span class="coming-soon-string">%!S:<?php echo Text::_("HELIX_ULTIMATE_SECOND"); ?>,<?php echo Text::_("HELIX_ULTIMATE_SECONDS"); ?>;</span></div>'));
						});
					});
				</script>
			<?php endif; ?>

			<?php if($theme->count_modules('comingsoon')) : ?>
				<div class="coming-soon-position">
					<jdoc:include type="modules" name="comingsoon" style="sp_xhtml" />
				</div>
			<?php endif; ?>

			<?php
				$facebook 	= $params->get('facebook');
				$twitter  	= $params->get('twitter');
				$pinterest 	= $params->get('pinterest');
				$youtube 	= $params->get('youtube');
				$linkedin 	= $params->get('linkedin');
				$dribbble 	= $params->get('dribbble');
				$behance 	= $params->get('behance');
				$skype 		= $params->get('skype');
				$flickr 	= $params->get('flickr');
				$vk 		= $params->get('vk');

				if( $params->get('comingsoon_social_icons') && ( $facebook || $twitter || $pinterest || $youtube || $linkedin || $dribbble || $behance || $skype || $flickr || $vk ) )
				{
					$social_output  = '<ul class="social-icons">';

					if( $facebook )
					{
						$social_output .= '<li><a target="_blank" rel="noopener noreferrer" href="'. $facebook .'"><i class="fab fa-facebook" aria-hidden="true"></i></a></li>';
					}
					if( $twitter )
					{
						$social_output .= '<li><a target="_blank" rel="noopener noreferrer" href="'. $twitter .'"><i class="fab fa-twitter" aria-hidden="true"></i></a></li>';
					}
					if( $pinterest )
					{
						$social_output .= '<li><a target="_blank" rel="noopener noreferrer" href="'. $pinterest .'"><i class="fab fa-pinterest" aria-hidden="true"></i></a></li>';
					}
					if( $youtube )
					{
						$social_output .= '<li><a target="_blank" rel="noopener noreferrer" href="'. $youtube .'"><i class="fab fa-youtube" aria-hidden="true"></i></a></li>';
					}
					if( $linkedin )
					{
						$social_output .= '<li><a target="_blank" rel="noopener noreferrer" href="'. $linkedin .'"><i class="fab fa-linkedin" aria-hidden="true"></i></a></li>';
					}
					if( $dribbble )
					{
						$social_output .= '<li><a target="_blank" rel="noopener noreferrer" href="'. $dribbble .'"><i class="fab fa-dribbble" aria-hidden="true"></i></a></li>';
					}
					if( $behance )
					{
						$social_output .= '<li><a target="_blank" rel="noopener noreferrer" href="'. $behance .'"><i class="fab fa-behance" aria-hidden="true"></i></a></li>';
					}
					if( $flickr )
					{
						$social_output .= '<li><a target="_blank" rel="noopener noreferrer" href="'. $flickr .'"><i class="fab fa-flickr" aria-hidden="true"></i></a></li>';
					}
					if( $vk )
					{
						$social_output .= '<li><a target="_blank" rel="noopener noreferrer" href="'. $vk .'"><i class="fab fa-vk" aria-hidden="true"></i></a></li>';
					}
					if( $skype )
					{
						$social_output .= '<li><a href="skype:'. $skype .'?chat"><i class="fab fa-skype" aria-hidden="true"></i></a></li>';
					}

					$social_output .= '</ul>';

					echo $social_output;
				}
			?>

			<?php if(isset($login) && $login) : ?>
				<?php echo $login_form; ?>
			<?php endif; ?>

			<?php $theme->after_body(); ?>
		</div>
		<?php if($params->get('comingsoon_bg_image')) : ?>
			<style>
				body{
					background-image: url(<?php echo Uri::base(true) . '/' . $params->get('comingsoon_bg_image'); ?>);
					background-size: cover;
    				background-position: center !important;
				}
			</style>
		<?php endif; ?>
	</body>
</html>