<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die();

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
		$offcanvs_position = $this->params->get('offcanvas_position', 'right');

		$output = '';

		if($menu_type == 'mega_offcanvas')
		{
			$output .= '<div class="sp-megamenu-wrapper">';
			if($offcanvs_position == 'right') 
			{
				$output .= '<a id="offcanvas-toggler" class="offcanvas-toggler-right" href="#"><span class="fa fa-bars"></span></a>';
			}
			$menu = new HelixUltimateMenu('d-none d-lg-block','');
			$output .= $menu->render();
			$output .= '</div>';
		}
		elseif ($menu_type == 'mega')
		{
			$output .= '<div class="sp-megamenu-wrapper">';
			if($offcanvs_position == 'right') 
			{
				$output .= '<a id="offcanvas-toggler" class="offcanvas-toggler-right d-block d-lg-none" href="#"><span class="fa fa-bars"></span></a>';
			}
			$menu = new HelixUltimateMenu('d-none d-lg-block','');
			$output .= $menu->render();
			$output .= '</div>';
		} else {
			if($offcanvs_position == 'right') 
			{
				$output .= '<a id="offcanvas-toggler" class="offcanvas-toggler-right" href="#"><span class="fa fa-bars"></span></a>';
			}
		}

		return $output;

	}
}
