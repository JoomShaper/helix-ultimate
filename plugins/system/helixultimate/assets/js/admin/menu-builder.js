jQuery(function ($) {
	const config = Joomla.getOptions('meta') || {};

	/**
	 * Activating the menu item sorting
	 */
	function activateMenuItemSorting() {
		$('.hu-menu-items')
			.sortable({
				containment: '.hu-menu-items-wrapper',
				cursor: 'move',
				opacity: 0.6,
				scroll: true,
				axis: 'x',
				tolerance: 'pointer',
				update: (event, ui) => {
					const $items = $('.hu-menu-items > li');
					const data = {
						cid: [],
						order: [],
					};

					$items.each(function (index, el) {
						data.cid.push($(el).data('cid'));
						data.order.push(index + 1);
					});

					saveMenuOrder(data);
				},
			})
			.disableSelection();
	}

	activateMenuItemSorting();

	function makeMegamenuSectionSortable() {
		$('#hu-megamenu-layout-container.active-layout').sortable({
			placeholder: 'ui-state-highlight',
			forcePlaceholderSize: true,
			containment: '.hu-menu-builder',
			handle: '.hu-megamenu-move-row',
			cursor: 'move',
			opacity: 1,
			axis: 'y',
			tolerance: 'pointer',
		});
		$('#hu-megamenu-layout-container.active-layout').disableSelection();
	}
	makeMegamenuSectionSortable();

	function triggerMenuSettings(active) {
		$('.hu-menu-item-settings').removeClass('active');
		$('.hu-menu-item-settings')
			.find('#hu-megamenu-layout-container')
			.removeClass('active-layout');

		$(`.hu-menu-item-settings.hu-menu-item-${active}`).addClass('active');
		$(`.hu-menu-item-settings.hu-menu-item-${active}`)
			.find('#hu-megamenu-layout-container')
			.addClass('active-layout');
	}

	/**
	 * Add new row
	 */
	function addNewRow() {
		$(document).on('click', '.hu-megamenu-add-row', function (e) {
			e.preventDefault();
			const $parent = $(this).closest('.hu-megamenu-layout-section');
			const $cloned = $('#hu-reserved-layout-section').clone(true);

			$cloned
				.removeAttr('id')
				.addClass('hu-megamenu-layout-section')
				.hide();
			$cloned.insertAfter($parent);
			$cloned.slideDown(300);
		});
	}
	addNewRow();

	/**
	 * Delete a row
	 */
	function deleteRow() {
		$(document).on('click', '.hu-megamenu-remove-row', function (e) {
			e.preventDefault();

			const totalSections = $(this)
				.closest('.hu-megamenu-layout-section')
				.siblings().length;

			if (totalSections <= 0) {
				return;
			}

			const confirm = window.confirm('Are you sure to delete the row?');
			if (confirm) {
				$section = $(this).closest('.hu-megamenu-layout-section');

				$section.slideUp(300, function () {
					$(this).remove();
				});
			} else {
				return;
			}
		});
	}
	deleteRow();

	/**
	 * Handling the menu selection on click event
	 */
	function handlingMenuItemSelection() {
		$('.hu-menu-item').on('click', function (event) {
			event.preventDefault();
			const $siblings = $(this).siblings();

			if ($siblings.hasClass('active')) {
				$siblings.removeClass('active');
			}

			if (!$(this).hasClass('active')) {
				$(this).addClass('active');
			}

			triggerMenuSettings($(this).data('name'));
		});
	}

	handlingMenuItemSelection();

	function saveMenuOrder(data) {
		const url = `${config.base}/administrator/index.php?option=com_menus&view=items&task=items.saveOrderAjax&tmpl=component`;

		$.ajax({
			method: 'POST',
			url,
			data,
			beforeSend: () => {
				Joomla.helixLoading(true);
			},
			success: response => {
				Joomla.reloadPreview();
			},
			error: err => {
				alert(err);
			},
			completed: () => {
				Joomla.helixLoading(false);
			},
		});
	}
});
