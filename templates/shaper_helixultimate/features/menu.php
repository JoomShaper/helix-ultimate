<?php
/**
 * @package Helix3 Framework
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2015 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/
//no direct accees
defined ('_JEXEC') or die('resticted aceess');

require_once JPATH_PLUGINS. '/system/helixultimate/core/classes/menu.php';

class HelixUltimateFeatureMenu {

	private $params;

	public function __construct($params){
		$this->params = $params;
		$this->position = 'menu';
	}

	public function renderFeature() {

		$menu_type = $this->params->get('menu_type');

		ob_start();

		if($menu_type == 'mega_offcanvas') {  ?>
			<div class='sp-megamenu-wrapper'>
				<a id="offcanvas-toggler" href="#"><i class="fa fa-bars"></i></a>
				<?php new Helix3Menu('hidden-sm hidden-xs',''); ?>
			</div>
		<?php } else if ($menu_type == 'mega') { die('Mega Menu'); ?>
			<div class='sp-megamenu-wrapper'>
				<a id="offcanvas-toggler" class="visible-sm visible-xs" href="#"><i class="fa fa-bars"></i></a>
				<?php new Helix3Menu('hidden-sm hidden-xs',''); ?>
			</div>
		<?php } else { ?>
			<a id="offcanvas-toggler" href="#"><i class="fa fa-bars"></i></a>
		<?php }

		return ob_get_clean();
	}
}

