/**
* @package Helix3 Framework
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2015 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

jQuery(function($){
    "use strict";
    $('.fieldset-header').on('click',function(e){
        e.preventDefault();

        if( $(this).closest('.fieldset-wrap').hasClass('active') ){
            return;
        }

        $('.fieldset-wrap').removeClass('active');
        $(this).closest('.fieldset-wrap').addClass('active');
        $('#hexli-ult-options').removeClass().addClass('active-fieldset');
    });

    $('.fieldset-toggle-icon').on('click',function(e){
        e.preventDefault();

        $('.fieldset-wrap').removeClass('active');
        $('#hexli-ult-options').removeClass();
    });

    $('.group-header-box').on('click',function(e){
        e.preventDefault();

        if( $(this).closest('.group-wrap').hasClass('active-group') ){
            $(this).closest('.group-wrap').removeClass('active-group');
            return;
        }

        $('.group-wrap').removeClass('active-group')
        $(this).closest('.group-wrap').addClass('active-group');
        
    });

    $('.header-design').on('click',function(e){
        e.preventDefault();

        var $parent = $(this).closest('.header-design-layout');

        $parent.find('.header-design').removeClass('active')
        $(this).addClass('active');

        var styleName = $(this).data('style'),
            filedName = $parent.data('name'),
            filedClass = '.header-design-' + filedName;

        var currentValue = $(filedClass).val();
        if(currentValue == ''){
            var newValue = {
                style : styleName
            }
            $(filedClass).val(JSON.stringify(newValue))
        } else {
            currentValue = JSON.parse(currentValue);
            currentValue.style = styleName;
            $(filedClass).val(JSON.stringify(currentValue))
        }
        
    });

    $('.choose-desinged-header').on('change',function(e){
        var changeValue = e.target.value,
            filedName = $(this).data('name'),
            filedClass = '.header-design-' + filedName;
        
        var currentValue = $(filedClass).val();
        if(currentValue == ''){
            var newValue = {
                header : changeValue
            }
            $(filedClass).val(JSON.stringify(newValue))
        } else {
            currentValue = JSON.parse(currentValue);
            currentValue.header = changeValue;
            $(filedClass).val(JSON.stringify(currentValue))
        }
    });

    $('.tmpl-style-save').on('click',function(e){
        e.preventDefault();

        $('#layout').val( JSON.stringify(getGeneratedLayout()) );

        var tmplID = $(this).data('tmplID'),
            tmplView = $(this).data('tmplView'),
            formData = {},
            data = $('#tmpl-style-form').serializeArray();

        $.each(data,function(key,row){
            formData[row.name] = row.value
        });

        var request = {
            'action': 'save-tmpl-style',
            'option' : 'com_ajax',
            'plugin' : 'helix3',
            'request': 'ajaxHelix',
            'data'   : formData,
            'format' : 'json'
        };

        $.ajax({
            type   : 'POST',
            data   : request,
            beforeSend: function(){
            },
            success: function (response) {
                var data = $.parseJSON(response)

                if(data.status){
                    document.getElementById('theme-preview').contentWindow.location.reload(true);
                }
                console.log(data.message)
            },
            error: function(){
                alert('Somethings wrong, Try again');
            }

        });
    });

    function getGeneratedLayout(){
		var item = [];
		$('#helix-layout-builder').find('.layoutbuilder-section').each(function(index){
			var $row 		= $(this),
				rowIndex 	= index,
				rowObj 		= $row.data();
			delete rowObj.sortableItem;

			var activeLayout 	= $row.find('.column-layout.active'),
				layoutArray 	= activeLayout.data('layout'),
				layout = 12;

			if( layoutArray != 12){
				layout = layoutArray.split(',').join('');
			}

			item[rowIndex] = {
				'type'  	: 'row',
				'layout'	: layout,
				'settings' 	: rowObj,
				'attr'		: []
			};

			// Find Column Elements
			$row.find('.layout-column').each(function(index) {

				var $column 	= $(this),
					colIndex 	= index,
					className 	= $column.attr('class'),
					colObj 		= $column.data();
				delete colObj.sortableItem;

				item[rowIndex].attr[colIndex] = {
					'type' 				: 'sp_col',
					'className' 		: className,
					'settings' 			: colObj
				};

			});
		});

		return item;
	}
});
