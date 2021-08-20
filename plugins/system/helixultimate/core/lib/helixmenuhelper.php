<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
namespace HelixUltimate\Framework\Core\Lib;

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use HelixUltimate\Framework\Core\Lib\FontawesomeIcons;

$current_menu_id = $this->form->getValue('id');
$JMenuSite = new \JMenuSite;
$module_list = $this->getModuleNameById();

$fontawesome = new FontawesomeIcons;

$mega_align = array(
	'left' 		=> Text::_('HELIX_ULTIMATE_GLOBAL_LEFT'),
	'center' 	=> Text::_('HELIX_ULTIMATE_GLOBAL_CENTER'),
	'right' 	=> Text::_('HELIX_ULTIMATE_GLOBAL_RIGHT'),
	'full' 		=> Text::_('HELIX_ULTIMATE_GLOBAL_FULL'),
);

$dropdown_list = array(
	'left' 	=> Text::_('HELIX_ULTIMATE_GLOBAL_LEFT'),
	'right' => Text::_('HELIX_ULTIMATE_GLOBAL_RIGHT')
);

$menu_width = 600;
$align = 'right';
$layout = array();
$enable_megamenu = 0;
$show_title = 1;
$custom_class = '';
$faicon = '';
$dropdown = 'right';
$badge = '';
$badge_position = '';
$badge_bg_color = '';
$badge_text_color = '';
$display_class = '';
$dropdown_class = '';
$unique_menu_item_count = 0;

if (isset($menu_data->megamenu))
{
	 $enable_megamenu = $menu_data->megamenu;
}

if (isset($menu_data->width))
{
	 $menu_width = $menu_data->width;
}

if (isset($menu_data->menualign))
{
	 $align = $menu_data->menualign;
}

if (isset($menu_data->layout))
{
	 $layout = $menu_data->layout;
}

if (isset($menu_data->showtitle))
{
	 $show_title = $menu_data->showtitle;
}

if (isset($menu_data->customclass))
{
	 $custom_class = $menu_data->customclass;
}

if (isset($menu_data->faicon) && $menu_data->faicon)
{
	 $faicon = $menu_data->faicon;
}

if (isset($menu_data->dropdown))
{
	 $dropdown = $menu_data->dropdown;
}

if (isset($menu_data->badge))
{
	 $badge = $menu_data->badge;
}

if (isset($menu_data->badge_position))
{
	 $badge_position = $menu_data->badge_position;
}

if (isset($menu_data->badge_bg_color))
{
	 $badge_bg_color = $menu_data->badge_bg_color;
}

if (isset($menu_data->badge_text_color))
{
	 $badge_text_color = $menu_data->badge_text_color;
}


if (!$enable_megamenu)
{
	$display_class = ' hide-menu-builder';
}
else
{
	$dropdown_class = ' hide-menu-builder';
}

$custom_class_label = Text::_('HELIX_ULTIMATE_MENU_CUSTOM_CLASS');

$badge_label = Text::_('HELIX_ULTIMATE_MENU_BADGE_TEXT');

$unique_menu_items = $this->uniqueMenuItems($current_menu_id, $layout);

if ($unique_menu_items)
{
	$unique_menu_item_count = count($unique_menu_items);
}
?>

	<div class="hu-row">
	<div class="hu-col-sm-9">
		
		<div class="hu-megamenu-wrap">

			<div class="hu-megamenu-actions">
				<?php
				if ((int) $menu_item->parent_id === 1)
				{
					echo $this->switchFieldHTML('toggler', Text::_('HELIX_ULTIMATE_MENU_ENABLED'), $enable_megamenu);
					echo $this->textFieldHTML('width', Text::_('HELIX_ULTIMATE_MENU_SUB_WIDTH'), 400, $menu_width, 'number', $display_class);
					echo $this->selectFieldHTML('alignment', Text::_('HELIX_ULTIMATE_MENU_SUB_ALIGNMENT'), $mega_align, $align, $display_class);
				}

				echo $this->switchFieldHTML('title-toggler', Text::_('HELIX_ULTIMATE_MENU_SHOW_TITLE'), $show_title);

				echo $this->selectFieldHTML('dropdown', 'Dropdown Position', $dropdown_list, $dropdown, $dropdown_class);

				echo $this->selectFieldHTML('fa-icon', Text::_('HELIX_ULTIMATE_MENU_ICON'), $fontawesome->getIcons(), $faicon);

				echo $this->textFieldHTML('custom-class', $custom_class_label, '', $custom_class);

				echo $this->textFieldHTML('menu-badge', $badge_label, '', $badge);

				echo $this->selectFieldHTML('badge-position', 'Badge Position', $dropdown_list, $badge_position);

				echo $this->colorFieldHTML('bg-color', 'Background Color', '#333333', $badge_bg_color);

				echo $this->colorFieldHTML('text-color', 'Text Color', '#ffffff', $badge_text_color);
				?>
			</div>

			<div id="hu-megamenu-layout" class="hu-megamenu-layout hu-megamenu-field-control<?php echo ($enable_megamenu != 1)?' hide-menu-builder':''?>" data-megamenu="<?php echo $enable_megamenu; ?>" data-width="<?php echo $menu_width; ?>" data-menualign="<?php echo $align; ?>" data-dropdown="<?php echo $dropdown; ?>" data-showtitle="<?php echo $show_title; ?>" data-customclass="<?php echo $custom_class; ?>" data-faicon="<?php echo $faicon; ?>" data-dropdown="<?php echo $dropdown; ?>" data-badge="<?php echo $badge; ?>" data-badge_position="<?php echo $badge_position; ?>" data-badge_bg_color="<?php echo $badge_bg_color; ?>" data-badge_text_color="<?php echo $badge_text_color; ?>">
				
				<?php if ($layout) { $col_number = 0; ?>
					<?php foreach ($layout as $key => $row) { ?>
						<div class="hu-megamenu-row">
							<div class="hu-megamenu-row-actions clearfix">
								<div class="hu-action-move-row"> <span class="fas fa-sort" aria-hidden="true"></span> Row</div>
								<a href="#" class="hu-action-detele-row"><span class="fas fa-trash" aria-hidden="true"></span></a>
							</div>

							<div class="hu-row">

								<?php if (! empty($row->attr) ) { ?>
									<?php foreach ($row->attr as $col_key => $col) { ?>

										<div class="hu-megmenu-col hu-col-sm-<?php echo $col->colGrid; ?>" data-grid="<?php echo $col->colGrid; ?>">
											<div class="hu-megamenu-column">

												<div class="hu-megamenu-column-actions">
													<span class="hu-action-move-column"><span class="fas fa-arrows-alt" aria-hidden="true"></span> Column</span>
												</div>

												<?php
													$col_list = '<div class="hu-megamenu-item-list">';
													if ( isset($col->items) && count($col->items))
													{
														foreach ($col->items as $item)
														{
															if ($item->type === 'module')
															{
																$modules = $this->getModuleNameById($item->item_id);
																$title = $modules->title . '<a href="javascript:;" class="hu-megamenu-remove-module"><span class="fas fa-times" aria-hidden="true"></span></a>';
															}
															elseif ($item->type === 'menu_item')
															{
																$title = $JMenuSite->getItem($item->item_id)->title;
															}

															$col_list .= '<div class="hu-megamenu-item" data-mod_id="'. $item->item_id .'" data-type="'. $item->type .'">';
															$col_list .= '<div class="hu-megamenu-item-module">';
															$col_list .= '<div class="hu-megamenu-item-module-title">' . $title . '</div>';
															$col_list .= '</div>';
															$col_list .= '</div>';
														}
													}

													if ($unique_menu_item_count && (int) $col_number === 0)
													{
														$col_number++;

														foreach ($unique_menu_items as $key => $item_id)
														{
															$col_list .= '<div class="hu-megamenu-item" data-mod_id="' . $item_id .'" data-type="menu_item">';
															$col_list .= '<div class="hu-megamenu-item-module">';
															$col_list .= '<div class="hu-megamenu-item-module-title">' . $JMenuSite->getItem($item_id)->title .'</div>';
															$col_list .= '</div>';
															$col_list .= '</div>';
														}
													}

													$col_list .= '</div>';
													echo $col_list;
												?>

											</div>
										</div>

									<?php } ?>
								<?php } ?>

							</div>
						</div>

					<?php } ?>
				<?php } ?>

			</div>

		</div>

		<div class="hu-megamenu-add-row hu-megamenu-field-control clearfix<?php echo ($enable_megamenu != 1)?' hide-menu-builder':''?>">
			<button id="hu-choose-megamenu-layout" class="hu-choose-megamenu-layout"><span class="fas fa-plus-circle" aria-hidden="true"></span> Add New Row</button>
			<div class="hu-megamenu-modal" id="hu-megamenu-layout-modal" style="display: none;" >
				<div class="hu-row">

				<?php foreach ($this->row_layouts as $row_layout) { $col_grids = explode('+', $row_layout); ?>
					<div class="hu-col-sm-4">
						<div class="hu-megamenu-grids" data-layout="<?php echo $row_layout; ?>">
							<div class="hu-row">

								<?php foreach ($col_grids as $col_grid) { ?>
									<div class="hu-col-sm-<?php echo $col_grid; ?>"><div><?php echo $col_grid; ?></div></div>
								<?php } ?>

							</div>
						</div>
					</div>

				<?php } ?>

				</div>
			</div>
		</div> <!-- End of Row Layout Structure -->

	</div>

	<?php if ((int) $menu_item->parent_id === 1 && $module_list) : ?>
		<div class="hu-col-sm-3">
			<div class="hu-megamenu-sidebar <?php echo ($enable_megamenu != 1) ? ' hide-menu-builder' : ''; ?>">
				<h3><span class="fas fa-bars" aria-hidden="true"></span> <?php echo Text::_('HELIX_ULTIMATE_MENU_MODULE_LIST'); ?></h3>
				<div class="hu-megamenu-module-list">
					<?php foreach ($module_list as $module) : ?>
						<div class="hu-megamenu-draggable-module" data-mod_id="<?php echo $module->id; ?>" data-type="module"><span class="fas fa-arrows-alt" aria-hidden="true"></span> <?php echo $module->title; ?></div>
					<?php endforeach; ?>
				</div>
			</div>
		</div> <!-- End of Module List -->
		<?php endif; ?>

	</div>
