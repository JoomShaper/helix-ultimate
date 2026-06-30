<?php
/**
 * Minimal bootstrap for Helix Ultimate security unit tests.
 *
 * @package Helix_Ultimate_Framework
 */

declare(strict_types=1);

define('_JEXEC', 1);

if (!defined('JPATH_ROOT'))
{
	define('JPATH_ROOT', sys_get_temp_dir() . '/helix-security-test');
}

if (!is_dir(JPATH_ROOT . '/images'))
{
	mkdir(JPATH_ROOT . '/images', 0777, true);
}

if (!class_exists('Joomla\\Filesystem\\Path'))
{
	class JoomlaFilesystemPathStub
	{
		public static function clean(string $path): string
		{
			return preg_replace('#/+#', '/', str_replace('\\', '/', $path)) ?? $path;
		}

		public static function check(string $path): void
		{
			if (str_contains($path, '..'))
			{
				throw new \RuntimeException('Path traversal detected.');
			}
		}
	}

	class_alias(JoomlaFilesystemPathStub::class, 'Joomla\\Filesystem\\Path');
}

if (!class_exists('Joomla\\CMS\\Component\\ComponentHelper'))
{
	class JoomlaComponentHelperStub
	{
		public static function getParams(string $component): object
		{
			return new class {
				public function get(string $key, $default = null)
				{
					return $key === 'image_path' ? 'images' : $default;
				}
			};
		}
	}

	class_alias(JoomlaComponentHelperStub::class, 'Joomla\\CMS\\Component\\ComponentHelper');
}

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
