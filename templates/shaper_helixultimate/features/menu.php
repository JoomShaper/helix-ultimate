<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die('Restricted access');

require_once JPATH_PLUGINS. '/system/helixultimate/core/classes/menu.php';

class HelixUltimateFeatureMenu
{

	private $params;

	public function __construct($params)
	{
		$this->params = $params;
		$this->position = 'menu';
	}

	public function renderFeature()
	{

		$menu_type = $this->params->get('menu_type');

		ob_start();

		if($menu_type == 'mega_offcanvas') {  ?>
			<div class='sp-megamenu-wrapper'>
				<a id="offcanvas-toggler" href="#"><span class="fa fa-bars"></span></a>
				<?php new Helix3Menu('d-none d-md-block',''); ?>
			</div>
		<?php } else if ($menu_type == 'mega') { ?>
			<div class='sp-megamenu-wrapper'>
				<a id="offcanvas-toggler" class="visible-sm visible-xs" href="#"><span class="fa fa-bars"></span></a>
				<?php new Helix3Menu('hidden-sm hidden-xs',''); ?>
			</div>
		<?php } else { ?>
			<a id="offcanvas-toggler" href="#"><span class="fa fa-bars"></span></a>
		<?php }

		return ob_get_clean();
	}
}
