/**
 * Helix Ultimate E2E Test: Media Picker & CSRF Token Validation
 *
 * Usage:
 *   node tests/e2e/run-e2e.js
 *
 * Environment Variables (optional):
 *   BASE_URL=https://helixultimatedev.test
 *   ADMIN_USER=admin
 *   ADMIN_PASS=demo12345678
 *   HEADLESS=true
 */

const { chromium } = require('playwright');

(async () => {
	const baseUrl = process.env.BASE_URL || 'https://helixultimatedev.test';
	const username = process.env.ADMIN_USER || 'admin';
	const password = process.env.ADMIN_PASS || 'demo12345678';
	const headless = process.env.HEADLESS !== 'false';

	console.log(`[E2E] Starting test against ${baseUrl}...`);

	const browser = await chromium.launch({
		headless: headless,
	});

	const context = await browser.newContext({
		ignoreHTTPSErrors: true,
		viewport: { width: 1440, height: 900 },
	});

	const page = await context.newPage();

	let alertTriggered = false;
	let alertMessage = '';

	page.on('dialog', async dialog => {
		alertTriggered = true;
		alertMessage = dialog.message();
		console.log(`[E2E Dialog] ${dialog.type()}: ${alertMessage}`);
		await dialog.dismiss();
	});

	page.on('console', msg => {
		if (msg.type() === 'error') {
			console.log(`[E2E Console Error] ${msg.text()}`);
		}
	});

	try {
		// 1. Log in to Joomla Administrator
		console.log('[E2E] Step 1: Navigating to administrator login...');
		await page.goto(`${baseUrl}/administrator/`, { waitUntil: 'networkidle' });

		const usernameInput = page.locator('#mod-login-username, input[name="username"]');
		if (await usernameInput.isVisible()) {
			console.log('[E2E] Logging in...');
			await usernameInput.fill(username);
			await page.locator('#mod-login-password, input[name="passwd"]').fill(password);
			await page.locator('button[type="submit"], #btn-login').first().click();
			await page.waitForLoadState('networkidle');
		}

		console.log('[E2E] Current URL after login:', page.url());

		// 2. Navigate to Template Options
		console.log('[E2E] Step 2: Navigating to Template Options...');
		await page.goto(`${baseUrl}/administrator/index.php?option=com_templates&view=styles`, {
			waitUntil: 'networkidle',
		});

		const customizerLink = page.locator('a[href*="helix=ultimate"], a[href*="action=edit"]').first();
		let customizerUrl = '';

		if (await customizerLink.count()) {
			const href = await customizerLink.getAttribute('href');
			customizerUrl = href.startsWith('http') ? href : `${baseUrl}/administrator/${href}`;
		} else {
			const styleLink = page.locator('a:has-text("shaper_helixultimate")').first();
			if (await styleLink.count()) {
				await styleLink.click();
				await page.waitForLoadState('networkidle');
				const btnCustomizer = page.locator('a:has-text("Template Options"), button:has-text("Template Options")').first();
				if (await btnCustomizer.count()) {
					const href = await btnCustomizer.getAttribute('href');
					customizerUrl = href.startsWith('http') ? href : `${baseUrl}/administrator/${href}`;
				}
			}
		}

		if (customizerUrl) {
			console.log(`[E2E] Navigating to Customizer URL: ${customizerUrl}`);
			await page.goto(customizerUrl, { waitUntil: 'networkidle' });
		} else {
			await page.goto(`${baseUrl}/administrator/index.php?option=com_ajax&helix=ultimate&request=task`, {
				waitUntil: 'networkidle',
			});
		}

		// Wait for Helix Ultimate interface to be ready
		console.log('[E2E] Step 3: Waiting for Helix Ultimate customizer to load...');
		await page.waitForSelector('#helix-ultimate, .hu-options-core', { timeout: 15000 });

		// Verify CSRF token option is present in JavaScript context
		const csrfToken = await page.evaluate(() => {
			return typeof Joomla !== 'undefined' && Joomla.getOptions ? Joomla.getOptions('csrf.token') : null;
		});

		console.log(`[E2E] Evaluated Joomla CSRF Token in browser: ${csrfToken ? 'PRESENT (' + csrfToken.substring(0, 8) + '...)' : 'MISSING'}`);
		if (!csrfToken) {
			throw new Error('Joomla.getOptions("csrf.token") is not defined in the customizer context!');
		}

		// 3. Open Basic > Logo
		console.log('[E2E] Step 4: Navigating to Basic -> Logo...');
		const basicTab = page.locator('.hu-action[data-target="#basic"], .hu-action:has-text("Basic")').first();
		if (await basicTab.isVisible()) {
			await basicTab.click();
			await page.waitForTimeout(500);
		}

		const logoAccordion = page.locator('.hu-sub-category[data-target="#logo"], .hu-sub-category:has-text("Logo")').first();
		if (await logoAccordion.isVisible()) {
			await logoAccordion.click();
			await page.waitForTimeout(500);
		}

		// 4. Click Media Picker "Select" button
		console.log('[E2E] Step 5: Clicking Logo Media Picker "Select" button...');
		const mediaPickerBtn = page.locator('.hu-media-picker').first();
		await mediaPickerBtn.waitFor({ state: 'visible', timeout: 5000 });
		await mediaPickerBtn.click();

		// Wait for response and modal
		console.log('[E2E] Step 6: Verifying Media Manager modal...');
		await page.waitForSelector('.hu-modal-inner', { timeout: 10000 });

		await page.waitForTimeout(1000);

		if (alertTriggered) {
			if (alertMessage.toLowerCase().includes('token') || alertMessage.toLowerCase().includes('denied')) {
				throw new Error(`CSRF Token Error Alert Triggered: "${alertMessage}"`);
			}
		}

		// Verify folders or content loaded
		const modalContent = await page.locator('.hu-modal-inner').innerHTML();
		const itemsCount = await page.locator('.hu-media-folder, .hu-media-image, .hu-media-item').count();

		console.log(`[E2E] Found ${itemsCount} media items in modal.`);
		if (itemsCount === 0 && modalContent.includes('Invalid security token')) {
			throw new Error('Media modal returned invalid security token message.');
		}

		console.log('\n========================================');
		console.log(' [PASS] E2E CSRF Media Picker Test Passed!');
		console.log('========================================\n');
	} catch (err) {
		console.error('\n========================================');
		console.error(' [FAIL] E2E Test Failed:', err.message);
		console.error('========================================\n');
		process.exitCode = 1;
	} finally {
		await browser.close();
	}
})();
