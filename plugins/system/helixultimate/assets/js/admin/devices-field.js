jQuery(document).ready(function ($) {
	let $wrapper = $('.helix-field');
	let $input = $wrapper.find('input');
	let settings = Joomla.getOptions('data') || {};

	if ($wrapper.length > 0) {
		$wrapper.find('.helix-devices .device-btn').on('click', function (e) {
			e.preventDefault();

			let deviceValue = $(this).data('device');
			switchBetweenDevices(deviceValue);

			$input.val(deviceValue).trigger('change');

			// Clear the active class previously added
			clearActiveClass();

			// Add active class on the current button
			$(this).addClass('active');
		});
	}

	function clearActiveClass() {
		$wrapper.find('.helix-devices .device-btn').each(function () {
			if ($(this).hasClass('active')) {
				$(this).removeClass('active');
			}
		});
	}

	/**
	 * Function to switch between various devices
	 *
	 * @param {string} device 	Device type i.e. `desktop`, `tablet`, `mobile`
	 */
	function switchBetweenDevices(device) {
		const widthMap = {
			lg: '100%',
			md: `${settings.breakpoints.tablet}px`,
			sm: `${settings.breakpoints.mobile}px`,
		};

		const deviceMap = {
			md: 'desktop',
			sm: 'tablet',
			xs: 'mobile',
			desktop: 'desktop',
			tablet: 'tablet',
			mobile: 'mobile',
		};

		$(`.hu-device[data-device=${deviceMap[device]}]`)
			.parent()
			.find('.active')
			.removeClass('active');
		$(`.hu-device[data-device=${deviceMap[device]}]`).addClass('active');

		const $iframe = $('#hu-template-preview');

		$iframe.animate(
			{
				width: widthMap[device],
			},
			500,
			'linear'
		);
	}
});
