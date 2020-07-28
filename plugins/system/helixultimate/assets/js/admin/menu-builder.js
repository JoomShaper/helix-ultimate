jQuery(function ($) {
	const config = Joomla.getOptions('meta') || {};
	const $builder = $('.hu-menu-builder');
	/**
	 * Perform operation in reactive way
	 */

	const state = {
		menuItems: {},
		some: 'other',
	};

	const fields = getFields();

	const setState = function (object, callback = undefined) {
		Object.entries(object).forEach(([key, value]) => {
			state[key] = value;
		});

		!!callback && callback(state);
		render();
	};

	(function componentDidMount() {
		const $builder = $('.hu-menu-builder');
		$builder.find('.hu-menu-item').each(function (index) {
			const itemId = $(this).data('cid');

			const item = {
				id: $(this).data('cid'),
				title: $(this).data('name'),
				menu_custom_classes: '',
				menu_icon: '',
				menu_caption: '',
				mega_menu: 0,
				mega_width: '',
				mega_custom_classes: '',
				mega_rows: [
					{
						id: 1,
						settings: {},
						columns: [],
					},
				],
			};

			setState({
				menuItems: {
					...state.menuItems,
					[itemId]: item,
				},
			});
		});
	})();

	function getFields() {
		return [
			{
				event: 'blur',
				parent: '.hu-menu-builder',
				selectors: [
					'input[name=menu_custom_classes]',
					'input[name=menu_icon]',
					'input[name=menu_caption]',
					'input[name=mega_width]',
					'input[name=mega_custom_classes]',
				],
			},
			{
				event: 'change',
				parent: '.hu-menu-builder',
				selectors: [
					'input[name=mega_menu]',
					'input[name=mega_alignment]',
				],
			},
		];
	}

	(function handleInputChange(fields) {
		fields.forEach(events => {
			events.selectors.forEach(selector => {
				$(document).on(
					events.event,
					`${events.parent} ${selector}`,
					function (e) {
						let value = $(this).val();
						const name = $(this).attr('name'),
							itemId = $(this).data('itemid'),
							type = $(this).attr('type');

						if (type === 'checkbox') {
							value = $(this).prop('checked') >> 0;
						}

						if (!itemId) return;

						setState({
							menuItems: {
								...state.menuItems,
								[itemId]: {
									...state.menuItems[itemId],
									[name]: value,
								},
							},
						});
					}
				);
			});
		});
	})(fields);

	function render() {
		// Update input value
		console.log(state);
		$('.hu-menu-builder')
			.find('.hu-megamenu-field')
			.val(JSON.stringify(state));
	}

	/** ================================================================== */

	/**
	 * Activating the menu item sorting
	 */
	function activateMenuItemSorting() {
		$('.hu-menu-items')
			.sortable({
				containment: '.hu-menu-items-wrapper',
				cursor: 'move',
				opacity: 0.6,
				scroll: true,
				axis: 'x',
				tolerance: 'pointer',
				update: (event, ui) => {
					const $items = $('.hu-menu-items > li');
					const data = {
						cid: [],
						order: [],
					};

					$items.each(function (index, el) {
						data.cid.push($(el).data('cid'));
						data.order.push(index + 1);
					});

					saveMenuOrder(data);
				},
			})
			.disableSelection();
	}

	activateMenuItemSorting();

	function makeMegamenuSectionSortable() {
		let prevIndex = null;

		$('#hu-megamenu-layout-container.active-layout')
			.sortable({
				placeholder: 'ui-state-highlight',
				forcePlaceholderSize: true,
				containment: '.hu-mega-basic-settings',
				handle: '.hu-megamenu-move-row',
				cursor: 'move',
				opacity: 1,
				axis: 'y',
				tolerance: 'pointer',
				start: function (event, ui) {
					prevIndex = ui.item.index();
				},
				update: function (event, ui) {
					const item = ui.item;
					const currIndex = item.index();
					const rowId = item.data('rowid');
					const itemId = item.data('itemid');

					swapRows(itemId, prevIndex - 1, currIndex - 1);
				},
			})
			.disableSelection();
	}
	makeMegamenuSectionSortable();

	function triggerMenuSettings(active) {
		$('.hu-menu-item-settings').removeClass('active');
		$('.hu-menu-item-settings')
			.find('#hu-megamenu-layout-container')
			.removeClass('active-layout');

		$(`.hu-menu-item-settings.hu-menu-item-${active}`).addClass('active');
		$(`.hu-menu-item-settings.hu-menu-item-${active}`)
			.find('#hu-megamenu-layout-container')
			.addClass('active-layout');

		makeMegamenuSectionSortable();
	}

	/**
	 * Add new row
	 */
	function addNewRow() {
		$(document).on('click', '.hu-megamenu-add-row', function (e) {
			e.preventDefault();
			$('.hu-megamenu-layout-row').sortable('destroy');
			const $parent = $(this).closest('.hu-megamenu-layout-section');
			const $cloned = $('#hu-megamenu-layout-container.active-layout')
				.find('.hu-reserved-layout-section')
				.clone(true);
			const rowId = getLastRowId() + 1;

			$cloned
				.removeClass('hu-reserved-layout-section')
				.addClass('hu-megamenu-layout-section')
				.attr('data-rowid', rowId)
				.data('rowid', rowId)
				.hide();

			$cloned.insertAfter($parent);
			$cloned.slideDown(300);

			const insertIndex = $parent.index();
			insertNewRow(101, insertIndex, {
				id: rowId,
				settings: {},
				columns: [],
			});

			const $column = $cloned.find('.hu-megamenu-layout-column');
			$column.data('rowid', rowId);
			$column.attr('data-rowid', rowId);

			columnSorting();
		});
	}
	addNewRow();

	function getLastRowId() {
		const ids = [];
		$(
			'.hu-menu-builder #hu-megamenu-layout-container.active-layout .hu-megamenu-layout-section'
		).each(function (index, el) {
			ids.push($(el).data('rowid'));
		});

		return Math.max(...ids);
	}

	function insertNewRow(itemId, index, newItem) {
		let rows = [...state.menuItems[itemId].mega_rows];
		rows.splice(index, 0, newItem);

		setState({
			menuItems: {
				...state.menuItems,
				[itemId]: {
					...state.menuItems[itemId],
					mega_rows: rows,
				},
			},
		});
	}

	function swapRows(itemId, prevIndex, currIndex) {
		const rows = [...state.menuItems[itemId].mega_rows];
		const item = rows.splice(prevIndex, 1);

		if (item.length === 0) return;

		rows.splice(currIndex, 0, item[0]);

		setState({
			menuItems: {
				...state.menuItems,
				[itemId]: {
					...state.menuItems[itemId],
					mega_rows: rows,
				},
			},
		});
	}

	/**
	 * Delete a row
	 */
	function deleteRow() {
		$(document).on('click', '.hu-megamenu-remove-row', function (e) {
			e.preventDefault();

			const totalSections = $(this)
				.closest('.hu-megamenu-layout-section')
				.siblings().length;

			if (totalSections <= 0) {
				return;
			}

			const confirm = window.confirm('Are you sure to delete the row?');
			if (confirm) {
				$section = $(this).closest('.hu-megamenu-layout-section');

				$section.slideUp(300, function () {
					$(this).remove();
				});
			} else {
				return;
			}
		});
	}
	deleteRow();

	function toggleColumnOptions() {
		$(document).on('click', '.hu-megamenu-add-columns', function (e) {
			e.preventDefault();
			const $colList = $(this).next('.hu-megamenu-column-list');
			$colList.toggleClass('show');
		});
	}
	toggleColumnOptions();

	function generateColumns() {
		$(document).on('click', '.hu-megamenu-column-layout', function (e) {
			$('.hu-megamenu-layout-row').sortable('destroy');

			const $section = $(this).closest('.hu-megamenu-layout-section');

			const itemId = $section.data('itemid');
			const rowId = $section.data('rowid');

			let layout = $(this).data('layout');

			if (layout === 'custom') {
				layout = prompt(
					'Enter your custom layout like 4+2+2+2+2 as total 12 grid',
					'4+2+2+2+2'
				);
			}

			const grids = layout
				.trim()
				.split('+')
				.map(col => col >> 0);

			if (isValidLayout(grids)) {
				let columnStr = '';

				grids.forEach((col, index) => {
					columnStr += `
						<div class="hu-megamenu-layout-column col-${col}" data-itemid="${itemId}" data-rowid="${rowId}" data-columnid="${
						index + 1
					}">
							<div class="hu-megamenu-column">
								<span class="hu-megamenu-column-title">none</span>
								<a class="hu-megamenu-column-options" href="#">
									<svg xmlns="http://www.w3.org/2000/svg" width="15" height="3" fill="none"><path fill="#020B53" fill-rule="evenodd" d="M3 1.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zm6 0a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM13.5 3a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" clip-rule="evenodd" opacity=".4"></path></svg>
								</a>
							</div>
						</div>
					`;
				});

				const $gParent = $(this).closest('.hu-megamenu-layout-section');
				$gParent.find('.hu-megamenu-layout-row').html(columnStr);
				$(this).closest('.hu-megamenu-column-list').removeClass('show');
				columnSorting();
			} else {
				alert(
					'Your grid is invalid. The summation of the columns never exceed 12'
				);
			}
		});
	}
	generateColumns();

	function columnSorting() {
		$('.hu-megamenu-layout-row')
			.sortable({
				connectWith: '.hu-megamenu-layout-row',
				placeholder: 'ui-state-highlight',
				forcePlaceholderSize: true,
				axis: 'x',
				opacity: 1,
				tolerance: 'pointer',

				start: function (event, ui) {
					$('.hu-megamenu-layout-section')
						.find('.ui-state-highlight')
						.addClass($(ui.item).attr('class'));
					$('.hu-megamenu-layout-section')
						.find('.ui-state-highlight')
						.css('height', $(ui.item).outerHeight());
				},
			})
			.disableSelection();
	}
	columnSorting();

	function isValidLayout(grids) {
		return grids.reduce((acc, curr) => acc + curr) <= 12;
	}

	/**
	 * Handling the menu selection on click event
	 */
	function handlingMenuItemSelection() {
		$('.hu-menu-item').on('click', function (event) {
			event.preventDefault();
			const $siblings = $(this).siblings();

			if ($siblings.hasClass('active')) {
				$siblings.removeClass('active');
			}

			if (!$(this).hasClass('active')) {
				$(this).addClass('active');
			}

			triggerMenuSettings($(this).data('name'));
		});
	}

	handlingMenuItemSelection();

	function saveMenuOrder(data) {
		const url = `${config.base}/administrator/index.php?option=com_menus&view=items&task=items.saveOrderAjax&tmpl=component`;

		$.ajax({
			method: 'POST',
			url,
			data,
			beforeSend: () => {
				Joomla.helixLoading(true);
			},
			success: response => {
				Joomla.reloadPreview();
			},
			error: err => {
				alert(err);
			},
			completed: () => {
				Joomla.helixLoading(false);
			},
		});
	}
});
