<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
use HelixUltimate\Framework\Core\Classes\HelixultimateMenu as BaseHelixUltimateMenu;
use Joomla\CMS\Log\Log;

defined('_JEXEC') or die();

/**
 * HelixUltimate menu for legacy support.
 *
 * @since   	2.0.0
 * @deprecated 	3.0.0 	Instead of using this class by requiring directly from index.php or other files,
 * 						use from the BaseHelixUltimateMenu directly.
 * 						@see templates/{template}/index.php file for reference.
 */
class HelixultimateMenu extends BaseHelixUltimateMenu
{
	/**
	 * Constructor class.
	 *
	 * @param	string	$class	Classes.
	 * @param	string	$name	Name attribute
	 *
	 * @return	void
	 * @since	2.0.0
	 */
	public function __construct($class = '', $name = '')
	{
		Log::add(
			sprintf('/plugins/system/helixultimate/core/classes/%s is deprecated. Use HelixUltimate\Framework\Core\Classes\HelixultimateMenu instead.', __CLASS__),
			Log::WARNING,
			'deprecated'
		);

		parent::__construct($class, $name);
	}
}
