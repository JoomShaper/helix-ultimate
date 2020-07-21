jQuery(function ($) {
	$('.hu-field-alignment .hu-switcher-action').on('click', function (e) {
		e.preventDefault();

		if ($('.hu-switcher-action').hasClass('active')) {
			$('.hu-switcher-action').removeClass('active');
		}

		$(this).addClass('active');
		$('.hu-field-alignment input[type=hidden]').val($(this).data('value'));
	});
});
