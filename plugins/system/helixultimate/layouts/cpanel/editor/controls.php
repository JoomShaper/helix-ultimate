<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();
use HelixUltimate\Framework\Platform\Settings;

extract($displayData);

$sidebar = new Settings;
?>

<div id="hu-options-panel">
	<div class="hu-panel-handle">
		<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 15 7"><path fill="#020B53" fill-rule="evenodd" d="M1.5 3a1.5 1.5 0 100-3 1.5 1.5 0 000 3zm0 4a1.5 1.5 0 100-3 1.5 1.5 0 000 3zM9 1.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM7.5 7a1.5 1.5 0 100-3 1.5 1.5 0 000 3zM15 1.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM13.5 7a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" clip-rule="evenodd" opacity=".2"/></svg>
	</div>
	<div class="hu-fade-border"></div>
	<?php echo $sidebar->renderBuilderControlBoard(); ?>
</div>