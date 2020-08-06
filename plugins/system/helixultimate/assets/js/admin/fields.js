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
					.find(`.control-group input[name=${key}]`);

				let controllerValue = null;

				switch ($controller.attr('type')) {
					case 'checkbox':
						controllerValue = $controller.prop('checked') >> 0;
						break;
					default:
						controllerValue = $controller.val();
						break;
				}

				if (controllerValue == value) {
					$(el).slideDown(100);
				} else {
					$(el).slideUp(100);
				}

				togglers[key] = $controller;
			}
		});
	}
	handleDepend();

	Object.values(togglers).forEach($element => {
		console.log($element);
		$(document).on('change', $element, function (e) {
			e.preventDefault();
			handleDepend();
		});
	});
});
