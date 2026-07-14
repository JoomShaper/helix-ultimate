<?php
/**
 * Minimal bootstrap for Helix Ultimate security unit tests.
 *
 * @package Helix_Ultimate_Framework
 */

declare(strict_types=1);

if (!function_exists('str_contains'))
{
	function str_contains($haystack, $needle)
	{
		return $needle === '' || strpos($haystack, $needle) !== false;
	}
}

if (!function_exists('str_starts_with'))
{
	function str_starts_with($haystack, $needle)
	{
		return $needle === '' || strncmp($haystack, $needle, strlen($needle)) === 0;
	}
}

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
	class_alias(JoomlaFilesystemPathStub::class, 'Joomla\\CMS\\Filesystem\\Path');
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

if (!class_exists('Joomla\\CMS\\Filter\\InputFilter'))
{
	class JoomlaInputFilterStub
	{
		private array $allowedTags;

		public function __construct(array $allowedTags)
		{
			$this->allowedTags = $allowedTags;
		}

		public static function getInstance(array $tags, array $attributes, int $tagsFlag, int $attrFlag): self
		{
			return new self($tags);
		}

		public function clean(string $source, string $type = 'html'): string
		{
			if ($type !== 'html')
			{
				return $source;
			}

			$pattern = '#<(?!/?(' . implode('|', $this->allowedTags) . ')\b)[^>]+>#i';

			return preg_replace($pattern, '', $source) ?? '';
		}
	}

	class_alias(JoomlaInputFilterStub::class, 'Joomla\\CMS\\Filter\\InputFilter');
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
