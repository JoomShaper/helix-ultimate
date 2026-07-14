<?php
/**
 * Phase 6: remaining hardening tests.
 *
 * @package Helix_Ultimate_Framework
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

use HelixUltimate\Framework\Platform\Helper;

final class Phase06RemainingHardeningTest
{
	public static function run(): array
	{
		$failures = [];
		$keys = Helper::getHelixAttribKeys();

		if (!in_array('helix_ultimate_audio', $keys, true))
		{
			$failures[] = 'Helix attribs allowlist should include helix_ultimate_audio.';
		}

		if (in_array('evil_key', $keys, true))
		{
			$failures[] = 'Helix attribs allowlist should only include helix_ultimate_* keys.';
		}

		$pluginSource = file_get_contents(dirname(__DIR__, 2) . '/helixultimate.php');

		if ($pluginSource !== false && !str_contains($pluginSource, "authorise('core.edit', 'com_templates')"))
		{
			$failures[] = 'Template settings export should require com_templates edit permission.';
		}

		$requestSource = file_get_contents(dirname(__DIR__, 2) . '/src/Platform/Request.php');

		if ($requestSource !== false && str_contains($requestSource, '$_POST'))
		{
			$failures[] = 'Request handlers should not read raw $_POST directly.';
		}

		return $failures;
	}
}
