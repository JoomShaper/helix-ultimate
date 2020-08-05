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

$layout = new FileLayout('fields.menuBuilder.grids', HELIX_LAYOUT_PATH);
$columnsLayout = new FileLayout('fields.menuBuilder.columns', HELIX_LAYOUT_PATH);

$rowId = !empty($reserve) ? '0' : $rowSettings->id;
?>

<div class="<?php echo !empty($reserve) ? 'hu-reserved-layout-section' : 'hu-megamenu-layout-section'; ?>" data-rowid="<?php echo $rowId; ?>" data-itemid="<?php echo $item->id; ?>">
	<div class="hu-megamenu-layout-section-inner">
		<div class="hu-megamenu-section-settings hu-d-flex hu-justify-content-between hu-align-items-center">
			<div>
				<a class="hu-megamenu-move-row hu-megamenu-layout-builder-action" href="#">
					<svg xmlns="http://www.w3.org/2000/svg" width="15" height="8" fill="none"><path fill-rule="evenodd" d="M1.5 3a1.5 1.5 0 100-3 1.5 1.5 0 000 3zm0 5a1.5 1.5 0 100-3 1.5 1.5 0 000 3zM9 1.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM7.5 8a1.5 1.5 0 100-3 1.5 1.5 0 000 3zM15 1.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM13.5 8a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" clip-rule="evenodd"/></svg>
				</a>
				<strong class="hu-megamenu-section-title"></strong>
			</div>
			<div>
				<ul class="hu-megamenu-row-option-list">
					<li class="hu-mr-1">
						<a class="hu-megamenu-add-columns hu-megamenu-layout-builder-action" href="#">
							<svg xmlns="http://www.w3.org/2000/svg" width="13" height="11" fill="none"><path d="M.996 4.805h3.926c.662 0 1.002-.323 1.002-1.014V1.02C5.924.322 5.584 0 4.922 0H.996C.34 0 0 .322 0 1.02V3.79c0 .691.34 1.014.996 1.014zm6.932 0h3.926c.662 0 1.002-.323 1.002-1.014V1.02c0-.698-.34-1.02-1.002-1.02H7.928c-.657 0-.996.322-.996 1.02V3.79c0 .691.34 1.014.996 1.014zm-6.92-.65c-.252 0-.363-.112-.363-.376V1.02c0-.251.11-.369.363-.369H4.91c.252 0 .363.118.363.37v2.76c0 .263-.11.374-.363.374H1.008zm6.937 0c-.258 0-.369-.112-.369-.376V1.02c0-.251.112-.369.37-.369h3.896c.252 0 .363.118.363.37v2.76c0 .263-.111.374-.363.374H7.945zM.996 10.61h3.926c.662 0 1.002-.322 1.002-1.013V6.826c0-.691-.34-1.013-1.002-1.013H.996C.34 5.813 0 6.135 0 6.825v2.772c0 .691.34 1.013.996 1.013zm6.932 0h3.926c.662 0 1.002-.322 1.002-1.013V6.826c0-.691-.34-1.013-1.002-1.013H7.928c-.657 0-.996.322-.996 1.013v2.772c0 .691.34 1.013.996 1.013zm-6.92-.644c-.252 0-.363-.117-.363-.375v-2.76c0-.258.11-.375.363-.375H4.91c.252 0 .363.117.363.375v2.76c0 .258-.11.375-.363.375H1.008zm6.937 0c-.258 0-.369-.117-.369-.375v-2.76c0-.258.112-.375.37-.375h3.896c.252 0 .363.117.363.375v2.76c0 .258-.111.375-.363.375H7.945z"/></svg>
						</a>
						<div class="hu-megamenu-column-list">
							<?php echo $layout->render(['item' => $item, 'columnSettings' => $rowSettings->columns, 'active' => $active, 'params' => $params, 'builder' => $builder]); ?>
						</div>
					</li>

					<li class="hu-mr-1">
						<a class="hu-megamenu-row-options hu-megamenu-layout-builder-action" href="#">
							<svg xmlns="http://www.w3.org/2000/svg" width="15" height="12" fill="none"><path d="M9.786 3.224c.731 0 1.347-.487 1.548-1.16h2.428c.23 0 .43-.194.43-.438 0-.25-.2-.444-.43-.444h-2.428A1.614 1.614 0 009.786 0c-.738 0-1.36.501-1.554 1.182H.444A.437.437 0 000 1.626c0 .244.193.437.444.437H8.24a1.61 1.61 0 001.547 1.16zm0-.73a.876.876 0 01-.88-.882c0-.502.386-.881.88-.881.495 0 .882.38.882.88a.876.876 0 01-.882.882zm-5.2 5.129c.737 0 1.36-.502 1.554-1.175h7.608c.244 0 .444-.2.444-.444 0-.251-.2-.445-.444-.445H6.133A1.618 1.618 0 004.585 4.4c-.73 0-1.354.494-1.547 1.16H.423A.433.433 0 000 6.004c0 .243.193.444.423.444h2.615a1.622 1.622 0 001.547 1.175zm0-.738a.872.872 0 01-.882-.881c0-.495.387-.882.881-.882s.881.387.881.882a.872.872 0 01-.88.88zM9.785 12c.731 0 1.354-.502 1.548-1.175h2.428c.23 0 .43-.193.43-.444 0-.244-.2-.437-.43-.437h-2.428a1.616 1.616 0 00-1.548-1.168c-.73 0-1.354.494-1.547 1.168H.444A.436.436 0 000 10.38c0 .25.193.444.444.444h7.788A1.625 1.625 0 009.786 12zm0-.73a.878.878 0 01-.88-.89c0-.493.386-.873.88-.873.495 0 .882.38.882.874a.878.878 0 01-.882.888z"/></svg>
						</a>
					</li>
					<li>
						<a class="hu-megamenu-remove-row hu-megamenu-layout-builder-action" href="#">
							<svg xmlns="http://www.w3.org/2000/svg" width="12" height="13" fill="none"><path d="M9.592 11.648l.433-8.748h.844a.335.335 0 00.334-.34.335.335 0 00-.334-.34H8.098V1.3c0-.773-.545-1.3-1.389-1.3h-2.22c-.844 0-1.384.527-1.384 1.3v.92H.34a.348.348 0 00-.34.34c0 .188.158.34.34.34h.844l.433 8.748c.041.75.569 1.266 1.33 1.266h5.315c.756 0 1.295-.516 1.33-1.266zM3.826 1.336c0-.38.281-.662.71-.662h2.132c.422 0 .715.281.715.662v.885H3.826v-.885zm-.82 10.898a.68.68 0 01-.68-.662L1.893 2.9h7.412l-.416 8.672a.682.682 0 01-.686.662H3.006zm4.348-1.148c.158 0 .275-.123.28-.293l.188-6.404c.006-.17-.111-.305-.275-.305-.147 0-.27.135-.27.299l-.193 6.398c0 .17.111.305.27.305zm-3.499 0c.159 0 .276-.135.27-.305l-.193-6.398c0-.164-.13-.299-.276-.299-.158 0-.275.129-.27.305l.194 6.404c.006.17.117.293.275.293zm1.752 0c.153 0 .282-.135.282-.299V4.39c0-.17-.13-.305-.282-.305-.152 0-.28.135-.28.305v6.398c0 .164.128.299.28.299z"/></svg>
						</a>
					</li>
				</ul>
			</div>
		</div>
		<div class="hu-megamenu-row-container">
			<div class="row hu-megamenu-layout-row">
				<?php
				if (!empty($rowSettings->columns))
				{
					foreach ($rowSettings->columns as $col)
					{
						echo $columnsLayout->render(
							[
								'item' => $item,
								'columnSettings' => $col,
								'active' => $active,
								'params' => $params,
								'builder' => $builder
							]
						);
					}
				}
				else
				{
					echo $columnsLayout->render(
						[
							'item' => $item,
							'columnSettings' => !empty($rowSettings->columns) ? $rowSettings->columns : [],
							'active' => $active,
							'params' => $params,
							'builder' => $builder
						]
					);
				}
				?>
			</div>
		</div>
		<button class="hu-megamenu-add-row hu-btn hu-btn-primary">
			<span class="fas fa-plus"></span>
		</button>
	</div>
</div>
