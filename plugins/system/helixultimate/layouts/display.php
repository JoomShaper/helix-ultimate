<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use HelixUltimate\Framework\Platform\Settings;
use Joomla\CMS\Factory;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Uri\Uri;

extract($displayData);

$app = Factory::getApplication();

$sidebar = new Settings;
?>

<div id="helix-ultimate">
	<?php echo LayoutHelper::render('cpanel.editor.topbar', ['id' => $id, 'view' => $view], HELIX_LAYOUTS_PATH); ?>
	<?php echo LayoutHelper::render('cpanel.editor.controls', ['id' => $id, 'view' => $view], HELIX_LAYOUTS_PATH); ?>
	<div class="hu-container">
		<div class="hu-preview">
			<?php echo LayoutHelper::render('preview.iframe', ['url' => Uri::root(true) . '/index.php?template=' . $style->template, 'width' => '100%', 'height' => '100%'], HELIX_LAYOUTS_PATH); ?>
		</div>
	</div>
</div>