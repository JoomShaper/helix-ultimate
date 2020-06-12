<?php
/**
 * @package 	Helix_Ultimate_Framework
 * @author 		JoomShaper <joomshaper@js.com>
 * @copyright 	Copyright (c) 2010 - 2018 JoomShaper
 * @license 	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
namespace HelixUltimate\Framework\System;

defined('_JEXEC') or die();

use Joomla\CMS\Cache\Cache;
use Joomla\CMS\Factory;

/**
 * Class for caching
 *
 * @since	2.0.0
 */
class HelixCache
{
	/**
	 * Cache key
	 *
	 * @var		string		$key	Cache key.
	 * @since	2.0.0
	 */
	private $key;

	/**
	 * Cache group where to cache store.
	 *
	 * @var		string	$group	Cache group.
	 * @since	2.0.0
	 */
	private	$group = 'helixultimate';

	/**
	 * Cache instance.
	 *
	 * @var		Cache	$cache	JCache instance.
	 * @since	2.0.0
	 */
	private $cache;

	/**
	 * Constructor function.
	 *
	 * @param	string		$key		Cache key
	 * @param	integer		$lifetime	Cache lifetime.
	 *
	 * @return	void
	 * @since	2.0.0
	 */
	public function __construct($key, $lifetime = 1440)
	{
		$this->key = $key;
		$this->setCacheInstance($lifetime);
	}

	/**
	 * Set cache group externally
	 *
	 * @param	string	$group	Group name
	 *
	 * @return	self	The class instance
	 * @since	2.0.0
	 */
	public function setGroup($group)
	{
		$this->group = $group;

		return $this;
	}

	/**
	 * Set cache key.
	 * This is for manipulate the key if anyone don't want to re-initiate the class.
	 *
	 * @param	string	$key	Cache key to set.
	 *
	 * @return	self	Class instance for chaining
	 * @since	2.0.0
	 */
	public function setCacheKey($key)
	{
		$this->key = $key;

		return $this;
	}

	/**
	 * Get Cache key for outside of the class.
	 *
	 * @return 	string	Cache key
	 * @since	2.0.0
	 */
	public function getCacheKey()
	{
		return $this->key;
	}

	/**
	 * Set cache instance
	 *
	 * @param	int		$lifetime	Cache lifetime.
	 *
	 * @return	self	The class instance for chaining.
	 * @since	2.0.0
	 */
	public function setCacheInstance($lifetime)
	{
		$config = Factory::getConfig();

		$options = [
			'caching' 	=> true,
			'cachebase' => $config->get('cache_path', JPATH_ROOT . '/cache'),
			'lifetime' 	=> $lifetime
		];

		$this->cache = Cache::getInstance('', $options);

		return $this;
	}

	/**
	 * Get cache instance
	 *
	 * @return	Cache	The cache instance
	 * @since	2.0.0
	 */
	public function getCacheInstance()
	{
		return $this->cache;
	}

	/**
	 * If cache data contains for the key.
	 *
	 * @return	boolean		true on success, false otherwise
	 * @since	2.0.0
	 */
	public function contains() : bool
	{
		return $this->cache->contains($this->key, $this->group);
	}

	/**
	 * Clean Cache
	 *
	 * @return	self	Class instance
	 * @since	2.0.0
	 */
	public function cleanCache()
	{
		$this->cache->clean($this->group);

		return $this;
	}

	/**
	 * Remove cache by key.
	 *
	 * @param	string	$key	The key string.
	 *
	 * @return	self
	 * @since	2.0.0
	 */
	public function removeCache($key = null)
	{
		if (empty($key))
		{
			$key = $this->key;
		}

		$this->cache->remove($key, $this->group);

		return $this;
	}

	/**
	 * Store cache with data.
	 *
	 * @param	array	$data	data to store as cache.
	 *
	 * @return	self	Class instance
	 * @since	2.0.0
	 */
	public function storeCache($data)
	{
		$this->cache->store($data, $this->key, $this->group);

		return $this;
	}

	/**
	 * Load cached data by key
	 *
	 * @return	mixed	Loaded data on success, null on no data.
	 * @since	2.0.0
	 */
	public function loadData()
	{
		$data = $this->cache->get($this->key, $this->group);

		if (!empty($data))
		{
			return $data;
		}

		return null;
	}
}
