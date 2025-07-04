/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2023 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

// Preloader
jQuery(window).on('load', function () {
	if (jQuery('.sp-loader-with-logo').length > 0) {

		move();
	}
	jQuery('.sp-pre-loader').fadeOut(500, function () {
		jQuery(this).remove();
	});
});

/**
 * Move the progress bar
 */
function move() {
	var elem = document.getElementById('line-load');
	var width = 1;
	var id = setInterval(frame, 10);
	function frame() {
		if (width >= 100) {
			clearInterval(id);
		} else {
			width++;
			elem.style.width = width + '%';
		}
	}

}

jQuery(function ($) {

	/**
	 * Helix settings data
	 *
	 */
	var settings = Joomla.getOptions('data') || {};

	/**
	 * sticky header
	 *
	 * @param {string} className the header className
	 */
	var handleStickiness = function (className, offsetTop) {
		if ($('body:not(.layout-edit-iframe)').hasClass(className)) {
			var $header = $('#sp-header');
			var headerHeight = $header.outerHeight();
			var $stickyHeaderPlaceholder = $('.sticky-header-placeholder');
			let $stickyOffset = '100';

			if (settings.header !== undefined && settings.header.stickyOffset !== undefined) {
				$stickyOffset = settings.header.stickyOffset || '100';
			}

			var stickyHeader = function () {
				var scrollTop = $(window).scrollTop();
				if (scrollTop >= offsetTop + Number($stickyOffset)) {
					$header.addClass('header-sticky');
					$stickyHeaderPlaceholder.height(headerHeight);
				} else {
					if ($header.hasClass('header-sticky')) {
						$header.removeClass('header-sticky');
						$stickyHeaderPlaceholder.height('inherit');
					}
				}
			};

			stickyHeader();
			$(window).scroll(function () {
				stickyHeader();
			});

			if ($('body').hasClass('layout-boxed')) {
				var windowWidth = $header.parent().outerWidth();
				$header.css({ 'max-width': windowWidth, left: 'auto' });
			}
		} else {
			var $header = $('#sp-header');
			if ($header.hasClass('header-sticky')) {
				$header.removeClass('header-sticky');
			}
			$(window).off('scroll');
		}
	};

	/**
	 * Calculate the header offset based on the
	 * backend preview iframe site and real site.
	 *
	 * @return  integer The offset value
	 */
	function getHeaderOffset() {
		/**
		 * Real site header offset top
		 *
		 */
		let $header = $('#sp-header');
		let stickyHeaderTop = $header.offset().top;

		/**
		 * Backend edit preview iframe header offset top
		 *
		 */
		let $backHeader = $('body.back-panel').find('#sp-header');
		let backPanelStickyHeaderTop = null;

		/**
		 * If the class .back-panel exists,
		 * that means this is from backend preview frame,
		 * then get the offset top, as it varies with the original one.
		 *
		 */
		if ($backHeader.length > 0) {
			backPanelStickyHeaderTop = $backHeader.offset().top;
		}

		// By Default the header offset is the original header top
		let headerOffset = stickyHeaderTop;

		/**
		 * If back panel sticky header top has value rather than null
		 * that means the device type is changes from desktop to table or mobile or vice-versa.
		 * If value found then subtract the settings topbar height from the offset top.
		 *
		 */
		if (backPanelStickyHeaderTop !== null) {
			headerOffset = backPanelStickyHeaderTop - settings.topbarHeight;
			headerOffset = headerOffset < 0 ? stickyHeaderTop : headerOffset;
		}

		return headerOffset;
	}

	const headerExist = $('#sp-header');
	if (headerExist.length > 0) {
		handleStickiness('sticky-header', getHeaderOffset());
	}

	// go to top
	$(window).scroll(function () {
		if ($(this).scrollTop() > 100) {
			$('.sp-scroll-up').fadeIn();
		} else {
			$('.sp-scroll-up').fadeOut(400);
		}
	});

	$('.sp-scroll-up').click(function () {
		$('html, body').animate(
			{
				scrollTop: -60,
			},
			600
		);
		return false;
	});

	//mega menu
	$('.sp-megamenu-wrapper').parent().parent().css('position', 'static').parent().css('position', 'relative');
	$('.sp-menu-full').each(function () {
		$(this).parent().addClass('menu-justify');
	});



	$('#offcanvas-toggler, .offcanvas-toggler-secondary, .offcanvas-toggler-full').on('click', function (event) {
		event.preventDefault();
		openOffcanvas();
	});

	// Close handlers
	$('.close-offcanvas, .offcanvas-overlay').on('click', function (event) {
		event.preventDefault();
		closeOffcanvas();
	});

	// Open function
	function openOffcanvas() {
		$('.offcanvas-init').addClass('offcanvas-active full-offcanvas');
		$(document.body).css('overflow', 'hidden');

		// Make offcanvas interactive
		$('.offcanvas-menu')
			.removeAttr('inert')
			.attr('tabindex', '0');

		// Set focus to close button (better for accessibility)
		setTimeout(() => {
			$('.close-offcanvas').focus();
		}, 100);

		// Add keyboard trap
		$(document).on('keydown.offcanvas', handleOffcanvasKeyboard);
	}

	// Close function
	function closeOffcanvas() {
		$('.offcanvas-init').removeClass('offcanvas-active full-offcanvas');
		$(document.body).css('overflow', '');

		// Make offcanvas non-interactive
		$('.offcanvas-menu')
			.attr('inert', '')
			.attr('tabindex', '-1');

		// Return focus to the trigger
		$('#offcanvas-toggler').focus();

		// Remove keyboard trap
		$(document).off('keydown.offcanvas');
	}

	// Keyboard trap handler on offcanvas
	function handleOffcanvasKeyboard(e) {
		if (!$('.offcanvas-init').hasClass('offcanvas-active')) return;

		const $offcanvas = $('.offcanvas-menu');
		const focusable = 'a[href], button, input, textarea, select, [tabindex="0"]';
		const $focusable = $offcanvas.find(focusable).filter(':visible');

		// Escape key closes
		if (e.key === 'Escape') {
			e.preventDefault();
			closeOffcanvas();
			return;
		}

		// Tab key containment
		if (e.key === 'Tab') {
			const $first = $focusable.first();
			const $last = $focusable.last();

			if (!$offcanvas[0].contains(document.activeElement)) {
				e.preventDefault();
				$first.focus();
				return;
			}

			if (e.shiftKey && document.activeElement === $first[0]) {
				e.preventDefault();
				$last.focus();
			} else if (!e.shiftKey && document.activeElement === $last[0]) {
				e.preventDefault();
				$first.focus();
			}
		}
	}

	// Prevent focus on inert elements
	$(document).on('focusin', function (e) {
		if ($(e.target).closest('[inert]').length) {
			e.preventDefault();
			$('#offcanvas-toggler').focus();
		}
	});

	// Load inert polyfill if needed
	if (!('inert' in document.createElement('div'))) {
		const inertPolyfill = document.createElement('script');
		inertPolyfill.src = 'https://cdn.jsdelivr.net/npm/inert-polyfill@3.1.1/inert.min.js';
		document.head.appendChild(inertPolyfill);
	}

	// Modal Menu
	if ($('#modal-menu').length > 0) {
		let $modalToggler = $('#modal-menu-toggler');
		let $modalMenu = $('#modal-menu');
		let $body = $('body');

		$modalToggler.on('click', function (e) {
			e.preventDefault();
			$modalMenu.toggleClass('active');
			$body.toggleClass('modal-menu-active');
			$(this).toggleClass('active');
		});

		// modal menu close with escape
		$(document).keyup(function (e) {
			if (e.key == 'Escape') {
				$modalMenu.removeClass('active');
				$modalToggler.removeClass('active');
				$body.removeClass('modal-menu-active');
			}
		});
	}

	// Tooltip
	const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"], .hasTooltip'));
	tooltipTriggerList.map(function (tooltipTriggerEl) {
		return new bootstrap.Tooltip(tooltipTriggerEl, {
			html: true
		});
	});

	// Popover
	const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"], .hasPopover'));
	popoverTriggerList.map(function (popoverTriggerEl) {
		return new bootstrap.Popover(popoverTriggerEl);
	});

	// Article Ajax voting
	$('.article-ratings .rating-star').on('click', function (event) {
		event.preventDefault();
		var $parent = $(this).closest('.article-ratings');

		var request = {
			option: 'com_ajax',
			template: template,
			action: 'rating',
			rating: $(this).data('number'),
			article_id: $parent.data('id'),
			format: 'json',
		};

		$.ajax({
			type: 'POST',
			data: request,
			beforeSend: function () {
				$parent.find('.fa-spinner').show();
			},
			success: function (response) {
				var data = $.parseJSON(response);
				$parent.find('.ratings-count').text(data.message);
				$parent.find('.fa-spinner').hide();

				if (data.status) {
					$parent.find('.rating-symbol').html(data.ratings);
				}

				setTimeout(function () {
					$parent.find('.ratings-count').text('(' + data.rating_count + ')');
				}, 3000);
			},
		});
	});

	//  Cookie consent
	$('.sp-cookie-allow').on('click', function (event) {
		event.preventDefault();

		var date = new Date();
		date.setTime(date.getTime() + 30 * 24 * 60 * 60 * 1000);
		var expires = '; expires=' + date.toGMTString();
		document.cookie = 'spcookie_status=ok' + expires + '; path=/';

		$(this).closest('.sp-cookie-consent').fadeOut();
	});

	$('.btn-group label:not(.active)').click(function () {
		var label = $(this);
		var input = $('#' + label.attr('for'));

		if (!input.prop('checked')) {
			label.closest('.btn-group').find('label').removeClass('active btn-success btn-danger btn-primary');
			if (input.val() === '') {
				label.addClass('active btn-primary');
			} else if (input.val() == 0) {
				label.addClass('active btn-danger');
			} else {
				label.addClass('active btn-success');
			}
			input.prop('checked', true);
			input.trigger('change');
		}
		var parent = $(this).parents('#attrib-helix_ultimate_blog_options');
		if (parent) {
			showCategoryItems(parent, input.val());
		}
	});
	$('.btn-group input[checked=checked]').each(function () {
		if ($(this).val() == '') {
			$('label[for=' + $(this).attr('id') + ']').addClass('active btn btn-primary');
		} else if ($(this).val() == 0) {
			$('label[for=' + $(this).attr('id') + ']').addClass('active btn btn-danger');
		} else {
			$('label[for=' + $(this).attr('id') + ']').addClass('active btn btn-success');
		}
		var parent = $(this).parents('#attrib-helix_ultimate_blog_options');
		if (parent) {
			parent.find('*[data-showon]').each(function () {
				$(this).hide();
			});
		}
	});

	function showCategoryItems(parent, value) {
		var controlGroup = parent.find('*[data-showon]');

		controlGroup.each(function () {
			var data = $(this).attr('data-showon');
			data = typeof data !== 'undefined' ? JSON.parse(data) : [];
			if (data.length > 0) {
				if (typeof data[0].values !== 'undefined' && data[0].values.includes(value)) {
					$(this).slideDown();
				} else {
					$(this).hide();
				}
			}
		});
	}

	$(window).on('scroll', function () {
		var scrollBar = $('.sp-reading-progress-bar');
		if (scrollBar.length > 0) {
			var s = $(window).scrollTop(),
				d = $(document).height(),
				c = $(window).height();
			var scrollPercent = (s / (d - c)) * 100;
			const position = scrollBar.data('position');
			if (position === 'top') {
				// var sticky = $('.header-sticky');
				// if( sticky.length > 0 ){
				//     sticky.css({ top: scrollBar.height() })
				// }else{
				//     sticky.css({ top: 0 })
				// }
			}
			scrollBar.css({ width: `${scrollPercent}%` });
		}
	});

	// Error Alert close issue fix for Joomla 3
	var observer = new MutationObserver(function (mutations) {
		$('#system-message-container .alert .close').attr('data-bs-dismiss', 'alert');
	});
	var target = document.querySelector('#system-message-container');
	observer.observe(target, {
		attributes: true
	});


});


// Handle accessibility on off-canvas dropdown menus
jQuery(function ($) {
	const menuSelector = '.menu-deeper.menu-parent';
	const togglerSelector = '.menu-toggler';
	const childSelector = '.menu-child';

	// Toggle submenu open/close
	function toggleSubmenu($item, open = null) {
		const $submenu = $item.children(childSelector);
		const isOpen = $item.hasClass('menu-parent-open');

		if (open === null) open = !isOpen;

		if (open) {
			$item.addClass('menu-parent-open').attr('aria-expanded', 'true');
			$submenu.slideDown(150);
		} else {
			$item.removeClass('menu-parent-open').attr('aria-expanded', 'false');
			$submenu.slideUp(150);
		}
	}

	// Prevent event bubbling from toggler to link
	$(document).on('click', togglerSelector, function (event) {
		event.preventDefault();
		event.stopPropagation();
		const $item = $(this).closest(menuSelector);
		const isOpen = $item.hasClass('menu-parent-open');
		toggleSubmenu($item, !isOpen);
	});

	// Handle menu link click or Enter/Space key
	$(document).on('click keydown', `${menuSelector} > a`, function (event) {
		const isToggleKey = event.type === 'click' || event.key === 'Enter' || event.key === ' ';
		const isKeyboard = event.key === 'Enter' || event.key === ' ';

		if (isToggleKey) {
			const $item = $(this).closest(menuSelector);
			const $submenu = $item.children(childSelector);

			// If submenu exists
			if ($submenu.length) {
				// If it's Space key (prevent scrolling), toggle submenu only
				if (event.key === ' ') {
					event.preventDefault();
					toggleSubmenu($item);
				}
				// If it's Enter or click — follow link (do not preventDefault)
				// but also toggle submenu if desired
				else if (event.key === 'Enter') {
					toggleSubmenu($item, true);

				} else if (event.type === 'click') {
					toggleSubmenu($item, true);
				}
			}
		}
	});

	// Handle arrow key navigation
	$(document).on('keydown', `${menuSelector} > a`, function (event) {
		const $currentItem = $(this).closest('li');
		const $siblings = $currentItem.parent().children('li:visible');
		const index = $siblings.index($currentItem);

		if (event.key === 'ArrowDown') {
			event.preventDefault();
			// If submenu is open, focus first child link
			const $submenu = $currentItem.children(childSelector);
			if ($submenu.length && $currentItem.hasClass('menu-parent-open')) {
				const $firstChildLink = $submenu.children('li:visible').find('a').first();
				if ($firstChildLink.length) {
					$firstChildLink.focus();
					return;
				}
			}
			// Otherwise, move to next sibling
			$siblings.eq((index + 1) % $siblings.length).find('a').first().focus();
		} else if (event.key === 'ArrowUp') {
			event.preventDefault();
			// Move to previous sibling
			$siblings.eq((index - 1 + $siblings.length) % $siblings.length).find('a').first().focus();
		} else if (event.key === 'ArrowRight') {
			// Open nested submenu if present
			const $submenu = $currentItem.children(childSelector);
			if ($submenu.length) {
				event.preventDefault();
				toggleSubmenu($currentItem, true);
				const $firstChildLink = $submenu.children('li:visible').find('a').first();
				if ($firstChildLink.length) {
					$firstChildLink.focus();
				}
			}
		} else if (event.key === 'ArrowLeft' || event.key === 'Escape') {
			event.preventDefault();
			// Close only the current open dropdown
			toggleSubmenu($currentItem, false);
			$currentItem.children('a').first().focus();
		}
	});

	// Handle arrow key navigation inside submenu
	$(document).on('keydown', `${childSelector} > li > a`, function (event) {
		const $currentItem = $(this).closest('li');
		const $siblings = $currentItem.parent().children('li:visible');
		const index = $siblings.index($currentItem);

		if (event.key === 'ArrowDown') {
			event.preventDefault();
			$siblings.eq((index + 1) % $siblings.length).find('a').first().focus();
		} else if (event.key === 'ArrowUp') {
			event.preventDefault();
			$siblings.eq((index - 1 + $siblings.length) % $siblings.length).find('a').first().focus();
		} else if (event.key === 'ArrowRight') {
			// Open nested submenu if present
			const $submenu = $currentItem.children(childSelector);
			if ($submenu.length) {
				event.preventDefault();
				toggleSubmenu($currentItem, true);
				const $firstChildLink = $submenu.children('li:visible').find('a').first();
				if ($firstChildLink.length) {
					$firstChildLink.focus();
				}
			}
		} else if (event.key === 'ArrowLeft' || event.key === 'Escape') {
			event.preventDefault();
			// Close submenu and focus parent
			const $parentMenu = $currentItem.parents(menuSelector).first();
			toggleSubmenu($parentMenu, false);
			$parentMenu.children('a').first().focus();
		}
	});

	// Close all menus when clicking outside
	$(document).on('click', function (event) {
		if (!$(event.target).closest('.menu-deeper').length) {
			$(menuSelector).removeClass('menu-parent-open').attr('aria-expanded', 'false');
			$(childSelector).slideUp(150);
		}
	});

	//mark all menu-parents as aria-haspopup
	$(menuSelector).attr('aria-haspopup', 'true').attr('aria-expanded', 'false');
});

// Handle accessibility on megamenu and profile dropdowns
jQuery(function ($) {
	const menuSelectors = '.sp-megamenu-parent > li, .sp-profile-wrapper';

	$(menuSelectors).each(function () {
		const $menuItem = $(this);
		const $trigger = $menuItem.children('a, button');
		const $dropdown = $menuItem.children('.sp-dropdown, .sp-profile-dropdown');

		if ($dropdown.length) {
			setupDropdownEvents($menuItem, $trigger, $dropdown);
		}
	});

	bindNestedDropdowns('body');

	function setupDropdownEvents($menuItem, $trigger, $dropdown) {
		// Show on focus or mouseenter
		$trigger.on('focus mouseenter', function () {
			openMenu($menuItem, $dropdown);
		});

		$menuItem.on('mouseenter', function () {
			openMenu($menuItem, $dropdown);
		});

		// Hide on focusout or mouseleave
		$menuItem.on('mouseleave focusout', function () {
			setTimeout(function () {
				if (!$menuItem.find(':focus').length && !$menuItem.is(':hover')) {
					closeMenu($menuItem, $dropdown);
				}
			}, 100);
		});

		// Keyboard trigger
		$trigger.on('keydown', function (event) {
			switch (event.key) {
				case ' ':
					event.preventDefault();
					openMenu($menuItem, $dropdown);
					break;
				case 'ArrowDown':
					event.preventDefault();
					openMenu($menuItem, $dropdown);
					focusFirstItem($dropdown);
					break;
				case 'Escape':
					event.preventDefault();
					closeMenu($menuItem, $dropdown);
					$trigger.focus();
					break;
			}
		});

		// Prevent click from toggling menu
		$trigger.on('click', function (event) {
			if ($dropdown.length) {
				openMenu($menuItem, $dropdown);
			}
		});

	}

	function bindNestedDropdowns(containerSelector) {
		$(containerSelector).find(' .sp-has-child').each(function () {
			const $subItem = $(this);
			const $trigger = $subItem.children('a, button');
			const $subDropdown = $subItem.children('.sp-dropdown');

			if ($subDropdown.length) {
				setupDropdownEvents($subItem, $trigger, $subDropdown);
				bindNestedDropdowns($subDropdown);
			}
		});
	}

	function openMenu($item, $dropdown) {
		$dropdown.show();
		// Only force display for .sp-profile-dropdown
		if ($dropdown.hasClass('sp-profile-dropdown')) {
			$dropdown.attr('style', 'display: block !important');
		}
		bindKeyboardNavigation($dropdown);
	}

	function closeMenu($item, $dropdown) {
		$dropdown.hide();
		// Reset style for .sp-profile-dropdown
		if ($dropdown.hasClass('sp-profile-dropdown')) {
			$dropdown.removeAttr('style');
		}
	}

	function focusFirstItem($dropdown) {
		const $focusable = $dropdown.find('a, button').filter(':visible');
		if ($focusable.length) {
			$focusable.first().focus();
		}
	}

	function bindKeyboardNavigation($dropdown) {
		const $items = $dropdown.find('a, button').filter(':visible');

		$items.off('keydown').on('keydown', function (event) {
			const currentIndex = $items.index(this);
			let newIndex = -1;

			if (event.key === 'ArrowDown') {
				event.preventDefault();
				newIndex = (currentIndex + 1) % $items.length;
			} else if (event.key === 'ArrowUp') {
				event.preventDefault();
				newIndex = (currentIndex - 1 + $items.length) % $items.length;
			} else if (event.key === 'Escape') {
				event.preventDefault();

				// Reset style for .sp-profile-dropdown
				if ($dropdown.hasClass('sp-profile-dropdown')) {
					$dropdown.removeAttr('style');
				}

				const $currentDropdown = $(this).closest('.sp-dropdown');
				const $parentItem = $currentDropdown.parent(' .sp-has-child, .sp-megamenu-parent > li');

				// Check if this is the root-level dropdown
				const isRoot = $parentItem.parent().is('.sp-megamenu-parent, .sp-megamenu-parent > ul, nav');

				if (isRoot) {
					// Close all menus
					$(menuSelectors).each(function () {
						const $item = $(this);
						closeMenu($item, $item.children('.sp-dropdown'));
					});
				} else {
					// Close only current submenu and focus its trigger
					const $trigger = $parentItem.children('a, button');
					closeMenu($parentItem, $currentDropdown);
					if ($trigger.length) {
						$trigger.focus();
					}
				}
				return;
			}

			if (newIndex > -1) {
				$items.eq(newIndex).focus();
			}
		});
	}

	// Close all menus on outside click
	$(document).on('click', function (event) {
		if (!$(event.target).closest(menuSelectors).length) {
			$(menuSelectors).each(function () {
				const $item = $(this);
				closeMenu($item, $item.children('.sp-dropdown'));
			});
		}
	});

});


