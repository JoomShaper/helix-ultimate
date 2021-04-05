/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

jQuery(function($) {

	$(document).ready(function() {
		var first_tab = $('#myTabTabs').find('>li').first();
		$('a[href="#attrib-helix_ultimate_blog_options"]').parent().insertAfter(first_tab);
	})

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
			data.append('option', 'com_ajax');
			data.append('helix', 'ultimate');
			data.append('request', 'task');
			data.append('action', 'upload-blog-image');
			data.append('format', 'json');

			if (file.type.match(/image.*/)) {
				data.append('image', file);

				$.ajax({
					type: "POST",
					data: data,
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

						var data = $.parseJSON(response);

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
							alert('Uploadress is not supported.');
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

		if (confirm("You are about to delete this item permanently. 'Cancel' to stop, 'OK' to delete.") == true) {
		    var request = {
				'option' : 'com_ajax',
				'helix' : 'ultimate',
				'request' : 'task',
				'action' : 'remove-blog-image',
				'src'	 : $parent.find('.sp-image-upload-wrapper').find('>img').data('src'),
				'format' : 'json'
			};

			$.ajax({
				type: "POST",
				data   : request,
				success: function(response)
				{
					var data = $.parseJSON(response);
					if(data.status) {
						$parent.find('.hu-image-upload-wrapper').empty();
						$('.hu-image-field').removeClass('hu-image-field-has-image').addClass('hu-image-field-empty');
						$parent.find('#jform_attribs_helix_ultimate_image').val('');

					} else {
						alert(data.output);
					}
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
			var ext = files[i].name.split('.').pop();
			var allowed = ((ext == 'png') || (ext == 'jpg') || (ext == 'jpeg') || (ext == 'gif') || (ext == 'svg'));
			if(allowed) {
				
				let gallery_id = 'gallery-id-' + Math.floor(Math.random() * (1e6 - 1 + 1) + 1);
				
				var data = new FormData();
				data.append('option', 'com_ajax');
				data.append('helix', 'ultimate');
				data.append('request', 'task');
				data.append('action', 'upload-blog-image');
				data.append('image', files[i]);
				data.append('index', gallery_id);
				data.append('gallery', true);
				data.append('format', 'json');

				$.ajax({
					type: "POST",
					data: data,
					contentType: false,
					cache: false,
					processData:false,
					beforeSend: function() {
						var loader = $('<li class="hu-gallery-item loading" id="'+ gallery_id +'"><div class="progress"><div class="bar"></div></div></li>');
						$('.hu-gallery-items').append(loader);
					},
					success: function(response)
					{

						var data = $.parseJSON(response);

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
							console.log('Uploadress is not supported.');
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
		if (confirm("You are about to delete this item permanently. 'Cancel' to stop, 'OK' to delete.") == true) {
		    var request = {
				'option' : 'com_ajax',
				'helix' : 'ultimate',
				'request' : 'task',
				'action' : 'remove-blog-image',
				'src'	 : $this.parent().data('src'),
				'format' : 'json'
			};

			$.ajax({
				type: "POST",
				data   : request,
				success: function(response)
				{
					var data = $.parseJSON(response);
					if(data.status) {
						$this.parent().remove();

						let images = [];

						$('.hu-gallery-item').each(function( index, value ) {
							images.push( '"' + $(value).data('src') + '"' );
						});

						let output = '{"helix_ultimate_gallery_images":['+ images +']}';
						$('#jform_attribs_helix_ultimate_gallery').val(output);

					} else {
						alert(data.output);
					}
				}
			});
		}
	});

});
