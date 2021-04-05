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
$columns = [];

if (!empty($row) && isset($row->attr))
{
    $columns = $row->attr;
}

$columnLayout = new FileLayout('megaMenu.column', HELIX_LAYOUT_PATH);
$slotLayout = new FileLayout('megaMenu.slots', HELIX_LAYOUT_PATH);

?>

<div class="hu-megamenu-row-wrapper" data-rowid="<?php echo $rowId; ?>">
	<div class="hu-megamenu-row-toolbar">
		<div class="hu-megamenu-row-toolbar-left hu-megamenu-row-drag-handlers">
			<svg xmlns="http://www.w3.org/2000/svg" width="15" height="8"><path fill-rule="evenodd" d="M1.5 3a1.5 1.5 0 100-3 1.5 1.5 0 000 3zm0 5a1.5 1.5 0 100-3 1.5 1.5 0 000 3zM9 1.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM7.5 8a1.5 1.5 0 100-3 1.5 1.5 0 000 3zM15 1.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM13.5 8a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" clip-rule="evenodd"></path></svg>
			<span>Row</span>
		</div>
		<div class="hu-megamenu-row-toolbar-right">
			<a href="#" class="hu-megamenu-columns">
				<svg xmlns="http://www.w3.org/2000/svg" width="13" height="11"><path d="M.996 4.805h3.926c.662 0 1.002-.323 1.002-1.014V1.02C5.924.322 5.584 0 4.922 0H.996C.34 0 0 .322 0 1.02V3.79c0 .691.34 1.014.996 1.014zm6.932 0h3.926c.662 0 1.002-.323 1.002-1.014V1.02c0-.698-.34-1.02-1.002-1.02H7.928c-.657 0-.996.322-.996 1.02V3.79c0 .691.34 1.014.996 1.014zm-6.92-.65c-.252 0-.363-.112-.363-.376V1.02c0-.251.11-.369.363-.369H4.91c.252 0 .363.118.363.37v2.76c0 .263-.11.374-.363.374H1.008zm6.937 0c-.258 0-.369-.112-.369-.376V1.02c0-.251.112-.369.37-.369h3.896c.252 0 .363.118.363.37v2.76c0 .263-.111.374-.363.374H7.945zM.996 10.61h3.926c.662 0 1.002-.322 1.002-1.013V6.826c0-.691-.34-1.013-1.002-1.013H.996C.34 5.813 0 6.135 0 6.825v2.772c0 .691.34 1.013.996 1.013zm6.932 0h3.926c.662 0 1.002-.322 1.002-1.013V6.826c0-.691-.34-1.013-1.002-1.013H7.928c-.657 0-.996.322-.996 1.013v2.772c0 .691.34 1.013.996 1.013zm-6.92-.644c-.252 0-.363-.117-.363-.375v-2.76c0-.258.11-.375.363-.375H4.91c.252 0 .363.117.363.375v2.76c0 .258-.11.375-.363.375H1.008zm6.937 0c-.258 0-.369-.117-.369-.375v-2.76c0-.258.112-.375.37-.375h3.896c.252 0 .363.117.363.375v2.76c0 .258-.111.375-.363.375H7.945z"></path></svg>
			</a>
			<a href="#" class="hu-megamenu-remove-row">
				<svg xmlns="http://www.w3.org/2000/svg" width="12" height="13"><path d="M9.592 11.648l.433-8.748h.844a.335.335 0 00.334-.34.335.335 0 00-.334-.34H8.098V1.3c0-.773-.545-1.3-1.389-1.3h-2.22c-.844 0-1.384.527-1.384 1.3v.92H.34a.348.348 0 00-.34.34c0 .188.158.34.34.34h.844l.433 8.748c.041.75.569 1.266 1.33 1.266h5.315c.756 0 1.295-.516 1.33-1.266zM3.826 1.336c0-.38.281-.662.71-.662h2.132c.422 0 .715.281.715.662v.885H3.826v-.885zm-.82 10.898a.68.68 0 01-.68-.662L1.893 2.9h7.412l-.416 8.672a.682.682 0 01-.686.662H3.006zm4.348-1.148c.158 0 .275-.123.28-.293l.188-6.404c.006-.17-.111-.305-.275-.305-.147 0-.27.135-.27.299l-.193 6.398c0 .17.111.305.27.305zm-3.499 0c.159 0 .276-.135.27-.305l-.193-6.398c0-.164-.13-.299-.276-.299-.158 0-.275.129-.27.305l.194 6.404c.006.17.117.293.275.293zm1.752 0c.153 0 .282-.135.282-.299V4.39c0-.17-.13-.305-.282-.305-.152 0-.28.135-.28.305v6.398c0 .164.128.299.28.299z"></path></svg>
			</a>

			<div class="hu-megamenu-row-slots">
				<?php echo $slotLayout->render(); ?>
			</div>
		</div>
	</div>
	<div class="row hu-megamenu-columns-container">
		<?php foreach ($columns as $key => $column): ?>
			<?php
				echo $columnLayout->render([
					'itemId' => $itemId,
					'builder' => $builder,
					'column' => $column,
					'rowId' => $rowId,
					'columnId' => $key + 1
				]);
			?>
		<?php endforeach ?>
	</div>
</div>