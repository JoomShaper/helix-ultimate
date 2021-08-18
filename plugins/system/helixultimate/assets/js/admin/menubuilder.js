/**
 * Menu Builder Javascript functions
 *
 * @since   2.0.0
 */

jQuery(function ($) {
	const config = Joomla.getOptions('meta') || {};
	var $itemModal = null,
		$itemFrame = null,
		menuType = $('select[name=menu]').val() || 'mainmenu';

	(function initialize() {
		fetchMenuItems(menuType);
		changeMenuType();

		handleAddNewMenu();
		removeEventListeners();
	})();

	function removeEventListeners() {
		$(document).off('click', '.hu-add-menu-item');
	}
	/**
	 * Rebuild menu items tree
	 */
	function rebuildMenu() {
		const url = `${config.base}/administrator/index.php?option=com_ajax&helix=ultimate&request=task&action=rebuildMenu&helix_id=${helixUltimateStyleId}`;
		$.ajax({
			method: 'GET',
			url,
			success(res) {},
			error(err) {
				alert('Rebuild menu failed with: ' + err.message);
			},
		});
	}

	/** User Defined functions. */
	function changeMenuType() {
		const $menuTypeField = $('select[name=menu]');
		$menuTypeField.on('change', function () {
			menuType = $(this).val();
			fetchMenuItems(menuType);
		});
	}

	/**
	 * Fetch the menu items for a menu type using ajax and attach the
	 * HTML contents to the panel.
	 */
	function fetchMenuItems(menuType) {
		const url = `${config.base}/administrator/index.php?option=com_ajax&helix=ultimate&request=task&action=getMenuItems&menutype=${menuType}&helix_id=${helixUltimateStyleId}`;
		$.ajax({
			type: 'GET',
			url,
			beforeSend() {
				/** Remove click event listener before a new menu item fetch request. */
				$(document).off('click', '.hu-branch-tools .hu-branch-tools-icon');
			},
			success(response) {
				response = response && JSON.parse(response);

				if (response.status) {
					$('#hu-menu-builder-container').html(response.data);
					removeEventListeners();

					/** After successful tree generation run the sortable. */
					Joomla.sortable.run();
					onAfterSort();
					openToolbar();
					handleEditMenuItem();
					handleDeleteMenuItem();
					openMegaMenuModal();
				}
			},
			complete() {
				Joomla.reloadPreview();
				Joomla.utils.calculateSiblingDistances();
			},
		});
	}

	function removeEventListeners() {
		$(document).off('click', '.hu-branch-tools .hu-branch-tools-list-delete');
		$(document).off('click', '.hu-branch-tools .hu-branch-tools-list-edit');
		$(document).off('click', '.hu-branch-tools .hu-branch-tools-list-megamenu');
	}

	/** Handle open megamenu options */
	function openMegaMenuModal() {
		$(document).on('click', '.hu-branch-tools .hu-branch-tools-list-megamenu', async function (e) {
			e.preventDefault();
			closeToolbar();

			const $branch = $(this).closest('.hu-menu-tree-branch');
			const parent = $branch.data('parent') || 1;
			const itemId = $branch.data('itemid') || 0;

			const response = await getMegaMenuBodyHTML(itemId);

			if (response.status) {
				$(document).helixUltimateMegamenuModal({
					title: parent == '1' ? 'Mega Menu' : 'Settings',
					className: 'hu-mega-menu-builder',
					targetType: 'id',
					target: 'megaMenuModal',
					body: response.html,
				});

				Joomla.helixMegaMenu.run();
			}
		});
	}

	/** Get Mega Menu Body HTML using AJAX */
	function getMegaMenuBodyHTML(itemId) {
		return new Promise((resolve, reject) => {
			const url = `${config.base}/administrator/index.php?option=com_ajax&helix=ultimate&request=task&action=generateMegaMenuBody&id=${itemId}&helix_id=${helixUltimateStyleId}`;
			$.ajax({
				method: 'GET',
				url,
				success(res) {
					res = typeof res === 'string' && res.length > 0 ? JSON.parse(res) : false;
					resolve(res);
				},
				error(err) {
					reject(err);
				},
			});
		});
	}

	/** ======================= Delete Menu Item Section ================= */

	/**
	 * Handling deleting a menu item.
	 */
	function handleDeleteMenuItem() {
		$(document).on('click', '.hu-branch-tools .hu-branch-tools-list-delete', function (e) {
			e.preventDefault();
			closeToolbar();

			const itemId = $(this).closest('.hu-menu-tree-branch').data('itemid') || 0;
			const confirm = window.confirm('Are you sure to delete the item?');

			if (confirm) {
				deleteMenuItem(itemId);
			}
		});
	}

	/**
	 * Delete / add to trash a menu item.
	 *
	 * @param {int} itemId
	 */
	function deleteMenuItem(itemId) {
		const url = `${config.base}/administrator/index.php?option=com_menus&task=items.trash&cid[]=${itemId}`;
		$.ajax({
			method: 'GET',
			url,
			success(res) {
				fetchMenuItems(menuType);
			},
			error(err) {
				Joomla.HelixToaster.error('Something went wrong!', 'Error');
			},
			complete() {
				rebuildMenu();
				Joomla.HelixToaster.error('Menu item has been successfully removed!', 'Success');
			},
		});
	}

	/**
	 * Open the options menu on click the tool icon
	 */
	function openToolbar() {
		$(document).on('click', '.hu-branch-tools .hu-branch-tools-icon', function (e) {
			e.preventDefault();
			let self = this;
			$('.hu-branch-tools .hu-branch-tools-list').each(function () {
				if ($(this).hasClass('active') && $(this)[0] !== $(self).next('.hu-branch-tools-list')[0]) {
					$(this).removeClass('active');
					$(this).fadeIn();
				}
			});
			$(this).next('.hu-branch-tools-list').toggleClass('active').fadeToggle();
		});
	}

	/**
	 * CLose toolbar
	 */
	function closeToolbar() {
		$('.hu-branch-tools .hu-branch-tools-list').each(function () {
			if ($(this).hasClass('active')) {
				$(this).removeClass('active');
				$(this).hide();
			}
		});
	}

	/**
	 * Handling editing menu item.
	 * This will open a modal for editing the item.
	 */
	function handleEditMenuItem() {
		$(document).on('click', '.hu-branch-tools .hu-branch-tools-list-edit', function (e) {
			e.preventDefault();
			closeToolbar();

			const itemId = $(this).closest('.hu-menu-tree-branch').data('itemid') || 0;
			openEditMenuItemModal(itemId);
		});
	}

	/**
	 * Opening the modal for editing menu item.
	 *
	 * @param {int} itemId
	 */
	function openEditMenuItemModal(itemId) {
		loadFrameModal({
			title: 'Edit Menu Item',
			targetType: 'id',
			target: 'editMenuItem',
			className: 'edit-menu-item',
			frameUrl:
				config.base +
				'/administrator/index.php?option=com_menus&task=item.edit&tmpl=component&menutype=' +
				menuType +
				'&id=' +
				itemId,
		});
		handleSaveMenuItem('edit-menu-item', 'item.save');
	}

	/**
	 * Handle after sorting tasks. i.e. check parental changes and update
	 * them to the database.
	 */
	function onAfterSort() {
		$(document).on('sortCompleted', async function (e, ui) {
			let itemId = ui.item.data('itemid'),
				parentId = ui.item.data('parent'),
				parent = ui.item.getParent(),
				newParentId = parent.length ? parent.data('itemid') : 1;

			if (+parentId !== +newParentId) {
				const resp = await parentAdoption({
					id: itemId,
					parent: newParentId,
				});

				if (resp.status) {
					ui.item.data('parent', newParentId).attr('data-parent', newParentId);
					handleBranchOrdering(parent);
				}
				return;
			}

			handleBranchOrdering(parent, true);
		});
	}

	/** Handle Menu Item ordering for a container. */
	function handleBranchOrdering(parent, onlyOrder = false) {
		let children = parent.length ? parent.getChildren() : $(document).getRootChildren();

		if (children.length === 0) return;

		const orderData = { cid: [], order: [] };
		children.each(function (index) {
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
			const url = `${config.base}/administrator/index.php?option=com_ajax&helix=ultimate&request=task&action=parentAdoption&helix_id=${helixUltimateStyleId}`;
			$.ajax({
				method: 'POST',
				url,
				data,
				success(response) {
					response = typeof response === 'string' && response.length > 0 ? JSON.parse(response) : response;
					resolve(response);
				},
				error(err) {
					reject(err);
				},
			});
		});
	}

	/** ======================= Add new menu item section ================= */

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

			loadFrameModal({
				title: 'Add New Item',
				targetType: 'id',
				target: 'addNewMenuItem',
				className: 'add-new-menu-item',
				frameUrl:
					config.base +
					'/administrator/index.php?option=com_menus&task=item.add&tmpl=component&menutype=' +
					menuType,
			});
			handleSaveMenuItem('add-new-menu-item', 'item.apply');
		});
	}

	/** Create Frame modal and load the iframe on it. */
	function loadFrameModal({ title, targetType, target, className, frameUrl }) {
		$(document).helixUltimateFrameModal({
			title,
			targetType,
			target,
			className,
			frameUrl,
		});

		/** Initialize variables */
		$itemModal = $(`.hu-modal.${className}`);
		$itemFrame = $itemModal.find('iframe');
		$itemFrame.off('load');
		$(document).off('click', `.hu-modal.${className} button.hu-save-btn`);
		$itemModal.find('.hu-save-btn').prop('disabled', true);
		handleCloseModal();
	}

	/** Close modal on clicking the cancel button. */
	function handleCloseModal() {
		$itemModal.find('.hu-cancel-btn').on('click', function (e) {
			$(this).closeModal();
		});
	}

	/** Show or hide the spinner */
	function showSpinner(status) {
		const $spinner = $('.hu-spinner');

		if (status) {
			if ($spinner.hasClass('hidden')) {
				$spinner.removeClass('hidden');
			}
		} else {
			if (!$spinner.hasClass('hidden')) {
				$spinner.addClass('hidden');
			}
		}
	}

	/** Handle Save functionality. */
	function handleSaveMenuItem(className, task = 'item.apply') {
		const saveBtnSelector = `.hu-modal.${className} button.hu-save-btn`;

		$itemFrame.on('load', function () {
			const frameDoc = $itemFrame.contents();
			$(saveBtnSelector).prop('disabled', false);

			/** If already the event attached then remove it first. */
			$(document).off('click', saveBtnSelector);

			/** Handle the click event. Use debounce for preventing multiple click. */
			$(document).on(
				'click',
				saveBtnSelector,
				Joomla.utils.debounce(async function () {
					const $form = $(frameDoc).find('form');

					// Set the task as `item.apply` for saving
					$form.find('input[name=task]').val(task);
					$form.find('input[name=task]').attr('value', task);
					const isValidForm = frameDoc[0].formvalidator.isValid($form[0]);

					showSpinner(true);

					// If the form is valid then refetch the menu items
					// & close the modal.
					if (isValidForm) {
						try {
							const res = await submitItemForm($form);
							const $responseElement = $('<div class="hu-menuitem-resp"></div>').hide().html(res);
							const $alertHeading = $responseElement.find('.alert-heading');
							const $alertMessage = $responseElement.find('.alert-message');
							const respType = $alertHeading.length > 0 ? $alertHeading.text() : '';
							const message = $alertMessage.length > 0 ? $alertMessage.text() : '';

							/**
							 * For Joomla!4 detect the system-message-container and inside the <noscript>
							 * tag find if there any `.alert-danger` class.
							 * If so then show the error.
							 */
							const $messageContainer = $responseElement.find('#system-message-container noscript');
							const $element = $('<div></div>').hide().html($messageContainer.text());
							const $errorAlert = $element.find('.alert-danger');

							if ($errorAlert.length > 0) {
								Joomla.HelixToaster.error($messageContainer.html(), 'Error');
								showSpinner(false);
								return;
							}

							if (res && respType !== 'Error') {
								fetchMenuItems(menuType);
								$(this).closeModal();

								if (task === 'item.apply') {
									Joomla.HelixToaster.success('Menu item has been successfully added!', 'Saved');
								} else if (task === 'item.save') {
									Joomla.HelixToaster.success('Changes have been successfully saved!', 'Updated');
								}
							} else {
								Joomla.HelixToaster.error(message, 'Error');
							}

							showSpinner(false);
						} catch (err) {
							Joomla.HelixToaster.error('Something went wrong!', 'Error');
							showSpinner(false);
						}
					} else {
						showSpinner(false);
					}
				}, 500)
			);
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
