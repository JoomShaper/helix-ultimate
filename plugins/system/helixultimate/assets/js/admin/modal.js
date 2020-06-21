/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

jQuery(function ($) {
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
			'<a href="#" class="action-hu-modal-close"><span class="fas fa-times"></span></a>';
		mediaModal +=
			'<input type="file" id="hu-file-input" accept="image/png, image/jpg, image/jpeg, image/gif, image/svg+xml, image/x-icon" style="display:none;" multiple>';
		mediaModal += '<div class="hu-modal-breadcrumbs"></div>';

		mediaModal += '<div class="hu-modal-actions-left">';
		mediaModal +=
			'<a href="#" class="hu-btn hu-btn-primary hu-modal-action-select hu-mr-2"><span class="fas fa-check"></span> Select</a>';
		mediaModal +=
			'<a href="#" class="hu-btn hu-btn-secondary hu-modal-action-cancel hu-mr-2"><span class="fas fa-times"></span> Cancel</a>';
		mediaModal +=
			'<a href="#" class="hu-btn hu-btn-danger hu-btn-last hu-modal-action-delete"><span class="fas fa-minus-circle"></span> Delete</a>';
		mediaModal += '</div>';

		mediaModal += '<div class="hu-modal-actions-right">';
		mediaModal +=
			'<a href="#" class="hu-btn hu-btn-primary hu-modal-action-upload hu-mr-2"><span class="fas fa-upload"></span> Upload</a>';
		mediaModal +=
			'<a href="#" class="hu-btn hu-btn-secondary hu-btn-last hu-modal-action-new-folder"><span class="fas fa-plus"></span> New Folder</a>';
		mediaModal += '</div>';
		mediaModal += '</div>';

		mediaModal += '<div class="hu-modal-inner">';
		mediaModal +=
			'<div class="hu-modal-preloader"><span class="fas fa-circle-notch fa-pulse fa-spin fa-3x fa-fw"></span></div>';
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

		$(
			'.hu-options-modal-overlay, .hu-options-modal'
		).remove();

		var optionsModal =
			'<div class="hu-options-modal-overlay"></div>';
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
			'<a href="#" class="action-hu-options-modal-close"><span class="fas fa-times"></span></a>';
		optionsModal += '</div>';

		optionsModal += '<div class="hu-options-modal-inner">';
		optionsModal += '<div class="hu-options-modal-content">';
		optionsModal += '</div>';
		optionsModal += '</div>';

		optionsModal += '<div class="hu-options-modal-footer">';
		optionsModal += `<a href="#" class="hu-btn hu-btn-primary hu-mr-2 ${options.applyBtnClass}" data-flag="${options.flag}"><span class="fas fa-check"></span> Apply</a>`;
		optionsModal +=
			'<a href="#" class="hu-btn hu-btn-secondary hu-settings-cancel"><span class="fas fa-times"></span> Cancel</a>';

		if (options.footerButtons.length) {
			optionsModal += options.footerButtons.map(button => button);
		}

		optionsModal += '</div>';

		optionsModal += '</div>';

		$('body')
			.addClass('hu-options-modal-open')
			.append(optionsModal);
	};
});
