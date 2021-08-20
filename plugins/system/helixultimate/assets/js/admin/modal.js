/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

jQuery(function ($) {
	$.fn.extend({
		closeModal() {
			$('.hu-modal-overlay, .hu-modal').fadeOut().remove();
			$('body').removeClass('hu-modal-open');
			return this;
		},
	});

	$.fn.helixUltimateMegamenuModal = function (options) {
		options = $.extend(
			{
				title: 'Mega Menu',
				className: '',
				targetType: 'id',
				target: '',
				body: '',
				footer: '',
			},
			options
		);

		$('.hu-modal-overlay, .hu-modal').remove();

		const { title, className, targetType, target, body, footer } = options;

		let megaMenuModal = '<div class="hu-modal-overlay"></div>';
		megaMenuModal +=
			'<div class="hu-modal ' +
			className +
			'" data-target_type="' +
			targetType +
			'" data-target="' +
			target +
			'" style="display:none;">';

		megaMenuModal += '<div class="hu-modal-header">';

		
		megaMenuModal += '<h4 class="hu-modal-header-title">' + title + '</h4>';
		megaMenuModal +=
			'<a href="#" class="action-hu-modal-close"><span class="fas fa-times" aria-hidden="true"></span></a>';
		megaMenuModal += '</div>';

		megaMenuModal += '<div class="hu-modal-inner">';
		megaMenuModal += '<div class="hu-modal-content">';

		if (body) {
			megaMenuModal += '<div class="hu-modal-megamenu-container">';
			megaMenuModal += body;
			megaMenuModal += '</div>';
		}

		megaMenuModal += '</div>';
		megaMenuModal += '</div>';

		megaMenuModal += '<div class="hu-modal-footer footer-right">';
		megaMenuModal +=
			'<button class="hu-btn hu-btn-link hu-megamenu-cancel-btn">Cancel</button>';
		megaMenuModal +=
			'<button class="hu-btn hu-btn-primary hu-megamenu-save-btn">Save</button>';
		megaMenuModal += '</div>';

		megaMenuModal += '</div>';
		const $modal = $('body').addClass('hu-modal-open');
		$modal.append(megaMenuModal);
		$modal.find('.hu-modal').fadeIn(300);
	};

	$.fn.helixUltimateFrameModal = function (options) {
		options = $.extend(
			{
				title: 'Helix Ultimate',
				className: '',
				targetType: '',
				target: '',
				footer: '',
				frameUrl: '',
			},
			options
		);

		const {
			title,
			target,
			targetType,
			body,
			footer,
			frameUrl,
			className,
		} = options;

		$('.hu-modal-overlay, .hu-modal').remove();

		let frameModal = '<div class="hu-modal-overlay"></div>';
		frameModal +=
			'<div class="hu-modal ' +
			className +
			'" data-target_type="' +
			targetType +
			'" data-target="' +
			target +
			'" style="display:none;">';

		frameModal += '<div class="hu-modal-header">';

		frameModal += '<h4 class="hu-modal-header-title">' + title + '</h4>';
		frameModal +=
			'<a href="#" class="action-hu-modal-close"><span class="fas fa-times" aria-hidden="true"></span></a>';
		frameModal += '</div>';

		frameModal += '<div class="hu-modal-inner">';
		frameModal += '<div class="hu-modal-content">';

		if (frameUrl) {
			frameModal += '<div class="hu-modal-frame-container">';
			frameModal +=
				'<iframe src="' +
				frameUrl +
				'" width="100%" height="100%"></iframe>';
			frameModal += '</div>';
		}

		frameModal += '</div>';
		frameModal += '</div>';

		frameModal += '<div class="hu-modal-footer footer-right">';
		frameModal += '<button class="hu-btn hu-btn-link hu-cancel-btn">Cancel</button>';
		frameModal += '<button class="hu-btn hu-btn-primary hu-save-btn">';
		frameModal += '<div class="hu-spinner hidden spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>';
		frameModal += ' Save';
		frameModal += '</button>';
		frameModal += '</div>';
		frameModal += '</div>';

		frameModal += '</div>';
		const $modal = $('body').addClass('hu-modal-open');
		$modal.append(frameModal);
		$modal.find('.hu-modal').fadeIn(300);
	};

	$.fn.helixUltimateModal = function (options) {
		var options = $.extend(
			{
				target_type: '',
				target: '',
			},
			options
		);

		$('.hu-modal-overlay, .hu-modal').remove();

		var mediaModal = '<div class="hu-modal-overlay"></div>';
		mediaModal +=
			'<div class="hu-modal" data-target_type="' +
			options.target_type +
			'" data-target="' +
			options.target +
			'">';

		mediaModal += '<div class="hu-modal-header">';
		mediaModal +=
			'<input type="file" id="hu-file-input" accept="image/png, image/jpg, image/jpeg, image/gif, image/svg+xml, image/x-icon" style="display:none;" multiple>';
			
		mediaModal += '<div class="hu-modal-breadcrumbs"></div>';

		mediaModal += '<div class="hu-modal-actions-left">';
		mediaModal +=
			'<a href="#" class="hu-btn hu-btn-primary hu-modal-action-select hu-mr-2"><span class="fas fa-check" aria-hidden="true"></span> Select</a>';
		mediaModal +=
			'<a href="#" class="hu-btn hu-btn-secondary hu-modal-action-cancel hu-mr-2"><span class="fas fa-times" aria-hidden="true"></span> Cancel</a>';
		mediaModal +=
			'<a href="#" class="hu-btn hu-btn-danger hu-btn-last hu-modal-action-delete"><span class="fas fa-minus-circle" aria-hidden="true"></span> Delete</a>';
		mediaModal += '</div>';

		mediaModal += '<div class="hu-modal-actions-right">';
		mediaModal +=
			'<a href="#" class="hu-btn hu-btn-primary hu-modal-action-upload hu-mr-2"><span class="fas fa-upload" aria-hidden="true"></span> Upload</a>';
		mediaModal +=
			'<a href="#" class="hu-btn hu-btn-secondary hu-btn-last hu-modal-action-new-folder"><span class="fas fa-plus" aria-hidden="true"></span> New Folder</a>';
		mediaModal +=
		'<a href="#" class="action-hu-modal-close"><span class="fas fa-times" aria-hidden="true"></span></a>';
		mediaModal += '</div>';
		mediaModal += '</div>';

		mediaModal += '<div class="hu-modal-inner">';
		mediaModal +=
			'<div class="hu-modal-preloader"><span class="fas fa-circle-notch fa-pulse fa-spin fa-3x fa-fw" aria-hidden="true"></span></div>';
		mediaModal += '</div>';
		mediaModal += '</div>';

		$('body').addClass('hu-modal-open').append(mediaModal);
	};

	$.fn.helixUltimateOptionsModal = function (options) {
		var options = $.extend(
			{
				target: '',
				title: 'Options',
				flag: '',
				class: '',
				applyBtnClass: 'hu-settings-apply',
				footerButtons: [],
			},
			options
		);

		$('.hu-options-modal-overlay, .hu-options-modal').remove();

		var optionsModal = '<div class="hu-options-modal-overlay"></div>';
		optionsModal +=
			'<div class="hu-options-modal ' +
			options.class +
			'" data-target="#' +
			options.target +
			'">';

		optionsModal += '<div class="hu-options-modal-header">';
		optionsModal +=
			'<span class="hu-options-modal-header-title">' +
			options.title +
			'</span>';
		optionsModal +=
			'<a href="#" class="action-hu-options-modal-close"><span class="fas fa-times" aria-hidden="true"></span></a>';
		optionsModal += '</div>';

		optionsModal += '<div class="hu-options-modal-inner">';
		optionsModal += '<div class="hu-options-modal-content">';
		optionsModal += '</div>';
		optionsModal += '</div>';

		optionsModal += '<div class="hu-options-modal-footer">';
		optionsModal += `<a href="#" class="hu-btn hu-btn-primary ${options.applyBtnClass}" data-flag="${options.flag}"><span class="fas fa-check"></span> Apply</a>`;
		// optionsModal +=
		// 	'<a href="#" class="hu-btn hu-btn-secondary hu-settings-cancel"><span class="fas fa-times"></span> Cancel</a>';

		if (options.footerButtons.length) {
			optionsModal += options.footerButtons.map(button => button);
		}

		optionsModal += '</div>';

		optionsModal += '</div>';

		$('body').addClass('hu-options-modal-open').append(optionsModal);
	};
});
