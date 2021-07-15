var megaMenu = {
	run() {
		this.declareDOMVariables();
		this.initMiniColors();
		this.jQueryPluginExtension();
		this.initChosen();

		this.removeEventListeners();

		this.handleMegaMenuToggle();
		this.toggleSidebarSettings($megamenu.prop('checked'));
		this.handleSidebarSettings();
		this.handleCloseModal();

		this.rowSortable('.hu-megamenu-rows-container');
		this.columnSortable('.hu-megamenu-columns-container');
		this.itemSortable('.hu-megamenu-column-contents');

		this.handleSaveMegaMenuSettings();

		this.handleRemoveRow();
		this.handleLoadSlots();
		this.handleCustomLayoutDisplay();

		this.handleLayoutOptionSelection();
		this.handleCustomLayoutSelection();
		this.handleRowWiseColumnLayoutSelection();

		this.openModulePopover();

		this.handleClosePopover();

		this.handleAddNewCell();
		this.handleRemoveCell();

		this.toggleColumnsSlots();
		this.handleModuleSearch();
	},

	jQueryPluginExtension() {
		$.fn.extend({
			test() {
				return this.css('color', '#fff');
			},
		});
	},

	initChosen() {
		$('select[data-husearch]').chosen({
			width: '100%',
			allow_single_deselect: true,
			placeholder_text_single: Joomla.Text._('HELIX_ULTIMATE_SELECT_ICON_LABEL'),
		});
	},

	declareDOMVariables() {
		$megamenu = $('.hu-megamenu-builder-megamenu');
		$settingsInput = $('#hu-megamenu-layout-settings');
		$saveBtn = $('.hu-megamenu-save-btn');
		$cancelBtn = $('.hu-megamenu-cancel-btn');
		$rowsContainer = $('.hu-megamenu-rows-container');
		$popover = $('.hu-megamenu-popover');

		const defaultSettingsData = {
			badge: '',
			badge_bg_color: '',
			badge_position: '',
			badge_text_color: '',
			customclass: '',
			dropdown: 'right',
			faicon: '',
			layout: [],
			megamenu: 0,
			menualign: 'full',
			showtitle: 1,
			width: '600px',
		};

		itemId = $('#hu-menu-itemid').val();
		settingsData = $settingsInput.val();
		settingsData = settingsData && JSON.parse(settingsData);
		settingsData = $.extend(defaultSettingsData, settingsData);

		baseUrl = $('#hu-base-url').val();
	},

	handleRemoveCell() {
		$(document).on('click', '.hu-megamenu-cell-remove', function () {
			const cellId = $(this).closest('.hu-megamenu-cell').data('cellid') || 1;
			const columnId = $(this).closest('.hu-megamenu-col').data('columnid') || 1;
			const rowId = $(this).closest('.hu-megamenu-row-wrapper').data('rowid') || 1;

			$(this)
				.closest('.hu-megamenu-cell')
				.slideUp(function () {
					$(this).remove();
					let column = settingsData.layout[rowId - 1].attr[columnId - 1];
					let items = column.items !== undefined ? column.items : [];
					items.length > 0 && items.splice(cellId - 1, 1);
					settingsData.layout[rowId - 1].attr[columnId - 1].items = items;
				});
		});
	},

	handleAddNewCell() {
		const self = this;
		$(document).on('click', '.hu-megamenu-insert-module', async function () {
			const item_id = $(this).data('module');
			const type = 'module';
			const rowId = $popover.data('rowid');
			const columnId = $popover.data('columnid');

			const column = settingsData.layout[rowId - 1].attr[columnId - 1] || { items: [] };

			if (column.items === undefined) column.items = [];

			const data = {
				type,
				item_id,
				itemId,
				rowId,
				columnId,
				cellId: column.items.length + 1,
			};

			const res = await self.addNewCell(data);

			if (res.status) {
				$(
					`.hu-megamenu-row-wrapper[data-rowid=${rowId}] .hu-megamenu-col[data-columnid=${columnId}] .hu-megamenu-column-contents`
				).append(res.html);

				self.closePopover();
				column.items.push({ type, item_id });
				settingsData.layout[rowId - 1].attr[columnId - 1] = column;
			}
		});
	},

	addNewCell(data) {
		let url = `${baseUrl}/administrator/index.php?option=com_ajax&helix=ultimate&request=task&action=generateNewCell&helix_id=${helixUltimateStyleId}`;

		return new Promise((resolve, reject) => {
			$.ajax({
				method: 'POST',
				url,
				data,
				success(res) {
					res = typeof res === 'string' && res.length > 0 ? JSON.parse(res) : false;
					resolve(res);
				},
				error(err) {
					reject(err);
				},
			});
		});
	},

	handleClosePopover() {
		const self = this;
		$(document).on('click', '.hu-megamenu-popover-close', function () {
			self.closePopover();
		});
	},

	openPopover() {
		!$popover.hasClass('show') && $popover.addClass('show');

		// Reset the search field value on popover open
		$('.hu-megamenu-module-search').val('');
	},

	closePopover() {
		$popover.hasClass('show') && $popover.removeClass('show');
	},

	openModulePopover() {
		const self = this;
		$(document).on('click', '.hu-megamenu-add-new-item', async function (e) {
			const columnId = $(this).closest('.hu-megamenu-col').data('columnid') || 1;
			const rowId = $(this).closest('.hu-megamenu-row-wrapper').data('rowid') || 1;

			$popover
				.data('rowid', rowId)
				.data('columnid', columnId)
				.attr('data-rowid', rowId)
				.attr('data-columnid', columnId);

			const res = await self.getModulesContents();
			res.status && $popover.find('.hu-megamenu-modules-container').html(res.html);

			self.openPopover();
		});
	},

	handleModuleSearch() {
		let timeout = null,
			self = this;

		$(document).on('keyup', '.hu-megamenu-module-search', function (e) {
			e.preventDefault();

			timeout && clearTimeout(timeout);

			timeout = setTimeout(async () => {
				let { value } = e.target;
				const res = await self.getModulesContents(value);
				res.status && $popover.find('.hu-megamenu-modules-container').html(res.html);
			}, 100);
		});
	},

	getModulesContents(keyword = '') {
		const url = `${baseUrl}/administrator/index.php?option=com_ajax&helix=ultimate&request=task&action=getModuleList&keyword=${keyword}&helix_id=${helixUltimateStyleId}`;

		return new Promise((resolve, reject) => {
			$.ajax({
				method: 'GET',
				url,
				success(res) {
					res = typeof res === 'string' && res.length > 0 ? JSON.parse(res) : false;
					resolve(res);
				},
				error(err) {
					reject(err);
				},
			});
		});
	},

	initMiniColors() {
		$('.hu-input-color').each(function () {
			$(this).addClass('minicolors');
		});
		$('.hu-megamenu-container .minicolors').each(function () {
			$(this).minicolors({
				control: 'hue',
				position: 'bottom',
				theme: 'bootstrap',
			});
		});
	},

	handleMegaMenuToggle() {
		let self = this;

		$megamenu.on('change', function (e) {
			e.preventDefault();
			self.toggleSidebarSettings($(this).prop('checked'));
		});
	},

	/** Status is true then expand, collapse otherwise */
	toggleSidebarSettings(status) {
		let $grid = $('.hu-megamenu-grid');
		let $settings = $('.hu-megamenu-settings');
		let $alignment = $('.hu-megamenu-alignment');
		let $dropdown = $('.hu-menuitem-dropdown-position');
		let $builderModal = $('.hu-mega-menu-builder');

		if (status) {
			if (!$settings.hasClass('show')) $settings.addClass('show');
			if (!$grid.hasClass('show')) $grid.addClass('show');
			if ($builderModal.hasClass('collapsed')) $builderModal.removeClass('collapsed');
			$alignment.show();
			$dropdown.hide();
		} else {
			if ($settings.hasClass('show')) $settings.removeClass('show');
			if ($grid.hasClass('show')) $grid.removeClass('show');
			if (!$builderModal.hasClass('collapsed')) $builderModal.addClass('collapsed');
			$alignment.hide();
			$dropdown.show();
		}
	},

	/** Handling custom layout panel toggling */
	handleCustomLayoutDisplay() {
		$(document).on('click', '.hu-megamenu-custom', function () {
			$(this).closest('.hu-megamenu-columns-layout').find('.hu-megamenu-custom-layout').slideToggle(100);
		});
	},

	closeRowLayoutDisplay() {
		let $slot = $('.hu-megamenu-row-slots');
		if ($slot.hasClass('show')) $slot.removeClass('show');
	},

	closeLayoutDisplay() {
		$('.hu-megamenu-add-slots').hide();
	},

	/** Remove all the event listeners */
	removeEventListeners() {
		$(document).off('click', '.hu-megamenu-add-slots .hu-megamenu-custom-layout-apply');
		$(document).off('click', '.hu-megamenu-remove-row');
		$(document).off('click', '.hu-megamenu-add-row > a');
		$(document).off('click', '.hu-megamenu-custom');
		$(document).off('click', '.hu-megamenu-columns');
		$(document).off('click', '.hu-megamenu-add-new-item');
		$(document).off('click', '.hu-megamenu-cell-options-item');
		$(document).off('click', '.hu-megamenu-popover-close');
		$(document).off('click', '.hu-megamenu-insert-module');

		$(document).off('click', '.hu-megamenu-cell-remove');
		$(document).off('click', '.hu-megamenu-add-slots .hu-megamenu-column-layout:not(.hu-megamenu-custom)');
		$(document).off('click', '.hu-megamenu-row-slots .hu-megamenu-column-layout:not(.hu-megamenu-custom)');
		$(document).off('click', '.hu-megamenu-row-slots .hu-megamenu-custom-layout-apply');
		$cancelBtn.off('click');
		$saveBtn.off('click');
		$megamenu.off('change');
	},

	/** Handling row wise column layout selection */
	handleRowWiseColumnLayoutSelection() {
		const self = this;
		$(document).on(
			'click',
			'.hu-megamenu-row-slots .hu-megamenu-column-layout:not(.hu-megamenu-custom)',
			async function () {
				const rowIndex = $(this).closest('.hu-megamenu-row-wrapper').data('rowid') - 1;
				const layout = $(this).data('layout') || '12';
				await self.changeRowsColumns({
					rowIndex,
					layout,
					$container: $(this).closest('.hu-megamenu-row-wrapper').find('.hu-megamenu-columns-container'),
				});
			}
		);

		$(document).on('click', '.hu-megamenu-row-slots .hu-megamenu-custom-layout-apply', async function () {
			const rowIndex = $(this).closest('.hu-megamenu-row-wrapper').data('rowid') - 1;
			const layout = $(this).parent().find('.hu-megamenu-custom-layout-field').val();
			await self.changeRowsColumns({
				rowIndex,
				layout,
				$container: $(this).closest('.hu-megamenu-row-wrapper').find('.hu-megamenu-columns-container'),
			});
		});
	},

	async changeRowsColumns({ rowIndex, layout, $container }) {
		const rowData = settingsData.layout[rowIndex];
		const res = await this.updateRowLayout({
			layout,
			rowData: JSON.stringify(rowData),
			rowId: rowIndex + 1,
			itemId,
		});

		if (res.status) {
			$container.html(res.html);
			settingsData.layout[rowIndex] = res.data;
			this.closeRowLayoutDisplay();
			this.refreshSortable(['item']);
		}
	},

	updateRowLayout({ layout, rowData, rowId, itemId }) {
		let self = this;
		return new Promise((resolve, reject) => {
			const url = `${baseUrl}/administrator/index.php?option=com_ajax&helix=ultimate&request=task&action=updateRowLayout&helix_id=${helixUltimateStyleId}`;
			const data = {
				layout,
				data: rowData,
				rowId,
				itemId,
			};
			$.ajax({
				method: 'POST',
				url,
				data,
				success(res) {
					res = typeof res === 'string' && res.length ? JSON.parse(res) : false;
					resolve(res);
				},
				error(err) {
					reject(err);
				},
			});
		});
	},

	/** Handling layout option selection. */
	handleLayoutOptionSelection() {
		let self = this;
		$(document).on(
			'click',
			'.hu-megamenu-add-slots .hu-megamenu-column-layout:not(.hu-megamenu-custom)',
			async function (e) {
				e.preventDefault();

				const layout = $(this).data('layout') || '12';
				const rowId = settingsData.layout.length + 1;
				const response = await self.generateRow(layout, rowId, itemId);

				if (response.status) {
					$rowsContainer.append(response.data);
					self.closeLayoutDisplay();

					settingsData.layout.push(response.row);
					self.refreshSortable(['column', 'item']);
				}
			}
		);
	},

	handleCustomLayoutSelection() {
		let self = this;

		$(document).on('click', '.hu-megamenu-add-slots .hu-megamenu-custom-layout-apply', async function (e) {
			e.preventDefault();
			let $field = $('.hu-megamenu-custom-layout-field'),
				layout = $field.val(),
				rowId = settingsData.layout.length + 1;

			if (layout == '') return;

			const res = await self.generateRow(layout, rowId, itemId);

			if (res.status) {
				$rowsContainer.append(res.data);
				self.closeLayoutDisplay();
				settingsData.layout.push(res.row);
				self.refreshSortable();
			}
		});
	},

	toggleColumnsSlots() {
		$(document).on('click', '.hu-megamenu-columns', function () {
			$(this).closest('.hu-megamenu-row-toolbar-right').find('.hu-megamenu-row-slots').toggleClass('show');
		});
	},

	/**
	 * Generating the row
	 */
	generateRow(layout, rowId, itemId) {
		let self = this;
		return new Promise((resolve, reject) => {
			const url = `${baseUrl}/administrator/index.php?option=com_ajax&helix=ultimate&request=task&action=generateRow&helix_id=${helixUltimateStyleId}`;
			const data = {
				layout,
				rowId,
				itemId,
			};
			$.ajax({
				method: 'POST',
				url,
				data,
				success(res) {
					res = typeof res === 'string' && res.length ? JSON.parse(res) : false;
					resolve(res);
				},
				error(err) {
					reject(err);
				},
			});
		});
	},

	/** Handling the sidebar fields settings. */
	handleSidebarSettings() {
		let self = this;
		const selectors = [
			'.hu-megamenu-sidebar [name=megamenu]',
			'.hu-megamenu-sidebar [name=width]',
			'.hu-megamenu-sidebar [name=dropdown]',
			'.hu-megamenu-sidebar [name=showtitle]',
			'.hu-megamenu-sidebar [name=menualign]',
			'.hu-megamenu-sidebar [name=faicon]',
			'.hu-megamenu-sidebar [name=customclass]',
			'.hu-megamenu-sidebar [name=badge]',
			'.hu-megamenu-sidebar [name=badge_position]',
			'.hu-megamenu-sidebar [name=badge_bg_color]',
			'.hu-megamenu-sidebar [name=badge_text_color]',
		];
		selectors.forEach(selector => {
			$(selector).on('change', function (e) {
				e.preventDefault();
				let { name, value } = e.target;
				const type = $(this).attr('type');

				value = type === 'checkbox' ? ($(this).prop('checked') >> 0).toString() : value;
				self.updateSettingsField(name, value);
			});
		});
	},

	/** Swap rows using previous index and the current index. */
	swapRow(prev, curr) {
		let layout = settingsData.layout,
			item = layout.splice(prev, 1);
		layout.splice(curr, 0, item[0]);
		settingsData.layout = layout;
	},

	/** Swap the columns */
	swapColumn(rowIndex, prev, curr) {
		let columns = settingsData.layout[rowIndex].attr,
			item = columns.splice(prev, 1);
		columns.splice(curr, 0, item[0]);
		settingsData.layout[rowIndex].attr = columns;
	},

	/** Swap the item */
	swapItem({ prevRowIndex, prevColIndex, prevItemIndex, currRowIndex, currColIndex, currItemIndex }) {
		let items = [...settingsData.layout[prevRowIndex].attr[prevColIndex].items],
			item = items.splice(prevItemIndex, 1);
		settingsData.layout[prevRowIndex].attr[prevColIndex].items = items;
		let currColumn = settingsData.layout[currRowIndex].attr[currColIndex];

		if (currColumn.items === undefined) {
			currColumn.items = [];
		}

		let currItems = [...currColumn.items];

		if (currItems.length === 0) {
			currItems.push(item[0]);
		} else {
			currItems.splice(currItemIndex, 0, item[0]);
		}

		currColumn.items = currItems;
		settingsData.layout[currRowIndex].attr[currColIndex] = currColumn;
	},

	updateSettings() {
		$settingsInput.val(JSON.stringify(settingsData));
	},

	updateSettingsField(key, value) {
		settingsData[key] = value;
	},

	handleRemoveRow() {
		$(document).on('click', '.hu-megamenu-remove-row', function (e) {
			e.preventDefault();

			let $parent = $(this).closest('.hu-megamenu-row-wrapper'),
				rowIndex = $parent.index();

			$parent.slideUp(100, function () {
				$(this).remove();
				settingsData.layout.splice(rowIndex, 1);
			});
		});
	},

	handleLoadSlots() {
		$(document).on('click', '.hu-megamenu-add-row > a', function () {
			$(this).closest('.hu-megamenu-grid').find('.hu-megamenu-add-slots').toggle();
		});
	},

	handleCloseModal() {
		$cancelBtn.on('click', function () {
			$(this).closeModal();
		});
	},

	handleSaveMegaMenuSettings() {
		let self = this;
		$saveBtn.on('click', function () {
			self.saveMegaMenuSettings();
		});
	},

	/** Save the mega menu settings. */
	saveMegaMenuSettings() {
		const url = `${baseUrl}/administrator/index.php?option=com_ajax&helix=ultimate&request=task&action=saveMegaMenuSettings&helix_id=${helixUltimateStyleId}`;
		const data = {
			settings: settingsData,
			id: itemId,
		};

		$.ajax({
			method: 'POST',
			url,
			data,
			success(res) {
				res = typeof res === 'string' && res.length > 0 ? JSON.parse(res) : false;
				if (res.status) Joomla.reloadPreview();
			},
			error(err) {
				alert('Something went wrong!');
			},
			complete() {
				$(document).closeModal();
				Joomla.HelixToaster.success('Saved mega menu settings!', 'Success');
			},
		});
	},

	refreshSortable(sortable) {
		if (!sortable) {
			sortable = ['row', 'column', 'item'];
		}

		if (typeof sortable === 'string') {
			sortable = [sortable];
		}

		const sortableMap = {
			row: {
				selector: '.hu-megamenu-rows-container',
				func: 'rowSortable',
			},
			column: {
				selector: '.hu-megamenu-columns-container',
				func: 'columnSortable',
			},
			item: {
				selector: '.hu-megamenu-column-contents',
				func: 'itemSortable',
			},
		};

		for (let i = 0; i < sortable.length; i++) {
			if (sortableMap[sortable[i]] !== undefined) {
				let item = sortableMap[sortable[i]];
				this[item.func](item.selector);
			}
		}
	},

	updateRows() {
		$('.hu-megamenu-row-wrapper').each(function (index) {
			$(this)
				.data('rowid', index + 1)
				.attr('data-rowid', index + 1);
		});
	},

	/** Make row sortable. */
	rowSortable(selector) {
		let self = this,
			prevIndex = null,
			currentIndex = null;
		$(selector)
			.sortable({
				handle: '.hu-megamenu-row-drag-handlers',
				placeholder: 'hu-row-sortable-placeholder',
				axis: 'y',
				items: '> *',
				tolerance: 'pointer',
				scroll: true,
				start(_, ui) {
					let height = ui.helper.outerHeight();
					height -= 2;
					ui.placeholder.css({ height });
					prevIndex = ui.item.index();
				},
				stop(_, ui) {
					currentIndex = ui.item.index();
					self.swapRow(prevIndex, currentIndex);
					self.updateRows();
				},
			})
			.disableSelection();
	},

	updateColumns(rowIndex) {
		$(`.hu-megamenu-row-wrapper[data-rowid=${rowIndex + 1}]`)
			.find('.hu-megamenu-col')
			.each(function (index) {
				$(this)
					.data('columnid', index + 1)
					.attr('data-columnid', index + 1);
			});
	},

	/** Make column sortable. */
	columnSortable(selector) {
		let prevIndex,
			currIndex,
			rowIndex,
			$rowEl,
			self = this;

		$(selector).sortable({
			handle: '.hu-megamenu-column-drag-handler',
			placeholder: 'hu-column-sortable-placeholder',
			containment: '.hu-megamenu-grid',
			axis: 'x',
			items: '> *',
			start(_, ui) {
				let height = ui.helper.outerHeight(),
					width = ui.helper.outerWidth();

				ui.placeholder.css({ height, width });

				rowIndex = ui.item.closest('.hu-megamenu-row-wrapper').data('rowid') - 1;
				prevIndex = ui.item.index();

				$rowEl = ui.item.closest('.hu-megamenu-columns-container');
				$rowEl.addClass('hu-megamenu-column-dragging');
			},
			stop(_, ui) {
				currIndex = ui.item.index();
				self.swapColumn(rowIndex, prevIndex, currIndex);
				self.updateColumns(rowIndex);
				$rowEl.removeClass('hu-megamenu-column-dragging');
			},
		});
	},

	/** Make column items sortable. */
	itemSortable(selector) {
		let currRowIndex,
			currColIndex,
			prevRowIndex,
			prevColIndex,
			prevItemIndex,
			currItemIndex,
			self = this,
			$removeBtn;
		$(selector)
			.sortable({
				connectWith: '.hu-megamenu-column-contents',
				placeholder: 'hu-item-sortable-placeholder',
				containment: '.hu-megamenu-grid',
				items: '> .hu-megamenu-cell',
				start(_, ui) {
					let height = ui.helper.outerHeight(),
						width = ui.helper.outerWidth();

					ui.placeholder.css({ height, width });

					prevColIndex = (ui.item.closest('.hu-megamenu-col').data('columnid') || 1) - 1;
					prevRowIndex = (ui.item.closest('.hu-megamenu-row-wrapper').data('rowid') || 1) - 1;
					prevItemIndex = ui.item.index();

					/**
					 * Hide the remove button from the right in the dragging time.
					 */
					$removeBtn = ui.item.find('.hu-megamenu-cell-remove');
					$removeBtn.css({ opacity: 0 });
				},
				stop(_, ui) {
					currColIndex = (ui.item.closest('.hu-megamenu-col').data('columnid') || 1) - 1;
					currRowIndex = (ui.item.closest('.hu-megamenu-row-wrapper').data('rowid') || 1) - 1;
					currItemIndex = ui.item.index();
					self.swapItem({
						prevRowIndex,
						prevColIndex,
						prevItemIndex,
						currRowIndex,
						currColIndex,
						currItemIndex,
					});
					$removeBtn.css({ opacity: 1 });
				},
			})
			.disableSelection();
	},
};

Joomla.helixMegaMenu = megaMenu;
