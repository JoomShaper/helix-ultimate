<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

extract($displayData);

?>
<?php foreach ($groups as $key => $group):  ?>
	<?php if ($key !== 'no-group'): ?>
		<div class="hu-group-wrap hu-group-<?php echo $key; ?>">
			<div class="hu-group-header-box">
				<span class="hu-group-title"><?php echo Text::_('HELIX_ULTIMATE_GROUP_' . strtoupper($key)); ?></span>
				<span class="hu-group-toggle-icon fas fa-angle-right"></span>
			</div>
			<div class="hu-field-list" data-uid="<?php echo $fieldset_name . '-'. $key; ?>">
				<?php echo LayoutHelper::render('cpanel.control-board.fieldset.fields', ['group' => $key, 'fields' => $group['fields']], HELIX_LAYOUTS_PATH); ?>
			</div>
		</div>
	<?php else: ?>
		<?php echo LayoutHelper::render('cpanel.control-board.fieldset.fields', ['group' => $key, 'fields' => $group['fields']], HELIX_LAYOUTS_PATH); ?>
	<?php endif ?>
<?php endforeach; ?>
