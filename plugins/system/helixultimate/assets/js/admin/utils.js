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

Joomla.utils = { asciiToHex, getCurrentTimeString };
