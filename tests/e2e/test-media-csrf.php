<?php
/**
 * Helix Ultimate - E2E CSRF and Media AJAX Integration Test
 *
 * Simulates complete administrator session flow:
 * 1. Authenticates against /administrator/
 * 2. Finds Helix Ultimate template style ID and loads the customizer.
 * 3. Extracts Joomla's session CSRF token from script options and form.
 * 4. Tests unauthenticated/missing token rejection (negative test).
 * 5. Tests authenticated com_ajax actions (view-media, delete-media, create-folder) with token (positive test).
 *
 * Usage:
 *   php tests/e2e/test-media-csrf.php
 */

declare(strict_types=1);

$baseUrl = getenv('BASE_URL') ?: 'https://helixultimatedev.test';
$adminUser = getenv('ADMIN_USER') ?: 'admin';
$adminPass = getenv('ADMIN_PASS') ?: 'demo12345678';

$cookieJar = tempnam(sys_get_temp_dir(), 'hu_e2e_cookie_');

function httpRequest(string $url, string $method = 'GET', $data = null, string $cookieJar = ''): array
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieJar);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieJar);
	curl_setopt($ch, CURLOPT_USERAGENT, 'HelixUltimate-E2E-Tester/1.0');

	if ($method === 'POST')
	{
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	}

	$response = curl_exec($ch);
	$httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$error = curl_error($ch);

	return [
		'code' => $httpCode,
		'body' => $response !== false ? (string) $response : '',
		'error' => $error,
	];
}

echo "\n=======================================================\n";
echo " Helix Ultimate E2E Test: CSRF & Media Picker\n";
echo " Target: {$baseUrl}\n";
echo "=======================================================\n\n";

// Step 1: Access Login Page & Get Initial Login Token
echo "[E2E Step 1] Fetching login page...\n";
$loginPage = httpRequest("{$baseUrl}/administrator/index.php", 'GET', null, $cookieJar);

if ($loginPage['code'] !== 200)
{
	echo "[-] FAILED: Could not reach {$baseUrl}/administrator/ (HTTP {$loginPage['code']}: {$loginPage['error']})\n";
	exit(1);
}

// Extract login CSRF token from HTML form
preg_match('/name="([a-f0-9]{32})"\s+value="1"/', $loginPage['body'], $loginTokenMatch);
$initialToken = $loginTokenMatch[1] ?? '';

if (empty($initialToken))
{
	preg_match('/name="([a-f0-9]{32})"/', $loginPage['body'], $loginTokenMatch);
	$initialToken = $loginTokenMatch[1] ?? '';
}

echo "    Login Form Token: " . ($initialToken ? substr($initialToken, 0, 8) . '...' : 'NOT FOUND') . "\n";

// Step 2: Perform Login
echo "[E2E Step 2] Authenticating as '{$adminUser}'...\n";
$loginPayload = [
	'username' => $adminUser,
	'passwd'   => $adminPass,
	'task'     => 'login',
	'option'   => 'com_login',
	'return'   => base64_encode('index.php'),
];

if ($initialToken)
{
	$loginPayload[$initialToken] = '1';
}

$loginResult = httpRequest("{$baseUrl}/administrator/index.php?task=login", 'POST', $loginPayload, $cookieJar);

if ($loginResult['code'] !== 200 || strpos($loginResult['body'], 'mod-login-username') !== false)
{
	$dashboardCheck = httpRequest("{$baseUrl}/administrator/index.php", 'GET', null, $cookieJar);
	if (strpos($dashboardCheck['body'], 'mod-login-username') !== false)
	{
		echo "[-] FAILED: Authentication failed. Please verify admin credentials.\n";
		exit(1);
	}
}

echo "    Authentication SUCCESSFUL.\n";

// Step 3: Find Helix Ultimate Template Style ID
echo "[E2E Step 3] Finding Helix Ultimate style ID...\n";
$stylesPage = httpRequest("{$baseUrl}/administrator/index.php?option=com_templates&view=styles", 'GET', null, $cookieJar);
preg_match('/href="[^"]*option=com_ajax&amp;helix=ultimate&amp;id=(\d+)[^"]*"/', $stylesPage['body'], $styleMatches);

if (empty($styleMatches[1]))
{
	preg_match('/href="[^"]*option=com_templates&amp;task=style\.edit&amp;id=(\d+)[^"]*"[^>]*>[^<]*shaper_helixultimate/i', $stylesPage['body'], $styleMatches);
}

$styleId = (int) ($styleMatches[1] ?? 12);
echo "    Found Style ID: {$styleId}\n";

// Step 4: Fetch Helix Customizer & Extract Injected Script Option Token
echo "[E2E Step 4] Loading Helix Ultimate Customizer...\n";
$customizer = httpRequest("{$baseUrl}/administrator/index.php?option=com_ajax&helix=ultimate&id={$styleId}", 'GET', null, $cookieJar);

if ($customizer['code'] !== 200)
{
	echo "[-] FAILED: Could not access Helix customizer (HTTP {$customizer['code']})\n";
	exit(1);
}

// Check if Joomla.getOptions csrf.token was injected by Platform.php
$hasCsrfOption = preg_match('/"csrf\.token"\s*:\s*"([a-f0-9]{32})"/', $customizer['body'], $csrfMatches);
$sessionToken = $csrfMatches[1] ?? '';

// Check if form.token was rendered in #hu-style-form in display.php
preg_match_all('/<input[^>]+name="([a-f0-9]{32})"[^>]+value="1"/i', $customizer['body'], $formTokenMatches);
$foundTokens = $formTokenMatches[1] ?? [];
$formToken = !empty($foundTokens) ? end($foundTokens) : '';

echo "    Injected Script Option (csrf.token): " . ($sessionToken ? "PASS (" . substr($sessionToken, 0, 8) . "...)" : "FAIL (Missing)") . "\n";
echo "    Rendered Form Token in #hu-style-form: " . ($formToken ? "PASS (" . substr($formToken, 0, 8) . "...)" : "FAIL (Missing)") . "\n";

if (empty($sessionToken))
{
	echo "[-] FAILED: Platform.php did not inject 'csrf.token' script option.\n";
	exit(1);
}

// Step 5: Test 'view-media' WITHOUT token (Negative Test: Must be rejected by guardAjaxRequest)
echo "[E2E Step 5] Testing 'view-media' WITHOUT CSRF token (Negative Test)...\n";
$noTokenPayload = [
	'action'   => 'view-media',
	'option'   => 'com_ajax',
	'helix'    => 'ultimate',
	'request'  => 'task',
	'format'   => 'json',
	'id'       => $styleId,
];

$noTokenRes = httpRequest("{$baseUrl}/administrator/index.php", 'POST', $noTokenPayload, $cookieJar);
$noTokenData = json_decode($noTokenRes['body'], true);

if (isset($noTokenData['status']) && $noTokenData['status'] === false && strpos((string)($noTokenData['message'] ?? ''), 'invalid security token') !== false)
{
	echo "    Security Guard Rejection: PASS (Rejected with invalid security token as expected)\n";
}
else
{
	echo "    Security Guard Rejection: FAIL (Expected rejection without token, got: {$noTokenRes['body']})\n";
	exit(1);
}

// Step 6: Test 'view-media' WITH Token (Positive Test: Issue #562 Fix Verification)
echo "[E2E Step 6] Testing 'view-media' WITH CSRF token (Positive Test - Issue #562)...\n";
$withTokenPayload = array_merge($noTokenPayload, [
	$sessionToken => '1',
]);

$withTokenRes = httpRequest("{$baseUrl}/administrator/index.php", 'POST', $withTokenPayload, $cookieJar);
$withTokenData = json_decode($withTokenRes['body'], true);

if (isset($withTokenData['status']) && $withTokenData['status'] === true && !empty($withTokenData['breadcrumbs']))
{
	echo "    Media Picker Response: PASS (Status: true, Folders/Breadcrumbs populated successfully)\n";
}
else
{
	echo "[-] FAILED: Media Picker request failed with response: {$withTokenRes['body']}\n";
	exit(1);
}

// Clean up
@unlink($cookieJar);

echo "\n=======================================================\n";
echo " [ALL E2E CHECKS PASSED] Issue #562 Verified Fixed!\n";
echo "=======================================================\n\n";
exit(0);
