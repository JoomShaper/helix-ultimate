<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use HelixUltimate\Framework\Platform\Builders\MenuBuilder;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Uri\Uri;

extract($displayData);

$builder = new MenuBuilder($params->get('menu', 'mainmenu'));

?>
<div class="hu-menu-items-container">
	<div class="hu-menu-items-wrapper">
		<ul class="hu-menu-items">
			<?php if (!empty($items)): ?>
				<?php foreach ($items as $key => $item): ?>
					<li class="hu-menu-item <?php echo $key === 0 ? 'active' : ''; ?>" data-name="<?php echo $item->alias; ?>"><?php echo $item->title; ?></li>
				<?php endforeach; ?>
			<?php endif; ?>
		</ul>
	</div>

	<?php if (!empty($items)): ?>
		<?php foreach ($items as $key => $item): ?>
			<?php
				$layout = new FileLayout('fields.menuBuilder.menuItem', HELIX_LAYOUT_PATH);
				echo $layout->render(['item' => $item, 'active' => ($key === 0),'params' => $params, 'builder' => $builder]);
			?>
		<?php endforeach ?>
	<?php endif ?>
</div>
