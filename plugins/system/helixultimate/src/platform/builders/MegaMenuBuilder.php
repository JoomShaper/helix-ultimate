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

/**
 * Helper class for building menu
 *
 * @since	2.0.0
 */
class MegaMenuBuilder extends Builder
{
	/**
	 * Constructor function for the builder.
	 *
	 * @param	string	$menuType	The menu type
	 *
	 * @since	2.0.0
	 */
	public function __construct()
	{
		parent::__construct();
	}
}
