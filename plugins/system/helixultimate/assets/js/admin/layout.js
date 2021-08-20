/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

jQuery(function ($) {
	/** Show/hide the column layout */
	$(document).on('click', '.hu-add-columns', function (event) {
		event.preventDefault();
		event.stopPropagation();

		$(this).closest('li').find('.hu-column-list').slideToggle(300);
		console.log('click fired');
	});

	/** Close the hu-column-list on select. */
	$(document).on('click', '.hu-column-layout:not(.hu-layout-custom-btn)', function () {
		$(this).closest('.hu-column-list').slideUp(300);
	});

	// Sortable
	$.fn.rowSortable = function () {
		let originalIndex = -1,
			currentIndex = -1;
		$(this)
			.sortable({
				placeholder: 'ui-state-highlight',
				forcePlaceholderSize: true,
				axis: 'x',
				opacity: 1,
				tolerance: 'pointer',
				start(_, ui) {
					$('.hu-layout-section [data-hu-layout-row]')
						.find('.ui-state-highlight')
						.addClass($(ui.item).attr('class'));
					$('.hu-layout-section [data-hu-layout-row]')
						.find('.ui-state-highlight')
						.css('height', $(ui.item).outerHeight());
					originalIndex = ui.item.index();
				},
				stop(_, ui) {
					currentIndex = ui.item.index();

					if (originalIndex !== currentIndex) {
						updateLayoutField();
						Joomla.HelixToaster.success('Columns position changed!', 'Layout Settings');
					}
				},
			})
			.disableSelection();
	};

	jqueryUiLayout();

	function jqueryUiLayout() {
		let originalIndex = -1,
			currentIndex = -1;
		$('#hu-layout-builder')
			.sortable({
				placeholder: 'ui-state-highlight',
				forcePlaceholderSize: true,
				axis: 'y',
				opacity: 1,
				tolerance: 'pointer',
				start(_, ui) {
					originalIndex = ui.item.index();
				},
				stop(_, ui) {
					currentIndex = ui.item.index();
					if (originalIndex !== currentIndex) {
						updateLayoutField();
						Joomla.HelixToaster.success('Rows position changed!', 'Layout Settings');
					}
				},
			})
			.disableSelection();

		$('.hu-layout-section').find('[data-hu-layout-row]').rowSortable();
	}

	// setInputValue Callback Function
	$.fn.setInputValue = function (options) {
		console.log(options);
		if (this.attr('type') == 'checkbox') {
			if (options.field == '1') {
				this.attr('checked', 'checked');
			} else {
				this.removeAttr('checked');
			}
		} else if (this.hasClass('input-select')) {
			this.val(options.field);
			this.trigger('liszt:updated');
			this.trigger('chosen:updated');
		} else if (this.hasClass('input-media')) {
			if (options.field) {
				$imgParent = this.parent('.media');
				$imgParent.find('img.media-preview').each(function () {
					$(this).attr('src', layoutbuilder_base + options.field);
				});
			}
			this.val(options.field);
		} else {
			this.val(options.field);
		}

		if (this.data('attrname') == 'column_type') {
			if (this.val() == 'component') {
				$('.form-group.name').hide();
			}
		}
	};

	// callback function, return checkbox value
	$.fn.getInputValue = function () {
		if (this.attr('type') == 'checkbox') {
			return this.prop('checked') ? '1' : '0';
		} else {
			return this.val();
		}
	};

	// color picker initialize
	$.fn.initColorPicker = function () {
		this.find('.minicolors').each(function () {
			$(this).minicolors({
				control: 'hue',
				position: 'bottom',
				theme: 'bootstrap',
			});
		});
	};

	// Open Row settings Modal
	$(document).on('click', '.hu-row-options', function (event) {
		event.preventDefault();
		$(this).helixUltimateOptionsModal({
			flag: 'row-setting',
			title: "<span class='fas fa-cogs hu-mr-2'></span> Row Options",
			class: 'hu-modal-small',
		});

		$('.hu-layout-section').removeClass('row-active');
		$parent = $(this).closest('.hu-layout-section');
		$parent.addClass('row-active');

		$('#hu-row-settings')
			.find('select.hu-input')
			.each(function () {
				$(this).chosen('destroy');
			});

		var $clone = $('#hu-row-settings').clone(true);
		$clone.find('.hu-input-color').each(function () {
			$(this).addClass('minicolors');
		});

		$clone.find('select.hu-input').each(function () {
			$(this).chosen({ width: '100%' });
		});

		$clone = $('.hu-options-modal-inner').html($clone.removeAttr('id').addClass('hu-options-modal-content'));

		$clone.find('.hu-input').each(function () {
			var $that = $(this),
				attrValue = $parent.data($that.data('attrname'));

			$that.setInputValue({ field: attrValue });
			if ($that.hasClass('hu-input-media')) {
				if (attrValue) {
					$that.prev('.hu-image-holder').html('<img src="' + $that.data('baseurl') + attrValue + '" alt="">');

					let $clear = $that.siblings('.hu-media-clear');

					if ($clear.hasClass('hide')) {
						$clear.removeClass('hide');
					}
				}
			}
		});

		$clone.initColorPicker();
	});

	// Open Column settings Modal
	$(document).on('click', '.hu-column-options', function (event) {
		event.preventDefault();
		$(this).helixUltimateOptionsModal({
			flag: 'column-setting',
			title: "<span class='fas fa-cog'></span> Column Options",
			class: 'hu-modal-small',
		});

		$('.hu-layout-column').removeClass('column-active');
		$parent = $(this).closest('.hu-layout-column');
		$parent.addClass('column-active');

		$('#hu-column-settings')
			.find('select.hu-input')
			.each(function () {
				$(this).chosen('destroy');
			});

		var $clone = $('#hu-column-settings').clone(true);
		$clone.find('.hu-input-color').each(function () {
			$(this).addClass('minicolors');
		});

		$clone = $('.hu-options-modal-inner').html($clone.removeAttr('id').addClass('hu-options-modal-content'));

		$clone.find('.hu-input').each(function () {
			var $that = $(this),
				attrValue = $parent.data($that.data('attrname'));
			$that.setInputValue({ field: attrValue });
		});

		$clone.find('select.hu-input').each(function () {
			$(this).chosen({ width: '100%' });
		});

		$clone.initColorPicker();
	});

	$('.hu-input-column_type').change(function (event) {
		var $parent = $(this).closest('.hu-modal-content'),
			flag = false;

		$('#hu-layout-builder')
			.find('.hu-layout-column')
			.not('.column-active')
			.each(function (index, val) {
				if ($(this).data('column_type') == '1') {
					flag = true;
					return false;
				}
			});

		if (flag) {
			alert('Component Area Taken');
			$(this).prop('checked', false);
			$parent.children('.control-group.name').slideDown('400');
			return false;
		}

		if ($(this).attr('checked')) {
			$('.hu-layout-column.column-active').find('.hu-column').addClass('hu-column-component');
			$parent.children('.control-group.name').slideUp('400');
		} else {
			$('#hu-layout-builder').find('.hu-column-component').removeClass('hu-column-component');
			$parent.children('.control-group.name').slideDown('400');
		}

		updateLayoutField();
	});

	// Save Row Column Settings
	$(document).on('click', '.hu-settings-apply', function (event) {
		event.preventDefault();

		var flag = $(this).data('flag');

		switch (flag) {
			case 'row-setting':
				$('.hu-options-modal-content')
					.find('.hu-input')
					.each(function () {
						var $this = $(this),
							$parent = $('.row-active'),
							$attrname = $this.data('attrname');
						$parent.removeData($attrname);

						if ($attrname == 'name') {
							var nameVal = $this.val();

							if (nameVal == '' || nameVal == null) {
								$('.row-active .hu-section-title').text('Section Header');
							} else {
								$('.row-active .hu-section-title').text($this.val());
							}
						}

						$parent.data($attrname, $this.getInputValue());
					});

				$('.hu-options-modal-overlay, .hu-options-modal').remove();
				$('body').removeClass('hu-options-modal-open');
				break;

			case 'column-setting':
				var component = false;

				$('.hu-options-modal-content')
					.find('.hu-input')
					.each(function () {
						var $this = $(this),
							$parent = $('.column-active'),
							$attrname = $this.data('attrname'),
							dataVal = $this.val();

						$parent.removeData($attrname);

						if ($attrname == 'column_type' && $(this).attr('checked')) {
							component = true;
							$('.column-active .hu-column-title').text('Component');
						} else if ($attrname == 'name' && component != true) {
							if (dataVal == '' || dataVal == undefined) {
								dataVal = 'none';
							}
							$('.column-active .hu-column-title').text(dataVal);
						}

						$parent.data($attrname, $this.getInputValue());
					});
				$('.hu-options-modal-overlay, .hu-options-modal').remove();
				$('body').removeClass('hu-options-modal-open');
				break;
			case 'menu-row-setting':
			case 'menu-col-setting':
				$('.hu-options-modal-overlay, .hu-options-modal').remove();
				$('body').removeClass('hu-options-modal-open');
				break;
			default:
				alert('You are doing somethings wrongs. Try again');
		}

		updateLayoutField();
		Joomla.HelixToaster.success('Changes applied successfully!', 'Layout Settings');
	});

	// Cancel Modal
	$(document).on('click', '.hu-settings-cancel, .action-hu-options-modal-close', function (event) {
		event.preventDefault();
		$('.hu-options-modal-overlay, .hu-options-modal').remove();
		$('body').removeClass('hu-options-modal-open');
	});

	// Column Layout Arrange
	$(document).on('click', '.hu-column-layout', function (event) {
		event.preventDefault();
		
		var $that = $(this),
			colType = $that.data('type'),
			column;

		if ($that.hasClass('active') && colType != 'custom') {
			return;
		}

		if (colType === 'custom') {
			return;
		}

		var $parent = $that.closest('.hu-column-list'),
			$gparent = $that.closest('.hu-layout-section'),
			oldLayoutData = $parent.find('.active').data('layout'),
			oldLayout = ['12'],
			layoutData = $that.data('layout'),
			newLayout = ['12'];

		if (oldLayoutData != 12) {
			oldLayout = oldLayoutData.split('+');
		}

		if (layoutData != 12) {
			newLayout = layoutData.split('+');
		}

		var col = [],
			colAttr = [];

		$gparent.find('.hu-layout-column').each(function (i, val) {
			col[i] = $(this).html();
			var colData = $(this).data();

			if (typeof colData == 'object') {
				colAttr[i] = $(this).data();
			} else {
				colAttr[i] = '';
			}
		});

		$parent.find('.active').removeClass('active');
		$that.addClass('active');

		var new_item = '';

		for (var i = 0; i < newLayout.length; i++) {
			var dataAttr = '';
			if (typeof colAttr[i] != 'object') {
				colAttr[i] = {
					grid_size: newLayout[i].trim(),
					column_type: 0,
					name: 'none',
				};
			} else {
				colAttr[i].grid_size = newLayout[i].trim();
			}
			$.each(colAttr[i], function (index, value) {
				dataAttr += ' data-' + index + '="' + value + '"';
			});

			new_item += '<div class="hu-layout-column col-' + newLayout[i].trim() + '" ' + dataAttr + '>';
			if (col[i]) {
				new_item += col[i];
			} else {
				new_item += '<div class="hu-column">';
				new_item += '<span class="hu-column-title">none</span>';
				new_item +=
					'<a class="hu-column-options" href="#"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="3" fill="none"><path fill="#020B53" fill-rule="evenodd" d="M3 1.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zm6 0a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM13.5 3a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" clip-rule="evenodd" opacity=".4"/></svg></a>';
				new_item += '</div>';
			}
			new_item += '</div>';
		}

		$old_column = $gparent.find('.hu-layout-column');
		$gparent.find('[data-hu-layout-row]').append(new_item);

		$old_column.remove();
		jqueryUiLayout();

		Joomla.HelixToaster.success(
			'Grid pattern updated to <strong>' + newLayout.join('+') + '</strong>',
			'Layout Settings'
		);
		updateLayoutField();
	});

	$(document).on('click', '.hu-layout-custom-btn', function (event) {
		event.preventDefault();
		const $custom = $(this).closest('.hu-column-list').find('.hu-layout-custom');
		$custom.slideToggle(300);
	});

	$(document).on('click', '.hu-layout-custom-apply', function (event) {
		event.preventDefault();

		let $that = $(this).closest('.hu-column-list').find('.hu-layout-custom-btn');

		let $parent = $that.closest('.hu-column-list'),
			$gparent = $that.closest('.hu-layout-section'),
			oldLayoutData = $parent.find('.active').data('layout'),
			oldLayout = ['12'],
			layoutData = (column = $(this).closest('div').find('input').val() || '12'),
			newLayout = ['12'];

		if (oldLayoutData != 12) {
			oldLayout = oldLayoutData.split('+');
		}

		if (layoutData != 12) {
			newLayout = layoutData.split('+');
		}

		var colArray = column.split('+');

		var colSum = colArray.reduce(function (a, b) {
			return Number(a) + Number(b);
		});

		if (colSum == 12) {
			newLayout = colArray;
			$that.data('layout', column).attr('data-layout', column);
		} else {
			alert('Invalid grid pattern!');
			return;
		}

		var col = [],
			colAttr = [];

		$gparent.find('.hu-layout-column').each(function (i, val) {
			col[i] = $(this).html();
			var colData = $(this).data();

			if (typeof colData == 'object') {
				colAttr[i] = $(this).data();
			} else {
				colAttr[i] = '';
			}
		});

		$parent.find('.active').removeClass('active');
		$that.addClass('active');

		let new_item = '';

		for (let i = 0; i < newLayout.length; i++) {
			let dataAttr = '';
			if (typeof colAttr[i] != 'object') {
				colAttr[i] = {
					grid_size: newLayout[i].trim(),
					column_type: 0,
					name: 'none',
				};
			} else {
				colAttr[i].grid_size = newLayout[i].trim();
			}
			$.each(colAttr[i], function (index, value) {
				dataAttr += ' data-' + index + '="' + value + '"';
			});

			new_item += '<div class="hu-layout-column col-' + newLayout[i].trim() + '" ' + dataAttr + '>';
			if (col[i]) {
				new_item += col[i];
			} else {
				new_item += '<div class="hu-column">';
				new_item += '<span class="hu-column-title">none</span>';
				new_item +=
					'<a class="hu-column-options" href="#"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="3" fill="none"><path fill="#020B53" fill-rule="evenodd" d="M3 1.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zm6 0a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM13.5 3a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" clip-rule="evenodd" opacity=".4"/></svg></a>';
				new_item += '</div>';
			}
			new_item += '</div>';
		}

		$old_column = $gparent.find('.hu-layout-column');
		$gparent.find('[data-hu-layout-row]').append(new_item);

		$old_column.remove();
		jqueryUiLayout();

		Joomla.HelixToaster.success(
			'Grid pattern updated to <strong>' + newLayout.join('+') + '</strong>',
			'Layout Settings'
		);
		updateLayoutField();

		$that.closest('.hu-column-list').slideUp(300);
	});

	// add row
	$(document).on('click', '.hu-add-row', function (event) {
		event.preventDefault();

		var $parent = $(this).closest('.hu-layout-section'),
			$rowClone = $('#hu-layout-section').clone(true);

		$rowClone.addClass('hu-layout-section').removeAttr('id');
		$($rowClone).insertAfter($parent);

		jqueryUiLayout();
		Joomla.HelixToaster.success('New row added!', 'Layout Settings');
		updateLayoutField();
	});

	// Remove Row
	$(document).on('click', '.hu-remove-row', function (event) {
		event.preventDefault();

		if (confirm('Click Ok button to delete Row, Cancel to leave.') == true) {
			$(this)
				.closest('.hu-layout-section')
				.slideUp(500, function () {
					$(this).remove();
					Joomla.HelixToaster.error('Row is removed!', 'Layout Settings');
					updateLayoutField();
				});
		}
	});

	// Remove Media
	$(document).on('click', '.remove-media', function () {
		var $that = $(this),
			$imgParent = $that.parent('.media');

		$imgParent.find('img.media-preview').each(function () {
			$(this).attr('src', '');
			$(this).closest('.image-preview').css('display', 'none');
		});
		updateLayoutField();
	});

	// Generate Layout JSON
	function getGeneratedLayout() {
		var item = [];
		$('#hu-layout-builder')
			.find('.hu-layout-section')
			.each(function (index) {
				var $row = $(this),
					rowIndex = index,
					rowObj = $row.data();
				delete rowObj.sortableItem;

				var activeLayout = $row.find('.hu-column-layout.active'),
					layoutArray = activeLayout.data('layout'),
					layout = 12;

				if (layoutArray != 12) {
					layout = layoutArray.split(',').join('');
				}

				item[rowIndex] = {
					type: 'row',
					layout: layout,
					settings: rowObj,
					attr: [],
				};

				// Find Column Elements
				$row.find('.hu-layout-column').each(function (index) {
					var $column = $(this),
						colIndex = index,
						colObj = $column.data();
					delete colObj.sortableItem;

					item[rowIndex].attr[colIndex] = {
						type: 'sp_col',
						settings: colObj,
					};
				});
			});

		return item;
	}

	function updateLayoutField() {
		$('#layout').val(JSON.stringify(getGeneratedLayout())).trigger('change');
	}
});
