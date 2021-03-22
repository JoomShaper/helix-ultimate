jQuery(function ($) {
	$('.hu-input-color').each(function () {
		$(this).addClass('minicolors');
	});

	$('.hu-menu-builder .minicolors').each(function () {
		$(this).minicolors({
			control: 'hue',
			position: 'bottom',
			theme: 'bootstrap',
		});
	});

	$('.hu-field-alignment .hu-switcher-action').on('click', function (e) {
		e.preventDefault();

		const $siblings = $(this).siblings();
		if ($siblings.hasClass('active')) {
			$siblings.removeClass('active');
		}

		$(this).addClass('active');
		const $inputField = $(this)
			.closest('.hu-field-alignment')
			.find('input[type=hidden]');
		$inputField.val($(this).data('value')).trigger('change');
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
			const $inputField = $(this)
				.closest('.hu-menu-hierarchy-container')
				.find('input[type=hidden]');

			let checked = $(this).prop('checked');
			let value = [];

			if (!checked) if (value.length > 0) value = [];

			$siblings.each(function () {
				const $input = $(this).find('input[type=checkbox]');
				$input.prop('checked', checked);
				const v = $input.val();
				if (checked) if (value.indexOf(v) === -1) value.push(v);
			});

			$inputField.val(JSON.stringify(value)).trigger('change');
		}
	);

	$(document).on(
		'change',
		'.hu-menu-hierarchy-container .hu-menu-item-selector:not(.level-0)',
		function (e) {
			e.preventDefault();
			const $inputField = $(this)
				.closest('.hu-menu-hierarchy-container')
				.find('input[type=hidden]');
			const $selectAllInputField = $(this)
				.closest('.hu-menu-hierarchy-list')
				.find('input[type=checkbox].select-all');
			const elements = $selectAllInputField.data('elements');

			let value = $inputField.val();
			value = (value.length && JSON.parse(value)) || [];

			const val = $(this).val();

			if ($(this).prop('checked')) {
				if (value.indexOf(val) === -1) value.push(val);
			} else {
				let index = value.indexOf(val);
				if (index > -1) {
					value.splice(index, 1);
				}
			}

			if (value.length === elements.length) {
				$selectAllInputField.prop('checked', true);
			} else {
				$selectAllInputField.prop('checked', false);
			}
			$inputField.val(JSON.stringify(value)).trigger('change');
		}
	);

	/**
	 * Method to check condition and change the target visibility
	 * @param {jQuery}  target
	 * @param {Boolean} animate
	 */
	function linkedoptions(target, animate, $context) {
		var showfield = true,
			jsondata = target.data('revealon') || [],
			itemval,
			condition,
			fieldName,
			$fields;

		// Check if target conditions are satisfied
		for (var j = 0, lj = jsondata.length; j < lj; j++) {
			condition = jsondata[j] || {};
			fieldName = condition.field;
			$fields = $context.find(
				'[name="' + fieldName + '"], [name="' + fieldName + '[]"]'
			);

			condition['valid'] = 0;

			// Test in each of the elements in the field array if condition is valid
			$fields.each(function () {
				var $field = $(this);

				// If checkbox or radio box the value is read from properties
				if (['checkbox', 'radio'].indexOf($field.attr('type')) !== -1) {
					itemval = ($field.prop('checked') >> 0).toString();
				} else {
					// select lists, textarea etc. Note that multiple-select list returns an Array here
					// se we can always tream 'itemval' as an array
					itemval = $field.val();
					// a multi-select <select> $field  will return null when no elements are selected so we need to define itemval accordingly
					if (
						itemval == null &&
						$field.prop('tagName').toLowerCase() == 'select'
					) {
						itemval = [];
					}
				}

				// Convert to array to allow multiple values in the field (e.g. type=list multiple)
				// and normalize as string
				if (!(typeof itemval === 'object')) {
					itemval = JSON.parse('["' + itemval + '"]');
				}

				for (var i in itemval) {
					if (!itemval.propertyIsEnumerable(i)) {
						continue;
					}

					if (
						jsondata[j]['sign'] == '=' &&
						jsondata[j]['values'].indexOf(itemval[i]) !== -1
					) {
						jsondata[j]['valid'] = 1;
					}

					if (
						jsondata[j]['sign'] == '!=' &&
						jsondata[j]['values'].indexOf(itemval[i]) === -1
					) {
						jsondata[j]['valid'] = 1;
					}
				}
			});

			if (condition['op'] === '') {
				if (condition['valid'] === 0) {
					showfield = false;
				}
			} else {
				if (
					condition['op'] === 'AND' &&
					condition['valid'] + jsondata[j - 1]['valid'] < 2
				) {
					showfield = false;
					condition['valid'] = 0;
				}

				if (
					condition['op'] === 'OR' &&
					condition['valid'] + jsondata[j - 1]['valid'] > 0
				) {
					showfield = true;
					condition['valid'] = 1;
				}
			}
		}

		if (target.is('option')) {
			target.toggle(showfield);
			target.attr('disabled', showfield ? false : true);

			var parent = target.parent();
			if ($('#' + parent.attr('id') + '_chzn').length) {
				parent.trigger('liszt:updated');
				parent.trigger('chosen:updated');
			}
		}

		animate =
			animate &&
			!target.hasClass('no-animation') &&
			!target.hasClass('no-animate') &&
			!target.find('.no-animation, .no-animate').length;

		if (animate) {
			showfield ? target.slideDown() : target.slideUp();
			return;
		}

		target.toggle(showfield);
	}

	/**
	 * Method for setup the 'showon' feature, for the fields in given container
	 * @param {HTMLElement} container
	 */
	Joomla.setUpShowon = function (container) {
		container = container || document;
		var $showonFields = $(container).find('[data-revealon]');

		for (var is = 0, ls = $showonFields.length; is < ls; is++) {
			(function () {
				var $target = $($showonFields[is]),
					jsondata = $target.data('revealon') || [],
					field,
					$fields = $();

				// Collect an all referenced elements
				for (var ij = 0, lj = jsondata.length; ij < lj; ij++) {
					field = jsondata[ij]['field'];
					$fields = $fields.add(
						$('[name="' + field + '"], [name="' + field + '[]"]')
					);
				}

				// Check current condition for element
				linkedoptions($target, true, container);

				// Attach events to referenced element, to check condition on change and keyup
				$fields.on('change keyup', function () {
					linkedoptions($target, true, container);
				});
			})();
		}
	};

	/**
	 * Unit field change handling.
	 * On change of the unit selector or on blur of the
	 * input field will change the value and store to the hidden field.
	 */
	$(document).on('blur', '.hu-unit-field-input', function (e) {
		e.preventDefault();

		let value = $(this).val(),
			unit = 'px',
			$field = $(this).parent().find('input.hu-unit-field-value');

		/** Remove all the spaced from the value. */
		value = value.replace(/\s/g, '');

		if (value === '') {
			$field.val('');
			return;
		}

		const regex = /^([+-]?(?:\d+|\d*\.\d+))(px|em|rem|%)?$/i;
		const match = value.match(regex);

		/**
		 * Check if the input value is valid or not.
		 * If valid then
		 */
		if (match && match.length > 0) {
			[_, value, unit] = match;
			if (unit === undefined) {
				unit =
					$(this).parent().find('select.hu-unit-select').val() ||
					'px';
			}
		} else {
			value = parseFloat(value) || '';
		}

		$(this).val(value);
		$(this).next('select.hu-unit-select').val(unit);
		$field.val(`${value}${unit}`).trigger('change');
	});

	$(document).on('change', 'select.hu-unit-select', function (e) {
		e.preventDefault();
		let unit = $(this).val() || 'px',
			$input = $(this).parent().find('input.hu-unit-field-input'),
			value = $input.val(),
			$field = $(this).parent().find('input.hu-unit-field-value');

		if (!!value) {
			$field.val(`${value}${unit}`).trigger('change');
		}
	});
});
