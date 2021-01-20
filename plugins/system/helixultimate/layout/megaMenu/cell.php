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

?>

<div class="hu-megamenu-cell" data-rowid="<?php echo $rowId; ?>" data-columnid="<?php echo $columnId; ?>" data-cellid="<?php echo $cellId; ?>">
	<span><?php echo $cell->type; ?></span>
	<span><?php echo $cell->item_id; ?></span>	
</div>