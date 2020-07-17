jQuery(function ($) {
	/**
	 * Activating the menu item sorting
	 */
	const activateMenuItemSorting = () => {
		$('.hu-menu-items').sortable({
			containment: '.hu-menu-items-container',
			cursor: 'move',
			opacity: 0.6,
			axis: 'x',
			tolerance: 'pointer',
		});

		$('.hu-menu-items').disableSelection();
	};

	activateMenuItemSorting();

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
		});
	};

	handlingMenuItemSelection();
});
