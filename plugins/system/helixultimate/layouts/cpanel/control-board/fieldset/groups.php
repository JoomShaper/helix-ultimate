<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

extract($displayData);
?>

<?php foreach ($groups as $key => $group):  ?>
	<?php if ($key !== 'no-group'): ?>
		<div class="hu-group-wrap hu-group-<?php echo $key; ?> <?php echo $group['isActive'] ? 'active' : ''; ?>" <?php echo !empty($group['dependent']) ? 'data-dependon="' . $group['dependent'] . '"' : ''; ?>>
			<div class="hu-group-header-box">
				<span class="hu-group-title"><?php echo Text::_('HELIX_ULTIMATE_GROUP_' . strtoupper($key)); ?></span>
				<span class="hu-group-toggle-icon fas fa-angle-right"></span>
			</div>
			<div class="hu-field-list <?php echo $group['isActive'] ? 'active-group' : ''; ?>" data-uid="<?php echo $fieldset_name . '-'. $key; ?>" <?php echo $group['isActive'] ? 'style="display:block;"' : ''; ?>>
				<?php echo LayoutHelper::render('cpanel.control-board.fieldset.fields', ['group' => $key, 'groupData' => $group], HELIX_LAYOUTS_PATH); ?>
			</div>
		</div>
	<?php else: ?>
		<div class="hu-no-group-wrap">
			<?php echo LayoutHelper::render('cpanel.control-board.fieldset.fields', ['group' => $key, 'groupData' => $group], HELIX_LAYOUTS_PATH); ?>
		</div>
	<?php endif ?>
<?php endforeach; ?>
