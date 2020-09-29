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

Joomla.utils = { asciiToHex, getCurrentTimeString, helixHash };
