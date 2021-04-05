<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use Joomla\CMS\Layout\LayoutHelper;

extract($displayData);

?>

<div id="hu-options">
	<?php foreach ($fieldsets as $key => $fieldset): ?>
		<?php echo LayoutHelper::render('cpanel.control-board.fieldset.icon', ['fieldset' => $fieldset, 'key' => $key, 'form' => $form], HELIX_LAYOUTS_PATH); ?>
	<?php endforeach; ?>
</div>