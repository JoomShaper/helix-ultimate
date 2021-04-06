<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use HelixUltimate\Framework\Platform\Helper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Uri\Uri;

extract($displayData);

$modules = Helper::getModules($keyword);

?>

<?php if (!empty($modules)): ?>
	<div class="row">
		<?php foreach ($modules as $module): ?>
			<div class="col-4 hu-megamenu-column">
				<div class="hu-megamenu-module-item">
					<strong class="hu-megamenu-module-title"><?php echo $module->title; ?></strong>
					<p class="hu-megamenu-module-desc"><?php echo (strlen($module->desc) > 80 ? substr($module->desc, 0, 80) . '...' : $module->desc); ?></p>
					<button type="button" role="button" class="hu-btn hu-btn-default hu-megamenu-insert-module" data-module="<?php echo $module->id; ?>"><?php echo Text::_('HELIX_ULTIMATE_MODULE_INSERT'); ?></button>
				</div>
			</div>
		<?php endforeach ?>
	</div>
<?php else: ?>
	<div class="hu-megamenu-module-not-found">
		<h4><?php echo Text::_('Opps! Nothing Found.'); ?></h4>
	</div>
<?php endif ?>