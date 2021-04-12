const asciiToHex = str =>
	'0x' +
	str
		.split('')
		.map(char => char.charCodeAt(0).toString(16))
		.join('');

const getCurrentTimeString = () => {
	const date = new Date();
	return (
		date.getFullYear() +
		'-' +
		(date.getMonth() + 1) +
		'-' +
		date.getDate() +
		'-' +
		date.getHours() +
		':' +
		date.getMinutes() +
		':' +
		date.getSeconds() +
		':' +
		date.getMinutes()
	);
};

const helixHash = str => {
	let hash = 0;
	const { length } = str;
	if (length === 0) return hash;

	for (let i = 0; i < length; i++) {
		const char = str.charCodeAt(i);
		hash = (hash << 5) - hash + char;
		hash &= hash;
	}

	return hash;
};

const triggerEvent = (element, eventName) => {
	if (document.createEvent) {
		const event = document.createEvent('HTMLEvents');
		event.initEvent(eventName, false, false);
		element.dispatchEvent(event);
	}
}

const setCookie = (name, value = '', days = 1) => {
	let expires = "";
    if (days) {
        let date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }

    document.cookie = name + "=" + value  + expires + "; path=/";
}

const getCookie = name => {
    name = name + "=";
    let cookieArray = document.cookie.split(';');

    for(let i = 0; i < cookieArray.length; i++) {
        let c = cookieArray[i];

        while (c.charAt(0)==' ') c = c.substring(1, c.length);

        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }

    return undefined;
}

const deleteCookie = name => {
	document.cookie = name+'=; Max-Age=-99999999;';
}

const debounce = (func, interval) => {
	let timeout;
	return function () {
		let context = this, args = arguments;
		let later = function () {
			timeout = null;
			func.apply(context, args);
		};

		clearTimeout(timeout);
		timeout = setTimeout(later, interval || 200);
	}
}


const getCenterPosition = element => {
	const {top, left, width, height} = element.getBoundingClientRect();

	return {
		x: left + width / 2,
		y: top + height / 2
	};
}

const getDistance = (elementA, elementB) => {
	const positionA = getCenterPosition(elementA);
	const positionB = getCenterPosition(elementB);

	// console.log(elementA, elementB);

	const distanceX = Math.floor(Math.abs(positionA.x - positionB.x));
	const distanceY = Math.floor(Math.abs(positionA.y - positionB.y));

	return {distanceX, distanceY};
}

function calculateSiblingDistances() {
	const branchSelector = '.hu-menu-tree-branch';

	$(branchSelector).each(function() {
		const level = $(this).getBranchLevel() || 1;
		$(this).find('.hu-menu-branch-path').show();

		if (typeof $(this).nextSibling !== 'function') return;

		if (level > 1) {
			const $sibling = $(this).nextSibling();

			/**
			 * If next sibling (siblings with same branch level) exists then
			 * calculate the distance between two siblings and set the path
			 * height according to the distance.
			 */
			if ($sibling.length) {
				const distance = getDistance($(this).get(0), $sibling.get(0));
				$sibling.find('.hu-menu-branch-path').css('height', `${Math.max(distance.distanceY + 8, 55)}px`);
			} else {

				/**
				 * If no sibling exists to a branch then find the child.
				 * If child exists then set the child height as the default 55px.
				 */
				const $nextBranch = $(this).next(branchSelector);
				const nextBranchLevel = $nextBranch.getBranchLevel() || 1;

				const isChild = $nextBranch.length > 0
					&& nextBranchLevel > level;

				if (isChild) {
					$nextBranch.find('.hu-menu-branch-path').css('height', '55px');
				}
			}

		} else {
			$(this).find('.hu-menu-branch-path').hide();
		}
	});
}

Joomla.utils = {
	asciiToHex,
	getCurrentTimeString,
	helixHash,
	triggerEvent,
	setCookie,
	getCookie,
	deleteCookie,
	debounce,
	getDistance,
	calculateSiblingDistances
};
