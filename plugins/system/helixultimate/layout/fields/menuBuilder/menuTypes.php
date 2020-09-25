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
$activeMenuType = $params->get('menu', 'mainmenu');

?>

<?php if (!empty($types)): ?>
	<?php foreach ($types as $type => $items): ?>
		<div class="hu-menu-type <?php echo $type === $activeMenuType ? 'active': ''; ?>" data-menutype="<?php echo $type; ?>">
		<?php
			$layout = new FileLayout('fields.menuBuilder.menuItems', HELIX_LAYOUT_PATH);
			echo $layout->render(['items' => $items, 'params' => $params, 'menuType' => $type, 'menuSettings' => $menuSettings, 'builder' => $builder]);
		?>
		</div>
	<?php endforeach ?>
<?php endif ?>