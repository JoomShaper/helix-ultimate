/**
* @package Helix Ultimate Framework
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2018 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

jQuery(function($) {
  $('#attrib-spmegamenu').find('.control-group').first().find('.control-label').remove();
  $('#attrib-spmegamenu').find('.control-group').first().find('>.controls').removeClass().addClass('megamenu').unwrap();

  $(document).on('click', '#helix-ultimate-megamenu-toggler', function(){
    var currentVal = $(this).is(":checked");
    $('#helix-ultimate-megamenu-layout').data('megamenu', currentVal);

    if(currentVal){
      $('.helix-ultimate-megamenu-field-control, .helix-ultimate-megamenu-sidebar').removeClass('hide-menu-builder');
    } else {
      $('.helix-ultimate-megamenu-field-control, .helix-ultimate-megamenu-sidebar').addClass('hide-menu-builder');
    }

  });

  $(document).on('click', '#helix-ultimate-megamenu-title-toggler', function(){
    $('#helix-ultimate-megamenu-layout').data('showtitle', $(this).is(":checked"));
  });

  // Custom Class
  $(document).on('change', '#helix-ultimate-megamenu-custom-class', function(){
    $('#helix-ultimate-megamenu-layout').data('customclass', $(this).val());
  });

  // Menu Width
  $(document).on('change', '#helix-ultimate-megamenu-width', function(){
    $('#helix-ultimate-megamenu-layout').data('width', $(this).val());
  });

  // Alignment
  $(document).on('change', '#helix-ultimate-megamenu-alignment', function(){
    $('#helix-ultimate-megamenu-layout').data('menualign', $(this).val());
  });

  // Alignment
  $(document).on('change', '#helix-ultimate-megamenu-fa-icon', function(){
    $('#helix-ultimate-megamenu-layout').data('faicon', $(this).val());
  });

  /**
  * Saving menu layout
  */
  document.adminForm.onsubmit = function(event) {
    //event.preventDefault();
    var layout = [];

    $('#helix-ultimate-megamenu-layout').find('.helix-ultimate-megamenu-row').each(function(index){
      var $row = $(this),
      rowIndex = index;
      layout[rowIndex] = {
        'type'      : 'row',
        'attr'      : []
      };

      // Get each column data;
      $row.find('.helix-ultimate-megmenu-col').each(function(index) {
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
        $column.find('.helix-ultimate-megamenu-item').each(function(index, el) {
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

    var initData = $('#helix-ultimate-megamenu-layout').data();
    var menumData = {
      'width'         : initData.width,
      'menuitem'      : initData.menuitem,
      'menualign'     : initData.menualign,
      'megamenu'      : initData.megamenu,
      'showtitle'     : initData.showtitle,
      'faicon'        : initData.faicon,
      'customclass'   : initData.customclass,
      'layout'        : layout
    };

    $('#jform_params_megamenu').val(1);
    $('#jform_params_menulayout').val(JSON.stringify(menumData));
  };

  $(document).on('click', '#helix-ultimate-choose-megamenu-layout', function(e){
    e.preventDefault();
    $('#helix-ultimate-megamenu-layout-modal').toggle();
  });

  $(document).on('click', '.helix-ultimate-megamenu-grids', function(e) {
    e.preventDefault();
    var data_layout = $(this).attr('data-layout');

    var layout_row_tpl = '<div class="helix-ultimate-megamenu-row">';
    layout_row_tpl += '<div class="helix-ultimate-megamenu-row-actions clearfix">';
    layout_row_tpl += '<div class="helix-ultimate-action-move-row">';
    layout_row_tpl += '<span class="fa fa-sort"></span> Row';
    layout_row_tpl += '</div>';
    layout_row_tpl += '<a href="#" class="helix-ultimate-action-detele-row"><span class="fa fa-trash-o"></span></a>';
    layout_row_tpl += '</div>';
    layout_row_tpl += '<div class="helix-ultimate-row">';

    var layout_col_tpl = '<div class="helix-ultimate-megmenu-col helix-ultimate-col-sm-{col}" data-grid="{grid}">';
    layout_col_tpl += '<div class="helix-ultimate-megamenu-column">';
    layout_col_tpl += '<div class="helix-ultimate-megamenu-column-actions">';
    layout_col_tpl += '<span class="helix-ultimate-action-move-column"><span class="fa fa-arrows"></span> Column</span>';
    layout_col_tpl += '</div>';
    layout_col_tpl += '<div class="helix-ultimate-megamenu-item-list"></div>';
    layout_col_tpl += '</div>';
    layout_col_tpl += '</div>';

    var appending_col = '';
    if (data_layout != 12)
    {
      var col_layout_data = data_layout.split('+');
      for (i=0; i<col_layout_data.length; i++)
      {
        appending_col += layout_col_tpl.replace('{col}', col_layout_data[i]).replace('{grid}', col_layout_data[i]);
      }
    }
    else
    {
      appending_col += layout_col_tpl.replace('{col}', 12).replace('{grid}', 12);
    }

    layout_row_tpl+= appending_col;
    layout_row_tpl+= '</div>';
    layout_row_tpl+= '</div>';

    $('#helix-ultimate-megamenu-layout').append(layout_row_tpl);
    $(this).closest('#helix-ultimate-megamenu-layout-modal').hide();

    helix_ultimate_megamenu_sorting_init();
  });

  function helix_ultimate_megamenu_sorting_init() {

    $(".helix-ultimate-megamenu-item-list").sortable({
      connectWith: ".helix-ultimate-megamenu-item-list",
      items: " .helix-ultimate-megamenu-item",
      placeholder: "drop-highlight",
      start: function(e,ui){
        ui.placeholder.height(ui.item.height());
      },
      stop: function(e,ui){
        //helix_ultimate_megamenu_sorting_init();
      }
    }).disableSelection();

    /**
    * Drag from module lists
    */
    $('.helix-ultimate-megamenu-module-list').sortable({
      connectWith: '.helix-ultimate-megamenu-item-list',
      items: " .helix-ultimate-megamenu-draggable-module",
      placeholder: "drop-highlight",
      helper: "clone",
      start: function(e,ui){
        ui.placeholder.height(ui.item.height());
      },
      update: function (e, ui) {
        var module_title = ui.item.text();
        var module_inner = '<div class="helix-ultimate-megamenu-item-module"><div class="helix-ultimate-megamenu-item-module-title">' + module_title + '</div></div>';
        ui.item.removeAttr('style class').addClass('helix-ultimate-megamenu-item').html(module_inner);

        ui.item.clone().insertAfter(ui.item.html('<span class="fa fa-arrows"></span> ' + module_title).removeAttr('class').addClass('helix-ultimate-megamenu-draggable-module'));
        $(this).sortable('cancel');

        helix_ultimate_megamenu_sorting_init();
      }
    }).disableSelection();

    $('.helix-ultimate-megamenu-row').sortable({
      start: function(e,ui){
        ui.placeholder.height(ui.item.height());
        ui.placeholder.width(ui.item.width() - 50);
      },
      items: '.helix-ultimate-megmenu-col',
      handle: '.helix-ultimate-action-move-column',
      placeholder: "drop-col-highlight",
      stop: function(e,ui){
        //helix_ultimate_megamenu_sorting_init();
      }
    });

    $('#helix-ultimate-megamenu-layout').sortable({
      start: function(e,ui){
        ui.placeholder.height(ui.item.height());
        ui.placeholder.width(ui.item.width() - 50);
      },
      items: '.helix-ultimate-megamenu-row',
      handle: '.helix-ultimate-action-move-row',
      placeholder: "drop-highlight",
      stop: function(e,ui){
        //helix_ultimate_megamenu_sorting_init();
      }
    });

    $(document).on('click', '.helix-ultimate-megamenu-remove-module', function (e) {
      e.preventDefault();
      $(this).closest('.helix-ultimate-megamenu-item').remove();
    })
  }
  helix_ultimate_megamenu_sorting_init();

  $(document).on('click', '.helix-ultimate-action-detele-row', function (e) {
    e.preventDefault();
    $(this).closest('.helix-ultimate-megamenu-row').remove();
  });
});
