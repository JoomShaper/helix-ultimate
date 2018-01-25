/**
* @package Helix3 Framework
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2015 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/
jQuery(function($) {

	//Web Fonts
	$('.list-font-families').on('change', function(event) {

		event.preventDefault();

		var $that = $(this),
			fontName = $that.val();
		
		var data = { fontName : fontName };

		var request = {
			'action' : 'fontVariants',
			'option' : 'com_ajax',
			'request': 'helixultimate',
			'data'   : data,
			'format' : 'json'
		};

		$.ajax({
			type   : 'POST',
			data   : request,
			success: function (response) {
				var font = $.parseJSON(response);
				$that.closest('.helix-ultimate-field-webfont').find('.list-font-weight').html(font.variants);
				$that.closest('.helix-ultimate-field-webfont').find('.list-font-subset').html(font.subsets);
			}
		});

        //Change Preview
        var font = $that.val().replace(" ", "+");
        $('head').append("<link href='//fonts.googleapis.com/css?family="+ font +"' rel='stylesheet' type='text/css'>");
        $(this).closest('.helix-ultimate-field-webfont').find('.webfont-preview').fadeIn().css('font-family', $(this).val());

        return false;
    });

	//Font Size
	$('.list-font-weight').on('change', function(event) {

		event.preventDefault();

		var variant = $(this).val(),
			weight 	= '', 
			style 	= '', 
        	family 	= $(this).closest('.helix-ultimate-field-webfont').find('.list-font-families').val().replace(" ", "+") + ':' + variant

        if(variant=='regular') {
        	weight 	= 'regular';
        	style 	= '';
        } else if (variant=='italic') {
        	weight 	= 'regular';
        	style 	= 'italic';
        } else {
        	weight = parseInt(variant);
        	style = $(this).val().replace(weight, '');
        }

        $('head').append("<link href='//fonts.googleapis.com/css?family="+ family +"' rel='stylesheet' type='text/css'>");
        $(this).closest('.helix-ultimate-field-webfont').find('.webfont-preview').fadeIn().css({
        	'font-family': $(this).closest('.helix-ultimate-field-webfont').find('.list-font-families').val(),
        	'font-weight': weight,
        	'font-style': style
        });
    });

	//Font Subset
	$('.list-font-subset').on('change', function(event) {

		event.preventDefault();

		var subsets = $(this).val(),
			variant = $(this).closest('.helix-ultimate-field-webfont').find('.list-font-weight').val(),
			weight 	= '', 
			style 	= '', 
        	family 	= $(this).closest('.helix-ultimate-field-webfont').find('.list-font-families').val().replace(" ", "+") + ':' + variant + '&subset=' + subsets

        if(variant=='regular') {
        	weight 	= 'regular';
        	style 	= '';
        } else if (variant=='italic') {
        	weight 	= 'regular';
        	style 	= 'italic';
        } else {
        	weight = parseInt(variant);
        	style = $(this).val().replace(weight, '');
        }

        $('head').append("<link href='//fonts.googleapis.com/css?family="+ family +"' rel='stylesheet' type='text/css'>");

    });

    //Font Size
	$('.webfont-size').on('change', function(event) {

		event.preventDefault();

		var font_size = $(this).val(),
		 	subsets = $(this).closest('.helix-ultimate-field-webfont').find('.list-font-subset').val(),
			variant = $(this).closest('.helix-ultimate-field-webfont').find('.list-font-weight').val(),
			weight 	= '', 
			style 	= '', 
        	family 	= $(this).closest('.helix-ultimate-field-webfont').find('.list-font-families').val().replace(" ", "+") + ':' + variant + '&subset=' + subsets

        if(variant=='regular') {
        	weight 	= 'regular';
        	style 	= '';
        } else if (variant=='italic') {
        	weight 	= 'regular';
        	style 	= 'italic';
        } else {
        	weight = parseInt(variant);
        	style = $(this).val().replace(weight, '');
        }

        $('head').append("<link href='//fonts.googleapis.com/css?family="+ family +"' rel='stylesheet' type='text/css'>");
        $(this).closest('.helix-ultimate-field-webfont').find('.webfont-preview').fadeIn().css({
        	'font-family': $(this).closest('.helix-ultimate-field-webfont').find('.list-font-families').val(),
        	'font-weight': weight,
        	'font-style': style,
        	'font-size': $(this).val() + 'px',
        	'line-height': '1',
        });

    });

    //Update Fonts list
    $('.btn-update-fonts-list').on('click', function(event){
        event.preventDefault();

        var $that   = $(this);
        var request = {
			'action' : 'update-font-list',
            'option' : 'com_ajax',
			'request': 'helixultimate',			
            'data'   : {},
            'format' : 'json'
        };

        $.ajax({
            type   : 'POST',
            data   : request,
            beforeSend: function(){
                $that.prepend('<i class="fa fa-spinner fa-spin"></i> ');
            },
            success: function (response) {
				var data = $.parseJSON(response);
				if (data.status){
					$that.after(data.message);
					$that.find('.fa-spinner').remove();
					$that.next().delay(1000).fadeOut(300, function(){
						$(this).remove();
					});
				}
            }
        });

        return false;

    });


});