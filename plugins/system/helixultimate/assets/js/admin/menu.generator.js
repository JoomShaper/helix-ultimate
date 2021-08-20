/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

jQuery(function ($) {
	$('#attrib-helixultimatemegamenu')
		.find('.control-group')
		.first()
		.find('.control-label')
		.remove();
	$('#attrib-helixultimatemegamenu')
		.find('.control-group')
		.first()
		.find('>.controls')
		.removeClass()
		.addClass('megamenu')
		.unwrap();

	$(document).on('click', '#hu-megamenu-toggler', function (
		event
	) {
		var currentVal = $(this).is(':checked');
		$('#hu-megamenu-layout').data('megamenu', currentVal);

		if (currentVal) {
			$(
				'.hu-megamenu-field-control, .hu-megamenu-sidebar'
			).removeClass('hide-menu-builder');
			$('.hu-dropdown-field-control').addClass(
				'hide-menu-builder'
			);
		} else {
			$(
				'.hu-megamenu-field-control, .hu-megamenu-sidebar'
			).addClass('hide-menu-builder');
			$('.hu-dropdown-field-control').removeClass(
				'hide-menu-builder'
			);
		}
	});

	$(document).on('change', '#hu-megamenu-width', function (
		event
	) {
		$('#hu-megamenu-layout').data('width', $(this).val());
	});

	$(document).on('change', '#hu-megamenu-alignment', function (
		event
	) {
		$('#hu-megamenu-layout').data('menualign', $(this).val());
	});

	$(document).on('click', '#hu-megamenu-title-toggler', function (
		event
	) {
		$('#hu-megamenu-layout').data(
			'showtitle',
			$(this).is(':checked')
		);
	});

	$(document).on('change', '#hu-megamenu-dropdown', function (
		event
	) {
		$('#hu-megamenu-layout').data('dropdown', $(this).val());
	});

	$(document).on('change', '#hu-megamenu-fa-icon', function (
		event
	) {
		$('#hu-megamenu-layout').data('faicon', $(this).val());
	});

	$(document).on('change', '#hu-megamenu-custom-class', function (
		event
	) {
		$('#hu-megamenu-layout').data('customclass', $(this).val());
	});

	$(document).on('change', '#hu-megamenu-menu-badge', function (
		event
	) {
		$('#hu-megamenu-layout').data('badge', $(this).val());
	});

	$(document).on(
		'change',
		'#hu-megamenu-badge-position',
		function (event) {
			$('#hu-megamenu-layout').data(
				'badge_position',
				$(this).val()
			);
		}
	);

	$(document).on('change', '#hu-menu-badge-bg-color', function (
		event
	) {
		$('#hu-megamenu-layout').data(
			'badge_bg_color',
			$(this).val()
		);
	});

	$(document).on('change', '#hu-menu-badge-text-color', function (
		event
	) {
		$('#hu-megamenu-layout').data(
			'badge_text_color',
			$(this).val()
		);
	});

	/**
	 * Saving menu layout
	 */
	document.adminForm.onsubmit = function (event) {
		var layout = [];

		$('#hu-megamenu-layout')
			.find('.hu-megamenu-row')
			.each(function (index) {
				var $row = $(this),
					rowIndex = index;

				layout[rowIndex] = {
					type: 'row',
					attr: [],
				};

				// Get each column data;
				$row.find('.hu-megmenu-col').each(function (index) {
					var $column = $(this),
						colIndex = index,
						colGrid = $column.attr('data-grid');

					layout[rowIndex].attr[colIndex] = {
						type: 'column',
						colGrid: colGrid,
						menuParentId: '',
						moduleId: '',
						items: [],
					};

					// get current child id
					var menuParentId = '';

					$column.find('h4').each(function (index, el) {
						menuParentId += $(this).data('current_child') + ',';
					});

					if (menuParentId) {
						menuParentId = menuParentId.slice(',', -1);
						layout[rowIndex].attr[
							colIndex
						].menuParentId = menuParentId;
					}

					// get modules id
					var moduleId = '';
					$column
						.find('.hu-megamenu-item')
						.each(function (index, el) {
							moduleId += $(this).data('mod_id') + ',';
							var type = $(this).data('type');
							var item_id = $(this).data('mod_id');
							layout[rowIndex].attr[colIndex].items[index] = {
								type: type,
								item_id: item_id,
							};
						});

					if (moduleId) {
						moduleId = moduleId.slice(',', -1);
						layout[rowIndex].attr[colIndex].moduleId = moduleId;
					}
				});
			});

		var initData = $('#hu-megamenu-layout').data();

		var menumData = {
			width: initData.width || '0',
			menuitem: initData.menuitem,
			menualign: initData.menualign,
			megamenu: initData.megamenu,
			showtitle: initData.showtitle,
			faicon: initData.faicon,
			customclass: initData.customclass,
			dropdown: initData.dropdown,
			badge: initData.badge,
			badge_position: initData.badge_position,
			badge_bg_color: initData.badge_bg_color,
			badge_text_color: initData.badge_text_color,
			layout: layout,
		};

		$('#jform_params_helixultimatemenulayout').val(
			JSON.stringify(menumData)
		);
	}; //End of onSubmit Event Call

	$(document).on('click', '#hu-choose-megamenu-layout', function (
		e
	) {
		e.preventDefault();
		$('#hu-megamenu-layout-modal').toggle();
	});

	$(document).on('click', '.hu-megamenu-grids', function (e) {
		e.preventDefault();
		var data_layout = $(this).attr('data-layout');

		var layout_row_tpl = '<div class="hu-megamenu-row">';
		layout_row_tpl +=
			'<div class="hu-megamenu-row-actions clearfix">';
		layout_row_tpl += '<div class="hu-action-move-row">';
		layout_row_tpl += '<span class="fas fa-sort" aria-hidden="true"></span> Row';
		layout_row_tpl += '</div>';
		layout_row_tpl +=
			'<a href="#" class="hu-action-detele-row"><span class="far fa-trash-alt" aria-hidden="true"></span></a>';
		layout_row_tpl += '</div>';
		layout_row_tpl += '<div class="hu-row">';

		var layout_col_tpl =
			'<div class="hu-megmenu-col hu-col-sm-{col}" data-grid="{grid}">';
		layout_col_tpl += '<div class="hu-megamenu-column">';
		layout_col_tpl +=
			'<div class="hu-megamenu-column-actions">';
		layout_col_tpl +=
			'<span class="hu-action-move-column"><span class="fas fa-arrows-alt" aria-hidden="true"></span> Column</span>';
		layout_col_tpl += '</div>';
		layout_col_tpl +=
			'<div class="hu-megamenu-item-list"></div>';
		layout_col_tpl += '</div>';
		layout_col_tpl += '</div>';

		var appending_col = '';
		if (data_layout != 12) {
			var col_layout_data = data_layout.split('+');
			for (i = 0; i < col_layout_data.length; i++) {
				appending_col += layout_col_tpl
					.replace('{col}', col_layout_data[i])
					.replace('{grid}', col_layout_data[i]);
			}
		} else {
			appending_col += layout_col_tpl
				.replace('{col}', 12)
				.replace('{grid}', 12);
		}

		layout_row_tpl += appending_col;
		layout_row_tpl += '</div>';
		layout_row_tpl += '</div>';

		$('#hu-megamenu-layout').append(layout_row_tpl);
		$(this).closest('#hu-megamenu-layout-modal').hide();

		helix_ultimate_megamenu_sorting_init();
	});

	function helix_ultimate_megamenu_sorting_init() {
		$('.hu-megamenu-item-list')
			.sortable({
				connectWith: '.hu-megamenu-item-list',
				items: ' .hu-megamenu-item',
				placeholder: 'drop-highlight',
				start: function (e, ui) {
					ui.placeholder.height(ui.item.height());
				},
				stop: function (e, ui) {},
			})
			.disableSelection();

		/**
		 * Drag from module lists
		 */
		$('.hu-megamenu-module-list')
			.sortable({
				connectWith: '.hu-megamenu-item-list',
				items: ' .hu-megamenu-draggable-module',
				placeholder: 'drop-highlight',
				helper: 'clone',
				start: function (e, ui) {
					ui.placeholder.height(ui.item.height());
				},
				update: function (e, ui) {
					var module_title = ui.item.text();
					var mod_delete_button =
						'<a href="javascript:;" class="hu-megamenu-remove-module"><span class="fas fa-times" aria-hidden="true"></span></a>';
					var module_inner =
						'<div class="hu-megamenu-item-module"><div class="hu-megamenu-item-module-title">' +
						mod_delete_button +
						'<span>' +
						module_title +
						'</span></div></div>';

					ui.item
						.removeAttr('style class')
						.addClass('hu-megamenu-item')
						.html(module_inner);
					ui.item.clone().insertAfter(
						ui.item
							.html(
								'<span class="fas fa-arrows-alt" aria-hidden="true"></span> ' +
									module_title
							)
							.removeAttr('class')
							.addClass(
								'hu-megamenu-draggable-module'
							)
					);
					$(this).sortable('cancel');

					helix_ultimate_megamenu_sorting_init();
				},
			})
			.disableSelection();

		$('.hu-megamenu-row').sortable({
			start: function (e, ui) {
				ui.placeholder.height(ui.item.height());
				ui.placeholder.width(ui.item.width() - 50);
			},
			items: '.hu-megmenu-col',
			handle: '.hu-action-move-column',
			placeholder: 'drop-col-highlight',
			stop: function (e, ui) {
				//helix_ultimate_megamenu_sorting_init();
			},
		});

		$('#hu-megamenu-layout').sortable({
			start: function (e, ui) {
				ui.placeholder.height(ui.item.height());
				ui.placeholder.width(ui.item.width() - 50);
			},
			items: '.hu-megamenu-row',
			handle: '.hu-action-move-row',
			placeholder: 'drop-highlight',
			stop: function (e, ui) {
				//helix_ultimate_megamenu_sorting_init();
			},
		});

		$(document).on(
			'click',
			'.hu-megamenu-remove-module',
			function (e) {
				e.preventDefault();
				$(this).closest('.hu-megamenu-item').remove();
			}
		);
	}
	helix_ultimate_megamenu_sorting_init();

	$(document).on('click', '.hu-action-detele-row', function (e) {
		e.preventDefault();
		$(this).closest('.hu-megamenu-row').remove();
	});
});
