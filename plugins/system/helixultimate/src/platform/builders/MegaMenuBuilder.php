<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
namespace HelixUltimate\Framework\Platform\Builders;

defined('_JEXEC') or die();

use HelixUltimate\Framework\Platform\Builders\Builder;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Menu\SiteMenu;
use Joomla\Registry\Registry;

/**
 * Helper class for building menu
 *
 * @since	2.0.0
 */
class MegaMenuBuilder extends Builder
{
	/**
	 * Menu Item ID
	 *
	 * @var		int		$itemId 	The menu item ID.
	 * @since	2.0.0
	 */
	protected $itemId = 0;

	/**
	 * Menu Item Params.
	 *
	 * @var		Registry	$params		The menu item params registry data.
	 * @since	2.0.0
	 */
	protected $params = null;

	/**
	 * Constructor function for the builder.
	 *
	 * @param	int		$itemId	The menu type
	 *
	 * @since	2.0.0
	 */
	public function __construct($itemId)
	{
		parent::__construct();

		$this->itemId = $itemId;
		$this->params = new Registry;
		$this->loadMenuItemParams();
	}

	/**
	 * Load the menu item params to the builder.
	 *
	 * @return 	void
	 * @since	2.0.0
	 */
	protected function loadMenuItemParams()
	{
		$menu = new SiteMenu;
		$item = $menu->getItem($this->itemId);
		$this->params = $item->getParams();
	}

	/**
	 * Get mega menu settings from the params.
	 *
	 * @return	stdClass	The mega menu settings object.
	 * @since	2.0.0
	 */
	public function getMegaMenuSettings()
	{
		$megaMenu = $this->params->get('helixultimatemenulayout', new \stdClass);

		if (\is_string($megaMenu))
		{
			$megaMenu = \json_decode($megaMenu);
		}

		return $megaMenu;
	}
}
