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
$col = isset($columnSettings->settings->col) ? $columnSettings->settings->col : 12;
$columnSettingsLayout = new FileLayout('fields.menuBuilder.settings.column', HELIX_LAYOUT_PATH);
?>

<div
	class="hu-megamenu-layout-column col-<?php echo $col; ?>"
	data-itemid="<?php echo $columnSettings->itemId; ?>"
	data-rowid="<?php echo $columnSettings->rowId; ?>"
	data-columnid="<?php echo $columnSettings->id; ?>"
>
	<div class="hu-megamenu-column">
		<span class="hu-megamenu-column-title">col-<?php echo $col; ?></span>
		<a class="hu-megamenu-column-options" href="#">
			<svg xmlns="http://www.w3.org/2000/svg" width="15" height="3" fill="none"><path fill="#020B53" fill-rule="evenodd" d="M3 1.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zm6 0a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM13.5 3a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" clip-rule="evenodd" opacity=".4"></path></svg>
		</a>
	</div>

	<?php
		echo $columnSettingsLayout->render(
			[
				'item' => $item,
				'params' => $params,
				'builder' => $builder,
				'settings' => $columnSettings->settings,
				'id' => $columnSettings->id,
				'rowId' => $columnSettings->rowId
			]
		);
	?>
</div>
