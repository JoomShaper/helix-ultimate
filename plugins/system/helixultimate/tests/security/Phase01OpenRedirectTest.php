<?php
/**
 * Phase 1: open redirect validation tests.
 *
 * @package Helix_Ultimate_Framework
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

use HelixUltimate\Framework\Platform\Helper;

final class Phase01OpenRedirectTest
{
	public static function run(): array
	{
		$failures = [];

		$internal = base64_encode('index.php?option=com_ajax&helix=ultimate&id=1');
		$result = Helper::validateInternalRedirect($internal);

		if ($result !== 'index.php?option=com_ajax&helix=ultimate&id=1')
		{
			$failures[] = 'Expected internal admin return URL to be accepted.';
		}

		$external = base64_encode('https://evil.example/phish');
		if (Helper::validateInternalRedirect($external) !== null)
		{
			$failures[] = 'Expected external HTTPS URL to be rejected.';
		}

		$protocolRelative = base64_encode('//evil.example/phish');
		if (Helper::validateInternalRedirect($protocolRelative) !== null)
		{
			$failures[] = 'Expected protocol-relative URL to be rejected.';
		}

		if (Helper::validateInternalRedirect('not-valid-base64!!!') !== null)
		{
			$failures[] = 'Expected invalid base64 payload to be rejected.';
		}

		return $failures;
	}
}
