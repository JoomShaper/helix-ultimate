var megaMenu = {
	run() {
		this.declareDOMVariables();
		this.initMiniColors();
		this.jQueryPluginExtension();
		this.handleMegaMenuToggle();
		this.toggleSidebarSettings($megamenu.prop('checked'));
		this.handleSidebarSettings();
		this.handleCloseModal();
		this.rowSortable();
		this.columnSortable();
		this.itemSortable();
		this.handleSaveMegaMenuSettings();
		this.handleRemoveRow();
		this.handleLoadSlots();
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
		$addNewRowBtn = $('.hu-megamenu-add-row > a');

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
		status ? $sidebarSettings.show() : $sidebarSettings.hide();
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

	updateSettings() {
		$settingsInput.val(JSON.stringify(settingsData));
	},

	updateSettingsField(key, value) {
		settingsData[key] = value;
	},

	handleRemoveRow() {
		$removeRowBtn.on('click', function (e) {
			e.preventDefault();
			let $parent = $(this).closest('.hu-row-container'),
				rowIndex = $parent.index();
			$parent.slideUp(300, function () {
				$(this).remove();
				settingsData.layout.splice(rowIndex, 1);
			});
		});
	},

	handleLoadSlots() {
		$addNewRowBtn.on('click', function () {
			$(this).parent().next('.hu-megamenu-row-slots').toggle();
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
				res.status && $(document).closeModal();
				res.status && Joomla.reloadPreview();
			},
			error(err) {
				alert('Something went wrong!');
			},
		});
	},

	/** Make row sortable. */
	rowSortable() {
		let self = this,
			prevIndex = null,
			currentIndex = null;
		$('.hu-megamenu-grid')
			.sortable({
				handle: '.hu-row-drag-handlers',
				placeholder: 'hu-row-sortable-placeholder',
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
			})
			.disableSelection();
	},

	/** Make column sortable. */
	columnSortable() {
		$('.hu-columns-container').sortable({
			handle: '.hu-column-drag-handler',
			placeholder: 'hu-column-sortable-placeholder',
			axis: 'x',
			items: '> *',
			start(_, ui) {
				let height = ui.helper.outerHeight(),
					width = ui.helper.outerWidth();

				ui.placeholder.css({ height, width });
			},
		});
	},

	/** Make column items sortable. */
	itemSortable() {
		$('.hu-column-contents')
			.sortable({
				connectWith: '.hu-column-contents',
				placeholder: 'hu-item-sortable-placeholder',
				items: '> .hu-megamenu-cell',
				start(_, ui) {
					let height = ui.helper.outerHeight(),
						width = ui.helper.outerWidth();

					ui.placeholder.css({ height, width });
				},
			})
			.disableSelection();
	},
};

Joomla.helixMegaMenu = megaMenu;
