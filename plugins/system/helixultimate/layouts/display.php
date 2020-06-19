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
	<?php echo LayoutHelper::render('cpanel.topbar.topbar', ['id' => $id, 'view' => $view], HELIX_LAYOUTS_PATH); ?>
	<div class="helix-ultimate-fieldset-contents">
		<form id="helix-ultimate-style-form" action="index.php">
			<?php echo $sidebar->renderFieldsetContents(); ?>

			<!-- meta hidden values  -->
			<input type="hidden" name="id" value="<?php echo $style->id; ?>">
			<input type="hidden" name="template" value="<?php echo $style->template; ?>">
			<input type="hidden" name="client_id" value="<?php echo $style->client_id; ?>">
			<input type="hidden" name="home" value="<?php echo $style->home; ?>">
			<input type="hidden" name="title" value="<?php echo $style->title; ?>">
		</form>
	</div>
	<div class="helix-ultimate-container">
		<div id="helix-ultimate-sidebar" class="helix-ultimate-sidebar">
			<div class="topbar-control-board">
				<?php echo $sidebar->renderBuilderControlBoard(); ?>
			</div>
		</div>
		<div class="helix-ultimate-preview">
			<?php echo LayoutHelper::render('preview.iframe', ['url' => Uri::root(true) . '/index.php?template=' . $style->template, 'width' => '100%', 'height' => '100%'], HELIX_LAYOUTS_PATH); ?>
		</div>
	</div>
</div>