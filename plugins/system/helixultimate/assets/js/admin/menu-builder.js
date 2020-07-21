jQuery(function ($) {
	const config = Joomla.getOptions('meta') || {};

	/**
	 * Activating the menu item sorting
	 */
	const activateMenuItemSorting = () => {
		$('.hu-menu-items').sortable({
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
		});

		$('.hu-menu-items').disableSelection();
	};

	activateMenuItemSorting();

	const triggerMenuSettings = active => {
		$('.hu-menu-item-settings').removeClass('active');
		$(`.hu-menu-item-settings.hu-menu-item-${active}`).addClass('active');
	};

	/**
	 * Handling the menu selection on click event
	 */
	const handlingMenuItemSelection = () => {
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
	};

	handlingMenuItemSelection();

	const saveMenuOrder = data => {
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
	};
});
