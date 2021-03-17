const HelixToaster = {
	options: {
		timeout: 5000,
		containerId: 'hu-toaster-container',
		prefix: 'hu-toaster',
		position: 'hu-toaster-bottom-right',
		titleClass: '',
		messageClass: '',
		target: 'body'
	},

	toasts: [],
	toastIndex: 0,
	elementTimeout: null,

	success(message, title, options) {
		this.createToaster({type: 'success', message, title, options});
	},

	error(message, title, options) {
		this.createToaster({type: 'error', message, title, options});
	},

	info(message, title, options) {
		this.createToaster({type: 'info', message, title, options});
	},

	warning(message, title, options) {
		this.createToaster({type: 'warning', message, title, options});
	},

	getTypeClass(type) {
		return `hu-toast-${type}`;
	},

	createContainer() {
		const container = document.createElement('div');
		container.setAttribute('id', this.options.containerId);
		container.setAttribute('class', this.options.position);
		document.querySelector(this.options.target).appendChild(container);

		return container;
	},

	createToaster({type, message, title, options}) {
		const toasterElement = document.createElement('div');
		toasterElement.setAttribute('class', this.options.prefix + ' ' + this.getTypeClass(type));

		let titleClass = `${this.options.prefix}-title ${this.options.titleClass}`,
			messageClass = `${this.options.prefix}-message ${this.options.messageClass}`

		let html = `
			<div class="${titleClass}">${title}</div>
			<div class="${messageClass}">${message}</div>
		`;

		toasterElement.innerHTML = html;
		toasterElement.style.animationName = 'huFadeInUp';
		toasterElement.style.animationDuration = '.35s';

		this.toasts.push(toasterElement);
		this.toastIndex++;

		this.getContainer().appendChild(toasterElement);

		this.elementTimeout = setTimeout(() => {
			toasterElement.style.animationName = 'huFadeInDown';
			toasterElement.style.animationDuration = '.35s';
			toasterElement.style.opacity = 0;
			setTimeout(() => {
				toasterElement.parentNode.removeChild(toasterElement);
			}, 450);
		}, this.options.timeout);

		/** Remove the toaster on clicking to the toaster and clear the timeout. */
		toasterElement.addEventListener('click', (e) => {
			e.preventDefault();

			if (this.elementTimeout) clearTimeout(this.elementTimeout);
			toasterElement.style.animationName = 'huFadeInDown';
			toasterElement.style.animationDuration = '.35s';
			toasterElement.style.opacity = 0;
			setTimeout(() => {
				toasterElement.parentNode.removeChild(toasterElement);
			}, 450);
		});
	},

	getContainer() {
		let container = document.querySelector(`#${this.options.containerId}`);

		if (!container) {
			container = this.createContainer();
		}

		return container;
	},

	displayToaster() {
		const container = this.getContainer();
		container.innerHTML = '';

		this.toasts.forEach(toast => {
			container.appendChild(toast);
		});
	},

	delay(ms = 1000) {
		return new Promise((resolve) => setTimeout(resolve, ms));
	},

	removeToaster(index) {
		let timeout = null;
		// if (timeout) clearTimeout();

		return new Promise((resolve) => {
			this.toasts.splice(index, 1);
			const container = document.querySelector(`#${this.options.containerId}`);
			
			if (container.firstChild) container.removeChild(container.firstChild);
			resolve({status: true});
		});
	}
}

Joomla.HelixToaster = HelixToaster;