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

$cellLayout = new FileLayout('megaMenu.cell', HELIX_LAYOUT_PATH);
$cells = [];

if (!empty($column->items))
{
	$cells = $column->items;
}

?>

<div class="hu-megamenu-col col-<?php echo $column->colGrid; ?>" data-rowid="<?php echo $rowId; ?>" data-columnid="<?php echo $columnId; ?>">
	<div class="hu-megamenu-column-contents-wrapper">
		<div class="hu-megamenu-column-toolbar hu-megamenu-column-drag-handler">
			<svg xmlns="http://www.w3.org/2000/svg" width="15" height="8"><path fill-rule="evenodd" d="M1.5 3a1.5 1.5 0 100-3 1.5 1.5 0 000 3zm0 5a1.5 1.5 0 100-3 1.5 1.5 0 000 3zM9 1.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM7.5 8a1.5 1.5 0 100-3 1.5 1.5 0 000 3zM15 1.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM13.5 8a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" clip-rule="evenodd"></path></svg>
			<span>Column</span>
		</div>
		<div class="hu-megamenu-column-contents">
			<?php foreach ($cells as $key => $cell): ?>
				<?php 
					echo $cellLayout->render([
						'itemId' => $itemId,
						'builder' => $builder,
						'cell' => $cell,
						'rowId' => $rowId,
						'columnId' => $columnId,
						'cellId' => $key + 1
					]);
				?>
			<?php endforeach ?>
		</div>
		
		<div class="hu-megamenu-add-item-wrapper">
			<button class="hu-megamenu-add-new-item" title="Add Module">
				<span class="fas fa-plus-circle" aria-hidden="true"></span>
			</button>
			<ul class="hu-megamenu-cell-options" style="display: none;">
				<li><a href="#" class="hu-megamenu-cell-options-item" data-type="menu"><?php echo Text::_('Menu Item'); ?></a></li>
				<li><a href="#" class="hu-megamenu-cell-options-item" data-type="module"><?php echo Text::_('Module'); ?></a></li>
			</ul>
		</div>
	</div>
</div>
