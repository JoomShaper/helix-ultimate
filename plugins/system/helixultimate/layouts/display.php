<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use HelixUltimate\Framework\Platform\Settings;
use Joomla\CMS\Factory;
use Joomla\CMS\Layout\LayoutHelper;

extract($displayData);

$app = Factory::getApplication();

$sidebar = new Settings;
?>

<div id="helix-ultimate">
	<?php echo LayoutHelper::render('cpanel.editor.topbar', ['id' => $id, 'view' => $view], HELIX_LAYOUTS_PATH); ?>
	<div class="hu-options-core">
		<div class="hu-options-container">
			<?php echo LayoutHelper::render('cpanel.editor.controls', ['id' => $id, 'view' => $view], HELIX_LAYOUTS_PATH); ?>
			<div class="hu-fieldset-contents">
				<form id="hu-style-form" action="index.php">
					<?php echo $sidebar->renderFieldsetContents(); ?>

					<!-- meta hidden values  -->
					<input type="hidden" name="id" value="<?php echo $style->id; ?>">
					<input type="hidden" name="template" value="<?php echo $style->template; ?>">
					<input type="hidden" name="client_id" value="<?php echo $style->client_id; ?>">
					<input type="hidden" name="home" value="<?php echo $style->home; ?>">
					<input type="hidden" name="title" value="<?php echo $style->title; ?>">
				</form>
			</div>
		</div>
	</div>
	<div class="hu-container">
		<div class="hu-preview">
			<?php echo LayoutHelper::render('preview.iframe', $iframe, HELIX_LAYOUTS_PATH); ?>
		</div>
	</div>
</div>