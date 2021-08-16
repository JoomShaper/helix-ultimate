<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;

$doc = Factory::getDocument();

$data = $displayData;
?>

<main id="sp-component" class="<?php echo $data->settings->className; ?>">
	<div class="sp-column <?php echo $data->settings->custom_class; ?>">
		<jdoc:include type="message" />

		<?php if ($doc->countModules('content-top')): ?>
			<div class="sp-module-content-top clearfix">
				<jdoc:include type="modules" name="content-top" style="sp_xhtml" />
			</div>
		<?php endif ?>

		<jdoc:include type="component" />

		<?php if ($doc->countModules('content-bottom')): ?>
			<div class="sp-module-content-bottom clearfix">
				<jdoc:include type="modules" name="content-bottom" style="sp_xhtml" />
			</div>
		<?php endif ?>
	</div>
</main>
