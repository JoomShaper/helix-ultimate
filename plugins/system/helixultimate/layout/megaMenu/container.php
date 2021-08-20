<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
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
	<div class="hu-megamenu-popover">
        <div class="hu-megamenu-popover-heading">
            <h5 class="title"><?php echo Text::_('HELIX_ULTIMATE_MENU_MODULE_LIST'); ?></h5>
            <button class="hu-btn hu-btn-link hu-megamenu-popover-close">
                <span class="fas fa-times" aria-hidden="true"></span>
            </button>
        </div>
        <div class="hu-megamenu-popover-body">
            <div class="hu-megamenu-search-wrapper">
                <span class="fas fa-search" aria-hidden="true"></span>
                <input type="search" class="hu-input hu-megamenu-module-search" placeholder="<?php echo Text::_('HELIX_ULTIMATE_SEARCH_MODULE_HINT'); ?>" />
            </div>

            <div class="hu-megamenu-modules-container">
               
            </div>
        </div>
    </div>
	<input type="hidden" id="hu-megamenu-layout-settings" value='<?php echo json_encode($settings); ?>' />
	<input type="hidden" id="hu-base-url" value="<?php echo Uri::root(); ?>" />
	<input type="hidden" id="hu-menu-itemid" value="<?php echo $itemId; ?>" />
</div>