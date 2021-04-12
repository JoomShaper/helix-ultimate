/**
 * Tree sortable jQuery library using jQuery UI sortable.
 *
 * @package TreeSortable
 * @license MIT
 * @author Sajeeb Ahamed
 */

var $ = jQuery;

var treeSortable = {
	options: {
		depth: 20,
		treeSelector: '#hu-menu-tree',
		branchSelector: '.hu-menu-tree-branch',
		dragHandlerSelector: '.hu-branch-drag-handler',
		placeholderName: 'hu-sortable-placeholder',
		childrenBusSelector: '.hu-menu-children-bus',
		levelPrefix: 'hu-branch-level',
		maxLevel: 10,
	},
	run() {
		this.jQuerySupplements();
		this.initSorting();
	},
	getTreeEdge() {
		return $(treeSortable.options.treeSelector).offset().left;
	},
	pxToNumber(str) {
		return new RegExp('px$', 'i').test(str) ? str.slice(0, -2) * 1 : 0;
	},
	numberToPx(num) {
		return `${num}px`;
	},
	jQuerySupplements() {
		const { options } = treeSortable;
		const { levelPrefix } = options;
		$.fn.extend({
			getBranchLevel() {
				if ($(this).length === 0) return 0;

				const { depth } = options;
				const margin = $(this).css('margin-left');

				return /(px)|(em)|(rem)$/i.test(margin)
					? Math.floor(margin.slice(0, -2) / depth) + 1
					: Math.floor(margin / depth) + 1;
			},
			updateBranchLevel(current, prev = null) {
				return this.each(function () {
					prev = prev || $(this).getBranchLevel() || 1;
					$(this)
						.removeClass(levelPrefix + '-' + prev)
						.addClass(levelPrefix + '-' + current);
				});
			},
			shiftBranchLevel(dx) {
				return this.each(function () {
					let level = $(this).getBranchLevel() || 1,
						newLevel = level + dx;
					$(this)
						.removeClass(levelPrefix + '-' + level)
						.addClass(levelPrefix + '-' + newLevel);
				});
			},
			getParent() {
				const {
					options: { branchSelector },
				} = treeSortable;
				const level = $(this).getBranchLevel() || 1;
				let $prev = $(this).prev(branchSelector);

				while ($prev.length && $prev.getBranchLevel() >= level) {
					$prev = $prev.prev(branchSelector);
				}

				return $prev;
			},
			getRootChildren() {
				const {
					options: { branchSelector, treeSelector, levelPrefix },
				} = treeSortable;

				return $(treeSelector).children(
					`${branchSelector}.${levelPrefix}-1`
				);
			},
			getChildren() {
				const {
					options: { branchSelector },
				} = treeSortable;
				let $children = $();

				this.each(function () {
					let level = $(this).getBranchLevel() || 1,
						$next = $(this).next(branchSelector);

					while ($next.length && $next.getBranchLevel() > level) {
						$children = $children.add($next);
						$next = $next.next(branchSelector);
					}
				});

				return $children;
			},
			nextBranch() {
				return $(this).next();
			},
			prevBranch() {
				return $(this).prev();
			},
			nextSibling() {
				const {
					options: { branchSelector },
				} = treeSortable;

				let level = $(this).getBranchLevel() || 1,
					$next = $(this).next(branchSelector),
					nextLevel = $next.getBranchLevel();

				while ($next.length && nextLevel > level) {
					$next = $next.next(branchSelector);
					nextLevel = $next.getBranchLevel();
				}

				return +nextLevel === +level ? $next : $();
			},
			prevSibling() {
				const {
					options: { branchSelector },
				} = treeSortable;
				let level = $(this).getBranchLevel() || 1,
					$prev = $(this).prev(branchSelector),
					prevLevel = $prev.getBranchLevel();

				while ($prev.length && prevLevel > level) {
					$prev = $prev.prev(branchSelector);
					prevLevel = $prev.getBranchLevel();
				}

				return prevLevel === level ? $prev : $();
			},
			getSiblings(level = null) {
				const { options: {treeSelector, branchSelector}} = treeSortable;
					level = level || $(this).getBranchLevel();

				let $siblings = [],
					$branches = $(`${treeSelector} > ${branchSelector}`),
					$self = this;

				$branches.length && $branches.each(function () {
					let branchLevel = $(this).getBranchLevel();

					if (+branchLevel === +level && $self[0] !== $(this)[0]) {
						$siblings.push($(this));
					}
				});

				return $siblings;
			}
		});
	},

	updateBranchZIndex() {
		const { options: { treeSelector, branchSelector }} = treeSortable;
		const $branches = $(`${treeSelector} > ${branchSelector}`);
		const length = $branches.length;

		$branches.length &&
			$branches.each(function (index) {
				$(this).css('z-index', Math.max(1, length - index));
			});
	},
	initSorting() {
		const {
			options,
			pxToNumber,
			numberToPx,
			updateBranchZIndex,
		} = treeSortable;
		const {
			treeSelector,
			dragHandlerSelector,
			placeholderName,
			childrenBusSelector,
		} = options;

		/** Store the current level, for sorting the item after stop dragging. */
		let currentLevel = 1,
			originalLevel = 1,
			childrenBus = null,
			helperHeight = 0,
			originalIndex = 0,
			aliasExists = false;

		/** Update the placeholder branch level by new level. */
		const updatePlaceholder = (placeholder, level) => {
			placeholder.updateBranchLevel(level);
			currentLevel = level;
		};

		/** Check if we can swap items vertically for branch with children */
		const canSwapItems = ui => {
			let offset = ui.helper.offset(),
				height = offset.top + helperHeight,
				nextBranch = ui.placeholder.nextBranch(),
				nextBranchOffset = nextBranch.offset() || 0,
				nextBranchHeight = nextBranch.outerHeight();

			return height > nextBranchOffset.top + nextBranchHeight / 3;
		};

		$(treeSelector).sortable({
			handle: dragHandlerSelector,
			placeholder: placeholderName,
			items: '> *',
			start(_, ui) {
				/**
				 * Synchronize the placeholder level with the item's level.
				 *
				 */
				const level = ui.item.getBranchLevel();
				ui.placeholder.updateBranchLevel(level);
				originalIndex = ui.item.index();

				/**  Store the original level. */
				originalLevel = level;

				/** Fill the children bus with the children. */
				childrenBus = ui.item.find(childrenBusSelector);
				childrenBus.append(ui.item.next().getChildren());

				/**
				 * Calculate the placeholder width & height according to the
				 * helper's width & height respectively.
				 */
				let height = childrenBus.outerHeight();
				let placeholderMarginTop = ui.placeholder.css('margin-top');

				height += height > 0 ? pxToNumber(placeholderMarginTop) : 0;
				height += ui.helper.outerHeight();
				helperHeight = height;
				height -= 2;

				let width =
					ui.helper.find(dragHandlerSelector).outerWidth() - 2;
				ui.placeholder.css({ height, width });

				const tmp = ui.placeholder.nextBranch();
				tmp.css('margin-top', numberToPx(helperHeight));
				ui.placeholder.detach();
				$(this).sortable('refresh');
				ui.item.after(ui.placeholder);
				tmp.css('margin-top', 0);

				// Set the current level by the initial item's level.
				currentLevel = level;
				$('.hu-menu-tree-branch .hu-menu-branch-path').hide();
				
			},
			sort(_, ui) {
				const { options, getTreeEdge } = treeSortable;
				const { depth, maxLevel } = options;
				let treeEdge = getTreeEdge(),
					offset = ui.helper.offset(),
					currentBranchEdge = offset.left,
					lowerBound = 1,
					upperBound = maxLevel;

				/**
				 * Calculate the upper bound. The upper bound would be,
				 * the minimum value between the
				 * (previous branch level + 1) and the maxLevel.
				 */
				let prevBranch = ui.placeholder.prevBranch();
				prevBranch =
					prevBranch[0] === ui.item[0]
						? prevBranch.prevBranch()
						: prevBranch;

				let prevBranchLevel = prevBranch.getBranchLevel();
				upperBound = Math.min(prevBranchLevel + 1, maxLevel);

				/**
				 * Calculate the lower bound. The lower bound would be,
				 * the maximum value between the
				 * Next Sibling Level and 1
				 */
				let nextSibling = ui.placeholder.nextSibling(),
					placeholderLevel = 1;

				if (nextSibling.length) {
					placeholderLevel = ui.placeholder.getBranchLevel() || 1;
				} else {
					/**
					 * If no sibling found then
					 * the placeholder level would be the next branch's level.
					 */
					let nextBranch = ui.placeholder.nextBranch();
					placeholderLevel = nextBranch.getBranchLevel() || 1;
				}

				lowerBound = Math.max(1, placeholderLevel);

				/**
				 * Calculate the position which is the current helper offset left
				 * minus the tree parent's offset left.
				 * Find the changed level by dividing the position by depth value.
				 *
				 * The final valid changed level would be a value
				 * between upper and lower bound inclusive.
				 */
				let position = Math.max(0, currentBranchEdge - treeEdge);
				let newLevel = Math.floor(position / depth) + 1;
				newLevel = Math.max(lowerBound, Math.min(newLevel, upperBound));

				if (canSwapItems(ui)) {
					let nextBranch = ui.placeholder.nextBranch();

					if (nextBranch.getChildren().length) {
						newLevel = nextBranch.getBranchLevel() + 1;
					}

					nextBranch.after(ui.placeholder);
					$(this).sortable('refreshPositions');
				}

				let $siblings = ui.item.getSiblings(newLevel);
				
				if ($siblings.length > 0) {
					let itemAlias = ui.item.data('alias');
					aliasExists = $siblings.some($item => $item.data('alias') === itemAlias);

					if (aliasExists) {
						return;
					}
				}

				/** Update the placeholder position by the changed level. */
				updatePlaceholder(ui.placeholder, newLevel);
			},
			change(_, ui) {
				let prevBranch = ui.placeholder.prevBranch();

				prevBranch = prevBranch[0] === ui.item[0]
					? prevBranch.prevBranch()
					: prevBranch;

				/**
				 * After changing branches bound the placeholder to the
				 * changed boundary.
				 */
				let prevBranchLevel = prevBranch.getBranchLevel() || 1;

				if (prevBranch.length) {
					ui.placeholder.detach();
					let children = prevBranch.getChildren();
					if (children && children.length) prevBranchLevel += 1;
					ui.placeholder.updateBranchLevel(prevBranchLevel);
					prevBranch.after(ui.placeholder);
				}
			},
			stop(_, ui) {
				$('.hu-menu-tree-branch:not(.hu-branch-level-1) .hu-menu-branch-path').show();

				/**
				 * If the changing item's alias exits to the newly updating
				 * level then shows error message for the users.
				 */
				if (aliasExists) {
					Joomla.HelixToaster.error(`Can't set the same alias <strong>${ui.item.data('alias')}</strong> in the same menu level!`, 'Error');
				}
				/**
				 * Place the children after the sorted item,
				 * and clear the children bus.
				 */
				const children = childrenBus.children().insertAfter(ui.item);
				childrenBus.empty();

				/** Update the item by currently changed level. */
				ui.item.updateBranchLevel(currentLevel);
				children.shiftBranchLevel(currentLevel - originalLevel);

				/**
				 * Change the settings icons. If the new level is 1 then 
				 * the icon would be the mega menu icon.
				 * Otherwise, this is the cog/gear icon.
				 */
				const gearIcon = `<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" class="bi bi-gear" viewBox="0 0 16 16"><path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z"></path><path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115l.094-.319z"></path></svg>`;
				const megaIcon = `<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" class="bi bi-grid-1x2" viewBox="0 0 16 16"><path d="M6 1H1v14h5V1zm9 0h-5v5h5V1zm0 9v5h-5v-5h5zM0 1a1 1 0 0 1 1-1h5a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1V1zm9 0a1 1 0 0 1 1-1h5a1 1 0 0 1 1 1v5a1 1 0 0 1-1 1h-5a1 1 0 0 1-1-1V1zm1 8a1 1 0 0 0-1 1v5a1 1 0 0 0 1 1h5a1 1 0 0 0 1-1v-5a1 1 0 0 0-1-1h-5z"></path></svg>`;

				ui.item.find('.hu-branch-tools-list-megamenu')
					.html(currentLevel > 1 ? gearIcon : megaIcon);

				/**
				 * Trigger `sortCompleted` event if the level changed or index changed.
				 * i.e. if the items sorted then trigger the event.
				 */
				if (currentLevel !== originalLevel || originalIndex !== ui.item.index()) {
					$(document).trigger('sortCompleted', [ui]);
				}

				Joomla.utils.calculateSiblingDistances();
			},
		});
	},
};

Joomla.sortable = treeSortable;
