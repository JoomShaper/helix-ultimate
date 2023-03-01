<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die();

use HelixUltimate\Framework\Core\HelixUltimate;
use HelixUltimate\Framework\Platform\Helper;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;

$app = Factory::getApplication();
$doc = Factory::getDocument();
$template = Helper::loadTemplateData();
$params = $template->params;
$fontCSS = '';

function addGoogleFont($fonts)
{
	$systemFonts = array(
		'Arial',
		'Tahoma',
		'Verdana',
		'Helvetica',
		'Times New Roman',
		'Trebuchet MS',
		'Georgia'
	);

	if (is_array($fonts))
	{
		$fontUrls = "";
		$styles = "";
		$fontCheck = [];

		foreach ($fonts as $key => $font)
		{
			$font = json_decode($font);

			if (!in_array($font->fontFamily, $systemFonts))
			{

				if (in_array($font->fontFamily, $fontCheck)) {
					continue;
				}

				$fontCheck[] = $font->fontFamily;
				
				$fontUrl = '//fonts.googleapis.com/css?family=' . $font->fontFamily . ':100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i';

				if (!empty(trim($font->fontSubset)))
				{
					$fontUrl .= '&subset=' . $font->fontSubset;
				}

				$fontUrl .= '&display=swap';

				$fontUrls .= '<link href="'. $fontUrl .'" rel="stylesheet" media="none" onload="media=media=&quot;all&quot;" />'. PHP_EOL . "\t\t";
			}

			$fontCSS = $key . "{";
			$fontCSS .= "font-family: '" . $font->fontFamily . "', sans-serif;";

			if (isset($font->fontSize) && $font->fontSize)
			{
				$fontCSS .= 'font-size: ' . $font->fontSize . (!preg_match("@(px|em|rem|%)$@", $font->fontSize) ? 'px;' : ';');
			}

			if (isset($font->fontWeight) && $font->fontWeight)
			{
				$fontCSS .= 'font-weight: ' . $font->fontWeight . ';';
			}

			if (isset($font->fontStyle) && $font->fontStyle)
			{
				$fontCSS .= 'font-style: ' . $font->fontStyle . ';';
			}

			if (!empty($font->fontColor))
			{
				$fontCSS .= 'color: ' . $font->fontColor . ';';
			}

			if (!empty($font->fontLineHeight))
			{
				$fontCSS .= 'line-height: ' . $font->fontLineHeight . ';';
			}

			if (!empty($font->fontLetterSpacing))
			{
				$fontCSS .= 'letter-spacing: ' . $font->fontLetterSpacing . ';';
			}

			if (!empty($font->textDecoration))
			{
				$fontCSS .= 'text-decoration: ' . $font->textDecoration . ';';
			}

			if (!empty($font->textAlign))
			{
				$fontCSS .= 'text-align: ' . $font->textAlign . ';';
			}

			$fontCSS .= "}";

			if (isset($font->fontSize_sm) && $font->fontSize_sm)
			{
				$fontCSS .= '@media (min-width:768px) and (max-width:991px){';
				$fontCSS .= $key . "{";
				$fontCSS .= 'font-size: ' . $font->fontSize_sm . (!preg_match("@(px|em|rem|%)$@", $font->fontSize_sm) ? 'px;' : ';');
				$fontCSS .= "}}";
			}

			if (isset($font->fontSize_xs) && $font->fontSize_xs)
			{
				$fontCSS .= '@media (max-width:767px){';
				$fontCSS .= $key . "{";
				$fontCSS .= 'font-size: ' . $font->fontSize_xs . (!preg_match("@(px|em|rem|%)$@", $font->fontSize_xs) ? 'px;' : ';');
				$fontCSS .= "}}";
			}

			$styles .= $fontCSS;
		}

		echo $fontUrls;
		echo "<style>" . $styles . "</style>" . PHP_EOL;
	}
}

$helixPlugin = new HelixUltimate();
$webfonts = array();

if ($helixPlugin->params->get('enable_body_font'))
{
	$webfonts['body'] = $helixPlugin->params->get('body_font');
}

if ($helixPlugin->params->get('enable_h1_font'))
{
	$webfonts['h1'] = $helixPlugin->params->get('h1_font');
}

if ($helixPlugin->params->get('enable_h2_font'))
{
	$webfonts['h2'] = $helixPlugin->params->get('h2_font');
}

if ($helixPlugin->params->get('enable_h3_font'))
{
	$webfonts['h3'] = $helixPlugin->params->get('h3_font');
}

if ($helixPlugin->params->get('enable_h4_font'))
{
	$webfonts['h4'] = $helixPlugin->params->get('h4_font');
}

if ($helixPlugin->params->get('enable_h5_font'))
{
	$webfonts['h5'] = $helixPlugin->params->get('h5_font');
}

if ($helixPlugin->params->get('enable_h6_font'))
{
	$webfonts['h6'] = $helixPlugin->params->get('h6_font');
}

if ($helixPlugin->params->get('enable_navigation_font'))
{
	$webfonts['.sp-megamenu-parent > li > a, .sp-megamenu-parent > li > span, .sp-megamenu-parent .sp-dropdown li.sp-menu-item > a'] = $helixPlugin->params->get('navigation_font');
}

if ($helixPlugin->params->get('enable_custom_font') && $helixPlugin->params->get('custom_font_selectors'))
{
	$webfonts[$helixPlugin->params->get('custom_font_selectors')] = $helixPlugin->params->get('custom_font');
}

$theme_url = Uri::base(true) . '/templates/'. $this->template;
?>

<!doctype html>
<html class="error-page" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
	<head>
		<title><?php echo $this->title; ?> <?php echo htmlspecialchars($this->error->getMessage(), ENT_QUOTES, 'UTF-8'); ?></title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<?php if ($favicon = $params->get('favicon')) : ?>
			<link rel="icon" href="<?php echo Uri::base(true) . '/' . $favicon; ?>" />
		<?php else: ?>
			<link rel="icon" href="<?php echo $theme_url .'/images/favicon.ico'; ?>" />
		<?php endif; ?>

		<?php if(file_exists( \JPATH_THEMES . '/' . $this->template . '/css/bootstrap.min.css' )) : ?>
			<link href="<?php echo $theme_url . '/css/bootstrap.min.css'; ?>" rel="stylesheet">
		<?php else: ?>
			<link href="<?php echo Uri::base(true) . '/plugins/system/helixultimate/css/bootstrap.min.css'; ?>" rel="stylesheet">
		<?php endif; ?>

		<?php if(file_exists( \JPATH_THEMES . '/' . $this->template . '/css/custom.css' )) : ?>
			<link href="<?php echo $theme_url . '/css/custom.css'; ?>" rel="stylesheet">
		<?php endif; ?>

		<link href="<?php echo $theme_url . '/css/font-awesome.min.css'; ?>" rel="stylesheet">
		<link href="<?php echo $theme_url . '/css/template.css'; ?>" rel="stylesheet">
		<?php
			$preset = $params->get('preset', json_encode(['preset' => 'preset1']));

			if (!empty($preset->preset))
			{
				$preset = $preset->preset;
			}
		?>

		<link href="<?php echo $theme_url . '/css/presets/' . $preset . '.css'; ?>" rel="stylesheet">
		
		<?php
			if ($params->get('error_font_load')) {
				addGoogleFont($webfonts);
			}
		?>
	</head>
	<body>
		<div class="container">
			<?php if($params->get('error_logo')) : ?>
				<a href="<?php echo $this->baseurl; ?>/index.php">
					<img class="error-logo" src="<?php echo Uri::base(true) . '/' . $params->get('error_logo'); ?>" alt="<?php echo htmlspecialchars($this->title); ?>">
				</a>
			<?php endif; ?>

			<h1 class="error-code"><?php echo $this->error->getCode(); ?></h1>
			<h2 class="error-message"><?php echo htmlspecialchars($this->error->getMessage(), ENT_QUOTES, 'UTF-8'); ?></h2>

			<jdoc:include type="modules" name="404" style="sp_xhtml"/>

			<a href="<?php echo $this->baseurl; ?>/index.php" class="btn btn-secondary"><span class="fas fa-home" aria-hidden="true"></span> <?php echo Text::_('JERROR_LAYOUT_HOME_PAGE'); ?></a>

			<?php if ($this->debug) : ?>
				<div class="error-debug mt-3">
					<?php echo $this->renderBacktrace(); ?>
					<?php if ($this->error->getPrevious()) : ?>
						<?php $loop = true; ?>
						<?php $this->setError($this->_error->getPrevious()); ?>
						<?php while ($loop === true) : ?>
							<p><strong><?php echo Text::_('JERROR_LAYOUT_PREVIOUS_ERROR'); ?></strong></p>
							<p><?php echo htmlspecialchars($this->_error->getMessage(), ENT_QUOTES, 'UTF-8'); ?></p>
							<?php echo $this->renderBacktrace(); ?>
							<?php $loop = $this->setError($this->_error->getPrevious()); ?>
						<?php endwhile; ?>
						<?php // Reset the main error object to the base error ?>
						<?php $this->setError($this->error); ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>

		<?php if($params->get('error_bg')) : ?>
			<style>
				body {
					background-image: url(<?php echo Uri::base(true) . '/' . $params->get('error_bg'); ?>)
				}
			</style>
		<?php endif; ?>
	</body>

</html>
