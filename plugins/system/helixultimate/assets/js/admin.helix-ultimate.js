/**
* @package Helix3 Framework
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2015 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

jQuery(function($){
    "use strict";

    // Swicther
    $('.radio-group').each(function( index ) {
      $(this).find('input').wrapAll( "<span class='helix-ultimate-switcher switcher' />");
      $('.helix-ultimate-switcher').append('<span class="switch"></span>')
      $(this).find('label').wrapAll( "<span class='switcher-labels' />");

      var inputs = $('.helix-ultimate-switcher').find('input');

      if(inputs.last().is(":checked")) {
        $(this).find('.helix-ultimate-switcher').addClass('active');
        $(this).find('.switcher-labels').find('label').removeClass().last().addClass('active');
      } else {
        $(this).find('.switcher-labels').find('label').removeClass().first().addClass('active');
      }

      $(this).on('click', function(event) {
        $(this).find('.helix-ultimate-switcher').toggleClass('active');
        if($(this).find('.helix-ultimate-switcher').hasClass('active')) {
          inputs.last().prop('checked', true);
          $(this).find('.switcher-labels').find('label').removeClass().last().addClass('active');
        } else {
          inputs.first().prop('checked', true);
          $(this).find('.switcher-labels').find('label').removeClass().first().addClass('active');
        }
      });

    });


    $('.helix-ultimate-fieldset-header-inner').on('click',function(e){
        e.preventDefault();

        if( $(this).closest('.helix-ultimate-fieldset').hasClass('active') ){
            return;
        }

        $('.helix-ultimate-fieldset').removeClass('active');
        $(this).closest('.helix-ultimate-fieldset').addClass('active');
        $('#helix-ultimate-options').removeClass().addClass('active-helix-ultimate-fieldset');
        $('#helix-ultimate').addClass('helix-ultimate-current-fieldset-' + $(this).data('fieldset'));
        $(this).closest('.helix-ultimate-fieldset').find('.helix-ultimate-group-list').find('.helix-ultimate-group-wrap').first().addClass('active-group');
    });

    $('.helix-ultimate-fieldset-toggle-icon').on('click',function(e){
        e.preventDefault();

        $('.helix-ultimate-fieldset').removeClass('active');
        $('#helix-ultimate, #helix-ultimate-options').removeClass();
    });

    $('.helix-ultimate-group-header-box').on('click',function(e){
        e.preventDefault();

        if( $(this).closest('.helix-ultimate-group-wrap').hasClass('active-group') ){
            $(this).closest('.helix-ultimate-group-wrap').removeClass('active-group');
            return;
        }

        $('.helix-ultimate-group-wrap').removeClass('active-group')
        $(this).closest('.helix-ultimate-group-wrap').addClass('active-group');

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

    $('.action-save-template').on('click',function(e){
        e.preventDefault();
        var self = this;

        $('#layout').val( JSON.stringify(getGeneratedLayout()) );

        var tmplID = $(this).data('id'),
            tmplView = $(this).data('view'),
            formData = {},
            data = $('#helix-ultimate-style-form').serializeArray();

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
              $(self).find('.fa').removeClass('fa-save').addClass('fa-spinner fa-spin');
            },
            success: function (response) {
                var data = $.parseJSON(response)

                if(data.status){
                    document.getElementById('helix-ultimate-template-preview').contentWindow.location.reload(true);
                }

                $(self).find('.fa').removeClass('fa-spinner fa-spin').addClass('fa-save');
            },
            error: function(){
                alert('Somethings wrong, Try again');
            }

        });
    });

    function getGeneratedLayout(){
		var item = [];
		$('#helix-ultimate-layout-builder').find('.helix-ultimate-layout-section').each(function(index){
			var $row 		= $(this),
				rowIndex 	= index,
				rowObj 		= $row.data();
			delete rowObj.sortableItem;

			var activeLayout 	= $row.find('.helix-ultimate-column-layout.active'),
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
			$row.find('.helix-ultimate-layout-column').each(function(index) {

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
