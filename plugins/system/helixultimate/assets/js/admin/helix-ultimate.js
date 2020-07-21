/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

jQuery(function ($) {
	'use strict';

	var settings = Joomla.getOptions('data') || {};
	let meta = Joomla.getOptions('meta') || {};
	const storage = localStorage || window.localStorage;

	/**
	 * Positioning the toolbar of its previous location.
	 */
	const initialToolbarPosition = () => {
		let position = storage.getItem('toolbarPosition') || {};
		position =
			typeof position === 'string' && position.length > 0
				? JSON.parse(position)
				: false;

		let $huContainer = $('.hu-container'),
			$huSidebar = $('#hu-options-panel'),
			containerWidth = $huContainer.width(),
			sidebarWidth = $huSidebar.width(),
			gap = 20;

		if (position.left + sidebarWidth > containerWidth) {
			position.left = containerWidth - sidebarWidth - gap;
		} else if (position.left < 0) {
			position.left = gap;
		}

		if (position) {
			$('.hu-options-core').css({
				left: position.left + 'px',
				top: position.top + 'px',
			});
		}
		$('.hu-options-core').show();
	};

	/**
	 * Listen the resize event and re positioning the toolbar from
	 * storage position.
	 */
	initialToolbarPosition();
	window.addEventListener('resize', initialToolbarPosition);

	var MutationObserver =
		window.MutationObserver ||
		window.WebKitMutationObserver ||
		window.MozMutationObserver;

	let $previewFrame = document.getElementById('hu-template-preview');

	/**
	 * Reload the preview Iframe
	 */
	function reloadPreview() {
		$previewFrame.contentWindow.location.reload();
	}

	Joomla.reloadPreview = reloadPreview;

	$previewFrame.addEventListener('load', function () {
		Joomla.helixLoading(false);
		let iDocument = $previewFrame.contentWindow.document;
		let innerWrapper = iDocument.querySelector('.body-innerwrapper');

		/**
		 * Set a class into preview iframe body
		 * as we can distinguish the original preview
		 * and the backend iframe preview.
		 */
		iDocument.body.classList.add('back-panel');

		if (innerWrapper) {
			innerWrapper.style.marginTop = `${settings.topbarHeight}px`;
		}

		/**
		 * Use MutationObserver for observing the sticky header class change.
		 * If changes then add a margin top of `${settings.topbarHeight}px`
		 */
		let headerElLg = iDocument.querySelector(
			'body.sticky-header #sp-header'
		);
		let headerElMd = iDocument.querySelector(
			'body.sticky-header-md #sp-header'
		);
		let headerElSm = iDocument.querySelector(
			'body.sticky-header-sm #sp-header'
		);

		if (headerElLg || headerElMd || headerElSm) {
			let observer = new MutationObserver(function (mutations) {
				mutations.forEach(function (mutation) {
					if (mutation.target.classList.contains('header-sticky')) {
						mutation.target.style.marginTop = `${settings.topbarHeight}px`;
					} else {
						mutation.target.style.marginTop = null;
					}
				});
			});

			if (headerElLg) {
				observer.observe(headerElLg, {
					attributes: true,
					attributeOldValue: true,
				});
			}

			if (headerElMd) {
				observer.observe(headerElMd, {
					attributes: true,
					attributeOldValue: true,
				});
			}

			if (headerElSm) {
				observer.observe(headerElSm, {
					attributes: true,
					attributeOldValue: true,
				});
			}
		}
	});

	$('.reload-preview-iframe').on('click', function (e) {
		e.preventDefault();
		let that = this;

		reloadPreview();
		$(this).addClass('spin');

		$previewFrame.addEventListener('load', function () {
			$(that).removeClass('spin');
		});
	});

	(function initTooltips() {
		$('.hu-topbar').tooltip({
			classes: {
				'ui-tooltip': 'ui-corner-all',
			},
			position: {
				my: 'left top+8px',
			},
			hide: false,
			show: false,
		});

		$('.action-reset-drafts, .reload-preview-iframe').tooltip({
			classes: {
				'ui-tooltip': 'ui-corner-all',
			},
			position: {
				my: 'left top+10px',
			},
			hide: false,
			show: false,
		});
	})();

	Joomla.helixLoading = function (status) {
		const loader = document.querySelector('.reload-preview-iframe');

		if (loader) {
			if (status) {
				loader.classList.add('spin');
			} else {
				loader.classList.remove('spin');
			}
		}
	};

	function updateSetvalue() {
		let $controls = $('form#hu-style-form').find(
			'.controls.helix-input-touched'
		);
		if ($controls.length > 0) {
			$controls.each(function (index, control) {
				let $controlEl = $(control);

				if ($controlEl.length > 0) {
					let inputSelector = $controlEl.data('selector');
					let $inputEl = $controlEl.find(inputSelector);

					if ($inputEl.length > 0) {
						$inputEl.attr('value', $inputEl.val());
					}

					// Reset by the setvalue.
					$controlEl.attr('data-setvalue', $inputEl.val());
					$controlEl.data('setvalue', $inputEl.val());
					$controlEl.removeClass('helix-input-touched');
				}
			});
		}
	}

	/**
	 * Reset field values by the last saved value.
	 * This is done after discarding the draft.
	 *
	 * @return void
	 */
	function resetBySafepointValue() {
		let $controls = $('form#hu-style-form').find(
			'.controls.helix-input-touched.field-reset'
		);

		if ($controls.length > 0) {
			$controls.each(function (index, control) {
				let $controlEl = $(control);

				if ($controlEl.length > 0) {
					let safepoint = $controlEl.data('safepoint');
					let inputSelector = $controlEl.data('selector');
					let $inputEl = $controlEl.find(inputSelector);

					// Reset by the setvalue.
					if ($inputEl.length > 0) {
						let type =
							typeof $inputEl.attr('type') != 'undefined'
								? $inputEl.attr('type').toLowerCase()
								: false;
						if (type && type === 'checkbox') {
							let checked = safepoint == 1 ? true : false;
							$inputEl.prop('checked', checked);
						}

						$inputEl.val(safepoint);
						$inputEl.attr('value', safepoint);

						// Fire change event for applying showon behavior
						$inputEl.change();

						// Update currPoint value
						$controlEl.attr('data-currpoint', safepoint);
						$controlEl.data('currpoint', safepoint);

						// Update chosen select values.
						if (
							$inputEl.prop('tagName').toLowerCase() ===
								'select' &&
							$controlEl.find(inputSelector + '_chzn').length > 0
						) {
							$inputEl.trigger('liszt:updated');
							$inputEl.trigger('chosen:updated');
						}
					}

					// Reset media preview image.
					let $imagePreview = $controlEl.find('.hu-image-holder img');

					if ($imagePreview.length > 0) {
						$imagePreview.attr('src', `${meta.base}/${safepoint}`);
					}

					// Reset the predefined header
					let $predefinedHeader = $controlEl.find('.hu-header-item');

					$predefinedHeader.each(function () {
						if ($(this).hasClass('active')) {
							$(this).removeClass('active');
						}

						if ($(this).data('style') === safepoint) {
							$(this).addClass('active');
						}
					});

					$controlEl.removeClass('helix-input-touched');
					$controlEl.removeClass('field-reset');
				}
			});
		}
	}

	function draftChanges() {
		$('#layout').val(JSON.stringify(getGeneratedLayout()));
		webfontData();

		$('.hu-input-preset').val(
			JSON.stringify($('.hu-preset.active').data())
		);

		let data = $('#hu-style-form')
			.find(':not(.hu-preset-container input)')
			.serializeArray();

		$.ajax({
			type: 'POST',
			url:
				'index.php?option=com_ajax&request=task&helix=ultimate&id=' +
				helixUltimateStyleId +
				'&action=draft-tmpl-style&format=json',
			data: data,
			beforeSend: function () {
				Joomla.helixLoading(true);
			},
			success: function (response) {
				var data = $.parseJSON(response);

				if (data.status) {
					let $previewFrame = document.getElementById(
						'hu-template-preview'
					);

					reloadPreview();

					$previewFrame.addEventListener('load', function () {
						Joomla.helixLoading(false);
					});
				}

				if (data.isDrafted) {
					let $resetDraft = $('.action-reset-drafts');
					if ($resetDraft.hasClass('hide')) {
						$resetDraft.removeClass('hide');
					}
				} else {
					let $resetDraft = $('.action-reset-drafts');
					if (!$resetDraft.hasClass('hide')) {
						$resetDraft.addClass('hide');
					}
				}
			},
			error: function (err) {
				console.error('error', err);
			},
		});
	}

	/**
	 * Track the input fields (input, select, textarea, checkbox, hidden, media ...) * are changed.
	 * This function is responsible for handling onClick/onChange/onBlur
	 * live changes preview
	 *
	 */
	(function trackChanges() {
		$('form#hu-style-form')
			.find(
				'input[type="text"], input[type="email"], input[type="number"], textarea'
			)
			.on('blur', function (e) {
				e.preventDefault();

				let $control = $(this).closest('.controls');
				if (
					!$control.hasClass('field-reset') &&
					$control.hasClass('trackable')
				) {
					let safePoint = $(this)
						.closest('.controls')
						.data('safepoint');
					let currPoint = $(this)
						.closest('.controls')
						.data('currpoint');
					let value = $(this).val();

					triggerDraftChange($(this), safePoint, currPoint, value);
				}
			});

		$('form#hu-style-form')
			.find('input[type="checkbox"], input[type=color]')
			.on('change', function (e) {
				e.preventDefault();

				let $control = $(this).closest('.controls');
				if (
					!$control.hasClass('field-reset') &&
					$control.hasClass('trackable')
				) {
					let safePoint = $(this)
						.closest('.controls')
						.data('safepoint');
					let currPoint = $(this)
						.closest('.controls')
						.data('currpoint');
					let value = $(this).prop('checked') ? 1 : 0;

					triggerDraftChange($(this), safePoint, currPoint, value);
				}
			});

		$('form#hu-style-form')
			.find('select, input[type="hidden"]')
			.on('change', function (e) {
				e.preventDefault();

				let $control = $(this).closest('.controls');
				if (
					!$control.hasClass('field-reset') &&
					$control.hasClass('trackable')
				) {
					let safePoint = $(this)
						.closest('.controls')
						.data('safepoint');
					let currPoint = $(this)
						.closest('.controls')
						.data('currpoint');
					let value = $(this).val();

					triggerDraftChange($(this), safePoint, currPoint, value);
				}
			});
	})();

	function prepareResetFields() {
		let $controls = $('form#hu-style-form').find(
			'.controls.helix-input-touched'
		);

		if ($controls.length > 0) {
			$controls.each((i, el) => {
				if (!$(el).hasClass('field-reset')) {
					$(el).addClass('field-reset');
				}
			});
		}
	}

	// Reset drafted settings
	$('.action-reset-drafts').on('click', function (e) {
		e.preventDefault();
		let self = this;

		if ($(this).hasClass('hide')) {
			return;
		}

		prepareResetFields();

		let confirm = window.confirm(
			'Do you really want to reset your settings?'
		);

		if (confirm) {
			$('#layout').val(JSON.stringify(getGeneratedLayout()));
			webfontData();

			$('.hu-input-preset').val(
				JSON.stringify($('.hu-preset.active').data())
			);

			$.ajax({
				type: 'GET',
				url:
					'index.php?option=com_ajax&request=task&helix=ultimate&id=' +
					helixUltimateStyleId +
					'&action=reset-drafted-settings&format=json',
				beforeSend: function () {
					Joomla.helixLoading(true);
				},
				success: function (response) {
					var data = $.parseJSON(response);

					if (data.status) {
						let $previewFrame = document.getElementById(
							'hu-template-preview'
						);

						reloadPreview();

						$previewFrame.addEventListener('load', function () {
							Joomla.helixLoading(false);
						});
					}

					if (!data.isDrafted) {
						$(self).addClass('hide');
					}
				},
				error: function (err) {
					console.error('error', err);
				},
				complete: function () {
					resetBySafepointValue();
				},
			});
		}
	});

	// Save settings
	$('.action-save-template').on('click', function (e) {
		e.preventDefault();
		var self = this;

		$('#layout').val(JSON.stringify(getGeneratedLayout()));
		webfontData();

		$('.hu-input-preset').val(
			JSON.stringify($('.hu-preset.active').data())
		);

		var tmplID = $(this).data('id'),
			tmplView = $(this).data('view'),
			data = $('#hu-style-form')
				.find(':not(.hu-preset-container input)')
				.serialize();

		$.ajax({
			type: 'POST',
			url:
				'index.php?option=com_ajax&request=task&helix=ultimate&id=' +
				helixUltimateStyleId +
				'&action=save-tmpl-style&format=json',
			data: data,
			beforeSend: function () {
				Joomla.helixLoading(true);
			},
			success: function (response) {
				var data = $.parseJSON(response);

				if (data.status) {
					let $previewFrame = document.getElementById(
						'hu-template-preview'
					);
					$previewFrame.contentWindow.location.reload(true);
					$previewFrame.addEventListener('load', function () {
						Joomla.helixLoading(false);
					});
				}

				if (data.isDrafted) {
					let $resetDraft = $('.action-reset-drafts');
					if ($resetDraft.hasClass('hide')) {
						$resetDraft.removeClass('hide');
					}
				} else {
					let $resetDraft = $('.action-reset-drafts');
					if (!$resetDraft.hasClass('hide')) {
						$resetDraft.addClass('hide');
					}
				}

				// Update the setvalues.
				updateSetvalue();
			},
			error: function (err) {
				console.error('error', err);
			},
		});
	});

	/**
	 * Trigger draft changing based on how the data is changed.
	 * If the input value is equal to the currentPoint then no change has done, so no drafting.
	 * If the input value is not equal to the currentPoint then change happens, draft the change.
	 * Update the current value to the newly changed value. Mark the control that, it's been touched.
	 * If any changed value is equal to the safePoint value then remove the touched flag.
	 *
	 * @param	HTMLElement	$el			The input element.
	 * @param	mixed		safePoint	The value of last saved value of the input.
	 * @param	mixed		currPoint	The value of the input which has just been changed before draft.
	 * @param	mixed		value		The changing value.
	 *
	 * @return	void
	 */
	function triggerDraftChange($el, safePoint, currPoint, value) {
		if (value != currPoint) {
			currPoint = value;
			$el.closest('.controls').attr('data-currpoint', currPoint);
			$el.closest('.controls').data('currpoint', currPoint);

			if (!$el.closest('.controls').hasClass('helix-input-touched')) {
				$el.closest('.controls').addClass('helix-input-touched');
			}
			draftChanges();
		}

		if (value == safePoint) {
			if ($el.closest('.controls').hasClass('helix-input-touched')) {
				$el.closest('.controls').removeClass('helix-input-touched');
			}
		}
	}

	// Device switching from `desktop -> tablet -> mobile` or vice versa.
	$('.hu-device').on('click', function (e) {
		e.preventDefault();
		const device = $(this).data('device');

		$(this).parent().find('.active').removeClass('active');
		$(this).addClass('active');

		switchBetweenDevices(device);
	});

	/**
	 * Function to switch between various devices
	 *
	 * @param {string} device 	Device type i.e. `desktop`, `tablet`, `mobile`
	 */
	function switchBetweenDevices(device) {
		const widthMap = {
			desktop: '100%',
			tablet: `${settings.breakpoints.tablet}px`,
			mobile: `${settings.breakpoints.mobile}px`,
			md: '100%',
			sm: `${settings.breakpoints.tablet}px`,
			xs: `${settings.breakpoints.mobile}px`,
		};

		const deviceMap = {
			md: 'desktop',
			sm: 'tablet',
			xs: 'mobile',
			desktop: 'desktop',
			tablet: 'tablet',
			mobile: 'mobile',
		};

		const map = {
			desktop: 'md',
			tablet: 'sm',
			mobile: 'xs',
		};

		const $iframe = $('#hu-template-preview');

		$(`.hu-device[data-device=${deviceMap[device]}]`)
			.parent()
			.find('.active')
			.removeClass('active');
		$(`.hu-device[data-device=${deviceMap[device]}]`).addClass('active');

		// Change the typography field device wise size field
		$('input[class^=hu-webfont-size-input]').each(function () {
			if ($(this).hasClass('active')) {
				$(this).removeClass('active');
			}
		});

		$(
			`input.hu-webfont-size-input${
				map[device] === 'md' ? '' : '-' + map[device]
			}`
		).addClass('active');

		$iframe.animate(
			{
				width: widthMap[device],
			},
			500,
			'linear'
		);
	}

	// Switcher
	$('#hu-style-form')
		.find('input[type="checkbox"]')
		.each(function () {
			var $this = $(this);
			$this.closest('.control-group').addClass('control-group-checkbox');
		});

	/**
	 * Draggable sidebar
	 */
	$('.hu-options-core').draggable({
		iframeFix: true,
		cursor: 'grabbing',
		handle: '.hu-panel-handle',
		containment: '#helix-ultimate',
		drag: function (event, ui) {
			storage.setItem('toolbarPosition', JSON.stringify(ui.position));
			panelPositioning();
		},
	});

	/**
	 * Calculate the editor panel position and display the panel
	 */
	function panelPositioning() {
		let $optionsCore = $('.hu-options-core');
		let $panel = $('.hu-edit-panel.active-panel');
		let $sidebar = $('#hu-options-panel');
		let $container = $('.hu-container');

		let sidebarOffset = $sidebar.offset();
		let sidebarWidth = $sidebar.width();
		let panelWidth = $panel.width();
		let containerWidth = $container.width();
		let gap = 10;

		let panelHorizontalPosition = sidebarOffset.left + sidebarWidth + gap;

		if (panelHorizontalPosition + panelWidth > containerWidth) {
			if ($optionsCore.hasClass('hu-panel-position-right')) {
				$optionsCore.removeClass('hu-panel-position-right');
			}

			$optionsCore.addClass('hu-panel-position-left');
		} else {
			if ($optionsCore.hasClass('hu-panel-position-left')) {
				$optionsCore.removeClass('hu-panel-position-left');
			}

			$optionsCore.addClass('hu-panel-position-right');
		}
	}

	/**
	 * Display editor panel on click sidebar icon
	 *
	 */
	$('.hu-fieldset-header').on('click', function (e) {
		e.preventDefault();

		let fieldset = $(this).data('fieldset');

		if ($('.' + fieldset + '-panel').hasClass('active-panel')) {
			$('.' + fieldset + '-panel').removeClass('active-panel');
			$(this).removeClass('active');
			return;
		}

		$('.' + fieldset + '-panel')
			.parent()
			.find('.active-panel')
			.removeClass('active-panel');
		$('.' + fieldset + '-panel').addClass('active-panel');

		// Make active sidebar icon
		if (
			$(this)
				.parents('#hu-options')
				.find('.hu-fieldset .hu-fieldset-header')
				.hasClass('active')
		) {
			$(this)
				.parents('#hu-options')
				.find('.hu-fieldset .hu-fieldset-header')
				.removeClass('active');
		}

		$(this).addClass('active');

		panelPositioning();
	});

	/**
	 * Close an opened panel
	 */
	$('.hu-panel-close').on('click', function (e) {
		e.preventDefault();

		if ($(this).closest('.hu-edit-panel').hasClass('active-panel')) {
			$(this).closest('.hu-edit-panel').removeClass('active-panel');
		}

		let $sidebarItem = $(
			`.${$(this).data('sidebarclass')} .hu-fieldset-header`
		);

		if ($sidebarItem.hasClass('active')) {
			$sidebarItem.removeClass('active');
		}
	});

	$('.hu-fieldset-toggle-icon').on('click', function (e) {
		e.preventDefault();

		$('.hu-fieldset').removeClass('active');
		$('#hu, #hu-options').removeClass();
	});

	$('.hu-group-header-box').on('click', function (e) {
		e.preventDefault();

		let $prevActiveEl = $(this)
			.closest('.hu-edit-panel')
			.find('.hu-group-wrap')
			.find('.hu-field-list.active-group');

		if ($prevActiveEl.length > 0) {
			let prevUid = $prevActiveEl.data('uid');
			let currUid = $(this).next().data('uid');

			if (prevUid !== currUid) {
				$prevActiveEl.removeClass('active-group');
				$prevActiveEl.parent().removeClass('active');
				$prevActiveEl.slideUp(400);
			}
		}

		let $fieldList = $(this).next();

		if ($fieldList.hasClass('active-group')) {
			$(this).parent().removeClass('active');
			$fieldList.removeClass('active-group');
			$fieldList.slideUp(400);
		} else {
			$fieldList.addClass('active-group');
			$(this).parent().addClass('active');
			$fieldList.slideDown(400);
		}
	});

	$('.hu-header-item').on('click', function (e) {
		e.preventDefault();

		var $parent = $(this).closest('.hu-header-list');

		$parent.find('.hu-header-item').removeClass('active');
		$(this).addClass('active');

		var styleName = $(this).data('style'),
			filedName = $parent.data('name');

		$('#' + filedName)
			.val(styleName)
			.trigger('change');
	});

	// Preset
	$(document).ready(function () {
		if ($('#custom_style').attr('checked') == 'checked') {
			$('.hu-fieldset-presets').find('.hu-group-wrap').show();
		} else {
			$('.hu-fieldset-presets').find('.hu-group-wrap').hide();
		}
	});

	$(document).on('change', '#custom_style', function (e) {
		e.preventDefault();
		if ($(this).attr('checked') == 'checked') {
			$('.hu-fieldset-presets').find('.hu-group-wrap').slideDown();
		} else {
			$('.hu-fieldset-presets').find('.hu-group-wrap').slideUp();
		}
	});

	$(document).on('click', '.hu-preset', function (e) {
		e.preventDefault();

		$('.hu-preset').removeClass('active');
		$(this).addClass('active');

		draftChanges();
	});

	$('.helix-responsive-devices span').click(function () {
		if ($(this).hasClass('active')) return;
		const parent = $(this).parents('.hu-webfont-size');
		parent.find('input').removeClass('active');
		const inputClass = $(this).data('active_class');
		parent.find(inputClass).addClass('active');
		$(this).parent().find('span.active').removeClass('active');
		$(this).addClass('active');
		const device = $(this).data('device');

		switchBetweenDevices(device);
	});

	window.purgeCss = function (self = null) {
		$.ajax({
			type: 'POST',
			url:
				'index.php?option=com_ajax&request=task&helix=ultimate&id=' +
				helixUltimateStyleId +
				'&action=purge-css-file&format=json',
			data: {},
			beforeSend: function () {
				self &&
					self.append(
						'<span class="fas fa-circle-notch fa-spin"></span>'
					);
			},
			success: function (response) {
				var data = $.parseJSON(response);
				if (self && data.status) {
					self.find('span').remove();
					self.removeClass('disable');
				}
			},
			error: function () {
				alert('Somethings wrong, Try again');
			},
		});
	};

	$('.btn-purge-hu-css').on('click', function (e) {
		e.preventDefault();
		var self = $(this);
		if (self.hasClass('disable')) {
			return;
		}
		self.addClass('disable');

		window.purgeCss(self);
	});

	// Import
	$('#btn-hu-import-settings').on('click', function (event) {
		event.preventDefault();

		var $that = $(this),
			temp_settings = $.trim($('#input-hu-settings').val());

		if (temp_settings == '') {
			return false;
		}

		if (
			confirm(
				'Warning: It will change all current settings of this Template.'
			) != true
		) {
			return false;
		}

		var data = {
			settings: temp_settings,
		};

		var request = {
			action: 'import-tmpl-style',
			option: 'com_ajax',
			helix: 'ultimate',
			request: 'task',
			data: data,
			format: 'json',
		};

		$.ajax({
			type: 'POST',
			data: request,
			success: function (response) {
				var data = $.parseJSON(response);
				if (data.status) {
					window.location.reload();
				}
			},
			error: function () {
				alert('Somethings wrong, Try again');
			},
		});
		return false;
	});

	function webfontData() {
		$('.hu-field-webfont').each(function () {
			var $that = $(this),
				webfont = {
					fontFamily: $that.find('.hu-webfont-list').val(),
					fontSize: $that.find('.hu-webfont-size-input').val(),
					fontSize_sm: $that.find('.hu-webfont-size-input-sm').val(),
					fontSize_xs: $that.find('.hu-webfont-size-input-xs').val(),
					fontWeight: $that.find('.hu-webfont-weight-list').val(),
					fontStyle: $that.find('.hu-webfont-style-list').val(),
					fontSubset: $that.find('.hu-webfont-subset-list').val(),
					fontColor: $that.find('.hu-font-color-input').val(),
					fontLineHeight: $that
						.find('.hu-font-line-height-input')
						.val(),
					fontLetterSpacing: $that
						.find('.hu-font-letter-spacing-input')
						.val(),
					textDecoration: $that.find('.hu-text-decoration').val(),
					textAlign: $that.find('.hu-text-align').val(),
				};

			$that.find('.hu-webfont-input').val(JSON.stringify(webfont));
		});
	}

	function getGeneratedLayout() {
		var item = [];
		$('#hu-layout-builder')
			.find('.hu-layout-section')
			.each(function (index) {
				var $row = $(this),
					rowIndex = index,
					rowObj = $row.data();
				delete rowObj.sortableItem;

				var activeLayout = $row.find('.hu-column-layout.active'),
					layoutArray = activeLayout.data('layout'),
					layout = 12;

				if (layoutArray != 12) {
					layout = layoutArray.split(',').join('');
				}

				item[rowIndex] = {
					type: 'row',
					layout: layout,
					settings: rowObj,
					attr: [],
				};

				// Find Column Elements
				$row.find('.hu-layout-column').each(function (index) {
					var $column = $(this),
						colIndex = index,
						colObj = $column.data();
					delete colObj.sortableItem;

					item[rowIndex].attr[colIndex] = {
						type: 'sp_col',
						settings: colObj,
					};
				});
			});

		return item;
	}

	/* Helix Help Control functionalities */
	$('.hu-help-icon').on('click', function (e) {
		e.preventDefault();
		let $helpElement = $(this)
			.closest('.control-group')
			.find('.control-help');

		$(this).toggleClass('active');

		if ($helpElement.hasClass('show')) {
			$helpElement.removeClass('show');
			$helpElement.slideUp(300);
		} else {
			$helpElement.addClass('show');
			$helpElement.slideDown(300);
		}

		let $siblings = $(this).closest('.control-group').siblings();

		$siblings.each(function () {
			let $help = $(this).find('.control-help');

			if ($help.hasClass('show')) {
				$help.removeClass('show');
				$help.slideUp(300);
			}
		});
	});

	/*Option Group*/
	$(document).on('click', '.hu-option-group-title', function (event) {
		event.preventDefault();
		$(this)
			.closest('.hu-option-group')
			.toggleClass('active')
			.siblings()
			.removeClass('active');
	});

	/* Helix Group Depend On functionalities */
	let $togglers = {};

	function handleDependOnRelationship() {
		let $groups = $('.hu-group-wrap');
		$groups.each(function () {
			if ($(this).attr('data-dependon')) {
				let depend = $(this).data('dependon');
				let [name, value] = depend.split(':');
				let $parentElement = $(`[name=${name}]`);
				let parentValue = $parentElement.val();

				if ($parentElement.prop('type') === 'checkbox') {
					parentValue = $parentElement.prop('checked');
					value = value == 1;
				}

				if (parentValue == value) {
					$(this).fadeIn(300);
				} else {
					$(this).fadeOut(300);
				}

				$togglers[name] = $parentElement;
			}
		});
	}

	handleDependOnRelationship();

	Object.values($togglers).forEach(function ($toggler) {
		$toggler.on('change', function (e) {
			e.preventDefault();
			handleDependOnRelationship();
		});
	});

	/* Helix dimension Field */
	(function handleDimensionData() {
		let $width = $('.hu-field-dimension-width');
		let $height = $('.hu-field-dimension-height');
		let $input = $('.hu-field-dimension-input');

		$width.on('keyup', function (e) {
			e.preventDefault();
			let fieldValue = $input.val() || '0x0';
			let value = $(this).val();
			let [width, height] = fieldValue.toLowerCase().split('x');

			if (value === '') {
				value = '0';
			}

			width = value;
			fieldValue = `${width}x${height}`;
			$input.val(fieldValue);
		});

		$height.on('keyup', function (e) {
			e.preventDefault();
			let fieldValue = $input.val() || '0x0';
			let value = $(this).val();
			let [width, height] = fieldValue.toLowerCase().split('x');

			if (value === '') {
				value = '0';
			}

			height = value;
			fieldValue = `${width}x${height}`;
			$input.val(fieldValue);
		});
	})();

	/** Handle enable on  */
	let $enableOnParentElements = [];

	function handleEnableOn() {
		let $childElement = $('.control-group[data-enableon]');

		$childElement.each(function () {
			let [name, value] = $(this).data('enableon').split(':');

			let $parentElement = $(`[name=${name}]`);
			$enableOnParentElements.push($parentElement);
			let parentValue = $parentElement.val();

			if ($parentElement.prop('type') === 'checkbox') {
				parentValue = $parentElement.prop('checked');
				value = value == 1;
			}

			if (parentValue == value) {
				$(this).find('input, select, textarea').prop('readonly', false);
				if ($(this).hasClass('uneditable'))
					$(this).removeClass('uneditable');
			} else {
				$(this).find('input, select, textarea').prop('readonly', true);
				if (!$(this).hasClass('uneditable'))
					$(this).addClass('uneditable');
			}
		});
	}

	handleEnableOn();

	$enableOnParentElements.forEach(function ($element) {
		$element.on('change', function () {
			handleEnableOn();
		});
	});

	/* Switcher action */
	$('.hu-switcher .hu-action-group [hu-switcher-action]').on(
		'click',
		function (e) {
			let value = $(this).data('value');
			$(this).siblings().removeClass('active');
			$(this).addClass('active');
			let $input = $(this)
				.closest('.hu-switcher')
				.find('input[type=hidden]');
			$input.val(value).trigger('change');
		}
	);
});
