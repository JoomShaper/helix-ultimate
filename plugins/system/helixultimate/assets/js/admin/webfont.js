/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

jQuery(function ($) {
	//Web Fonts
	let $parentId = $('.hu-field-webfont').data('id');
	let $parentEl = $('#' + $parentId);

	function renderPreview($parent) {
		let fontFamily = $parent.find('.hu-webfont-list').val(),
			fontWeight = $parent.find('.hu-webfont-weight-list').val(),
			fontSize = $parent.find('.hu-webfont-size-input.active').val(),
			fontColor = $parent.find('.hu-font-color-input').val(),
			fontSubset = $parent.find('.hu-webfont-subset-list').val(),
			fontLineHeight = $parent.find('.hu-font-line-height-input').val(),
			fontSpacing = $parent.find('.hu-font-letter-spacing-input').val(),
			textDecoration = $parent.find('input.hu-text-decoration').val(),
			textAlign = $parent.find('input.hu-text-align').val();

		let $preview = $parent.find('.hu-webfont-preview');

		if (!!fontFamily) {
			$preview.css('font-family', fontFamily);
		}

		if (!!fontWeight) {
			$preview.css('font-weight', fontWeight);
		} else {
			$preview.css('font-weight', '100');
		}

		if (!!fontSize) {
			if (!/^\d+[^\d]+/.test(fontSize)) {
				fontSize += 'px';
			}
			fontSize = fontSize.replace(/\s+/, '');

			$preview.css('font-size', fontSize);
		} else {
			$preview.css('font-size', '');
		}

		if (!!fontColor) {
			$preview.css('color', fontColor);
		} else {
			$preview.css('color', '#000000');
		}

		if (!!fontLineHeight) {
			$preview.css('line-height', fontLineHeight);
		} else {
			$preview.css('line-height', '');
		}

		if (!!fontSpacing) {
			if (!/^\d+[^\d]+/.test(fontSpacing)) {
				fontSpacing += 'px';
			}

			fontSpacing = fontSpacing.replace(/\s+/, '');

			$preview.css('letter-spacing', fontSpacing);
		} else {
			$preview.css('letter-spacing', '');
		}

		if (!!textDecoration) {
			$preview.css('text-decoration', textDecoration);
		}

		if (!!textAlign) {
			$preview.css('text-align', textAlign);
		}
	}

	$('.hu-field-webfont').each(function () {
		renderPreview($(this));
	});

	$(document).on('change', '.hu-webfont-list', function (event) {
		event.preventDefault();

		var $that = $(this),
			fontName = $that.val();

		var systemFonts = [
			'Arial',
			'Tahoma',
			'Verdana',
			'Helvetica',
			'Times New Roman',
			'Trebuchet MS',
			'Georgia',
		];

		if ($.inArray(fontName, systemFonts) !== -1) {
			$that
				.closest('.hu-field-webfont')
				.find('.hu-webfont-subset-list')
				.html('')
				.trigger('liszt:updated');
		} else {
			var data = {
				fontName: fontName,
			};

			var request = {
				action: 'fontVariants',
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
					var font = $.parseJSON(response);
					$that
						.closest('.hu-field-webfont')
						.find('.hu-webfont-subset-list')
						.html(font.subsets)
						.trigger('liszt:updated');
				},
			});

			var font = $that.val().replace(' ', '+');
			$('head').append(
				"<link href='//fonts.googleapis.com/css?family=" +
					font +
					":100,100italic,200,200italic,300,300italic,400,400italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic&display=swap' rel='stylesheet' type='text/css'>"
			);
		}

		renderPreview($(this).closest('.hu-field-webfont'));

		return false;
	});

	// Font Size
	$(document).on('change', '.hu-webfont-size-input', function (event) {
		event.preventDefault();
		renderPreview($(this).closest('.hu-field-webfont'));
	});

	$('.hu-font-color-input').on('input', function (event) {
		event.preventDefault();
		renderPreview($(this).closest('.hu-field-webfont'));
	});

	$('.hu-font-line-height-input').on('change', function (event) {
		event.preventDefault();
		renderPreview($(this).closest('.hu-field-webfont'));
	});

	$('.hu-font-letter-spacing-input').on('change', function (event) {
		event.preventDefault();
		renderPreview($(this).closest('.hu-field-webfont'));
	});

	// Font Weight
	$(document).on('change', '.hu-webfont-weight-list', function (event) {
		event.preventDefault();
		renderPreview($(this).closest('.hu-field-webfont'));
	});

	// Font Style
	$(document).on('change', '.hu-webfont-style-list', function (event) {
		event.preventDefault();
		renderPreview($(this).closest('.hu-field-webfont'));
	});

	//Font Subset
	$('.list-font-subset').on('change', function (event) {
		event.preventDefault();

		var font = $(this)
			.closest('.hu-field-webfont')
			.find('.hu-webfont-list')
			.val()
			.replace(' ', '+');
		$('head').append(
			"<link href='//fonts.googleapis.com/css?family=" +
				font +
				':100,100italic,200,200italic,300,300italic,400,400italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic&subset=' +
				$(this).val() +
				'&display=swap' +
				"' rel='stylesheet' type='text/css'>"
		);
	});

	// Text decoration
	$('.hu-font-decoration .hu-action-group .hu-switcher-action').on(
		'click',
		function (e) {
			e.preventDefault();
			e.stopPropagation();

			$(this).siblings().removeClass('active');
			$(this).addClass('active');
			$(this)
				.closest('.hu-font-decoration')
				.find('input.hu-text-decoration')
				.val($(this).data('value'))
				.trigger('change');

			renderPreview($(this).closest('.hu-field-webfont'));
		}
	);

	// Text alignment
	$('.hu-font-alignment .hu-action-group .hu-switcher-action').on(
		'click',
		function (e) {
			e.preventDefault();
			e.stopPropagation();

			$(this).siblings().removeClass('active');
			$(this).addClass('active');
			$(this)
				.closest('.hu-font-alignment')
				.find('input.hu-text-align')
				.val($(this).data('value'))
				.trigger('change');

			renderPreview($(this).closest('.hu-field-webfont'));
		}
	);

	//Update Fonts list
	$('.btn-update-hu-fonts').on('click', function (event) {
		event.preventDefault();

		var $that = $(this);
		var request = {
			action: 'update-font-list',
			option: 'com_ajax',
			helix: 'ultimate',
			request: 'task',
			data: {},
			format: 'json',
		};

		$.ajax({
			type: 'POST',
			data: request,
			beforeSend: function () {
				$that.prepend('<i class="fas fa-circle-notch fa-spin"></i> ');
			},
			success: function (response) {
				var data = $.parseJSON(response);
				if (data.status) {
					$that.after(data.message);
					$that.find('.fa-spinner').remove();
					$that
						.next()
						.delay(1000)
						.fadeOut(300, function () {
							$(this).remove();
						});
				} else {
					$that.after(
						"<p class='font-update-failed'>Unexpected error occurs. Please make sure that, you have inserted Google Font API key.</p>"
					);
					$that.find('.fa-spinner').remove();
				}
			},
		});

		return false;
	});
});
