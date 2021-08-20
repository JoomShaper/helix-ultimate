/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2020 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

jQuery(function ($) {
	let $presetsDataElement = $('.hu-presets #presets-data');
	let $presetsDivElement = $(
		'.hu-presets .hu-preset'
	);

	// All the presets data
	let presetsData = $presetsDataElement.val();

	if (typeof presetsData === 'string' && presetsData.length) {
		presetsData = JSON.parse(presetsData);
	}

	function detectMajorColor(presetData) {
		let colorsFrequency = Object.values(presetData).reduce(function (
			accumulator,
			color
		) {
			accumulator[color] = (accumulator[color] || 0) + 1;
			return accumulator;
		},
		{});

		let max = 0;
		return Object.entries(colorsFrequency).reduce(function (
			major,
			[color, frequency]
		) {
			if (frequency >= max) {
				max = frequency;
				major = color;
			}

			return major;
		},
		'');
	}

	function reflectPresetChanges(
		data,
		name,
		$presetContainerOriginal,
		$presetContainer
	) {
		// Change the presets data with updated data
		presetsData[name].data = data;
		console.log(data);

		// Update the input field #presets-data
		$presetsDataElement.val(JSON.stringify(presetsData));

		// Update the attributes of the .hu-preset
		$presetsDivElement.each(function () {
			let self = this;
			if ($(this).data('preset') === name) {
				Object.entries(data).forEach(function ([key, value]) {
					$(self).attr(`data-${key}`, value);
					$(self).data(key, value);
				});
			}
		});

		/**
		 * Replace the original preset container
		 * with the cloned, updated container because,
		 * next time when we shall reopen the modal
		 * then we can get the last changes.
		 */
		$presetContainerOriginal.html($presetContainer.find('> div'));
		window.purgeCss();

		/**
		 * Close the modal
		 */
		$(
			'.hu-options-modal-overlay, .hu-options-modal'
		).remove();
		$('body').removeClass('hu-options-modal-open');

		let majorColor = detectMajorColor(data);
		let $presetElement = $(`.hu-preset[data-preset=${name}]`);

		$presetElement.css({
			'background-color': majorColor,
		});

		$presetElement
			.find('.hu-edit-preset')
			.css({ color: majorColor });

		$(`.hu-preset[data-preset=${name}]`).click();
	}

	$(document).on('click', '.hu-edit-preset', function (e) {
		e.preventDefault();
		e.stopPropagation();

		let presetData = $(this).data('preset_data') || '';
		let { name, data } = presetData;

		/**
		 * Clone the preset container for displaying
		 * into the settings modal.
		 */
		let $presetContainerOriginal = $(
			`.hu-preset-container#${name}`
		);
		let $presetContainer = $presetContainerOriginal.clone(true);

		/**
		 * Remove all the ids from the cloned elements input fields
		 * for solving the non-unique id problem.
		 */
		$presetContainer.each(function () {
			$(this).find('input').removeAttr('id');
		});

		/**
		 * Initiate the modal
		 */
		$(this).helixUltimateOptionsModal({
			flag: 'edit-presets',
			title: `<span class='fas fa-cogs hu-mr-2'></span> Edit Preset: ${name}`,
			class: `hu-modal-small edit-preset-modal modal-${name}`,
			applyBtnClass: 'hu-save-preset',
			footerButtons: [
				`<a href="#" class="hu-btn hu-btn-secondary helix-preset-reset hu-ml-auto"><span class="fas fa-sync-alt" aria-hidden="true"></span> Reset to Default</a>`,
			],
		});

		/**
		 * Add the cloned container into
		 * the modal inner body
		 */
		$('.hu-options-modal-inner').html(
			$presetContainer
				.removeAttr('id')
				.removeAttr('style')
				.addClass('hu-options-modal-content')
		);

		let $colorInputs = $presetContainer.find('input.preset-control');

		/**
		 * Get the changed input value
		 * and update the data object.
		 * So, after that the data object will be updated
		 */
		$colorInputs.each(function () {
			$(this).on('change', function (e) {
				e.preventDefault();
				let prop = $(this).attr('name');
				let value = $(this).val();

				data[prop] = value;
			});
		});

		let $applyBtn = $(`.edit-preset-modal.modal-${name}`).find(
			'.hu-save-preset'
		);

		let $resetBtn = $(`.edit-preset-modal.modal-${name}`).find(
			'.helix-preset-reset'
		);

		if ($applyBtn.length) {
			/**
			 * On click Apply button
			 */
			$applyBtn.on('click', function (e) {
				e.preventDefault();
				reflectPresetChanges(
					data,
					name,
					$presetContainerOriginal,
					$presetContainer
				);
			});

			$resetBtn.on('click', function (e) {
				e.preventDefault();

				let confirm = window.confirm(
					'Do you really want to reset your changes to default?'
				);

				if (confirm) {
					let resetData = JSON.parse($('#default-values').val());
					reflectPresetChanges(
						resetData[name],
						name,
						$presetContainerOriginal,
						$presetContainer
					);
				}
			});
		}
	});
});
