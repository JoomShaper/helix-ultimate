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

$theme_url = Uri::base(true) . '/templates/'. $this->template;
$option = $app->input->get('option', '', 'STRING');

$body_class = htmlspecialchars(str_replace('_', '-', $option));
$body_class .= ' view-' . htmlspecialchars($app->input->get('view', '', 'STRING'));
$body_class .= ' layout-' . htmlspecialchars($app->input->get('layout', 'default', 'STRING'));
$body_class .= ' task-' . htmlspecialchars($app->input->get('task', 'none', 'STRING'));

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

?>
<!doctype html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<?php
			$theme->head();
			$theme->loadFontAwesome();
		?>
	</head>

	<body class="<?php echo $theme->bodyClass('contentpane'); ?>">
		<jdoc:include type="component" />
	</body>
</html>
