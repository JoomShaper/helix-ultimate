var megaMenu = {
	run() {
		this.declareDOMVariables();
		this.jQueryPluginExtension();
		this.handleMegaMenuToggle();
		this.toggleSidebarSettings($megamenu.prop('checked'));
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
};

Joomla.helixMegaMenu = megaMenu;
