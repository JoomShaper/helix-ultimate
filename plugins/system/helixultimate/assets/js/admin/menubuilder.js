/**
 * Menu Builder Javascript functions
 *
 * @since   2.0.0
 */

jQuery(function ($) {
	const config = Joomla.getOptions('meta') || {};
	var $itemModal = null,
		$itemFrame = null;

	(function initialize() {
		fetchMenuItems($('select[name=menu]').val() || 'mainmenu');
		changeMenuType();

		handleAddNewMenu();
	})();

	/** User Defined functions. */
	function changeMenuType() {
		const $menuTypeField = $('select[name=menu]');
		$menuTypeField.on('change', function () {
			const menuType = $(this).val();
			fetchMenuItems(menuType);
		});
	}

	/**
	 * Fetch the menu items for a menu type using ajax and attach the
	 * HTML contents to the panel.
	 */
	function fetchMenuItems(menuType) {
		const url = `${config.base}/administrator/index.php?option=com_ajax&helix=ultimate&request=task&action=getMenuItems&menutype=${menuType}`;
		$.ajax({
			type: 'GET',
			url,
			success(response) {
				response = response && JSON.parse(response);
				console.log(response);
				if (response.status) {
					$('#hu-menu-builder-container').html(response.data);
					itemSorting();
				}
			},
		});
	}

	/** Instantiate the sortable for the menu items. */
	function itemSorting() {
		$('#hu-menu-builder-container')
			.find('.hu-menuitem-list')
			.each(function () {
				$(this)
					.sortable({
						connectWith: '.hu-menuitem-list',
						placeholder: 'ui-state-highlight',
						forcePlaceholderSize: true,
						forceHelperSize: true,
						handle: '.drag-handler',
						cursor: 'move',
						opacity: 0.8,
						tolerance: 'intersect',
						start: function (e, ui) {
							$('.hu-menuitem-list')
								.find('.ui-state-highlight')
								.addClass($(ui.item).attr('class'))
								.css('height', '40px');
						},
						update: function (e, ui) {
							$item = $(ui.item);
							const $container = $item.closest('ul');
							const containerId =
								$container.data('container') || 1;
							const containerLevel =
								$container.data('level') || 1;

							const itemId = $item.data('itemid') || false;
							const parentId = $item.data('parent') || false;

							/**
							 * If drop container id and item's parent id is not same,
							 * then update the parent of the drag item by the container id.
							 */
							if (parentId !== containerId) {
								parentAdoption({
									id: itemId,
									parent: containerId,
								}).then(function (res) {
									res =
										typeof res === 'string' &&
										res.length > 0
											? JSON.parse(res)
											: false;

									if (res && res.status) {
										// Update the sorted item's parent id and the level.
										$item
											.data('parent', containerId)
											.attr('data-parent', containerId);
										$item
											.data('level', containerLevel)
											.attr('data-level', containerLevel);

										// After changing the parent re-order the list
										handleItemOrdering($container);
									}
								});
							} else {
								handleItemOrdering($container);
							}
						},
					})
					.disableSelection();
			});
	}

	/** Handle Menu Item ordering for a container. */
	function handleItemOrdering($container) {
		/** Create the ordering data & perform ordering by ajax. */
		const orderData = { cid: [], order: [] };

		$container.find('> li').each(function (index) {
			orderData.cid.push($(this).data('itemid'));
			orderData.order.push(index + 1);
		});

		saveOrderAjax(orderData).then(function () {
			Joomla.reloadPreview();
		});
	}

	/** Change the parent id of an item after sorting. */
	function parentAdoption(data) {
		return new Promise(function (resolve, reject) {
			const url = `${config.base}/administrator/index.php?option=com_ajax&helix=ultimate&request=task&action=parentAdoption`;
			$.ajax({
				method: 'POST',
				url,
				data,
				success(response) {
					resolve(response);
				},
				error(err) {
					reject(err);
				},
			});
		});
	}

	/** Save Menu Item's order by ajax request. */
	function saveOrderAjax(data) {
		return new Promise(function (resolve, reject) {
			const url = `${config.base}/administrator/index.php?option=com_menus&view=items&task=items.saveOrderAjax&tmpl=component`;
			$.ajax({
				method: 'POST',
				url,
				data,
				success(response) {
					resolve(response);
				},
				error(err) {
					reject(err);
				},
			});
		});
	}

	/** Create a new menu */
	function handleAddNewMenu() {
		$(document).on('click', '.hu-add-menu-item', function (e) {
			e.preventDefault();

			loadFrameModal();
			handleSaveMenuItem();
		});
	}

	/** Create Frame modal and load the iframe on it. */
	function loadFrameModal() {
		$(document).helixUltimateFrameModal({
			title: 'Add New Menu Item',
			targetType: 'id',
			target: 'addNewMenuItem',
			className: 'add-new-menu-item',
			frameUrl:
				config.base +
				'/administrator/index.php?option=com_menus&task=item.add&tmpl=component&menutype=mainmenu',
		});

		/** Initialize variables */
		$itemModal = $('.hu-modal.add-new-menu-item');
		$itemFrame = $itemModal.find('iframe');
		$itemFrame.off('load');
		$(document).off(
			'click',
			'.hu-modal.add-new-menu-item button.hu-save-btn'
		);
		$itemModal.find('.hu-save-btn').prop('disabled', true);
		handleCloseModal();
	}

	/** Close modal on clicking the cancel button. */
	function handleCloseModal() {
		$itemModal.find('.hu-cancel-btn').on('click', function (e) {
			$(this).closeModal();
		});
	}

	/** Handle Save functionality. */
	function handleSaveMenuItem() {
		const saveBtnSelector =
			'.hu-modal.add-new-menu-item button.hu-save-btn';

		$itemFrame.on('load', function () {
			const frameDoc = $itemFrame.contents();
			$(saveBtnSelector).prop('disabled', false);

			$(document).on('click', saveBtnSelector, async function () {
				const $form = $(frameDoc).find('form');

				// Set the task as `item.apply` for saving
				$form.find('input[name=task]').val('item.apply');
				const isValidForm = frameDoc[0].formvalidator.isValid($form[0]);

				// If the form is valid then refetch the menu items
				// & close the modal.
				if (isValidForm) {
					try {
						const res = await submitItemForm($form);
						if (res) {
							fetchMenuItems(
								$('select[name=menu]').val() || 'mainmenu'
							);
							$(this).closeModal();
						}
					} catch (err) {
						alert('Something went wrong!');
					}
				}
			});
		});
	}

	/** Submit the menu item form using ajax and return promise. */
	function submitItemForm($form) {
		const url = $form.attr('action');
		const data = $form.serializeArray();

		return new Promise((resolve, reject) => {
			$.ajax({
				method: 'POST',
				url,
				data,
				success(res) {
					resolve(res);
				},
				error(err) {
					reject(err);
				},
			});
		});
	}
});
