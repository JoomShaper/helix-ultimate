<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Uri\Uri;

extract($displayData);

$sidebarLayout = new FileLayout('megaMenu.sidebar', HELIX_LAYOUT_PATH);
$gridLayout = new FileLayout('megaMenu.grid', HELIX_LAYOUT_PATH);
$settings = $builder->getMegaMenuSettings();

if (!class_exists('MegaFields'))
{
    require_once __DIR__ . '/megaFields.php';
}

?>

<div class="hu-megamenu-container hu-d-flex hu-justify-content-between">
	<?php echo $sidebarLayout->render(['itemId' => $itemId, 'builder' => $builder, 'settings' => $settings]); ?>
	<?php echo $gridLayout->render(['itemId' => $itemId, 'builder' => $builder, 'settings' => $settings]); ?>
	<input type="hidden" id="hu-megamenu-layout-settings" value='<?php echo json_encode($settings); ?>' />
	<input type="hidden" id="hu-base-url" value="<?php echo Uri::root(); ?>" />
	<input type="hidden" id="hu-menu-itemid" value="<?php echo $itemId; ?>" />
</div>