<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die('Restricted Direct Access!');

use HelixUltimate\Framework\Core\HelixUltimate;
use HelixUltimate\Framework\Platform\Helper;
use HelixUltimate\Framework\System\JoomlaBridge;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

$app = Factory::getApplication();
$this->setHtml5(true);

/**
 * Load the framework bootstrap file for enabling the HelixUltimate\Framework namespacing.
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

/**
 * Get the theme instance from Helix framework.
 *
 * @var		$theme		The theme object from the class HelixUltimate.
 * @since	1.0.0
 */
$theme = new HelixUltimate;
$template = Helper::loadTemplateData();
$this->params = $template->params;


/** Load needed data for javascript */
Helper::flushSettingsDataToJs();

$requestFromIframe = $app->input->get('helixMode', '') === 'edit';

// Coming Soon
if (!$requestFromIframe) 
{
	if (!\is_null($this->params->get('comingsoon', null)))
	{
		header("Location: " . Route::_(Uri::root(true) . "/index.php?templateStyle={$template->id}&tmpl=comingsoon", false));
		exit();
	}
}

$scssVars = $theme->getSCSSVariables();

$boxedLayout = $this->params->get('boxed_layout');

// Body Background Image
if ($boxedLayout && $this->params->get('body_bg_image'))
{
	$bg_image = $this->params->get('body_bg_image');
	$body_style = 'background-image: url(' . Uri::base(true) . '/' . $bg_image . ');';
	$body_style .= 'background-repeat: ' . $this->params->get('body_bg_repeat') . ';';
	$body_style .= 'background-size: ' . $this->params->get('body_bg_size') . ';';
	$body_style .= 'background-attachment: ' . $this->params->get('body_bg_attachment') . ';';
	$body_style .= 'background-position: ' . $this->params->get('body_bg_position') . ';';
	$body_style = 'body.site {' . $body_style . '}';
	$this->addStyledeclaration($body_style);
}

// Custom CSS
if ($custom_css = $this->params->get('custom_css'))
{
	$this->addStyledeclaration($custom_css);
}

$progress_bar_position = $this->params->get('reading_timeline_position');

if($app->input->get('view') === 'article' && $this->params->get('reading_time_progress', 0))
{
	$progress_style = 'position:fixed;';
	$progress_style .= 'z-index:9999;';
	$progress_style .= 'height:'.$this->params->get('reading_timeline_height').';';
	$progress_style .= 'background-color:'.$this->params->get('reading_timeline_bg').';';
	$progress_style .= $progress_bar_position == 'top' ? 'top:0;' : 'bottom:0;';
	$progress_style = '.sp-reading-progress-bar { '.$progress_style.' }';
	$this->addStyledeclaration($progress_style);
}

// Custom JS
if ($custom_js = $this->params->get('custom_js', null))
{
	$this->addScriptDeclaration($custom_js);
}
?>

<!doctype html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
	<head>
		<?php echo $theme->googleAnalytics(); ?>

		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<?php

		$theme->head();
		$theme->loadFontAwesome();
		$theme->add_js('main.js');

		/**
		 * Add custom.js for user
		 */
		if (file_exists(JPATH_THEMES . '/' . $template->template . '/js/custom.js'))
		{
			$theme->add_js('custom.js');
		}

		$theme->add_scss('master', $scssVars, 'template');

		if($this->direction === 'rtl')
		{
			$theme->add_scss('rtl', $scssVars, 'rtl');
		}

		$theme->add_scss('presets', $scssVars, 'presets/' . $scssVars['preset']);

		$theme->add_scss('custom', $scssVars, 'custom-compiled');
		$theme->add_css('custom.css');

		//Before Head
		if ($before_head = $this->params->get('before_head'))
		{
			echo $before_head . "\n";
		}
		?>
	</head>
	<body class="<?php echo $theme->bodyClass(); ?>">

		<?php if ($this->params->get('after_body', '')): ?>
			<?php echo $this->params->get('after_body') . "\n"; ?>
		<?php endif ?>

		<?php if($this->params->get('preloader')) : ?>
			<div class="sp-pre-loader">
				<?php echo $theme->getPreloader($this->params->get('loader_type', '')); ?>
			</div>
		<?php endif; ?>

		<div class="body-wrapper">
			<div class="body-innerwrapper">
				<?php echo $theme->getHeaderStyle(); ?>
				<?php $theme->render_layout(); ?>
			</div>
		</div>

		<!-- Off Canvas Menu -->
		<div class="offcanvas-overlay"></div>
		<!-- Rendering the offcanvas style -->
		<!-- If canvas style selected then render the style -->
		<!-- otherwise (for old templates) attach the offcanvas module position -->
		<?php if (!empty($this->params->get('offcanvas_style', '1-LeftAlign'))): ?>
			<?php echo $theme->getOffcanvasStyle(); ?>
		<?php else : ?>
			<div class="offcanvas-menu">
				<a href="#" class="close-offcanvas" aria-label="<?php echo Text::_('HELIX_ULTIMATE_CLOSE_OFFCANVAS_ARIA_LABEL'); ?>"><span class="fas fa-times" aria-hidden="true"></span></a>
				<div class="offcanvas-inner">
					<?php if ($this->countModules('offcanvas')) : ?>
						<jdoc:include type="modules" name="offcanvas" style="sp_xhtml" />
					<?php else: ?>
						<p class="alert alert-warning">
							<?php echo Text::_('HELIX_ULTIMATE_NO_MODULE_OFFCANVAS'); ?>
						</p>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>
		

		<?php $theme->after_body(); ?>

		<jdoc:include type="modules" name="debug" style="none" />

		<!-- Go to top -->
		<?php if ($this->params->get('goto_top', 0)) : ?>
			<a href="#" class="sp-scroll-up" aria-label="<?php echo Text::_('HELIX_ULTIMATE_SCROLL_UP_ARIA_LABEL'); ?>"><span class="fas fa-angle-up" aria-hidden="true"></span></a>
		<?php endif; ?>
		<?php if( $app->input->get('view') === 'article' && $this->params->get('reading_time_progress', 0) ): ?>
			<div data-position="<?php echo $progress_bar_position; ?>" class="sp-reading-progress-bar"></div>
		<?php endif; ?>
	</body>
</html>