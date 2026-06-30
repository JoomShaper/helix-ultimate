<?php
/**
 * Phase 2: CSRF/ACL permission map tests.
 *
 * @package Helix_Ultimate_Framework
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

use HelixUltimate\Framework\Platform\Helper;

final class Phase02CsrfAclTest
{
	public static function run(): array
	{
		$failures = [];
		$adminMap = Helper::getActionPermissions();
		$siteMap = Helper::getSiteActionPermissions();

		$requiredAdmin = [
			'save-tmpl-style' => 'com_templates',
			'parentAdoption' => 'com_menus',
			'upload-blog-image' => 'com_content',
		];

		foreach ($requiredAdmin as $action => $asset)
		{
			if (!isset($adminMap[$action][$asset]))
			{
				$failures[] = "Admin map missing {$action} => {$asset}.";
			}
		}

		$requiredSite = [
			'upload-blog-image' => ['com_content', 'com_media'],
			'view-media' => ['com_media'],
		];

		foreach ($requiredSite as $action => $assets)
		{
			if (!isset($siteMap[$action]))
			{
				$failures[] = "Site map missing action {$action}.";
				continue;
			}

			foreach ($assets as $asset)
			{
				if (!isset($siteMap[$action][$asset]))
				{
					$failures[] = "Site map missing {$action} => {$asset}.";
				}
			}
		}

		if (isset($adminMap['unknown-action']))
		{
			$failures[] = 'Admin map should not define unknown actions.';
		}

		return $failures;
	}
}
