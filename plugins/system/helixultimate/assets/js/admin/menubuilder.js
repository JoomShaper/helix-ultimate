/**
 * Menu Builder Javascript functions
 *
 * @since   2.0.0
 */

jQuery(function($) {
    const config = Joomla.getOptions('meta') || {};
    const state = {
        data: {
            title: '',
            alias: '',
            link: '',
            target: '',
            params: {}
        }
    }


    /**
     * Set state function
     */
    const setState = function (object, callback = undefined) {
		Object.entries(object).forEach(([key, value]) => {
			state[key] = value;
		});

		!!callback && callback(state);
		render();
	};

    ;(function componentDidMount(){
        console.log("Component Did mount!");
        fetchMenuItems($('select[name=menu]').val() || 'mainmenu');
        changeMenuType();

        handleAddNewMenu();
    })();


    /**
     * Render function
     */
    function render() {
        console.log("state", state);
    }



    /** User Defined functions. */

    function changeMenuType() {
        const $menuTypeField = $('select[name=menu]');
        $menuTypeField.on('change', function() {
            const menuType = $(this).val();
            fetchMenuItems(menuType);
        });
    }

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
            }
        });
    }


    function itemSorting() {
        $('#hu-menu-builder-container').find('.hu-menuitem-list').each(function() {
            $(this).sortable({
                connectWith: '.hu-menuitem-list',
                placeholder: 'ui-state-highlight',
                forcePlaceholderSize: true,
                forceHelperSize: true,
                handle: '.drag-handler',
                cursor: 'move',
                opacity: 0.8,
                tolerance: 'intersect',
                start: function(e, ui) {
                    $('.hu-menuitem-list')
                        .find('.ui-state-highlight')
                        .addClass($(ui.item).attr('class'))
                        .css('height', '40px');
                },
                update: function(e, ui) {
                    $item = $(ui.item);
                    const $container = $item.closest('ul');
                    const containerId = $container.data('container') || 1;
                    const containerLevel = $container.data('level') || 1;

                    const itemId = $item.data('itemid') || false;
                    const parentId = $item.data('parent') || false;

                    /**
                     * If drop container id and item's parent id is not same,
                     * then update the parent of the drag item by the container id.
                     */
                    if (parentId !== containerId)
                    {
                        parentAdoption({id: itemId, parent: containerId})
                            .then(function(res) {
                                res = typeof (res) === 'string' && res.length > 0
                                    ? JSON.parse(res)
                                    : false;

                                if (res && res.status) {
                                    // Update the sorted item's parent id and the level.
                                    $item.data('parent', containerId).attr('data-parent', containerId);
                                    $item.data('level', containerLevel).attr('data-level', containerLevel);

                                    // After changing the parent re-order the list
                                    handleItemOrdering($container);
                                }
                            });
                    } else {
                        handleItemOrdering($container);
                    }

                }
            }).disableSelection();
        });
    }

    function handleItemOrdering($container) {
        /** Create the ordering data & perform ordering by ajax. */
        const orderData = {cid: [], order: []};

        $container.find('> li').each(function(index) {
            orderData.cid.push($(this).data('itemid'));
            orderData.order.push(index + 1);
        });

        saveOrderAjax(orderData).then(function() {
            Joomla.reloadPreview();
        });
    }

    function parentAdoption(data) {
        return new Promise(function(resolve, reject) {
            const url = `${config.base}/administrator/index.php?option=com_ajax&helix=ultimate&request=task&action=parentAdoption`;
            $.ajax({
                method: "POST",
                url,
                data,
                success(response) {
                    resolve(response);
                },
                error(err) {
                    reject(err);
                }
            });
        });
    }

    function saveOrderAjax(data) {
        return new Promise(function(resolve, reject) {
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
                }
            })
        })
    }

    /** Create a new menu */
    function handleAddNewMenu() {
        $(document).on('click', '.hu-add-menu-item', async function(e) {
            e.preventDefault();
            await openMenuItemModal();
        });
    }

    /** Open modal for adding new menu item */
    async function openMenuItemModal() {
        $(this).helixUltimateOptionsModal({
            flag: 'add-new-menuitem',
            title: "Create New Menu Item",
            class: 'hu-modal-small hu-add-new-menuitem',
        });

        $('.hu-options-modal-inner')
            .html('<div class="hu-modal-content">Please wait...</div>');

        const resp = await getModalContents();
        const {data} = typeof resp === 'string' && resp.length > 0
            ? JSON.parse(resp)
            : {data: ''};

        $('.hu-options-modal-inner').html(data);
    }

    /** Get modal contents from PHP  */
    function getModalContents() {
        return new Promise((resolve, reject) => {
            const url = `${config.base}/administrator/index.php?option=com_ajax&helix=ultimate&request=task&action=getMenuItemModalContents`;
            $.ajax({
                method: "GET",
                url,
                beforeSend() {
                    /**
                     * Remove the click event listeners.
                     */
                    $(document).off('click', '.hu-dropdown-toggle');
                    $(document).off('click', '.hu-menu-type-item');
                },
                success(response) {
                    resolve(response);
                },
                error(err) {
                    reject(err);
                },
                complete() {
                    /**
                     * Attach the click events on the toggler and menu type selection
                     */
                    menuTypeDropdownToggle();
                    handleMenuTypeSelection();
                }
            });
        });
    }

    function menuTypeDropdownToggle() {
        $(document).on('click', '.hu-dropdown-toggle', function() {
            const target = $(this).data('target');

            if ($(target).hasClass('active')) {
                $(target).slideUp().removeClass('active');
                return;
            }

            $(target).slideDown().addClass('active');
        });
    }

    function handleMenuTypeSelection() {
        $(document).on('click', '.hu-menu-type-item', function(e) {
            e.preventDefault();
            const menuType = $(this).data('menutype');
            setMenuType(menuType).then(res => {
                res = typeof res === 'string' && res.length > 0
                    ? JSON.parse(res)
                    : false;
                if (res && res.status) {
                    $('.hu-modal-content').find('.hu-item-request').html(res.request);
                    $('.hu-modal-content').find('.hu-item-link').html(res.link);
                }
            });
            $('.hu-dropdown').fadeOut().removeClass('active');
        });
    }

    function setMenuType(type) {
        return new Promise((resolve, reject) => {
            const url = `${config.base}/administrator/index.php?option=com_ajax&helix=ultimate&request=task&action=setMenuType&type=${type}`;
            $.ajax({
                method: "GET",
                url,
                success(response) {
                    resolve(response);
                },
                error(err) {
                    reject(err);
                }
            });
        })
    }

});