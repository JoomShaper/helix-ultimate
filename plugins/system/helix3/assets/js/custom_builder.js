/**
 * @package Helix3 Framework
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2015 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

var layoutStore = [];

jQuery(function($) {

    $.helix4AdminLayoutMain = function() {
        var layout_add_row_class 	= '.layout-add-row';
        var row_delete_btn_class 	= '.row_delete_btn';
        var builder_section_class	= '.builder-section';
        var builder_section	= $(builder_section_class);
        var builder_section_row_class	= '.builder-section-row';
        var builder_section_row 	= $('.builder-section-row');
        var helix4_layout_builder 	= $('.helix4-layout-builder');
        var helix4_document 		= $(document);

        var add_row_within_row 		= '.add_row_within_row';
        var builder_col_class           = '.layout-column';
        var builder_col                 = $(builder_col_class);

        var helix4_column_layout_class 	= '.helix4-column-layout';
        var col_module_set_class 	= '.col-module-set';
        var helix4_row_settings 	= '.helix4-row-setitngs';

        var helix4_save_settings_id = '#helix4-save-settings';
        var switch_header_class = '.switch-header';
        var switch_header = $(switch_header_class);

        //Getting inner html of row settings
        var helix4_row_settings_html = $('#helix4LayoutColSettings').html();


        //Modal
        var layout_model                = $('#helix4-layout-modal');
        var layout_model_title          = layout_model.find('.sp-modal-title');
        var layout_model_save_settings  = layout_model.find(helix4_save_settings_id);

        $layout_section_row = '<div class="builder-section"> <div class="builder-section-header"> <a href="javascript:;" class="helix-4-row-move"><i class="fa fa-arrows"></i> </a> '+helix4_row_settings_html+' </div>  <div class="builder-section-row' +
            ' row-fluid"> <div class="layout-column col-sm-12"><a class="col-module-set pull-right" href="#">  <i class="fa fa-gears"></i> </a> <a href="javascript:;" class="helixfw_col_short pull-right"> <i class="fa fa-arrows"></i> </a> <a href="javascript:;" class="add_row_within_row pull-right"><i class="fa fa-plus-square-o"></i>  </a> <h6' +
            ' class="helix4-col-title pull-left">None</h6>' +
            '  </div>  </div> </div>';

        $layout_section_replace_col = '<div class="layout-column replace_class"><a class="col-module-set pull-right" href="#">  <i class="fa fa-gears"></i> </a> <a href="javascript:;" class="helixfw_col_short pull-right"> <i class="fa fa-arrows"></i> </a> <a href="javascript:;" class="add_row_within_row pull-right"><i class="fa fa-plus-square-o"></i>  </a> <h6' +
            ' class="helix4-col-title pull-left">None</h6> </div>';

        helix4_document.on('click', layout_add_row_class, function(){
            helix4_layout_builder.append($layout_section_row);
            builder_row_col_sortable();
        });

        /**
         * Delete row
         */
        helix4_document.on('click', row_delete_btn_class, function (e) {
            e.preventDefault();

            if ($(this).closest(builder_col_class).length > 0){
                $(this).closest(builder_col_class).find(add_row_within_row).first().show();
            }
            $(this).closest(builder_section_class).remove();
        });

        /**
         * Add nestable row within column,
         * Nestable for next one level
         */
        helix4_document.on('click', add_row_within_row, function (e) {
            e.preventDefault();

            if ($(this).parents(builder_col_class).length < 2){
                $(this).closest(builder_col_class).append($layout_section_row);
                $(this).closest(builder_col_class).find(add_row_within_row).hide();
            }
        });

        function builder_row_col_sortable() {
            helix4_layout_builder.sortable({
                placeholder: "helix4-row-placeholder",
                handle: ".helix-4-row-move"
            }).disableSelection();

            builder_section_row.sortable({
                start: function(e,ui){
                    ui.placeholder.height(ui.item.height());
                },
                //placeholder: "helix4-col-placeholder",
                handle: ".helixfw_col_short"
            }).disableSelection();
        }
        builder_row_col_sortable();

        var helixfw_header_block = $('.helixfw-header-block');

        helixfw_header_block.sortable({
            start: function(e,ui){
                ui.placeholder.height(ui.item.height());
                ui.placeholder.width(ui.item.width());
            },
            placeholder: 'placeholder'
            //handle: ".helixfw_col_short"

        }).disableSelection();

        /**
         * Add or re arrange column
         */
        helix4_document.on('click', helix4_column_layout_class, function (e) {
            e.preventDefault();
            $selector = $(this);
            var new_layout_data = $selector.data('layout');
           
            $(helix4_column_layout_class).removeClass('active');
            $selector.addClass('active');

            if (new_layout_data != 12){
                var new_layout_data_array = new_layout_data.split(',');
                var builder_col_last_index = $selector.closest(builder_section_class).find('>'+builder_section_row_class+'>'+builder_col_class).length - 1;

                for (var i = 0; i < new_layout_data_array.length; i++){
                    //console.log(i+'layout');
                    $selector.closest(builder_section_class).find('>'+builder_section_row_class+'>'+builder_col_class).each(function(index){
                        //console.log(index+'+');
                        if (index === i){
                            var col_span = $(this).attr('class').match(/col-sm-(\d+)\b/)[1];
                            $(this).removeClass('col-sm-'+col_span).addClass('col-sm-'+new_layout_data_array[i]);
                        }
                    });

                    //Append additional blank column
                    if (i > builder_col_last_index){
                        $selector.closest(builder_section_class).find('>'+builder_section_row_class).append($layout_section_replace_col.replace('replace_class', 'col-sm-'+new_layout_data_array[i]));
                    }
                }
            }

            $selector.closest(builder_section_class).find('>'+builder_section_row_class+'>'+builder_col_class).each(function(index){
                if (new_layout_data != 12) {
                    if (index > new_layout_data_array.length - 1) {
                        $(this).remove();
                    }
                }else{
                    if (index > 0){
                        $(this).remove();
                    }else{
                        var col_span = $(this).attr('class').match(/col-sm-(\d+)\b/)[1];
                        $(this).removeClass('col-sm-'+col_span).addClass('col-sm-12');
                    }
                }
            });
        });

        helix4_document.on('click', col_module_set_class, function (e) {
            e.preventDefault();

            builder_col.removeClass('column-active');
            $parent = $(this).closest(builder_col_class);
            $parent.addClass('column-active');

            layout_model.find('.sp-modal-body').empty();
            layout_model_title.text('Helix4 Column Settings');
            //layout_model_save_settings.data('flag', 'col-setting');
            layout_model_save_settings.attr('data-flag', 'col-setting');

            var $clone = $('.column-settings').clone(true);
            $clone.find('.sppb-color').each(function(){
                $(this).addClass('minicolors');
            });

            $clone = layout_model.find('.sp-modal-body').append( $clone );
            var comFlug = false;
            $clone.find('.addon-input').each(function(){
                var $that = $(this),
                    $attrname = $that.data('attrname'),
                    attrValue = $parent.data($attrname);

                if ( $attrname == 'column_type' && attrValue == '1' ) {
                    comFlug = true;
                }else if($attrname == 'name' && comFlug == true){
                    $that.closest('.form-group').slideUp('fast');
                }

                $that.setInputValue({filed: attrValue});
            });

            $clone.initColorPicker();

            $clone.find('select').chosen({
                allow_single_deselect: true
            });

            layout_model.randomIds();
            layout_model.spmodal();
        });



        helix4_document.on('click', helix4_row_settings, function(event){
            event.preventDefault();

            builder_section.removeClass('row-active');
            $parent = $(this).closest(builder_section_class);
            $parent.addClass('row-active');

            layout_model.find('.sp-modal-body').empty();
            layout_model_title.text('Helix4 Row Settings');
            layout_model_save_settings.attr('data-flag', 'row-setting');

            var $clone = $('.row-settings').clone(true);
            $clone.find('.sppb-color').each(function(){
                $(this).addClass('minicolors');
            });

            $clone = layout_model.find('.sp-modal-body').append( $clone );

            $clone.find('.addon-input').each(function(){
                var $that = $(this),
                    attrValue = $parent.data( $that.data('attrname'));
                $that.setInputValue({filed: attrValue});
            });

            $clone.initColorPicker();

            layout_model.randomIds();

            $clone.find('select').chosen({
                allow_single_deselect: true
            });

            layout_model.spmodal();
        });


        helix4_document.on('click', helix4_save_settings_id, function(event) {
            event.preventDefault();

            var layout_model = $('#helix4-layout-modal');
            var flag = $(this).attr('data-flag');

            switch(flag){
                case 'row-setting':
                    layout_model.find('.addon-input').each(function(){
                        var $this = $(this),
                            $parent = $('.row-active'),
                            $attrname = $this.data('attrname');
                        $parent.removeData( $attrname );

                        if ($attrname == 'name') {
                            var nameVal = $this.val();

                            if (nameVal  !='' || $this.val() != null) {
                                $('.row-active .section-title').text($this.val());
                            }else{
                                $('.row-active .section-title').text('Section Header');
                            }
                        }

                        $parent.attr('data-' + $attrname, $this.getInputValue());
                    });
                    break;

                case 'col-setting':
                    var component = false;
                    layout_model.find('.addon-input').each(function(){

                        var $this = $(this),
                            $parent = $('.column-active'),
                            $attrname = $this.data('attrname');
                        $parent.removeData( $attrname );

                        var dataVal = $this.val();

                        if ( $attrname == 'column_type' && $(this).attr("checked") ) {
                            component = true;
                            $('.column-active .helix4-col-title').text('Component');
                        }else if( $attrname == 'name' && component != true ) {
                            if (dataVal == '' || dataVal == undefined) {
                                dataVal = 'none';
                            }
                            $('.column-active .helix4-col-title').text(dataVal);
                        }

                        $parent.attr('data-' + $attrname, $this.getInputValue());
                    });
                    break;

                case 'save-layout':
                    var layoutName = layout_model.find('.addon-input').val(),
                        data = {
                            action : 'save',
                            layoutName : layoutName,
                            content: JSON.stringify(getGeneratedLayout())
                        };

                    if (layoutName =='' || layoutName ==' ') {
                        alert("Without Name Layout Can't be save");
                        return false;
                    }

                    var request = {
                        'option' : 'com_ajax',
                        'plugin' : 'helix3',
                        'data'   : data,
                        'format' : 'json'
                    };

                    $.ajax({
                        type   : 'POST',
                        data   : request,
                        beforeSend: function(){
                        },
                        success: function (response) {
                            var data = $.parseJSON(response.data),
                                layouts = data.layout,
                                tplHtml = '';

                            $('#jform_params_layoutlist').find('option').remove();
                            if (layouts.length) {
                                for (var i = 0; i < layouts.length; i++) {
                                    tplHtml += '<option value="'+ layouts[i] +'">'+ layouts[i].replace('.json','')+'</option>';
                                }

                                $('#jform_params_layoutlist').html(tplHtml);
                            }
                        },
                        error: function(){
                            alert('Somethings wrong, Try again');
                        }

                    });
                    break;
                case 'header-settings-button':

                    layout_model.find('.addon-input').each(function(){
                        var $this = $(this),
                            $parent = $('.helix-4-header-area'),
                            $attrname = $this.data('attrname');
                        $parent.removeData( $attrname );
                        $parent.attr('data-' + $attrname, $this.getInputValue());
                    });

                    var background_color = layout_model.find('[data-attrname="background_color"]');

                    //alert(background_color.val());
                    $('.helix-4-header-area').css('background-color', background_color.val());


                    var background_image = layout_model.find('[data-attrname="background_image"]');
                    if (background_image.length ){
                        var image_src = background_image.val();
                        if ( image_src ){
                            var image_html = '<img src="'+layoutbuilder_base+image_src+'" />';
                            $('a.helix-4-logo').html(image_html);
                        }
                    }

                /*    var menu_links = JSON.parse(layout_model.find('[data-attrname="header_link"]').val());
                    if (menu_links.length){
                        var output = '<ul>';
                        for (i=0; i < menu_links.length; i++ ){
                            output += '<li><a href="'+menu_links[i].url+'">'+menu_links[i].title+'</a> </li>';
                        }
                        output += '<ul>';

                        $('.helixfwHeaderMenuUl').html(output);
                    }*/

                    break;


                case 'footer-settings-button':
                    layout_model.find('.addon-input').each(function(){
                        var $this = $(this),
                            $parent = $('.helixfw-footer-wrap'),
                            $attrname = $this.data('attrname');
                        $parent.removeData( $attrname );
                        $parent.attr('data-' + $attrname, $this.getInputValue());
                    });

                    var color = layout_model.find('[data-attrname="color"]');
                    $('.helix-4-footer-area').css('background-color', color.val());

                    var footer_logo = layout_model.find('[data-attrname="footer_logo"]');
                    if (footer_logo.length ){
                        var image_src = footer_logo.val();
                        if ( image_src ){
                            var image_html = '<img src="'+layoutbuilder_base+image_src+'" />';
                            $('a.footer-logo').html(image_html);
                        }
                    }
                    break;

                /*case 'header-footer-setting':
                    var footer_logo = layout_model.find('[data-attrname="footer_logo"]');
                    if (footer_logo.length ){
                        var image_src = footer_logo.val();
                        if ( image_src ){
                            var image_html = '<img src="'+layoutbuilder_base+image_src+'" />';
                            $('.footerLogoWrap').html(image_html);
                        }
                    }

                    var footer_about_us = layout_model.find('[data-attrname="footer_about_us"]');
                    $('.footerAboutUsWrap').html('<p>'+footer_about_us.val()+'</p>');

                    var social_links = JSON.parse(layout_model.find('[data-attrname="footer_social_links"]').val());
                    if (social_links.length){
                        var output = '<ul>';
                        for (i=0; i < social_links.length; i++ ){
                            output += '<li><a href="'+social_links[i].url+'"><i class="fa fa-'+social_links[i].title+'"></i></a> </li>';
                        }
                        output += '<ul>';

                        $('.footerSocialIconWrap').html(output);
                    }

                    var footer_links_one = JSON.parse(layout_model.find('[data-attrname="footer_links_one"]').val());
                    if (footer_links_one.length){
                        var output = '<h4>'+layout_model.find('[data-attrname="footer_links_one_heading"]').val()+'</h4>';
                        output += '<ul>';
                        for (i=0; i < footer_links_one.length; i++ ){
                            output += '<li><a href="'+footer_links_one[i].url+'">'+footer_links_one[i].title+'</a> </li>';
                        }
                        output += '<ul>';
                        $('.footer_links_one').html(output);
                    }


                    var footer_links_two = JSON.parse(layout_model.find('[data-attrname="footer_links_two"]').val());
                    if (footer_links_two.length){
                        var output = '<h4>'+layout_model.find('[data-attrname="footer_links_two_heading"]').val()+'</h4>';
                        output += '<ul>';
                        for (i=0; i < footer_links_two.length; i++ ){
                            output += '<li><a href="'+footer_links_two[i].url+'">'+footer_links_two[i].title+'</a> </li>';
                        }
                        output += '<ul>';
                        $('.footer_links_two').html(output);
                    }


                    var footer_links_three = JSON.parse(layout_model.find('[data-attrname="footer_links_three"]').val());
                    if (footer_links_three.length){
                        var output = '<h4>'+layout_model.find('[data-attrname="footer_links_three_heading"]').val()+'</h4>';
                        output += '<ul>';
                        for (i=0; i < footer_links_three.length; i++ ){
                            output += '<li><a href="'+footer_links_three[i].url+'">'+footer_links_three[i].title+'</a> </li>';
                        }
                        output += '<ul>';
                        $('.footer_links_three').html(output);
                    }

                    break;

                case 'header-logo-block':
                case 'header-menu-block':
                case 'footer-about-us-social-block':
                case 'footer-block-2-block':
                case 'footer-block-3-block':
                case 'footer-block-4-block':
                case 'header2-block3':
                    var selected_module = layout_model.find('[data-attrname="helixfw_module_list"]').val();
                    if (selected_module){
                        var request_action = flag.replace(/-/g, '_')+'_load';
                        var data = {
                            selected_module : selected_module,
                            action : request_action
                        };
                        var request = {
                            'option' : 'com_ajax',
                            'plugin' : 'helix3',
                            'data'   : data,
                            'format' : 'json'
                        };

                        $.ajax({
                            type: 'POST',
                            data: request,
                            beforeSend: function () {
                            },
                            success: function (response) {
                                response_data = JSON.parse(response);
                                if (response_data.success){
                                    $('.'+flag+'-wrap').html(response_data.data);
                                }
                            },
                            error: function () {
                                alert('Somethings wrong, Try again');
                            }
                        });
                    }

                    break;*/
                default:
                    alert('You are doing somethings wrongs. Try again');
            }
        });


        function helix4getGeneratedLayout(){
            var item = [];

            //$('#helix-layout-builder').find('.layoutbuilder-section').each(function(index){

            $('.helix4-layout-builder').find('> .builder-section').each(function(index){
                var $row 		= $(this),
                    rowIndex 	= index,
                    rowObj 		= $row.data();
                delete rowObj.sortableItem;

                item[rowIndex] = {
                    'type'  	: 'row',
                    'settings' 	: rowObj,
                    'attr'		: []
                };

                // Find Column Elements
                $row.find('> .builder-section-row > .layout-column').each(function(index) {

                    var $column 	= $(this),
                        colIndex 	= index,
                        className 	= $column.attr('class'),
                        colObj 		= $column.data();
                    delete colObj.sortableItem;

                    item[rowIndex].attr[colIndex] = {
                        'type' 				: 'sp_col',
                        'className' 		: className,
                        'settings' 			: colObj,
                        'attr'              : []
                    };

                    //Nested Row within Column


                    //console.log('col = '+index+', '+$column.find('> .builder-section').length);

                    $column.find('> .builder-section').each(function(index){
                        var $nestedRow 	    = $(this),
                            nestedRowIndex 	= index,
                            nestedRowObj    = $nestedRow.data();
                        delete nestedRowObj.sortableItem;

                        item[rowIndex].attr[colIndex].attr[nestedRowIndex] = {
                            'type'  	: 'row',
                            'settings' 	: nestedRowObj,
                            'attr'		: []
                        };

                        // Find Column Elements
                        $nestedRow.find('> .builder-section-row > .layout-column').each(function(index) {
                            var $nestedColumn 	= $(this),
                                nestedColIndex 	= index,
                                className 	= $nestedColumn.attr('class'),
                                nestedColObj 		= $nestedColumn.data();
                            delete nestedColObj.sortableItem;

                            item[rowIndex].attr[colIndex].attr[nestedRowIndex].attr[nestedColIndex] = {
                                'type' 				: 'sp_col',
                                'className' 		: className,
                                'settings' 			: nestedColObj
                            };

                        });
                    });


                    //End nested Row
                });


            });



            var header_footer_item = { 'header' : [], 'footer' : [] };

            $('.helix-4-header-area').each(function(index){
                var $row 		= $(this),
                    rowIndex 	= index,
                    headerRowObj 		= $row.data();
                delete headerRowObj.sortableItem;

                header_footer_item.header[rowIndex] = {
                    'settings' 	: headerRowObj
                };
            });

            $('.helixfw-footer-wrap').each(function(index){
                var $row 		= $(this),
                    rowIndex 	= index,
                    footerRowObj 		= $row.data();
                delete footerRowObj.sortableItem;

                header_footer_item.footer[rowIndex] = {
                    'settings' 	: footerRowObj
                };
            });

            item.push(header_footer_item);
            //console.log(JSON.stringify(item));

            return item;
        }

        helix4_document.on('click', '.live-header-settings-btn', function(event){
            event.preventDefault();

            layout_model.find('.sp-modal-body').empty();
            layout_model_title.text('Header Settings');
            layout_model_save_settings.attr('data-flag', 'header-settings-button');


            //Parent Div to implement settings from the pop up modal
            $parent = $('.helix-4-header-area');

            var $clone = $('.live-header-settings').clone(true);
            $clone.find('.sppb-color').each(function(){
                $(this).addClass('minicolors');
            });

            $clone = layout_model.find('.sp-modal-body').append( $clone );


            $clone.find('.addon-input').each(function(){
                var $that = $(this),
                    attrValue = $parent.data( $that.data('attrname'));
                $that.setInputValue({filed: attrValue});
            });

            $clone.initColorPicker();

            layout_model.randomIds();

            $clone.find('select').chosen({
                allow_single_deselect: true
            });

            layout_model.spmodal();
        });


        helix4_document.on('click', '.live-footer-settings-btn', function(event){
            event.preventDefault();

            layout_model.find('.sp-modal-body').empty();
            layout_model_title.text('Footer Settings');
            layout_model_save_settings.attr('data-flag', 'footer-settings-button');


            //Parent Div to implement settings from the pop up modal
            $parent = $('.helix-4-footer-area');

            var $clone = $('.helixfw-footer-settings').clone(true);
            $clone.find('.sppb-color').each(function(){
                $(this).addClass('minicolors');
            });

            $clone = layout_model.find('.sp-modal-body').append( $clone );


            $clone.find('.addon-input').each(function(){
                var $that = $(this),
                    attrValue = $parent.data( $that.data('attrname'));
                $that.setInputValue({filed: attrValue});
            });

            $clone.initColorPicker();

            layout_model.randomIds();

            $clone.find('select').chosen({
                allow_single_deselect: true
            });

            layout_model.spmodal();
        });



        //live-footer-settings-btn
        helix4_document.on('click', '.helixfwFooterOption', function(event){
            event.preventDefault();

            layout_model.find('.sp-modal-body').empty();
            layout_model_title.text('Helix4 Footer Settings');
            layout_model_save_settings.attr('data-flag', 'header-footer-setting');

            var $clone = $('.helixfw-footer-settings').clone(true);
            $clone.find('.sppb-color').each(function(){
                $(this).addClass('minicolors');
            });

            $clone = layout_model.find('.sp-modal-body').append( $clone );

            $clone.initColorPicker();
            layout_model.randomIds();

            layout_model.spmodal();
        });

/*

        helix4_document.on('click', '.helixfwLogoOptionLogo', function(event){
            event.preventDefault();

            layout_model.find('.sp-modal-body').empty();
            layout_model_title.text('Helix4 Header Logo');
            layout_model_save_settings.attr('data-flag', 'header-logo-block');

            var $clone = $('.helixfw-logo-block-settings').clone(true);
            $clone.find('.sppb-color').each(function(){
                $(this).addClass('minicolors');
            });

            $clone = layout_model.find('.sp-modal-body').append( $clone );

            $clone.initColorPicker();
            layout_model.randomIds();

            layout_model.spmodal();
        });



        helix4_document.on('click', '.helixfwMenuOptionLogo', function(event){
            event.preventDefault();

            layout_model.find('.sp-modal-body').empty();
            layout_model_title.text('Helix4 Header Logo');
            layout_model_save_settings.attr('data-flag', 'header-menu-block');

            var $clone = $('.helixfw-logo-block-settings').clone(true);
            $clone.find('.sppb-color').each(function(){
                $(this).addClass('minicolors');
            });

            $clone = layout_model.find('.sp-modal-body').append( $clone );

            $clone.initColorPicker();
            layout_model.randomIds();

            layout_model.spmodal();
        });


        helix4_document.on('click', '.helixfwFooterAboutUsSocialOption', function(event){
            event.preventDefault();

            layout_model.find('.sp-modal-body').empty();
            layout_model_title.text('Helix4 Header Logo');
            layout_model_save_settings.attr('data-flag', 'footer-about-us-social-block');

            var $clone = $('.helixfw-logo-block-settings').clone(true);
            $clone.find('.sppb-color').each(function(){
                $(this).addClass('minicolors');
            });

            $clone = layout_model.find('.sp-modal-body').append( $clone );

            $clone.initColorPicker();
            layout_model.randomIds();

            layout_model.spmodal();
        });

        helix4_document.on('click', '.footerBlock2', function(event){
            event.preventDefault();

            layout_model.find('.sp-modal-body').empty();
            layout_model_title.text('Helix4 Header Logo');
            layout_model_save_settings.attr('data-flag', 'footer-block-2-block');

            var $clone = $('.helixfw-logo-block-settings').clone(true);
            $clone.find('.sppb-color').each(function(){
                $(this).addClass('minicolors');
            });

            $clone = layout_model.find('.sp-modal-body').append( $clone );

            $clone.initColorPicker();
            layout_model.randomIds();

            layout_model.spmodal();
        });

        helix4_document.on('click', '.footerBlock3', function(event){
            event.preventDefault();

            layout_model.find('.sp-modal-body').empty();
            layout_model_title.text('Helix4 Header Logo');
            layout_model_save_settings.attr('data-flag', 'footer-block-3-block');

            var $clone = $('.helixfw-logo-block-settings').clone(true);
            $clone.find('.sppb-color').each(function(){
                $(this).addClass('minicolors');
            });

            $clone = layout_model.find('.sp-modal-body').append( $clone );

            $clone.initColorPicker();
            layout_model.randomIds();

            layout_model.spmodal();
        });

        helix4_document.on('click', '.footerBlock4', function(event){
            event.preventDefault();

            layout_model.find('.sp-modal-body').empty();
            layout_model_title.text('Helix4 Header Logo');
            layout_model_save_settings.attr('data-flag', 'footer-block-4-block');

            var $clone = $('.helixfw-logo-block-settings').clone(true);
            $clone.find('.sppb-color').each(function(){
                $(this).addClass('minicolors');
            });

            $clone = layout_model.find('.sp-modal-body').append( $clone );

            $clone.initColorPicker();
            layout_model.randomIds();

            layout_model.spmodal();
        });

        helix4_document.on('click', '.block3Selector', function(event){
            event.preventDefault();
            layout_model.find('.sp-modal-body').empty();
            layout_model_title.text('Helix4 Header Logo');
            layout_model_save_settings.attr('data-flag', 'header2-block3');

            var $clone = $('.helixfw-logo-block-settings').clone(true);
            $clone.find('.sppb-color').each(function(){
                $(this).addClass('minicolors');
            });

            $clone = layout_model.find('.sp-modal-body').append( $clone );
            $clone.initColorPicker();
            layout_model.randomIds();
            layout_model.spmodal();
        });
*/


        $(document).on('hover', builder_section_class, function (e) {
            e.preventDefault();
            $(this).find('.layout-add-row').toggle();
        });

        $(document).on('click', '#testLayout', function (e) {
            e.preventDefault();
            console.log(JSON.stringify(helix4getGeneratedLayout()));
        });

        // document.adminForm.onsubmit = function(event){
        //     //Webfonts
        //     $('.webfont').each(function(){
        //         var $that = $(this),
        //             webfont = {
        //                 'fontFamily' : $that.find('.list-font-families').val(),
        //                 'fontWeight' : $that.find('.list-font-weight').val(),
        //                 'fontSubset' : $that.find('.list-font-subset').val(),
        //                 'fontSize'	: $that.find('.webfont-size').val()
        //             };

        //         $that.find('.input-webfont').val( JSON.stringify(webfont) )

        //     });
        //     //Generate Layout
        //     $('#jform_params_layout').val( JSON.stringify(helix4getGeneratedLayout()) );
        // };


        $(document).on('change', switch_header_class, function (e) {
            e.preventDefault();
            var header = $(this).val();
            $('.helixfw-header-block').hide();
            $('.'+header).show();
            var data = {
                selected_header : header,
                action : 'helixfw_save_option'
            };
            var request = {
                'option' : 'com_ajax',
                'plugin' : 'helix3',
                'data'   : data,
                'format' : 'json'
            };

            $.ajax({
                type: 'POST',
                data: request,
                beforeSend: function () {
                },
                success: function (response) {
                    //
                },
                error: function () {
                    alert('Somethings wrong, Try again');
                }
            });
        });

        $(document).on('change', '.switch-footer', function (e) {
            e.preventDefault();
            var footer = $(this).val();
            $('.helixfw-footer-block').hide();
            $('.'+footer).show();
            var data = {
                selected_footer : footer,
                action : 'helixfw_switch_footer'
            };
            var request = {
                'option' : 'com_ajax',
                'plugin' : 'helix3',
                'data'   : data,
                'format' : 'json'
            };

            $.ajax({
                type: 'POST',
                data: request,
                beforeSend: function () {
                },
                success: function (response) {
                    //
                },
                error: function () {
                    alert('Somethings wrong, Try again');
                }
            });
        });



        $(document).on('change', '.switch-helixfw-header', function (e) {
            e.preventDefault();
            var header = $(this).val();

            var data = {
                selected_header : header,
                action : 'helixfw_switch_header'
            };
            var request = {
                'option' : 'com_ajax',
                'plugin' : 'helix3',
                'data'   : data,
                'format' : 'json'
            };

            $.ajax({
                type: 'POST',
                data: request,
                beforeSend: function () {
                    //
                },
                success: function (response) {
                    $('#helixfw-live-header-wrap').html(response);
                    //
                },
                error: function () {
                    alert('Somethings wrong, Try again');
                }
            });
        });


        $(document).on('change', '.switch-helixfw-footer', function (e) {
            e.preventDefault();
            var footer = $(this).val();

            var data = {
                selected_footer : footer,
                action : 'helixfw_switch_footer'
            };
            var request = {
                'option' : 'com_ajax',
                'plugin' : 'helix3',
                'data'   : data,
                'format' : 'json'
            };

            $.ajax({
                type: 'POST',
                data: request,
                beforeSend: function () {
                    //
                },
                success: function (response) {
                    $('#helixfw-live-footer-wrap').html(response);
                    //
                },
                error: function () {
                    alert('Somethings wrong, Try again');
                }
            });
        });

    };


    $.helix4AdminLayoutMain();

    


});

