var megaMenu = {
	run() {
		this.declareDOMVariables();
		this.initMiniColors();
		this.jQueryPluginExtension();

		this.removeEventListeners();

		this.handleMegaMenuToggle();
		this.toggleSidebarSettings($megamenu.prop('checked'));
		this.handleSidebarSettings();
		this.handleCloseModal();

		this.rowSortable('.hu-megamenu-rows-container');
		this.columnSortable('.hu-columns-container');
		this.itemSortable('.hu-column-contents');

		this.handleSaveMegaMenuSettings();

		this.handleRemoveRow();
		this.handleLoadSlots();
		this.handleCustomLayoutDisplay();

		this.handleLayoutOptionSelection();
		this.handleCustomLayoutSelection();

		this.toggleColumnsSlots();
		console.log(settingsData);
	},

	jQueryPluginExtension() {
		$.fn.extend({
			test() {
				return this.css('color', '#fff');
			},
		});
	},

	declareDOMVariables() {
		$megamenu = $('.hu-megamenu-builder-megamenu');
		$sidebarSettings = $('.hu-mega-menu-settings');
		$settingsInput = $('#hu-megamenu-layout-settings');
		$saveBtn = $('.hu-megamenu-save-btn');
		$cancelBtn = $('.hu-megamenu-cancel-btn');
		$removeRowBtn = $('.hu-megamenu-remove-row');
		$rowsContainer = $('.hu-megamenu-rows-container');
		$addNewRowBtn = $('.hu-megamenu-add-row > a');
		$customLayoutBtn = $('.hu-megamenu-custom');
		$customLayoutContainer = $('.hu-custom-layout');
		$layoutItem = $('.hu-megamenu-column-layout');

		itemId = $('#hu-menu-itemid').val();
		settingsData = $settingsInput.val();
		settingsData = settingsData && JSON.parse(settingsData);
		baseUrl = $('#hu-base-url').val();
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

		if (status) {
			if (!$settings.hasClass('show')) $settings.addClass('show');
			if (!$grid.hasClass('show')) $grid.addClass('show');
		} else {
			if ($settings.hasClass('show')) $settings.removeClass('show');
			if ($grid.hasClass('show')) $grid.removeClass('show');
		}
	},

	/** Handling custom layout panel toggling */
	handleCustomLayoutDisplay() {
		$(document).on('click', '.hu-megamenu-custom', function () {
			$customLayoutContainer.slideToggle(100);
		});
	},

	closeLayoutDisplay() {
		$('.hu-megamenu-row-slots').hide();
	},

	/** Remove all the event listeners */
	removeEventListeners() {
		$(document).off('click', '.hu-custom-layout-apply');
		$(document).off('click', '.hu-megamenu-remove-row');
		$(document).off('click', '.hu-megamenu-add-row > a');
		$(document).off('click', '.hu-megamenu-custom');
		$(document).off(
			'click',
			'.hu-megamenu-column-layout:not(.hu-megamenu-custom)'
		);
		$cancelBtn.off('click');
		$saveBtn.off('click');
		$megamenu.off('change');
	},

	/** Handling layout option selection. */
	handleLayoutOptionSelection() {
		let self = this;
		$(document).on(
			'click',
			'.hu-megamenu-column-layout:not(.hu-megamenu-custom)',
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

					console.log(settingsData);
				}
			}
		);
	},

	handleCustomLayoutSelection() {
		let self = this;

		$(document).on('click', '.hu-custom-layout-apply', async function (e) {
			e.preventDefault();
			let $field = $('.hu-custom-layout-field'),
				layout = $field.val(),
				rowId = settingsData.layout.length + 1;

			if (layout == '') return;

			const res = await self.generateRow(layout, rowId, itemId);

			if (res.status) {
				$rowsContainer.append(res.data);
				self.closeLayoutDisplay();
				settingsData.layout.push(response.row);
				self.refreshSortable();
			}
		});
	},

	toggleColumnsSlots() {
		$(document).on('click', '.hu-megamenu-columns', function () {
			$(this)
				.closest('.hu-row-toolbar-right')
				.find('.hu-megamenu-slots')
				.toggleClass('show');
		});
	},

	/**
	 * Generating the row
	 */
	generateRow(layout, rowId, itemId) {
		let self = this;
		return new Promise((resolve, reject) => {
			const url = `${baseUrl}/administrator/index.php?option=com_ajax&helix=ultimate&request=task&action=generateRow`;
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
					res =
						typeof res === 'string' && res.length
							? JSON.parse(res)
							: false;
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
		$('.hu-megamenu-sidebar')
			.find('input, select')
			.each(function () {
				$(this).on('change', function (e) {
					e.preventDefault();
					let { name, value } = e.target;
					const type = $(this).attr('type');

					value =
						type === 'checkbox'
							? ($(this).prop('checked') >> 0).toString()
							: value;
					self.updateSettingsField(name, value);
					console.log(settingsData);
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
		console.log(settingsData);
	},

	/** Swap the item */
	swapItem({
		prevRowIndex,
		prevColIndex,
		prevItemIndex,
		currRowIndex,
		currColIndex,
		currItemIndex,
	}) {
		let items = [
				...settingsData.layout[prevRowIndex].attr[prevColIndex].items,
			],
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

			let $parent = $(this).closest('.hu-row-wrapper'),
				rowIndex = $parent.index();

			$parent.slideUp(300, function () {
				$(this).remove();
				settingsData.layout.splice(rowIndex, 1);
			});
		});
	},

	handleLoadSlots() {
		$(document).on('click', '.hu-megamenu-add-row > a', function () {
			$(this)
				.closest('.hu-megamenu-grid')
				.find('.hu-megamenu-row-slots')
				.toggle();
		});
	},

	/** @TODO: may be not required. */
	loadSlots() {
		const url = `${baseUrl}/administrator/index.php?option=com_ajax&helix=ultimate&request=task&action=loadSlots`;

		return new Promise((resolve, reject) => {
			$.ajax({
				method: 'GET',
				url,
				success(res) {
					res =
						typeof res === 'string' && res.length > 0
							? JSON.parse(res)
							: false;
					resolve(res);
				},
				error(err) {
					reject(err);
				},
			});
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
		const url = `${baseUrl}/administrator/index.php?option=com_ajax&helix=ultimate&request=task&action=saveMegaMenuSettings`;
		const data = {
			settings: settingsData,
			id: itemId,
		};

		$.ajax({
			method: 'POST',
			url,
			data,
			success(res) {
				res =
					typeof res === 'string' && res.length > 0
						? JSON.parse(res)
						: false;
				res.status && Joomla.reloadPreview();
			},
			error(err) {
				alert('Something went wrong!');
			},
			complete() {
				$(document).closeModal();
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
				selector: '.hu-columns-container',
				func: 'columnSortable',
			},
			item: { selector: '.hu-column-contents', func: 'itemSortable' },
		};

		for (let i = 0; i < sortable.length; i++) {
			if (sortableMap[sortable[i]] !== undefined) {
				let item = sortableMap[sortable[i]];
				this[item.func](item.selector);
			}
		}
	},

	updateRows() {
		$('.hu-row-wrapper').each(function (index) {
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
				handle: '.hu-row-drag-handlers',
				placeholder: 'hu-row-sortable-placeholder',
				containment: '.hu-megamenu-grid',
				axis: 'y',
				items: '> *',
				start(_, ui) {
					let height = ui.helper.outerHeight();
					height -= 2;
					ui.placeholder.css({ height });
					prevIndex = ui.item.index();
				},
				stop(_, ui) {
					currentIndex = ui.item.index();
					self.swapRow(prevIndex, currentIndex);
				},
				deactivate(_, ui) {
					self.updateRows();
				},
			})
			.disableSelection();
	},

	updateColumns(rowIndex) {
		$(`.hu-row-wrapper[data-rowid=${rowIndex + 1}]`)
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
			self = this;

		$(selector).sortable({
			handle: '.hu-column-drag-handler',
			placeholder: 'hu-column-sortable-placeholder',
			containment: '.hu-megamenu-grid',
			axis: 'x',
			items: '> *',
			start(_, ui) {
				let height = ui.helper.outerHeight(),
					width = ui.helper.outerWidth();

				ui.placeholder.css({ height, width });

				rowIndex = ui.item.closest('.hu-row-wrapper').data('rowid') - 1;
				prevIndex = ui.item.index();
			},
			stop(_, ui) {
				currIndex = ui.item.index();
				self.swapColumn(rowIndex, prevIndex, currIndex);
				self.updateColumns(rowIndex);
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
			self = this;
		$(selector)
			.sortable({
				connectWith: '.hu-column-contents',
				placeholder: 'hu-item-sortable-placeholder',
				containment: '.hu-megamenu-grid',
				items: '> .hu-megamenu-cell',
				start(_, ui) {
					let height = ui.helper.outerHeight(),
						width = ui.helper.outerWidth();

					ui.placeholder.css({ height, width });

					prevColIndex =
						(ui.item.closest('.hu-megamenu-col').data('columnid') ||
							1) - 1;
					prevRowIndex =
						(ui.item.closest('.hu-row-wrapper').data('rowid') ||
							1) - 1;
					prevItemIndex = ui.item.index();
				},
				stop(_, ui) {
					currColIndex =
						(ui.item.closest('.hu-megamenu-col').data('columnid') ||
							1) - 1;
					currRowIndex =
						(ui.item.closest('.hu-row-wrapper').data('rowid') ||
							1) - 1;
					currItemIndex = ui.item.index();
					self.swapItem({
						prevRowIndex,
						prevColIndex,
						prevItemIndex,
						currRowIndex,
						currColIndex,
						currItemIndex,
					});
				},
			})
			.disableSelection();
	},
};

Joomla.helixMegaMenu = megaMenu;
