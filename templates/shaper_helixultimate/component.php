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

$theme_url = URI::base(true) . '/templates/'. $this->template;
$option = $app->input->get('option', '', 'STRING');


$body_class = htmlspecialchars(str_replace('_', '-', $option));
$body_class .= ' view-' . htmlspecialchars($app->input->get('view', '', 'STRING'));
$body_class .= ' layout-' . htmlspecialchars($app->input->get('layout', 'default', 'STRING'));
$body_class .= ' task-' . htmlspecialchars($app->input->get('task', 'none', 'STRING'));

?>
<!doctype html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<?php if ($favicon = $this->params->get('favicon')) : ?>
		<link rel="icon" href="<?php echo URI::base(true) . '/' . $favicon; ?>" />
		<?php else: ?>
		<link rel="icon" href="<?php echo $theme_url .'/images/favicon.ico'; ?>" />
		<!-- Apple Touch Icon (reuse 192px icon.png) -->
		<link rel="apple-touch-icon" href="<?php echo URI::base(true) . '/' . $favicon; ?>">

		<?php endif; ?>

		<jdoc:include type="head" />

		<?php if($option != 'com_sppagebuilder') : ?>
			<?php if(file_exists(\JPATH_THEMES . '/' . $this->template . '/css/bootstrap.min.css' )) : ?>
				<link href="<?php echo $theme_url . '/css/bootstrap.min.css'; ?>" rel="stylesheet">
			<?php else: ?>
				<link href="<?php echo URI::root(true) . '/plugins/system/helixultimate/css/bootstrap.min.css'; ?>" rel="stylesheet">
			<?php endif; ?>

			<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/template.css" type="text/css" />
		<?php endif; ?>

		<?php if ($this->params->get('enable_fontawesome', '1')): ?>
			<?php if (file_exists(\JPATH_THEMES . '/' . $this->template . '/css/font-awesome.min.css')): ?>
				<link rel="stylesheet" href="<?php echo Uri::root(true) . '/templates/' . $this->template . '/css/font-awesome.min.css'; ?>" />
			<?php endif ?>
		<?php endif ?>

		<?php if (JVERSION < 4): ?>
			<link rel="stylesheet" href="<?php echo Uri::root(true) . '/plugins/system/helixultimate/assets/css/icomoon.css'; ?>" />
		<?php endif ?>
	</head>

	<?php 
	$joomlaVersion = 'joomla3';
	if (JVERSION >= 4)
	{
		$joomlaVersion = "joomla4";
	}
	?>

	<body class="contentpane <?php echo $joomlaVersion . ' ' . $body_class; ?>">
		<jdoc:include type="component" />

		<!-- Add lazy loading in the component tmpl -->
		<?php if ($this->params->get('image_lazy_loading', '0')): ?>
			<?php if (file_exists(\JPATH_THEMES . '/' . $this->template . '/js/lazysizes.min.js')): ?>
				<script src="<?php echo Uri::root(true) . '/templates/' . $this->template . '/js/lazysizes.min.js'; ?>"></script>
			<?php endif ?>
		<?php endif ?>
	</body>
</html>
