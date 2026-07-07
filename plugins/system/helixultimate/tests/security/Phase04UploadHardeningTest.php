<?php
/**
 * Phase 4: upload hardening tests.
 *
 * @package Helix_Ultimate_Framework
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

final class Phase04UploadHardeningTest
{
	public static function run(): array
	{
		$failures = [];
		$requestSource = file_get_contents(dirname(__DIR__, 2) . '/src/Platform/Request.php');

		if ($requestSource === false)
		{
			$failures[] = 'Unable to read Request.php for switch fallthrough verification.';

			return $failures;
		}

		if (!preg_match("/case 'upload-media':\s*\n\s*Media::uploadMedia\(\);\s*\n\s*break;/", $requestSource))
		{
			$failures[] = 'upload-media case must break before import-tmpl-style.';
		}

		$mediaSource = file_get_contents(dirname(__DIR__, 2) . '/src/Platform/Media.php');

		if ($mediaSource !== false && str_contains($mediaSource, "'svg'"))
		{
			$failures[] = 'Media upload allowlist should not include svg.';
		}

		$blogSource = file_get_contents(dirname(__DIR__, 2) . '/src/Platform/Blog.php');

		if ($blogSource !== false && !str_contains($blogSource, 'File::makeSafe'))
		{
			$failures[] = 'Blog upload should sanitize filenames with File::makeSafe().';
		}

		return $failures;
	}
}
