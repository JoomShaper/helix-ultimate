/**
* @package Helix3 Framework
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2015 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

jQuery(function($) {
	
		$(document).ready(function(){
	
			$(this).find('select').each(function(){
				$(this).chosen('destroy');
			});
	
		});//end ready
	
		/* ----------   Load existing template   ------------- */
		$('#hexli-ult-options').on('click', '.layout-del-action', function(event) {
			event.preventDefault();
	
			var $that = $(this),
			layoutName = $(".layoutlist select").val(),
			data = {
				layoutName : layoutName
			};
	
			if ( confirm("Click Ok button to delete "+layoutName+", Cancel to leave.") != true ){
				return false;
			}
	
			var request = {
				'action' : 'remove-layout-file',
				'option' : 'com_ajax',
				'plugin' : 'helix3',
				'request': 'ajaxHelix',
				'data'   : data,
				'format' : 'json'
			};
	
			$.ajax({
				type   : 'POST',
				data   : request,
				beforeSend: function(){
					$('.layout-del-action .fa-spin').show();
				},
				success: function (response) {
					var data = $.parseJSON(response),
					layouts = data.layout,
					tplHtml = '';
	
					if ( data.status == false){
						alert(data.message)
						return;
					}
	
					$('#layoutlist').find('option').remove();
					if (layouts.length) {
						for (var i = 0; i < layouts.length; i++) {
							tplHtml += '<option value="'+ layouts[i] +'">'+ layouts[i].replace('.json','')+'</option>';
						}
	
						$('#layoutlist').html(tplHtml);
					}
	
					$('.layout-del-action .fa-spin').fadeOut('fast');
				},
				error: function(){
					alert('Somethings wrong, Try again');
					$('.layout-del-action .fa-spin').fadeOut('fast');
				}
			});
			return false;
		});
	
		// Save new copy of layout
		$('.form-horizontal').on('click', '.layout-save-action', function(event) {
			$('#layout-modal').find('.sp-modal-body').empty();
			$('#layout-modal .sp-modal-title').text('Save New Layout');
			$('#layout-modal #save-settings').data('flag', 'save-layout');
	
			var $clone = $('.save-box').clone(true);
			$('#layout-modal').find('.sp-modal-body').append( $clone );
	
			$('#layout-modal').spmodal();
		});
	
		// load layout from file
	
		$(".layoutlist select").change(function(){
			var $that = $(this),
			layoutName = $that.val(),
			data = {
				layoutName : layoutName
			};
	
			if ( layoutName == '' || layoutName == ' ' ){
				alert('You are doing somethings wrong.');
			}
	
			var request = {
				'action' : 'render-layout',
				'option' : 'com_ajax',
				'plugin' : 'helix3',
				'request': 'ajaxHelix',
				'data'   : data,
				'format' : 'raw'
			};
	
			$.ajax({
				type   : 'POST',
				data   : request,
				dataType: "html",
				beforeSend: function(){
				},
				success: function (response) {
					var data = $.parseJSON(response);
					if(data.status) {
						$('#helix-layout-builder').empty();
						$('#helix-layout-builder').append(data.layoutHtml).fadeIn('normal');
						jqueryUiLayout();
					}
				}
			});
			return false;
		});
	
		/*********   Lyout Builder JavaScript   **********/
	
		jqueryUiLayout();
	
		function jqueryUiLayout()
		{
			$( "#helix-ultimate-layout-builder" ).sortable({
				placeholder: "ui-state-highlight",
				forcePlaceholderSize: true,
				axis: 'y',
				opacity: 0.8,
				tolerance: 'pointer'
	
			}).disableSelection();
	
			$('.helix-ultimate-layout-section').find('.row').rowSortable();
		}
	
		// setInputValue Callback Function
		$.fn.setInputValue = function(options){
			if (this.attr('type') == 'checkbox') {
				if (options.filed == '1') {
					this.attr('checked','checked');
				}else{
					this.removeAttr('checked');
				}
			}else if(this.hasClass('input-media')){
				if(options.filed){
					$imgParent = this.parent('.media');
					$imgParent.find('img.media-preview').each(function() {
						$(this).attr('src',layoutbuilder_base+options.filed);
					});
				}
				this.val( options.filed );
			}else{
				this.val( options.filed );
			}
	
			if (this.data('attrname') == 'column_type'){
				if (this.val() == 'component') {
					$('.form-group.name').hide();
				}
			}
		}
	
		// callback function, return checkbox value
		$.fn.getInputValue = function(){
			if (this.attr('type') == 'checkbox') {
				if (this.attr("checked")) {
					return '1';
				}else{
					return '0';
				}
			}else{
				return this.val();
			}
		}
	
		// color picker initialize
		$.fn.initColorPicker = function(){
			this.find('.minicolors').each(function() {
				$(this).minicolors({
					control: 'hue',
					position: 'bottom',
					theme: 'bootstrap'
				});
			});
		}
	
		// Open Row settings Modal
		$(document).on('click', '.helix-ultimate-row-options', function(event){
			event.preventDefault();
			$(this).helixUltimateOptionsModal({
				flag: 'row-setting',
				title: "Row Options",
				class: 'helix-ultimate-modal-small'
			});
	
			$('.helix-ultimate-layout-section').removeClass('row-active');
			$parent = $(this).closest('.helix-ultimate-layout-section');
			$parent.addClass('row-active');
	
			var $clone = $('#helix-ultimate-row-settings').clone(true);
			$clone.find('.helix-ultimate-input-color').each(function(){
				$(this).addClass('minicolors');
			});
	
			$clone = $('.helix-ultimate-modal-inner').html($clone.removeAttr('id').addClass('helix-ultimate-modal-content'));
	
			$clone.find('.helix-ultimate-input').each(function(){
				var $that = $(this),
				attrValue = $parent.data( $that.data('attrname'));
				$that.setInputValue({filed: attrValue});
			});
	
			$clone.initColorPicker();
	
		});
	
		// Open Column settings Modal
		$(document).on('click', '.helix-ultimate-column-options',function(event) {
			event.preventDefault();
			$(this).helixUltimateOptionsModal({
				flag: 'column-setting',
				title: "Column Options",
				class: 'helix-ultimate-modal-small'
			});
	
			$('.helix-ultimate-layout-column').removeClass('column-active');
			$parent = $(this).closest('.helix-ultimate-layout-column');
			$parent.addClass('column-active');
	
			var $clone = $('#helix-ultimate-column-settings').clone(true);
			$clone.find('.helix-ultimate-input-color').each(function(){
				$(this).addClass('minicolors');
			});
	
			$clone = $('.helix-ultimate-modal-inner').html($clone.removeAttr('id').addClass('helix-ultimate-modal-content'));
	
			$clone.find('.helix-ultimate-input').each(function(){
				var $that = $(this),
				attrValue = $parent.data( $that.data('attrname'));
				$that.setInputValue({filed: attrValue});
			});
	
			$clone.initColorPicker();
		});
	
	
		$('.helix-ultimate-input-column_type').change(function(event) {
	
			var $parent = $(this).closest('.helix-ultimate-modal-content'),
				flag = false;
	
			$('#helix-ultimate-layout-builder').find('.helix-ultimate-layout-column').not( ".column-active" ).each(function(index, val) {
				if ($(this).data('column_type') == '1') {
					flag = true;
					return false;
				}
			});
	
			if (flag) {
				alert('Component Area Taken');
				$(this).prop('checked',false);
				$parent.children('.control-group.name').slideDown('400');
				return false;
			}
	
			if ($(this).attr("checked")) {
				$('.helix-ultimate-layout-column.column-active').find('.helix-ultimate-column').addClass('helix-ultimate-column-component');
				$parent.children('.control-group.name').slideUp('400');
			}else{
				$('#helix-ultimate-layout-builder').find('.helix-ultimate-column-component').removeClass('helix-ultimate-column-component');
				$parent.children('.control-group.name').slideDown('400');
			}
		});
	
		// Save Row Column Settings
		$(document).on('click', '.helix-ultimate-settings-apply', function(event) {
			event.preventDefault();
	
			var flag = $(this).data('flag');
	
			switch(flag){
				case 'row-setting':
					$('.helix-ultimate-modal-content').find('.helix-ultimate-input').each(function(){
						var $this = $(this),
						$parent = $('.row-active'),
						$attrname = $this.data('attrname');
						$parent.removeData( $attrname );
		
						if ($attrname == 'name') {
							var nameVal = $this.val();
		
							if (nameVal  !='' || $this.val() != null) {
								$('.row-active .helix-ultimate-section-title').text($this.val());
							}else{
								$('.row-active .helix-ultimate-section-title').text('Section Header');
							}
						}
		
						$parent.attr('data-' + $attrname, $this.getInputValue());
					});
		
					$('.helix-ultimate-modal-overlay, .helix-ultimate-modal').remove();
					$('body').addClass('helix-ultimate-modal-open');
					break;
	
				case 'column-setting':
					var component = false;
		
					$('.helix-ultimate-modal-content').find('.helix-ultimate-input').each(function(){
		
						var $this = $(this),
						$parent = $('.column-active'),
						$attrname = $this.data('attrname');
						$parent.removeData( $attrname ),
						dataVal = $this.val();
		
						if ( $attrname == 'column_type' && $(this).attr("checked") ) {
							component = true;
							$('.column-active .helix-ultimate-column-title').text('Component');
						}else if( $attrname == 'name' && component != true ) {
							if (dataVal == '' || dataVal == undefined) {
								dataVal = 'none';
							}
							$('.column-active .helix-ultimate-column-title').text(dataVal);
						}
		
						$parent.attr('data-' + $attrname, $this.getInputValue());
					});
					$('.helix-ultimate-modal-overlay, .helix-ultimate-modal').remove();
					$('body').addClass('helix-ultimate-modal-open');
					break;
	
				default:
				alert('You are doing somethings wrongs. Try again');
			}
		});
	
		// Cancel Modal
		$(document).on('click', '.helix-ultimate-settings-cancel', function(event) {
			event.preventDefault();
			$('.helix-ultimate-modal-overlay, .helix-ultimate-modal').remove();
			$('body').addClass('helix-ultimate-modal-open');
		});
	
		// Column Layout Arrange
		$(document).on('click', '.helix-ultimate-column-layout', function(event) {
			event.preventDefault();
	
			var $that = $(this),
				colType = $that.data('type'), column;
	
			if ($that.hasClass('active') && colType != 'custom' ) {
				return;
			}
	
			if (colType == 'custom') {
				column = prompt('Enter your custom layout like 4+2+2+2+2 as total 12 grid','4+2+2+2+2');
			}
	
			var $parent 		= $that.closest('.helix-ultimate-column-list'),
				$gparent 		= $that.closest('.helix-ultimate-layout-section'),
				oldLayoutData 	= $parent.find('.active').data('layout'),
				oldLayout       = ['12'],
				layoutData 		= $that.data('layout'),
				newLayout 		= ['12'];
	
			if ( oldLayoutData != 12 ) {
				oldLayout = oldLayoutData.split('+');
			}
	
			if(layoutData != 12 ){
				newLayout = layoutData.split('+');
			}
	
			if ( colType == 'custom' ) {
				var error 	= true;
	
				if ( column != null ) {
					var colArray = column.split('+');
	
					var colSum = colArray.reduce(function(a, b) {
						return Number(a) + Number(b);
					});
	
					if ( colSum == 12 ) {
						newLayout = colArray;
						$(this).data('layout', column)
						error = false;
					}
				}
	
				if (error) {
					alert('Error generated. Please correct your column arragnement and try again.');
					return false;
				}
			}
	
			var col = [],
				colAttr = [];
	
			$gparent.find('.helix-ultimate-layout-column').each(function(i,val){
				col[i] = $(this).html();
				var colData = $(this).data();

				if (typeof colData == 'object') {
					colAttr[i] = $(this).data();
				}else{
					colAttr[i] = '';
				}
			});
	
			$parent.find('.active').removeClass('active');
			$that.addClass('active');
	
			var new_item = '';
	
			for(var i=0; i < newLayout.length; i++)
			{
				var dataAttr = '';
				if (typeof colAttr[i] != 'object') {
					colAttr[i] = { grid_size : newLayout[i].trim() }
				} else {
					colAttr[i].grid_size = newLayout[i].trim()
				}
				$.each(colAttr[i],function(index,value){
					dataAttr += ' data-'+index+'="'+value+'"';
				});
	
				new_item +='<div class="helix-ultimate-layout-column col-md-'+ newLayout[i].trim() +'" '+dataAttr+'>';
				if (col[i]) {
					new_item += col[i];
				}else{
					new_item += '<div class="helix-ultimate-column clearfix">';
					new_item += '<span class="helix-ultimate-column-title">none</span>';
					new_item += '<a class="helix-ultimate-column-options" href="#"><i class="fa fa-gear"></i></a>';
					new_item += '</div>';
				}
				new_item +='</div>';
			}
	
			$old_column = $gparent.find('.helix-ultimate-layout-column');
			$gparent.find('.row.ui-sortable').append( new_item );
	
			$old_column.remove();
			jqueryUiLayout();
		});
	
		// add row
		$(document).on('click', '.helix-ultimate-add-row',function(event){
			event.preventDefault();
	
			var $parent = $(this).closest('.helix-ultimate-layout-section'),
			$rowClone = $('#helix-ultimate-layout-section').clone(true);
	
			$rowClone.addClass('helix-ultimate-layout-section').removeAttr('id');
			$($rowClone).insertAfter($parent);
	
			jqueryUiLayout();
		});
	
		// Remove Row
		$(document).on('click', '.helix-ultimate-remove-row', function(event){
			event.preventDefault();
	
			if ( confirm("Click Ok button to delete Row, Cancel to leave.") == true ) {
				$(this).closest('.helix-ultimate-layout-section').slideUp(500, function(){
					$(this).remove();
				});
			}
		});
	
		// Remove Media
		$(document).on('click','.remove-media',function(){
			var $that = $(this),
			$imgParent = $that.parent('.media');
	
			$imgParent.find('img.media-preview').each(function() {
				$(this).attr('src','');
				$(this).closest('.image-preview').css('display', 'none');
			});
		});
	
		// Generate Layout JSON
		function getGeneratedLayout(){
			var item = [];
			$('#helix-ultimate-layout-builder').find('.helix-ultimate-layout-section').each(function(index){
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