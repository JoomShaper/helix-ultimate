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

Joomla.utils = { asciiToHex, getCurrentTimeString, helixHash, triggerEvent, setCookie, getCookie, deleteCookie };
