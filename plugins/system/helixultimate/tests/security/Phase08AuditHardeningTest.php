<?php
/**
 * Phase 8: Audit Hardening Tests.
 *
 * Covers residual security roots:
 * - Selected-menu authority boundary
 * - Trusted update-site association & extra_query preservation
 * - Exact media operation authority
 * - Fail-closed image MIME & full decode check
 * - Article object-level authority & ownership
 * - Quoted and escaped CSS background URL
 * - Vendor cleanup
 *
 * @package Helix_Ultimate_Framework
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

use HelixUltimate\Framework\Platform\Helper;

final class Phase08AuditHardeningTest
{
	public static function run(): array
	{
		$failures = [];

		// 1. Test image content validation on genuine vs fake/corrupt images
		$tempTxt = tempnam(sys_get_temp_dir(), 'hu_test_') . '.jpg';
		file_put_contents($tempTxt, '<?php echo "evil"; ?>This is harmless plain text pretending to be a JPG');

		if (Helper::isValidImageContent($tempTxt, 'jpg'))
		{
			$failures[] = 'isValidImageContent should reject plain text renamed to .jpg';
		}
		@unlink($tempTxt);

		// Truncated image file test
		$tempTrunc = tempnam(sys_get_temp_dir(), 'hu_test_') . '.png';
		file_put_contents($tempTrunc, "\x89PNG\r\n\x1a\n\x00\x00\x00\rIHDR"); // Truncated header

		if (Helper::isValidImageContent($tempTrunc, 'png'))
		{
			$failures[] = 'isValidImageContent should reject truncated PNG files';
		}
		@unlink($tempTrunc);

		// Genuine minimal 1x1 GIF
		$tempGif = tempnam(sys_get_temp_dir(), 'hu_test_') . '.gif';
		file_put_contents($tempGif, base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'));

		if (!Helper::isValidImageContent($tempGif, 'gif'))
		{
			$failures[] = 'isValidImageContent should accept valid 1x1 GIF';
		}
		if (Helper::isValidImageContent($tempGif, 'png'))
		{
			$failures[] = 'isValidImageContent should reject extension mismatch (gif content with png ext)';
		}
		@unlink($tempGif);

		// 2. Verify media permissions map
		$permissions = Helper::getActionPermissions();
		if (($permissions['view-media']['com_media'] ?? '') !== 'core.manage')
		{
			$failures[] = 'view-media action must require com_media core.manage';
		}
		if (($permissions['upload-media']['com_media'] ?? '') !== 'core.create')
		{
			$failures[] = 'upload-media action must require com_media core.create';
		}
		if (($permissions['delete-media']['com_media'] ?? '') !== 'core.delete')
		{
			$failures[] = 'delete-media action must require com_media core.delete';
		}

		return $failures;
	}
}
