/**
* @package Helix Ultimate Framework
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2018 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/
jQuery(function($) {

	$('.helix-image-field').each(function(index, el) {

		var $field = $(el);

		// Upload form
		$field.find('.btn-helix-image-upload').on('click', function(event) {
			event.preventDefault();
			$field.find('.helix-image-upload').click();
		});

		//Upload
		$field.find(".helix-image-upload").on('change', (function(e) {
			e.preventDefault();
			var $this = $(this);
			var file = $(this).prop('files')[0];

			var data = new FormData();
			data.append('option', 'com_ajax');
			data.append('plugin', 'helixultimate');
			data.append('action', 'upload_image');
			data.append('imageonly', false);
			data.append('format', 'json');

			if (file.type.match(/image.*/)) {
				data.append('image', file);

				$.ajax({
					type: "POST",
					data:  data,
					contentType: false,
					cache: false,
					processData:false,
					beforeSend: function() {
						$this.prop('disabled', true);
						$field.find('.btn-helix-image-upload').attr('disabled', 'disabled');
						var loader = $('<div class="helix-image-item-loader"><div class="progress" id="upload-image-progress"><div class="bar"></div></div></div>');
						$field.find('.helix-image-upload-wrapper').addClass('loading').html(loader)
					},
					success: function(response)
					{

						var data = $.parseJSON(response);

						if(data.status) {
							$field.find('.helix-image-upload-wrapper').removeClass('loading').empty().html(data.output);
						} else {
							$field.find('.helix-image-upload-wrapper').removeClass('loading').empty();
							alert(data.output);
						}

						var $image = $field.find('.helix-image-upload-wrapper').find('>img');

						if($image.length) {
							$field.find('.btn-helix-image-upload').addClass('hide');
							$field.find('.btn-helix-image-remove').removeClass('hide');
							$field.find('#jform_attribs_helix_featured_image').val($image.data('src'));
						} else {
							$field.find('.btn-helix-image-upload').removeClass('hide');
							$field.find('.btn-helix-image-remove').addClass('hide');
							$field.find('#jform_attribs_helix_featured_image').val('');
						}

		 				$this.val('');
		 				$this.prop('disabled', false);
		 				$field.find('.btn-helix-image-upload').removeAttr('disabled');

					},
					xhr: function() {
						myXhr = $.ajaxSettings.xhr();
						if(myXhr.upload){
							myXhr.upload.addEventListener('progress', function(evt) {
								$('#upload-image-progress').find('.bar').css('width', Math.floor(evt.loaded / evt.total *100) + '%');
								//$('#upload-image-progress').find('.helix-ultimate-media-upload-percentage').text(Math.floor(evt.loaded / evt.total *100) + '% ');
							}, false);
						} else {
							alert('Uploadress is not supported.');
						}
						return myXhr;
					},
					error: function()
					{
						$field.find('.helix-image-upload-wrapper').empty();
						$this.val('');
					}
				});
			}

			$this.val('');

		}));

	});

	// Delete Image
	$(document).on('click', '.btn-helix-image-remove', function(event) {

		event.preventDefault();

		var $this = $(this);
		var $parent = $this.closest('.helix-image-field');

		if (confirm("You are about to permanently delete this item. 'Cancel' to stop, 'OK' to delete.") == true) {
		    var request = {
				'option' : 'com_ajax',
				'plugin' : 'helixultimate',
				'action' : 'remove_image',
				'src'	   : $parent.find('.sp-image-upload-wrapper').find('>img').data('src'),
				'format' : 'json'
			};

			$.ajax({
				type: "POST",
				data   : request,
				success: function(response)
				{
					var data = $.parseJSON(response);
					if(data.status) {
						$parent.find('.helix-image-upload-wrapper').empty();
						$parent.find('.btn-helix-image-upload').removeClass('hide');
						$parent.find('.btn-helix-image-remove').addClass('hide');
						$parent.find('#jform_attribs_helix_featured_image').val('');

					} else {
						alert(data.output);
					}
				}
			});
		}
	});

});
