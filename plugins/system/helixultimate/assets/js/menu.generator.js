/**
 * @package Helix3 Framework
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2015 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

jQuery(function($) {
    $('#attrib-spmegamenu').find('.control-group').first().find('.control-label').remove();
    $('#attrib-spmegamenu').find('.control-group').first().find('>.controls').removeClass().addClass('megamenu').unwrap();

    // Menu Width
    $(document).on('change paste','#menuWidth', function(){
        var width = $(this).val();
        if(width >= 200){
            $('#hfwmm-layout').data('width',width);
        }else{
            alert("Width can't be less than 200 Pixels");
            $(this).val($('#hfwmm-layout').data('width'));
        }
    });

    // Mega menu alignment
    $('.action-bar').on('click','.alignment',function(event){
        event.preventDefault();

        var $that = $(this);
        $('.alignment').removeClass('active');
        $that.addClass('active');
        $('#hfwmm-layout').data('menu_align',$(this).data('al_flag'));
    });

    /**
     * Saving menu layout
     */
    document.adminForm.onsubmit = function(event) {
        //event.preventDefault();
        var layout = [];

        $('#hfwmm-layout').find('.hfwmm-row').each(function(index){
            var $row = $(this),
                rowIndex = index;
            layout[rowIndex] = {
                'type'      : 'row',
                'attr'      : []
            };

            // Get each column data;
            $row.find('.hfwmm-col').each(function(index) {
                var $column = $(this),
                    colIndex = index,
                    colGrid = $column.attr('data-grid');

                layout[rowIndex].attr[colIndex] = {
                    'type'          : 'column',
                    'colGrid'       : colGrid,
                    'menuParentId'  : '',
                    'moduleId'      : '',
                    'items'         : []
                };

                // get current child id
                var menuParentId = '';

                $column.find('h4').each(function(index, el) {
                    menuParentId += $(this).data('current_child')+',';
                });

                if (menuParentId) {
                    menuParentId = menuParentId.slice(',',-1);
                    layout[rowIndex].attr[colIndex].menuParentId = menuParentId;
                }

                // get modules id
                var moduleId = '';
                $column.find('.widget').each(function(index, el) {
                    moduleId += $(this).data('mod_id')+',';

                    var type = $(this).data('type');
                    var item_id = $(this).data('mod_id');

                    layout[rowIndex].attr[colIndex].items[index] = { 'type': type, 'item_id' : item_id };
                });

                if(moduleId){
                    moduleId = moduleId.slice(',',-1);
                    layout[rowIndex].attr[colIndex].moduleId = moduleId;
                }
            });

        });

        var initData = $('#hfwmm-layout').data();
        var menumData = {
            'width'         : initData.width,
            'menuItem'      : initData.menu_item,
            'menuAlign'     : initData.menu_align,
            'layout'        : layout
        };

        //console.log(JSON.stringify(layout));
        $('#jform_params_megamenu').val(1);
        $('#jform_params_menulayout').val( JSON.stringify(menumData) );
    };


    /**
     * New Megamenu Layout Started
     * @since helix.v.4
     * @date Aug 02, 17
     */

    $(document).on('click', 'button#choose_layout', function(e){
        e.preventDefault();
        $('#hfwmm-layout-modal').toggle();
    });

    $(document).on('click', '.menu-layout-list li a', function(e) {
        e.preventDefault();
        var data_layout = $(this).attr('data-layout');

        var layout_row_tpl = '<div class="hfwmm-row"> <div class="hfwmm-row-actions"> <p class="hfwmm-row-left hfwmmRowSortingIcon"> <i class="fa fa-sort"></i> Row  </p> <p class="hfwmm-row-right"> <span class="hfwmmRowDeleteIcon"><i class="fa fa-trash-o"></i> </span> </p> <div class="clearfix"></div> </div>';

        var layout_col_tpl = '<div class="hfwmm-col hfwmm-col-replace_col_number" data-grid="replace_grid"> <div' +
            ' class="hfwmm-item-wrap"> <div class="hfwmm-column-actions"> <span class="hfwmmColSortingIcon"><i class="fa fa-arrows"></i> Column</span> </div> </div> </div>';

        var appending_col = '';
        if (data_layout != 12){
            var col_layout_data = data_layout.split(',');
            for (i=0; i<col_layout_data.length; i++){
                appending_col += layout_col_tpl.replace('replace_col_number', col_layout_data[i]).replace('replace_grid', col_layout_data[i]);
            }
        }else{
            appending_col += layout_col_tpl.replace('replace_col_number', 12).replace('replace_grid', 12);
        }
        layout_row_tpl+= appending_col;
        layout_row_tpl+= '</div>';

        $('#hfwmm-layout').append(layout_row_tpl);
        $(this).closest('#hfwmm-layout-modal').hide();

        hfwmm_sorting_init();
    });

    function hfwmm_sorting_init() {
        $(".hfwmm-item-wrap").sortable({
            connectWith: ".hfwmm-item-wrap",
            items: " .widget",
            placeholder: "drop-highlight",
            start: function(e,ui){
                ui.placeholder.height(ui.item.height());
            },
            stop: function(e,ui){
                //hfwmm_sorting_init();
            }
        }).disableSelection();

        /**
         * Drag from module lists
         */
        $(".modules-list").sortable({
            connectWith: ".hfwmm-item-wrap",
            items: " .draggable-module",
            placeholder: "drop-highlight",
            helper: "clone",
            start: function(e,ui){
                ui.placeholder.height(ui.item.height());
            },
            update: function (e, ui) {
                var module_title = ui.item.text();
                var module_inner = '<div class="widget-top"> <div class="widget-title"> <h3>'+module_title+'</h3> </div> </div>';
                ui.item.removeAttr('style class').addClass('widget').html(module_inner);

                ui.item.clone().insertAfter(ui.item.html(module_title+'<i class="fa fa-arrows"></i>').removeAttr('class').addClass('draggable-module'));
                $(this).sortable('cancel');

                hfwmm_sorting_init();
            }
        }).disableSelection();

        $('.hfwmm-row').sortable({
            start: function(e,ui){
                ui.placeholder.height(ui.item.height());
                ui.placeholder.width(ui.item.width() - 50);
            },
            items: '.hfwmm-col',
            handle: '.hfwmmColSortingIcon',
            placeholder: "drop-col-highlight",
            stop: function(e,ui){
                //hfwmm_sorting_init();
            }
        });

        $('#hfwmm-layout').sortable({
            start: function(e,ui){
                ui.placeholder.height(ui.item.height());
                ui.placeholder.width(ui.item.width() - 50);
            },
            items: '.hfwmm-row',
            handle: '.hfwmmRowSortingIcon',
            placeholder: "drop-highlight",
            stop: function(e,ui){
                //hfwmm_sorting_init();
            }
        });

        $(document).on('click', '.text-warning', function (e) {
            e.preventDefault();
            $(this).closest('.widget').remove();
        })

    }
    hfwmm_sorting_init();
    $(document).on('click', '.hfwmmRowDeleteIcon', function (e) {
        e.preventDefault();
        $(this).closest('.hfwmm-row').remove();
    });
});