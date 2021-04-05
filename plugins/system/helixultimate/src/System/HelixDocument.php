<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace HelixUltimate\Framework\System;

use HelixUltimate\Framework\System\JoomlaBridge;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * Helix Document a abstraction of the Document/HtmlDocument for making B\C of Joomla 3 & 4.
 *
 * @since	2.0.0
 */
class HelixDocument
{
	/**
	 * The document instance
	 *
	 * @var		Document	the document instance.
	 * @since	2.0.0
	 */
	protected $document = null;

	/**
	 * Web Asset Manager, the instance of WebAssetManager
	 *
	 * @var		WebAssetManager|Document	Return WebAssetManager if the Joomla version
	 * 										is greater than or equal 4, otherwise the Document instance.
	 * @since	2.0.0
	 */
	protected $webAssetManager = null;

	/**
	 * Joomla major version.
	 *
	 * @var		integer
	 * @since	2.0.0
	 */
	private $joomlaMajor = 0;

	/**
	 * Joomla 3 4 asset mapping.
	 *
	 * @var		array
	 * @since	2.0.0
	 */
	private $assetMap = [];

	public function __construct()
	{
		$this->document = Factory::getDocument();
		$this->joomlaMajor = JoomlaBridge::getVersion('major');
		$this->assetMap = JoomlaBridge::getAssetMap();
		$this->webAssetManager = $this->loadWebAssetManager();
	}

	/**
	 * Magic call method for handling register<Type>, use<Type> and registerAndUse<Type> methods.
	 *
	 * @param	string	$method		The method name.
	 * @param	array	$arguments	The arguments for the method.
	 *
	 * @return	mixed
	 * @since	2.0.0
	 *
	 * @throws	\BadMethodException
	 */
	public function __call($method, $arguments)
	{
		$method = strtolower($method);

		if (strpos($method, 'use') === 0)
		{
			$type = substr($method, 3);

			if (empty($arguments[0]))
			{
				throw new \BadMethodCallException(sprintf('Asset name is required!'));
			}

			return $this->useAsset($type, $arguments[0]);
		}

		if (strpos($method, 'addinline') === 0)
		{
			$type = substr($method, 9);

			if (empty($arguments[0]))
			{
				throw new \BadMethodCallException(sprintf('Asset content is required!'));
			}

			return $this->addInline($type, ...$arguments);
		}

		if (strpos($method, 'register') === 0)
		{
			$andUse = substr($method, 8, 6) === 'anduse';
			$type = $andUse ? substr($method, 14) : substr($method, 8);

			if ($andUse)
			{
				return $this->registerAndUseAsset($type, ...$arguments);
			}
			else
			{
				return $this->registerAsset($type, ...$arguments);
			}
		}

		if ($this->joomlaMajor >= 4)
		{
			if (method_exists($this->webAssetManager, $method))
			{
				return call_user_func_array([$this->webAssetManager, $method], $arguments);
			}
			else
			{
				throw new \BadMethodCallException(sprintf('Undefined method %s in class %s', $method, get_class($this)));
			}
		}

		throw new \BadMethodCallException(sprintf('Undefined method %s in class %s', $method, get_class($this)));
	}

	/**
	 * Load webAssetManager for the specific Joomla! major version.
	 * If it is J3 then return Document as webAssetManager and for
	 * J4 it returns the instance of document->getWebAssetManager
	 *
	 * @return	mixed
	 * @since	2.0.0
	 */
	protected function loadWebAssetManager()
	{
		if ($this->joomlaMajor < 4)
		{
			return $this->document;
		}

		return $this->document->getWebAssetManager();
	}

	/**
	 * Get the webAssetManager instance.
	 *
	 * @return	mixed	Document|WebAssetManager
	 * @since	2.0.0
	 */
	public function getWebAssetManager()
	{
		return $this->webAssetManager;
	}

	/**
	 * Register Asset. This method is working on the J4 only as J3 has nothing similar to it.
	 *
	 * @param	string	$type			The asset type. Possible values are 'script' and 'style'
	 * @param	string	$name			The asset name. The asset will be identified by this name in future.
	 * @param	string	$uri			The asset location.
	 * @param	array	$options		The options array for the asset.
	 * @param	array	$attributes		The attributes array for the asset.
	 * @param	array	$dependencies	The dependencies array for the asset.
	 *
	 * @return	self
	 * @since	2.0.0
	 */
	public function registerAsset(string $type, string $name, string $uri = '', array $options = [], array $attributes = [], array $dependencies = []) : self
	{
		if ($this->joomlaMajor >= 4)
		{
			$this->webAssetManager->registerStyle($name, $uri, $options, $attributes, $dependencies);
		}

		return $this;
	}

	/**
	 * Use asset to the site. This asset will look for assetMap at JoomlaBridge and
	 * add asset according to the map for both J3 and J4
	 *
	 * @param	string	$type	The asset type. Possible values are 'script' and 'style'.
	 * @param	string	$name	The asset name which is registered before.
	 *
	 * @return	self
	 * @since	2.0.0
	 */
	public function useAsset(string $type, string $name) : self
	{
		if ($this->joomlaMajor >= 4)
		{
			if (isset($this->assetMap[$name]))
			{
				if (!empty($this->assetMap[$name][1]))
				{
					$name = $this->assetMap[$name][1];
					$this->webAssetManager->useAsset($type, $name);
				}
			}
			else
			{
				$this->webAssetManager->useAsset($type, $name);
			}
		}
		else
		{
			if (isset($this->assetMap[$name]))
			{
				if (!empty($this->assetMap[$name][0]))
				{
					$name = $this->assetMap[$name][0];
					HTMLHelper::_($name);
				}
			}
		}

		return $this;
	}

	/**
	 * Register and Use Asset. This is the combination of both `registerAsset` then `useAsset`.
	 *
	 * @param	string	$type			The asset type. Possible values are 'script' and 'style'
	 * @param	string	$name			The asset name. The asset will be identified by this name in future.
	 * @param	string	$uri			The asset location.
	 * @param	array	$options		The options array for the asset.
	 * @param	array	$attributes		The attributes array for the asset.
	 * @param	array	$dependencies	The dependencies array for the asset.
	 *
	 * @return	self
	 * @since	2.0.0
	 */
	public function registerAndUseAsset(string $type, string $name, string $uri = '', array $options = [], array $attributes = [], array $dependencies = []) : self
	{
		if ($this->joomlaMajor >= 4)
		{
			if (isset($this->assetMap[$name]) && !empty($this->assetMap[$name][1]))
			{
				$uri = $this->assetMap[$name][1];
			}

			// Generating method like registerAndUseStyle/registerAndUseScript
			$registerAndUseMethod = 'registerAndUse' . ucfirst($type);
			$this->webAssetManager->$registerAndUseMethod($name, $uri, $options, $attributes, $dependencies);
		}
		else
		{
			if (isset($this->assetMap[$name]) && !empty($this->assetMap[$name][0]))
			{
				$uri = $this->assetMap[$name][0];
			}

			$nameMap = ['script' => 'script', 'style' => 'stylesheet'];

			if (!empty($uri))
			{
				if (isset($this->assetMap[$name]) && !empty($this->assetMap[$name][2]) && $this->assetMap[$name][2] === 'registered')
				{
					HTMLHelper::_($uri);
				}
				else
				{
					HTMLHelper::_($nameMap[$type], $uri, $options, $attributes);
				}
			}
		}

		return $this;
	}

	/**
	 * Add Inline asset.
	 *
	 * @param	string	$type			The asset type. Possible values are 'script' and 'style'
	 * @param	string	$name			The asset name. The asset will be identified by this name in future.
	 * @param	string	$uri			The asset location.
	 * @param	array	$options		The options array for the asset.
	 * @param	array	$attributes		The attributes array for the asset.
	 * @param	array	$dependencies	The dependencies array for the asset
	 *
	 * @return	self
	 * @since	2.0.0
	 */
	public function addInline(string $type, string $content, array $options = [], array $attributes = [], array $dependencies = []) : self
	{
		if ($this->joomlaMajor >= 4)
		{
			$this->webAssetManager->addInline($type, $content, $options, $attributes, $dependencies);
		}
		else
		{
			// Generating method name like addScriptDeclaration/addStyleDeclaration
			$declarationMethod = 'add' . ucfirst($type) . 'Declaration';
			$this->webAssetManager->$declarationMethod($content);
		}

		return $this;
	}
}
