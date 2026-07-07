/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

jQuery(function($) {

	function huText(key, fallback) {
		if (typeof Joomla !== 'undefined' && Joomla.Text) {
			return Joomla.Text._(key, fallback);
		}

		return fallback;
	}

	function getCsrfTokenData() {
		var tokenData = {};
		var token = typeof Joomla !== 'undefined' ? Joomla.getOptions('csrf.token') : null;

		if (token) {
			tokenData[token] = 1;
			return tokenData;
		}

		$('#adminForm input[type="hidden"]').each(function() {
			if (this.value === '1' && /^[a-f0-9]{32}$/i.test(this.name)) {
				tokenData[this.name] = '1';
			}
		});

		return tokenData;
	}

	function parseAjaxResponse(response) {
		if (response && typeof response === 'object') {
			return response;
		}

		if (typeof response !== 'string' || !response.length) {
			return null;
		}

		try {
			return JSON.parse(response);
		} catch (e) {
			return null;
		}
	}

	function getArticleId() {
		var $idField = $('#jform_id');

		if ($idField.length) {
			var fieldId = parseInt($idField.val(), 10);

			if (fieldId > 0) {
				return fieldId;
			}
		}

		var match = window.location.search.match(/(?:[?&](?:id|a_id)=)(\d+)/);

		return match ? parseInt(match[1], 10) : 0;
	}

	function clearFeaturedImageField($field) {
		$field.find('.hu-image-upload-wrapper').empty();
		$field.removeClass('hu-image-field-has-image').addClass('hu-image-field-empty');
		$field.find('#jform_attribs_helix_ultimate_image').val('');
	}

	$(document).ready(function() {
		var first_tab = $('#myTabTabs').find('>li').first();
		$('a[href="#attrib-helix_ultimate_blog_options"]').parent().insertAfter(first_tab);

		activateBlogMediaContent();
	})

	function activateBlogMediaContent() {
		// Find the label with the "active" class within the fieldset
		var activeLabel = $('#jform_attribs_helix_ultimate_article_format label.active');

		// Check if a label with "active" class was found
		if (activeLabel.length > 0) {
			// Trigger a click event on the found label
			activeLabel.click();
		}
	}

	$('.hu-image-field').each(function(index, el) {

		var $field = $(el);

		// Upload form
		$field.find('.btn-hu-image-upload').on('click', function(event) {
			event.preventDefault();
			$field.find('.hu-image-upload').click();
		});

		//Upload
		$field.find(".hu-image-upload").on('change', (function(e) {
			e.preventDefault();
			var $this = $(this);
			var file = $(this).prop('files')[0];

			var data = new FormData();
			var tokenData = getCsrfTokenData();

			data.append('option', 'com_ajax');
			data.append('helix', 'ultimate');
			data.append('request', 'task');
			data.append('action', 'upload-blog-image');
			data.append('format', 'json');

			Object.keys(tokenData).forEach(function(key) {
				data.append(key, tokenData[key]);
			});

			if (file.type.match(/image.*/)) {
				data.append('image', file);

				$.ajax({
					type: "POST",
					data: data,
					dataType: 'json',
					contentType: false,
					cache: false,
					processData:false,
					beforeSend: function() {
						$this.prop('disabled', true);
						$field.find('.btn-hu-image-upload').attr('disabled', 'disabled');
						var loader = $('<div class="hu-image-item-loader"><div class="progress" id="upload-image-progress"><div class="bar"></div></div></div>');
						$field.find('.hu-image-upload-wrapper').addClass('loading').html(loader)
					},
					success: function(response)
					{
						var data = parseAjaxResponse(response);

						if (!data) {
							$field.find('.hu-image-upload-wrapper').removeClass('loading').empty();
							alert(huText('HELIX_ULTIMATE_UPLOAD_IMAGE_FAILED', 'Unable to upload image. Please try again.'));
							return;
						}

						if(data.status) {
							$field.find('.hu-image-upload-wrapper').removeClass('loading').empty().html(data.output);
						} else {
							$field.find('.hu-image-upload-wrapper').removeClass('loading').empty();
						}

						var $image = $field.find('.hu-image-upload-wrapper').find('>img');

						if($image.length) {
							$('.hu-image-field').removeClass('hu-image-field-empty').addClass('hu-image-field-has-image');
							$field.find('#jform_attribs_helix_ultimate_image').val($image.data('src'));
						} else {
							$('.hu-image-field').removeClass('hu-image-field-has-image').addClass('hu-image-field-empty');
							$field.find('#jform_attribs_helix_ultimate_image').val('');
						}

		 				$this.val('');
		 				$this.prop('disabled', false);
		 				$field.find('.btn-hu-image-upload').removeAttr('disabled');

					},
					xhr: function() {
						myXhr = $.ajaxSettings.xhr();
						if(myXhr.upload){
							myXhr.upload.addEventListener('progress', function(evt) {
								$('#upload-image-progress').find('.bar').css('width', Math.floor(evt.loaded / evt.total *100) + '%');
							}, false);
						} else {
							alert(huText('HELIX_ULTIMATE_UPLOAD_PROGRESS_NOT_SUPPORTED', 'Upload progress is not supported.'));
						}
						return myXhr;
					},
					error: function()
					{
						$field.find('.hu-image-upload-wrapper').empty();
						$this.val('');
					}
				});
			}

			$this.val('');

		}));

	});

	// Delete Image
	$(document).on('click', '.btn-hu-image-remove', function(event) {

		event.preventDefault();

		var $this = $(this);
		var $parent = $this.closest('.hu-image-field');
		var articleId = getArticleId();

		if (confirm(huText('JGLOBAL_CONFIRM_DELETE', 'Are you sure you want to delete?')) === true) {
			if (articleId <= 0) {
				clearFeaturedImageField($parent);
				return;
			}

		    var request = $.extend({
				'option' : 'com_ajax',
				'helix' : 'ultimate',
				'request' : 'task',
				'action' : 'remove-blog-image',
				'id'     : articleId,
				'src'	 : $parent.find('.hu-image-upload-wrapper').find('>img').attr('data-src') || $parent.find('.hu-image-upload-wrapper').find('>img').data('src'),
				'format' : 'json'
			}, getCsrfTokenData());

			$.ajax({
				type: "POST",
				data   : request,
				dataType: 'json',
				success: function(response)
				{
					var data = parseAjaxResponse(response);

					if (!data) {
						alert(huText('HELIX_ULTIMATE_REMOVE_IMAGE_FAILED', 'Unable to remove image. Please try again.'));
						return;
					}

					if(data.status) {
						clearFeaturedImageField($parent);
					} else {
						alert(data.output || huText('HELIX_ULTIMATE_REMOVE_IMAGE_FAILED', 'Unable to remove image. Please try again.'));
					}
				},
				error: function() {
					alert(huText('HELIX_ULTIMATE_REMOVE_IMAGE_FAILED', 'Unable to remove image. Please try again.'));
				}
			});
		}
	});

	// Gallery
	$('.btn-hu-gallery-item-upload').on('click', function(event) {
		event.preventDefault();
		$('#hu-gallery-item-upload').click();
	});

	$('#hu-gallery-item-upload').on('change', function(event) {
		event.preventDefault();

		var $this = $(this);
		var files = $(this).prop('files');
		var paths = Joomla.getOptions('system.paths');

		for (i=0;i<files.length;i++){
			var ext = files[i].name.split('.').pop().toLowerCase();
			var allowed = ((ext == 'png') || (ext == 'jpg') || (ext == 'jpeg') || (ext == 'gif') || (ext == 'svg') || (ext == 'webp'));
			if(allowed) {
				
				let gallery_id = 'gallery-id-' + Math.floor(Math.random() * (1e6 - 1 + 1) + 1);
				
				var data = new FormData();
				var tokenData = getCsrfTokenData();

				data.append('option', 'com_ajax');
				data.append('helix', 'ultimate');
				data.append('request', 'task');
				data.append('action', 'upload-blog-image');
				data.append('image', files[i]);
				data.append('index', gallery_id);
				data.append('gallery', true);
				data.append('format', 'json');

				Object.keys(tokenData).forEach(function(key) {
					data.append(key, tokenData[key]);
				});

				$.ajax({
					type: "POST",
					data: data,
					dataType: 'json',
					contentType: false,
					cache: false,
					processData:false,
					beforeSend: function() {
						var loader = $('<li class="hu-gallery-item loading" id="'+ gallery_id +'"><div class="progress"><div class="bar"></div></div></li>');
						$('.hu-gallery-items').append(loader);
					},
					success: function(response)
					{
						var data = parseAjaxResponse(response);

						if (!data) {
							$('#' + gallery_id).remove();
							alert(huText('HELIX_ULTIMATE_UPLOAD_GALLERY_IMAGE_FAILED', 'Unable to upload gallery image. Please try again.'));
							return;
						}

						if(data.status) {
							$('#' + gallery_id).attr('data-src', data.data_src).removeClass('loading').empty().html(data.output);
						} else {
							$('#' + gallery_id).remove();
							alert(data.output);
						}

						let images = [];
		 				$('.hu-gallery-items').find('>.hu-gallery-item').each(function( index, value ) {
		 					images.push( '"' + $(value).data('src') + '"' );
		 				});
		 				let output = '{"helix_ultimate_gallery_images":['+ images +']}';
		 				$('#jform_attribs_helix_ultimate_gallery').val(output);
						
					},
					xhr: function() {
						myXhr = $.ajaxSettings.xhr();
						if(myXhr.upload) {
							myXhr.upload.addEventListener('progress', function(evt) {
								$('#' + gallery_id).find('.bar').css('width', Math.floor(evt.loaded / evt.total *100) + '%');
							}, false);
						} else {
							console.log(huText('HELIX_ULTIMATE_UPLOAD_PROGRESS_NOT_SUPPORTED', 'Upload progress is not supported.'));
						}
						return myXhr;
					}
				});
			}
		}
		
		$this.val('');

	});

	// Sortable
	$('.hu-gallery-items').sortable({
		stop : function(event,ui){
			let images = [];

			$('.hu-gallery-item').each(function( index, value ) {
				images.push( '"' + $(value).data('src') + '"' );
			});

			let output = '{"helix_ultimate_gallery_images":['+ images +']}';
			$('#jform_attribs_helix_ultimate_gallery').val(output);
		}
	});

	$(document).on('click', '.btn-hu-remove-gallery-image', function(event) {
		event.preventDefault();
		var $this = $(this);
		var $galleryItem = $this.parent();
		var articleId = getArticleId();

		if (confirm(huText('JGLOBAL_CONFIRM_DELETE', 'Are you sure you want to delete?')) === true) {
			var updateGalleryField = function() {
				$galleryItem.remove();

				let images = [];

				$('.hu-gallery-item').each(function( index, value ) {
					images.push( '"' + $(value).data('src') + '"' );
				});

				let output = '{"helix_ultimate_gallery_images":['+ images +']}';
				$('#jform_attribs_helix_ultimate_gallery').val(output);
			};

			if (articleId <= 0) {
				updateGalleryField();
				return;
			}

		    var request = $.extend({
				'option' : 'com_ajax',
				'helix' : 'ultimate',
				'request' : 'task',
				'action' : 'remove-blog-image',
				'id'     : articleId,
				'src'	 : $galleryItem.attr('data-src') || $galleryItem.data('src'),
				'format' : 'json'
			}, getCsrfTokenData());

			$.ajax({
				type: "POST",
				data   : request,
				dataType: 'json',
				success: function(response)
				{
					var data = parseAjaxResponse(response);

					if (!data) {
						alert(huText('HELIX_ULTIMATE_REMOVE_GALLERY_IMAGE_FAILED', 'Unable to remove gallery image. Please try again.'));
						return;
					}

					if(data.status) {
						updateGalleryField();
					} else {
						alert(data.output || huText('HELIX_ULTIMATE_REMOVE_GALLERY_IMAGE_FAILED', 'Unable to remove gallery image. Please try again.'));
					}
				},
				error: function() {
					alert(huText('HELIX_ULTIMATE_REMOVE_GALLERY_IMAGE_FAILED', 'Unable to remove gallery image. Please try again.'));
				}
			});
		}
	});

});
