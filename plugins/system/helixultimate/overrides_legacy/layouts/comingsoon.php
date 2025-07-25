<?php

/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use HelixUltimate\Framework\Core\HelixUltimate;
use Joomla\CMS\Helper\AuthenticationHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

extract($displayData);

// Initialize
$app = Factory::getApplication();
$doc = Factory::getDocument();
$isOffline = $isOffline ?? false;
$site_title = $site_title ?? $app->get('sitename');

$twofactormethods	= [];
if (version_compare(JVERSION, '4.2.0', '<')) {
	$twofactormethods 	= AuthenticationHelper::getTwoFactorMethods();
}

/**
 * Load the bootstrap file for enabling the HelixUltimate\Framework namespacing.
 *
 * @since	2.0.0
 */
$bootstrap_path = JPATH_PLUGINS . '/system/helixultimate/bootstrap.php';

if (file_exists($bootstrap_path)) {

	require_once $bootstrap_path;
} else {

	die('Install and activate <a target="_blank" rel="noopener noreferrer" href="https://www.joomshaper.com/helix">Helix Ultimate Framework</a>.');
}

$theme = new HelixUltimate;
?>

<!doctype html>
<html class="coming-soon" lang="<?php echo $language; ?>" dir="<?php echo $direction; ?>">

<head>
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<?php
	$theme->head();
	$theme->add_js('jquery.countdown.min.js');
	$theme->add_js('custom.js');
	$theme->add_css('font-awesome.min.css');
	$theme->add_css('template.css');
	$theme->add_css('presets/' . $params->get('preset', 'preset1') . '.css');
	$theme->add_css('custom.css');

	//Custom CSS
	if ($custom_css = $params->get('custom_css')) {
		$doc->addStyledeclaration($custom_css);
	}

	//Custom JS
	if ($custom_js = $params->get('custom_js')) {
		$doc->addScriptdeclaration($custom_js);
	}
	?>
</head>

<body class="<?php echo $isOffline ? 'offline-mode' : 'coming-soon-mode'; ?>">
	<div class="container">

		<jdoc:include type="message" />

		<?php if ($isOffline) : ?>
			<!-- OFFLINE CONTENT -->
			<?php if ($app->get('offline_image')) : ?>
				<style>
					body {
						background-image: url('<?php echo Uri::base(true) . '/' . ltrim($app->get('offline_image'), '/'); ?>');
						background-size: cover;
						background-position: center !important;
					}
				</style>
			<?php endif; ?>

			<?php if ($app->get('display_offline_message', 0) == 1 && str_replace(' ', '', $app->get('offline_message')) != '') : ?>
				<div class="offline-message">
					<?php echo $app->get('offline_message'); ?>
				</div>
			<?php elseif ($app->get('display_offline_message', 0) == 2) : ?>
				<div class="offline-message">
					<?php echo Text::_('JOFFLINE_MESSAGE'); ?>
				</div>
			<?php endif; ?>

			<?php if (isset($login) && $login) : ?>
				<?php echo $login_form; ?>
			<?php endif; ?>

		<?php else : ?>
			<!-- COMING SOON CONTENT -->
			<?php if ($params->get('comingsoon_logo')) : ?>
				<img class="coming-soon-logo" src="<?php echo $params->get('comingsoon_logo'); ?>" alt="<?php echo htmlspecialchars($site_title ?? ''); ?>">
			<?php endif; ?>

			<?php if ($params->get('comingsoon_bg_image')) : ?>
				<style>
					body {
						background-image: url('<?php echo Uri::base(true) . '/' . ltrim($params->get('comingsoon_bg_image'), '/'); ?>');
						background-size: cover;
						background-position: center !important;
					}
				</style>
			<?php endif; ?>

			<?php if ($params->get('comingsoon_title_status',0)) : ?>
				<h1 class="coming-soon-title">
					<?php echo htmlspecialchars($params->get('comingsoon_title', $site_title)); ?>
				</h1>
			<?php endif; ?>

			<?php if ($params->get('comingsoon_content_status',0) && $params->get('comingsoon_content')) : ?>
				<div class="row justify-content-center">
					<div class="col-lg-8">
						<div class="coming-soon-content">
							<?php echo $params->get('comingsoon_content'); ?>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php if ($params->get('comingsoon_countdown', 0) && $params->get('comingsoon_date')) : ?>
				<?php $comingsoon_date = explode('-', $params->get('comingsoon_date')); ?>
				<div id="coming-soon-countdown" class="clearfix"></div>
				<script type="text/javascript">
					jQuery(function($) {
						$('#coming-soon-countdown').countdown('<?php echo trim($comingsoon_date[0]); ?>/<?php echo trim($comingsoon_date[1]); ?>/<?php echo trim($comingsoon_date[2]); ?>', function(event) {
							$(this).html(event.strftime('<div class="coming-soon-days"><span class="coming-soon-number">%-D</span><span class="coming-soon-string">%!D:<?php echo Text::_("HELIX_ULTIMATE_DAY"); ?>,<?php echo Text::_("HELIX_ULTIMATE_DAYS"); ?>;</span></div><div class="coming-soon-hours"><span class="coming-soon-number">%H</span><span class="coming-soon-string">%!H:<?php echo Text::_("HELIX_ULTIMATE_HOUR"); ?>,<?php echo Text::_("HELIX_ULTIMATE_HOURS"); ?>;</span></div><div class="coming-soon-minutes"><span class="coming-soon-number">%M</span><span class="coming-soon-string">%!M:<?php echo Text::_("HELIX_ULTIMATE_MINUTE"); ?>,<?php echo Text::_("HELIX_ULTIMATE_MINUTES"); ?>;</span></div><div class="coming-soon-seconds"><span class="coming-soon-number">%S</span><span class="coming-soon-string">%!S:<?php echo Text::_("HELIX_ULTIMATE_SECOND"); ?>,<?php echo Text::_("HELIX_ULTIMATE_SECONDS"); ?>;</span></div>'));
						});
					});
				</script>
			<?php endif; ?>


			<?php if ($theme->count_modules('comingsoon')) : ?>
				<div class="coming-soon-position">
					<jdoc:include type="modules" name="comingsoon" style="sp_xhtml" />
				</div>
			<?php endif; ?>


			<?php
			$facebook 	= $params->get('facebook');
			$instagram 	= $params->get('instagram');
			$twitter  	= $params->get('twitter');
			$pinterest 	= $params->get('pinterest');
			$youtube 	= $params->get('youtube');
			$linkedin 	= $params->get('linkedin');
			$dribbble 	= $params->get('dribbble');
			$behance 	= $params->get('behance');
			$skype 		= $params->get('skype');
			$flickr 	= $params->get('flickr');
			$vk 		= $params->get('vk');

			if ($params->get('comingsoon_social_icons') && ($facebook || $instagram || $twitter || $pinterest || $youtube || $linkedin || $dribbble || $behance || $skype || $flickr || $vk)) {
				$social_output  = '<ul class="social-icons">';

				if ($facebook) {
					$social_output .= '<li><a target="_blank" rel="noopener noreferrer" href="' . $facebook . '"><i class="fab fa-facebook" aria-hidden="true"></i></a></li>';
				}
				if ($instagram) {
					$social_output .= '<li><a target="_blank" rel="noopener noreferrer" href="' . $instagram . '"><i class="fab fa-instagram" aria-hidden="true"></i></a></li>';
				}
				if ($twitter) {
					$social_output .= '<li><a target="_blank" rel="noopener noreferrer" href="' . $twitter . '"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor" style="width: 13.56px;position: relative;top: -1.5px;"><path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"/></svg></a></li>';
				}
				if ($pinterest) {
					$social_output .= '<li><a target="_blank" rel="noopener noreferrer" href="' . $pinterest . '"><i class="fab fa-pinterest" aria-hidden="true"></i></a></li>';
				}
				if ($youtube) {
					$social_output .= '<li><a target="_blank" rel="noopener noreferrer" href="' . $youtube . '"><i class="fab fa-youtube" aria-hidden="true"></i></a></li>';
				}
				if ($linkedin) {
					$social_output .= '<li><a target="_blank" rel="noopener noreferrer" href="' . $linkedin . '"><i class="fab fa-linkedin" aria-hidden="true"></i></a></li>';
				}
				if ($dribbble) {
					$social_output .= '<li><a target="_blank" rel="noopener noreferrer" href="' . $dribbble . '"><i class="fab fa-dribbble" aria-hidden="true"></i></a></li>';
				}
				if ($behance) {
					$social_output .= '<li><a target="_blank" rel="noopener noreferrer" href="' . $behance . '"><i class="fab fa-behance" aria-hidden="true"></i></a></li>';
				}
				if ($flickr) {
					$social_output .= '<li><a target="_blank" rel="noopener noreferrer" href="' . $flickr . '"><i class="fab fa-flickr" aria-hidden="true"></i></a></li>';
				}
				if ($vk) {
					$social_output .= '<li><a target="_blank" rel="noopener noreferrer" href="' . $vk . '"><i class="fab fa-vk" aria-hidden="true"></i></a></li>';
				}
				if ($skype) {
					$social_output .= '<li><a href="skype:' . $skype . '?chat"><i class="fab fa-skype" aria-hidden="true"></i></a></li>';
				}

				$social_output .= '</ul>';

				echo $social_output;
			}
			?>
			<?php if (($params->get('comingsoon_enable_login', 0))) : ?>
				<div class="coming-soon-login">
					<form action="<?php echo Route::_('index.php', true); ?>" method="post" id="form-login" class="mt-5">
						<div class="row gx-3 align-items-center">
							<div class="col-auto">
								<label class="visually-hidden" for="username"><?php echo Text::_('JGLOBAL_USERNAME'); ?></label>
								<div class="input-group mb-2">
									<div class="input-group-text"><span class="fas fa-user" aria-hidden="true"></span></div>
									<input name="username" type="text" class="form-control" id="username" placeholder="<?php echo Text::_('JGLOBAL_USERNAME'); ?>">
								</div>
							</div>

							<div class="col-auto">
								<label class="visually-hidden" for="password"><?php echo Text::_('JGLOBAL_PASSWORD'); ?></label>
								<div class="input-group mb-2">
									<div class="input-group-text"><span class="fas fa-key" aria-hidden="true"></span></div>
									<input name="password" type="password" class="form-control" id="password" placeholder="<?php echo Text::_('JGLOBAL_PASSWORD'); ?>">
								</div>
							</div>

							<?php if (count($twofactormethods) > 1) : ?>
								<div class="col-auto">
									<label class="visually-hidden" for="secretkey"><?php echo Text::_('JGLOBAL_SECRETKEY'); ?></label>
									<div class="input-group mb-2">
										<div class="input-group-text"><span class="fas fa-user-secret" aria-hidden="true"></span></div>
										<input name="secretkey" type="text" class="form-control" id="secretkey" placeholder="<?php echo Text::_('JGLOBAL_SECRETKEY'); ?>">
									</div>
								</div>
							<?php endif; ?>

							<div class="col-auto">
								<input type="submit" name="Submit" class="btn btn-success mb-2 login" value="<?php echo Text::_('JLOGIN'); ?>" />
								<input type="hidden" name="option" value="com_users" />
								<input type="hidden" name="task" value="user.login" />
								<input type="hidden" name="return" value="<?php echo base64_encode(Uri::base()); ?>" />
								<?php echo HTMLHelper::_('form.token'); ?>
							</div>

						</div>
					</form>
				</div>

			<?php endif; ?>
		<?php endif; ?>



		<?php $theme->after_body(); ?>
	</div>

</body>

</html>