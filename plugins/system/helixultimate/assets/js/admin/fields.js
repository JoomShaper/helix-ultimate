jQuery(function ($) {
	$('.hu-field-alignment .hu-switcher-action').on('click', function (e) {
		e.preventDefault();

		if ($('.hu-switcher-action').hasClass('active')) {
			$('.hu-switcher-action').removeClass('active');
		}

		$(this).addClass('active');
		$('.hu-field-alignment input[type=hidden]').val($(this).data('value'));
		$('.hu-field-alignment input[type=hidden]').trigger('change');
	});

	/**
	 * Handle depend on fields
	 */
	let togglers = {};
	function handleDepend() {
		let $field = $('.control-group[data-depend]');
		$field.each(function (index, el) {
			let depend = $(el).data('depend');
			if (depend) {
				let [key, value] = depend.split(':');
				let $controller = $(el)
					.parent()
					.find(
						`.control-group input[name=${key}], .control-group select[name=${key}]`
					);

				let controllerValue = null;

				switch ($controller.attr('type')) {
					case 'checkbox':
						controllerValue = $controller.prop('checked') >> 0;
						break;
					default:
						controllerValue = $controller.val();
						break;
				}

				value = value.split('|');

				if (value.indexOf(controllerValue) > -1) {
					$(el).slideDown(300);
				} else {
					$(el).slideUp(300);
				}

				togglers[key] = $controller;
			}
		});
	}
	handleDepend();

	Object.values(togglers).forEach($element => {
		$(document).on('change', $element, function (e) {
			e.preventDefault();
			handleDepend();
		});
	});

	/**
	 * Menu Items selector
	 *
	 */
	// Select all
	$(document).on(
		'change',
		'.hu-menu-hierarchy-container .hu-menu-item-selector.select-all',
		function (e) {
			e.preventDefault();
			const $parent = $(this).closest('.hu-menu-hierarchy-list');
			const $siblings = $parent.find(
				'.hu-menu-hierarchy-item:not(.level-0)'
			);

			let val = $(this).prop('checked');
			let value = [];

			if (!val) if (value.length > 0) value = [];

			$siblings.each(function () {
				const $input = $(this).find('input[type=checkbox]');
				$input.prop('checked', val);
				const v = $input.val();
				if (val) if (value.indexOf(v) === -1) value.push(v);
			});

			$('.hu-menu-hierarchy-container input[type=hidden]').val(
				JSON.stringify(value)
			);
		}
	);
	$(document).on(
		'change',
		'.hu-menu-hierarchy-container .hu-menu-item-selector:not(.level-0)',
		function (e) {
			e.preventDefault();
			let value = $(
				'.hu-menu-hierarchy-container input[type=hidden]'
			).val();

			value = value.length && JSON.parse(value);

			const val = $(this).val();

			if ($(this).prop('checked')) {
				if (value.indexOf(val) === -1) value.push(val);
			} else {
				let index = value.indexOf(val);
				if (index > -1) {
					value.splice(index, 1);
				}
			}

			$('.hu-menu-hierarchy-container input[name=menu-hierarchy]').val(
				JSON.stringify(value)
			);
		}
	);
});
