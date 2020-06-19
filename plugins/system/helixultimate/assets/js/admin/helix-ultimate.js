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

	var MutationObserver =
		window.MutationObserver ||
		window.WebKitMutationObserver ||
		window.MozMutationObserver;

	let $previewFrame = document.getElementById(
		'helix-ultimate-template-preview'
	);

	/**
	 * Reload the preview Iframe
	 */
	function reloadPreview() {
		$previewFrame.contentWindow.location.reload();
	}

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
		$('.helix-ultimate-topbar').tooltip({
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
		let $controls = $('form#helix-ultimate-style-form').find(
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
		let $controls = $('form#helix-ultimate-style-form').find(
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

						// Fire change event for appling showon behavior
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
					let $imagePreview = $controlEl.find(
						'.helix-ultimate-image-holder img'
					);

					if ($imagePreview.length > 0) {
						$imagePreview.attr('src', `${meta.base}/${safepoint}`);
					}

					$controlEl.removeClass('helix-input-touched');
					$controlEl.removeClass('field-reset');
				}
			});
		}
	}

	function draftChanges() {
		$('#layout').val(JSON.stringify(getGeneratedLayout()));
		webfontData();

		$('.helix-ultimate-input-preset').val(
			JSON.stringify($('.helix-ultimate-preset.active').data())
		);

		let data = $('#helix-ultimate-style-form')
			.find(':not(.helix-ultimate-preset-container input)')
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
						'helix-ultimate-template-preview'
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
		$('form#helix-ultimate-style-form')
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

		$('form#helix-ultimate-style-form')
			.find('input[type="checkbox"]')
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

		$('form#helix-ultimate-style-form')
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
		let $controls = $('form#helix-ultimate-style-form').find(
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

			$('.helix-ultimate-input-preset').val(
				JSON.stringify($('.helix-ultimate-preset.active').data())
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
							'helix-ultimate-template-preview'
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

		$('.helix-ultimate-input-preset').val(
			JSON.stringify($('.helix-ultimate-preset.active').data())
		);

		var tmplID = $(this).data('id'),
			tmplView = $(this).data('view'),
			data = $('#helix-ultimate-style-form')
				.find(':not(.helix-ultimate-preset-container input)')
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
						'helix-ultimate-template-preview'
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

		const $iframe = $('#helix-ultimate-template-preview');

		$(`.hu-device[data-device=${deviceMap[device]}]`)
			.parent()
			.find('.active')
			.removeClass('active');
		$(`.hu-device[data-device=${deviceMap[device]}]`).addClass('active');

		$iframe.animate(
			{
				width: widthMap[device],
			},
			500,
			'linear'
		);
	}

	// Switcher
	$('#helix-ultimate-style-form')
		.find('input[type="checkbox"]')
		.each(function () {
			var $this = $(this);
			$this.closest('.control-group').addClass('control-group-checkbox');
		});

	/**
	 * Draggable sidebar
	 */
	$('#hu-options-panel').draggable({
		cursor: 'grabbing',
		handle: '.hu-panel-handle',
		containment: 'body',
		iframeFix: true,
		drag: function (event, ui) {
			panelPositioning();
		},
	});

	/**
	 * Calculate the editor panel position and display the panel
	 */
	function panelPositioning() {
		let $fieldsetContents = $('.helix-ultimate-fieldset-contents');
		let $panel = $('.helix-ultimate-edit-panel');
		let $sidebar = $('#hu-options-panel');
		let $container = $('.helix-ultimate-container');

		let sidebarOffset = $sidebar.offset();
		let sidebarWidth = $sidebar.width();

		let panelWidth = $panel.width();
		let panelHeight = $panel.height();

		let containerWidth = $container.width();
		let containerHeight = $container.height();
		let gap = 20;

		let panelHorizontalPosition = sidebarOffset.left + sidebarWidth + gap;
		let panelVerticalPosition = sidebarOffset.top;

		if (panelHorizontalPosition + panelWidth > containerWidth) {
			panelHorizontalPosition = sidebarOffset.left - panelWidth - gap;
		}

		if (panelVerticalPosition + panelHeight > containerHeight) {
			panelVerticalPosition = containerHeight - panelHeight;
		}

		$fieldsetContents.css('left', panelHorizontalPosition + 'px');
		$fieldsetContents.css('top', panelVerticalPosition + 'px');
	}

	/**
	 * Display editor panel on click sidebar icon
	 *
	 */
	$('.helix-ultimate-fieldset-header').on('click', function (e) {
		e.preventDefault();

		panelPositioning();

		let fieldset = $(this).data('fieldset');

		if ($('.' + fieldset + '-panel').hasClass('active-panel')) {
			$('.' + fieldset + '-panel').removeClass('active-panel');
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
				.parents('#helix-ultimate-options')
				.find(
					'.helix-ultimate-fieldset .helix-ultimate-fieldset-header'
				)
				.hasClass('active')
		) {
			$(this)
				.parents('#helix-ultimate-options')
				.find(
					'.helix-ultimate-fieldset .helix-ultimate-fieldset-header'
				)
				.removeClass('active');
		}

		$(this).addClass('active');

		// $('.helix-ultimate-edit-panel.active-panel').draggable({
		// 	cursor: 'grabbing',
		// 	handle: '.helix-ultimate-panel-header',
		// 	containment: 'body',
		// });
	});

	/**
	 * Close an opned panel
	 */
	$('.helix-ultimate-panel-close').on('click', function (e) {
		e.preventDefault();

		if (
			$(this)
				.closest('.helix-ultimate-edit-panel')
				.hasClass('active-panel')
		) {
			$(this)
				.closest('.helix-ultimate-edit-panel')
				.removeClass('active-panel');
		}

		let $sidebarItem = $(
			`.${$(this).data('sidebarclass')} .helix-ultimate-fieldset-header`
		);

		if ($sidebarItem.hasClass('active')) {
			$sidebarItem.removeClass('active');
		}
	});

	$('.helix-ultimate-fieldset-toggle-icon').on('click', function (e) {
		e.preventDefault();

		$('.helix-ultimate-fieldset').removeClass('active');
		$('#helix-ultimate, #helix-ultimate-options').removeClass();
	});

	$('.helix-ultimate-group-header-box').on('click', function (e) {
		e.preventDefault();

		let $prevActiveEl = $(this)
			.closest('.helix-ultimate-edit-panel')
			.find('.helix-ultimate-group-wrap')
			.find('.helix-ultimate-field-list.active-group');

		if ($prevActiveEl.length > 0) {
			let prevUid = $prevActiveEl.data('uid');
			let currUid = $(this).next().data('uid');

			if (prevUid !== currUid) {
				$prevActiveEl.removeClass('active-group');
				$prevActiveEl.slideUp(400);
				$prevActiveEl
					.parent()
					.find(
						'.helix-ultimate-group-header-box .helix-ultimate-group-toggle-icon'
					)
					.removeClass('fa-angle-up')
					.addClass('fa-angle-down');
			}
		}

		// helix-ultimate-group-toggle-icon
		let $iconEl = $(this).find('.helix-ultimate-group-toggle-icon');
		if ($iconEl.hasClass('fa-angle-down')) {
			$iconEl.removeClass('fa-angle-down').addClass('fa-angle-up', 1000);
		} else if ($iconEl.hasClass('fa-angle-up')) {
			$iconEl.removeClass('fa-angle-up').addClass('fa-angle-down', 1000);
		}

		let $fieldList = $(this).next();

		if ($fieldList.hasClass('active-group')) {
			$fieldList.removeClass('active-group');
			$fieldList.slideUp(400);
		} else {
			$fieldList.addClass('active-group');
			$fieldList.slideDown(400);
		}

		// @TODO: remove after successfull testing.
		// if( $(this).closest('.helix-ultimate-group-wrap').hasClass('active-group') ){
		//     $(this).closest('.helix-ultimate-group-wrap').removeClass('active-group');
		//     return;
		// }

		// $('.helix-ultimate-group-wrap').removeClass('active-group')
		// $(this).closest('.helix-ultimate-group-wrap').addClass('active-group');
	});

	$('.helix-ultimate-header-item').on('click', function (e) {
		e.preventDefault();

		var $parent = $(this).closest('.helix-ultimate-header-list');

		$parent.find('.helix-ultimate-header-item').removeClass('active');
		$(this).addClass('active');

		var styleName = $(this).data('style'),
			filedName = $parent.data('name');

		$('#' + filedName).val(styleName);
	});

	// Preset
	$(document).ready(function () {
		if ($('#custom_style').attr('checked') == 'checked') {
			$('.helix-ultimate-fieldset-presets')
				.find('.helix-ultimate-group-wrap')
				.show();
		} else {
			$('.helix-ultimate-fieldset-presets')
				.find('.helix-ultimate-group-wrap')
				.hide();
		}
	});

	$(document).on('change', '#custom_style', function (e) {
		e.preventDefault();
		if ($(this).attr('checked') == 'checked') {
			$('.helix-ultimate-fieldset-presets')
				.find('.helix-ultimate-group-wrap')
				.slideDown();
		} else {
			$('.helix-ultimate-fieldset-presets')
				.find('.helix-ultimate-group-wrap')
				.slideUp();
		}
	});

	$(document).on('click', '.helix-ultimate-preset', function (e) {
		e.preventDefault();

		$('.helix-ultimate-preset').removeClass('active');
		$(this).addClass('active');

		draftChanges();
	});

	$('.helix-responsive-devices span').click(function () {
		if ($(this).hasClass('active')) return;
		const parent = $(this).parents('.helix-ultimate-webfont-size');
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

	$('.btn-purge-helix-ultimate-css').on('click', function (e) {
		e.preventDefault();
		var self = $(this);
		if (self.hasClass('disable')) {
			return;
		}
		self.addClass('disable');

		window.purgeCss(self);
	});

	// Import
	$('#btn-helix-ultimate-import-settings').on('click', function (event) {
		event.preventDefault();

		var $that = $(this),
			temp_settings = $.trim($('#input-helix-ultimate-settings').val());

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
		$('.helix-ultimate-field-webfont').each(function () {
			var $that = $(this),
				webfont = {
					fontFamily: $that
						.find('.helix-ultimate-webfont-list')
						.val(),
					fontSize: $that
						.find('.helix-ultimate-webfont-size-input')
						.val(),
					fontSize_sm: $that
						.find('.helix-ultimate-webfont-size-input-sm')
						.val(),
					fontSize_xs: $that
						.find('.helix-ultimate-webfont-size-input-xs')
						.val(),
					fontWeight: $that
						.find('.helix-ultimate-webfont-weight-list')
						.val(),
					fontStyle: $that
						.find('.helix-ultimate-webfont-style-list')
						.val(),
					fontSubset: $that
						.find('.helix-ultimate-webfont-subset-list')
						.val(),
				};

			$that
				.find('.helix-ultimate-webfont-input')
				.val(JSON.stringify(webfont));
		});
	}

	function getGeneratedLayout() {
		var item = [];
		$('#helix-ultimate-layout-builder')
			.find('.helix-ultimate-layout-section')
			.each(function (index) {
				var $row = $(this),
					rowIndex = index,
					rowObj = $row.data();
				delete rowObj.sortableItem;

				var activeLayout = $row.find(
						'.helix-ultimate-column-layout.active'
					),
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
				$row.find('.helix-ultimate-layout-column').each(function (
					index
				) {
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

	/*Option Group*/
	$(document).on('click', '.helix-ultimate-option-group-title', function (
		event
	) {
		event.preventDefault();
		$(this)
			.closest('.helix-ultimate-option-group')
			.toggleClass('active')
			.siblings()
			.removeClass('active');
	});
});
