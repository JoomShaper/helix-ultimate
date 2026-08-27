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

		// Helper::isValidImageContent tests
		// 1. Text file renamed as .jpg
		$tmpText = tempnam(sys_get_temp_dir(), 'test_text');
		file_put_contents($tmpText, "<?php echo 'hello'; ?>");
		if (\HelixUltimate\Framework\Platform\Helper::isValidImageContent($tmpText, 'jpg'))
		{
			$failures[] = 'isValidImageContent should reject plain text file disguised as jpg.';
		}
		@unlink($tmpText);

		// 2. Valid minimal 1x1 PNG
		$validPngData = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
		$tmpPng = tempnam(sys_get_temp_dir(), 'test_png');
		file_put_contents($tmpPng, $validPngData);
		if (!\HelixUltimate\Framework\Platform\Helper::isValidImageContent($tmpPng, 'png'))
		{
			$failures[] = 'isValidImageContent should accept valid PNG file.';
		}

		// 3. Valid PNG with mismatched extension .jpg
		if (\HelixUltimate\Framework\Platform\Helper::isValidImageContent($tmpPng, 'jpg'))
		{
			$failures[] = 'isValidImageContent should reject PNG file when expected extension is jpg.';
		}
		@unlink($tmpPng);

		// 4. Valid minimal 1x1 GIF
		$validGifData = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
		$tmpGif = tempnam(sys_get_temp_dir(), 'test_gif');
		file_put_contents($tmpGif, $validGifData);
		if (!\HelixUltimate\Framework\Platform\Helper::isValidImageContent($tmpGif, 'gif'))
		{
			$failures[] = 'isValidImageContent should accept valid GIF file.';
		}
		@unlink($tmpGif);

		// 5. Empty file
		$tmpEmpty = tempnam(sys_get_temp_dir(), 'test_empty');
		file_put_contents($tmpEmpty, '');
		if (\HelixUltimate\Framework\Platform\Helper::isValidImageContent($tmpEmpty, 'png'))
		{
			$failures[] = 'isValidImageContent should reject empty file.';
		}
		@unlink($tmpEmpty);

		return $failures;
	}
}
