<?php
/**
 * Phase 3: path traversal hardening tests.
 *
 * @package Helix_Ultimate_Framework
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

use HelixUltimate\Framework\Platform\Helper;

final class Phase03PathTraversalTest
{
	public static function run(): array
	{
		$failures = [];

		if (Helper::sanitizeLayoutName('my-layout') !== 'my-layout.json')
		{
			$failures[] = 'Valid layout name should be accepted.';
		}

		if (Helper::sanitizeLayoutName('..') !== null)
		{
			$failures[] = 'Parent-directory layout name should be rejected.';
		}

		if (Helper::sanitizeLayoutName('bad name!') !== null)
		{
			$failures[] = 'Layout name with invalid characters should be rejected.';
		}

		if (Helper::resolveMediaPath('../configuration.php') !== null)
		{
			$failures[] = 'Traversal media path should be rejected.';
		}

		if (Helper::resolveMediaPath('/etc/passwd') !== null)
		{
			$failures[] = 'Paths outside media root should be rejected.';
		}

		if (Helper::resolveMediaPath("images/test.jpg\0.php") !== null)
		{
			$failures[] = 'Paths containing null bytes should be rejected.';
		}

		// Symlink pointing outside images root test
		$outsideDir = sys_get_temp_dir() . '/helix_outside_' . uniqid();
		@mkdir($outsideDir, 0777, true);
		$symlinkPath = JPATH_ROOT . '/images/symlink_outside_' . uniqid();
		if (@symlink($outsideDir, $symlinkPath))
		{
			$relSymlink = 'images/' . basename($symlinkPath) . '/file.txt';
			if (Helper::resolveMediaPath($relSymlink) !== null)
			{
				$failures[] = 'Symlink pointing outside media root should be rejected.';
			}
			@unlink($symlinkPath);
		}
		@rmdir($outsideDir);

		return $failures;
	}
}
