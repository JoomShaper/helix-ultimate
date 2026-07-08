<?php
/**
 * Phase 7: Mega menu XSS and auth bypass hardening tests.
 *
 * @package Helix_Ultimate_Framework
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

use HelixUltimate\Framework\Platform\Helper;

final class Phase07MegaMenuSecurityTest
{
	public static function run(): array
	{
		$failures = [];
		$pluginSource = file_get_contents(dirname(__DIR__, 2) . '/helixultimate.php');
		$modMenuSource = file_get_contents(dirname(__DIR__, 2) . '/overrides/mod_menu/default.php');
		$helixMenuSource = file_get_contents(dirname(__DIR__, 2) . '/src/Core/Classes/HelixultimateMenu.php');

		if ($pluginSource === false || !str_contains($pluginSource, 'Helper::guardAjaxRequest($method)'))
		{
			$failures[] = 'onAjaxHelixultimate should call Helper::guardAjaxRequest($method).';
		}

		if ($modMenuSource === false || !str_contains($modMenuSource, "htmlspecialchars(\$class, ENT_QUOTES, 'UTF-8')"))
		{
			$failures[] = 'mod_menu override should escape the li class attribute.';
		}

		if ($helixMenuSource === false || !str_contains($helixMenuSource, 'sanitizeMegaMenuCustomClass'))
		{
			$failures[] = 'HelixultimateMenu should sanitize customclass before rendering.';
		}

		if ($helixMenuSource === false || !str_contains($helixMenuSource, 'sanitizeMegaMenuBadge'))
		{
			$failures[] = 'HelixultimateMenu should sanitize badge text before rendering.';
		}

		if (!method_exists(Helper::class, 'sanitizeMegaMenuSettings'))
		{
			$failures[] = 'Helper::sanitizeMegaMenuSettings should exist.';
		}

		$maliciousClass = Helper::sanitizeMegaMenuCustomClass('"><script>alert(1)</script><span class="');

		if ($maliciousClass !== '')
		{
			$failures[] = 'sanitizeMegaMenuCustomClass should strip XSS payloads.';
		}

		$validClass = Helper::sanitizeMegaMenuCustomClass('my-nav-item highlight');

		if ($validClass !== 'my-nav-item highlight')
		{
			$failures[] = 'sanitizeMegaMenuCustomClass should preserve valid class names.';
		}

		$invalidColor = Helper::sanitizeMegaMenuColor('red; expression(alert(1))');

		if ($invalidColor !== '')
		{
			$failures[] = 'sanitizeMegaMenuColor should reject non-hex values.';
		}

		$validColor = Helper::sanitizeMegaMenuColor('#ff0033');

		if ($validColor !== '#ff0033')
		{
			$failures[] = 'sanitizeMegaMenuColor should preserve valid hex colors.';
		}

		$sanitized = Helper::sanitizeMegaMenuSettings([
			'customclass' => '"><script>alert(1)</script>',
			'badge' => '<img src=x onerror=alert(1)>',
			'badge_bg_color' => 'javascript:alert(1)',
			'faicon' => '"><script>alert(1)</script>',
			'layout' => [
				[
					'type' => 'row',
					'attr' => [
						[
							'type' => 'column',
							'colGrid' => '6',
							'moduleId' => '101',
							'menuParentId' => '202',
							'items' => [
								[
									'type' => 'module',
									'id' => '55',
								],
							],
						],
					],
				],
			],
		]);

		if (($sanitized['customclass'] ?? null) !== '')
		{
			$failures[] = 'sanitizeMegaMenuSettings should strip malicious customclass values.';
		}

		if (($sanitized['badge'] ?? null) !== '')
		{
			$failures[] = 'sanitizeMegaMenuSettings should strip HTML from badge text.';
		}

		if (($sanitized['badge_bg_color'] ?? null) !== '')
		{
			$failures[] = 'sanitizeMegaMenuSettings should reject invalid badge colors.';
		}

		if (($sanitized['faicon'] ?? null) !== '')
		{
			$failures[] = 'sanitizeMegaMenuSettings should reject invalid icon classes.';
		}

		if (!isset($sanitized['layout'][0]['attr'][0]['items'][0]['moduleId']))
		{
			$failures[] = 'sanitizeMegaMenuSettings should preserve valid layout structure.';
		}

		$jsLayout = Helper::sanitizeMegaMenuSettings([
			'layout' => [
				[
					'type' => 'row',
					'attr' => [
						[
							'type' => 'column',
							'colGrid' => '6',
							'items' => [
								[
									'type' => 'menu_item',
									'item_id' => '2933',
								],
								[
									'type' => 'module',
									'item_id' => '311',
								],
							],
						],
					],
				],
			],
		]);

		$menuCell = $jsLayout['layout'][0]['attr'][0]['items'][0] ?? [];
		$moduleCell = $jsLayout['layout'][0]['attr'][0]['items'][1] ?? [];

		if (($menuCell['type'] ?? null) !== 'menu_item' || ($menuCell['item_id'] ?? null) !== '2933')
		{
			$failures[] = 'sanitizeMegaMenuSettings should preserve menu_item cells with item_id from the builder payload.';
		}

		if (($moduleCell['type'] ?? null) !== 'module' || ($moduleCell['item_id'] ?? null) !== '311' || ($moduleCell['moduleId'] ?? null) !== '311')
		{
			$failures[] = 'sanitizeMegaMenuSettings should preserve module cells with item_id and moduleId from the builder payload.';
		}

		return $failures;
	}
}
