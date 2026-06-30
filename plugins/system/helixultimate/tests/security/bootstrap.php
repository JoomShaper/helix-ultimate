<?php
/**
 * Minimal bootstrap for Helix Ultimate security unit tests.
 *
 * @package Helix_Ultimate_Framework
 */

declare(strict_types=1);

define('_JEXEC', 1);

if (!class_exists('Joomla\\CMS\\Uri\\Uri'))
{
	/**
	 * Lightweight Uri stub matching Joomla internal URL rules used in tests.
	 */
	class JoomlaUriStub
	{
		public static function isInternal(string $url): bool
		{
			$url = trim($url);

			if ($url === '')
			{
				return false;
			}

			if (str_starts_with($url, '//'))
			{
				return false;
			}

			if (preg_match('#^https?://#i', $url))
			{
				return false;
			}

			return true;
		}
	}

	class_alias(JoomlaUriStub::class, 'Joomla\\CMS\\Uri\\Uri');
}

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

require_once dirname(__DIR__, 2) . '/src/Platform/Helper.php';
