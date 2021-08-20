/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

jQuery(function ($) {
	// Media
	$('.hu-media-picker').on('click', function (e) {
		e.preventDefault();
		var self = this;
		var target_type = 'id';
		var target = '';

		if (typeof $(this).data('id') != 'undefined') {
			target_type = 'id';
			target = $(this).data('id');
		} else if (typeof $(this).data('target') != 'undefined') {
			target_type = 'data';
			target = $(this).data('target');
		}

		$(this).helixUltimateModal({
			target_type: target_type,
			target: target,
		});

		var request = {
			action: 'view-media',
			option: 'com_ajax',
			helix: 'ultimate',
			request: 'task',
			format: 'json',
		};

		$.ajax({
			type: 'POST',
			data: request,
			beforeSend: function () {
				$(self).find('.fa').removeClass('fa-picture-o').addClass('fa-spinner fa-spin');
			},
			success: function (response) {
				var data = $.parseJSON(response);
				$(self).find('.fa').removeClass('fa-spinner fa-spin').addClass('fa-picture-o');
				if (data.status) {
					$('.hu-modal-breadcrumbs').html(data.breadcrumbs);
					$('.hu-modal-inner').html(data.output);
				} else {
					$('.hu-modal-overlay, .hu-modal').remove();
					$('body').addClass('hu-modal-open');
					alert(data.output);
				}
			},
			error: function () {
				alert('Somethings wrong, Try again');
			},
		});
	});

	$(document).on('dblclick', '.hu-media-folder', function (e) {
		e.preventDefault();
		var self = this;

		var request = {
			action: 'view-media',
			option: 'com_ajax',
			helix: 'ultimate',
			request: 'task',
			path: $(self).data('path'),
			format: 'json',
		};

		$.ajax({
			type: 'POST',
			data: request,
			beforeSend: function () {
				$('.hu-media-selected').removeClass('hu-media-selected');
				$('.hu-modal-actions-left').hide();
				$('.hu-modal-actions-right').show();
				$('.hu-modal-inner').html(
					'<div class="hu-modal-preloader"><span class="fas fa-circle-notch fa-pulse fa-spin fa-3x fa-fw" aria-hidden="true"></span></div>'
				);
			},
			success: function (response) {
				var data = $.parseJSON(response);
				if (data.status) {
					$('.hu-modal-breadcrumbs').html(data.breadcrumbs);
					$('.hu-modal-inner').html(data.output);
				} else {
					alert(data.output);
				}
			},
			error: function () {
				alert('Somethings wrong, Try again');
			},
		});
	});

	$(document).on('click', '.hu-media-breadcrumb-item > a', function (e) {
		e.preventDefault();
		var self = this;

		var request = {
			action: 'view-media',
			option: 'com_ajax',
			helix: 'ultimate',
			request: 'task',
			path: $(self).data('path'),
			format: 'json',
		};

		$.ajax({
			type: 'POST',
			data: request,
			beforeSend: function () {
				$('.hu-modal-inner').html(
					'<div class="hu-modal-preloader"><span class="fas fa-circle-notch fa-pulse fa-spin fa-3x fa-fw" aria-hidden="true"></span></div>'
				);
			},
			success: function (response) {
				var data = $.parseJSON(response);
				if (data.status) {
					$('.hu-modal-breadcrumbs').html(data.breadcrumbs);
					$('.hu-modal-inner').html(data.output);
				} else {
					alert(data.output);
				}
			},
			error: function () {
				alert('Somethings wrong, Try again');
			},
		});
	});

	$(document).on('click', '.hu-media-folder, .hu-media-image', function (event) {
		event.preventDefault();
		$('.hu-media-selected').removeClass('hu-media-selected');
		$(this).addClass('hu-media-selected');
		if ($(this).hasClass('hu-media-folder')) {
			$('.hu-modal-action-select').hide();
		} else {
			$('.hu-modal-action-select').removeAttr('style');
		}
		$('.hu-modal-actions-left').show();
		$('.hu-modal-actions-right').hide();
	});

	$(document).on('click', '.hu-modal-action-select', function (event) {
		event.preventDefault();
		var value = $('.hu-media-selected').data('path');
		var preview = $('.hu-media-selected').data('preview');
		var target = $('.hu-modal').attr('data-target');
		var target_type = $('.hu-modal').attr('data-target_type');

		if (target_type == 'data') {
			$('.hu-options-modal')
				.find('[data-attrname="' + target + '"]')
				.val(value)
				.trigger('change');

			const targetField = document.querySelector(`.hu-options-modal [data-attrname=${target}]`);
			Joomla.utils.triggerEvent(targetField, 'change');

			$('.hu-options-modal')
				.find('[data-attrname="' + target + '"]')
				.prev('.hu-image-holder')
				.html('<img src="' + preview + '" alt="">');

			// Visible the clear button if hidden
			let $clear = $('.hu-options-modal')
				.find('[data-attrname="' + target + '"]')
				.siblings('.hu-media-clear');

			if ($clear.hasClass('hide')) {
				$clear.removeClass('hide');
			}
		} else {
			$('#' + target)
				.val(value)
				.trigger('change');
			Joomla.utils.triggerEvent(document.querySelector(`#${target}`), 'change');
			$('#' + target)
				.prev('.hu-image-holder')
				.html('<img src="' + preview + '" alt="">');

			// Visible the clear button if hidden
			let $clear = $('#' + target).siblings('.hu-media-clear');

			if ($clear.hasClass('hide')) {
				$clear.removeClass('hide');
			}
		}

		$('.hu-modal-overlay, .hu-modal').remove();
		$('body').removeClass('hu-modal-open');
	});

	$(document).on('click', '.hu-modal-action-cancel', function (event) {
		event.preventDefault();
		$('.hu-media-selected').removeClass('hu-media-selected');
		$('.hu-modal-actions-left').hide();
		$('.hu-modal-actions-right').show();
	});

	$(document).on('click', '.action-hu-modal-close', function (event) {
		event.preventDefault();
		$('.hu-modal-overlay, .hu-modal').remove();
		$('body').removeClass('hu-modal-open');
	});

	$(document).on('click', '.hu-media-clear', function (event) {
		event.preventDefault();
		$(this).parent().find('input').val('').trigger('change');
		Joomla.utils.triggerEvent(event.target.parentNode.querySelector('input'), 'change');
		$(this).parent().find('.hu-image-holder').empty();
		if (!$(this).hasClass('hide')) $(this).addClass('hide');
	});

	//Delete Media
	$(document).on('click', '.hu-modal-action-delete', function (e) {
		e.preventDefault();
		var self = this;
		var deleteType = 'file';

		if ($('.hu-media-selected').length) {
			if ($('.hu-media-selected').hasClass('hu-media-folder')) {
				deleteType = 'folder';
			} else {
				deleteType = 'file';
			}
		} else {
			alert('Please select a file or directory first to delete.');
			return;
		}

		if (confirm('Are you sure you want to delete this ' + deleteType + '?')) {
			var request = {
				action: 'delete-media',
				option: 'com_ajax',
				helix: 'ultimate',
				request: 'task',
				type: deleteType,
				path: $('.hu-media-selected').data('path'),
				format: 'json',
			};

			$.ajax({
				type: 'POST',
				data: request,
				success: function (response) {
					var data = $.parseJSON(response);
					if (data.status) {
						$('.hu-media-selected').remove();
						$('.hu-modal-actions-left').hide();
						$('.hu-modal-actions-right').show();
					} else {
						alert(data.message);
					}
				},
				error: function () {
					alert('Somethings wrong, Try again');
				},
			});
		}
	});

	// Create folder
	$(document).on('click', '.hu-modal-action-new-folder', function (e) {
		e.preventDefault();
		var self = this;
		var folder_name = prompt('Please enter the name of the directory which should be created.');

		if (folder_name == null || folder_name == '') {
		} else {
			var request = {
				action: 'create-folder',
				option: 'com_ajax',
				helix: 'ultimate',
				request: 'task',
				folder_name: folder_name,
				path: $('.hu-media-breadcrumb-item.active').data('path'),
				format: 'json',
			};

			$.ajax({
				type: 'POST',
				data: request,
				success: function (response) {
					var data = $.parseJSON(response);
					if (data.status) {
						$('.hu-modal-inner').html(data.output);
					} else {
						alert(data.message);
					}
				},
				error: function () {
					alert('Somethings wrong, Try again');
				},
			});
		}
	});

	$.fn.uploadMedia = function (options) {
		var options = $.extend(
			{
				data: '',
				index: '',
			},
			options
		);

		$.ajax({
			type: 'POST',
			url:
				'index.php?option=com_ajax&helix=ultimate&request=task&action=upload-media&format=json&helix_id=' +
				helixUltimateStyleId,
			data: options.data,
			contentType: false,
			cache: false,
			processData: false,
			beforeSend: function () {
				var progress = '<li class="hu-media-progress ' + options.index + '">';
				progress += '<div class="hu-media-thumb">';
				progress += '<div class="hu-progress"><div class="hu-progress-bar"></div></div>';
				progress += '</div>';
				progress +=
					'<div class="hu-media-label"><span class="fas fa-circle-notch fa-spin" aria-hidden="true"></span> <span class="hu-media-upload-percentage"></span>Uploading...</div>';
				progress += '</li>';

				$('#hu-media-manager').animate(
					{
						scrollTop: $('#hu-media-manager').prop('scrollHeight'),
					},
					1000
				);
				$('.hu-media').append(progress);
			},
			success: function (response) {
				var data = $.parseJSON(response);
				if (data.status) {
					$('.' + options.index)
						.removeClass()
						.addClass('hu-media-image')
						.attr('data-path', data.path)
						.attr('data-preview', data.src)
						.html(data.output);
				} else {
					$('.' + options.index).remove();
					alert(data.message);
				}
			},
			xhr: function () {
				myXhr = $.ajaxSettings.xhr();
				if (myXhr.upload) {
					myXhr.upload.addEventListener(
						'progress',
						function (evt) {
							$('.' + options.index)
								.find('.hu-progress-bar')
								.css('width', Math.floor((evt.loaded / evt.total) * 100) + '%');
							$('.' + options.index)
								.find('.hu-media-upload-percentage')
								.text(Math.floor((evt.loaded / evt.total) * 100) + '% ');
						},
						false
					);
				} else {
					alert('Uploadress is not supported.');
				}
				return myXhr;
			},
		});
	};

	// Upload Image
	$(document).on('click', '.hu-modal-action-upload', function (e) {
		e.preventDefault();
		$('#hu-file-input').click();
	});

	$(document).on('change', '#hu-file-input', function (event) {
		event.preventDefault();
		var $this = $(this);
		var files = $(this).prop('files');

		for (i = 0; i < files.length; i++) {
			var file_ext = files[i].name.split('.').pop();
			var allowed =
				file_ext == 'png' ||
				file_ext == 'jpg' ||
				file_ext == 'jpeg' ||
				file_ext == 'gif' ||
				file_ext == 'svg' ||
				file_ext == 'ico';
			if (allowed) {
				var formdata = new FormData();
				formdata.append('file', files[i]);
				formdata.append('path', $('.hu-media-breadcrumb-item.active').data('path'));
				formdata.append('index', 'media-id-' + Math.floor(Math.random() * (1e6 - 1 + 1) + 1));
				$(this).uploadMedia({
					data: formdata,
					index: 'media-id-' + Math.floor(Math.random() * (1e6 - 1 + 1) + 1),
				});
			}
		}

		$this.val('');
	});
});
