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
$bodyLayout = new FileLayout('megaMenu.body', HELIX_LAYOUT_PATH);

?>

<div class="hu-megamenu-container hu-d-flex hu-justify-content-between">
		<?php echo $sidebarLayout->render(['itemId' => $itemId, 'builder' => $builder]); ?>
		<?php echo $bodyLayout->render(['itemId' => $itemId, 'builder' => $builder]); ?>
</div>