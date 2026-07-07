<?php
/**
 * Phase 5: XSS sanitization tests.
 *
 * @package Helix_Ultimate_Framework
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

use HelixUltimate\Framework\Platform\Helper;

final class Phase05XssSanitizeTest
{
	public static function run(): array
	{
		$failures = [];
		$dirty = '<script>alert(1)</script><iframe src="https://example.com"></iframe>';
		$clean = Helper::sanitizeEmbed($dirty);

		if (str_contains($clean, '<script'))
		{
			$failures[] = 'sanitizeEmbed should strip script tags.';
		}

		if (!str_contains($clean, '<iframe'))
		{
			$failures[] = 'sanitizeEmbed should preserve allowed iframe tags.';
		}

		$audioLayout = file_get_contents(dirname(__DIR__, 2) . '/overrides/layouts/joomla/content/blog/audio.php');

		if ($audioLayout !== false && !str_contains($audioLayout, 'Helper::sanitizeEmbed'))
		{
			$failures[] = 'Audio layout should sanitize embed output.';
		}

		return $failures;
	}
}
