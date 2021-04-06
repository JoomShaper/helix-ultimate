<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die('Restricted Access');

use HelixUltimate\Framework\Core\HelixUltimate as BaseHelixUltimate;
use HelixUltimate\Framework\Platform\Helper;
use Joomla\CMS\Log\Log;

/**
 * Extends the Helix Ultimate for legacy support.
 *
 * @since		2.0.0
 * @deprecated 	3.0		Instead of using HelixUltimate from helixultimate/core/helixultimate.php
 * 						Use from HelixUltimate\Framework\Core\HelixUltimate namespace.
 */
class HelixUltimate extends BaseHelixUltimate
{
	/**
	 * Constructor function for the legacy helixultimate.
	 *
	 * @since	2.0.0
	 */
	public function __construct()
	{
		Log::add(
			sprintf('/plugins/system/helixultimate/core/%s is deprecated. Use from the namespace HelixUltimate\Framework\Core\HelixUltimate instead.', __CLASS__),
			Log::WARNING,
			'deprecated'
		);

		parent::__construct();
		Helper::flushSettingsDataToJs();
	}
}