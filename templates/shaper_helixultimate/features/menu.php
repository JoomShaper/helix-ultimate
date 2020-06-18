<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2020 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use HelixUltimate\Framework\Core\Classes\HelixultimateMenu;

/**
 * Helix Ultimate Menu class
 *
 * @since	1.0.0
 */
class HelixUltimateFeatureMenu
{
	/**
	 * Template parameters
	 *
	 * @var		object	$params		The parameters object
	 * @since	1.0.0
	 */
	private $params;

	/**
	 * Constructor function
	 *
	 * @param	object	$params		The template parameters
	 *
	 * @since	1.0.0
	 */
	public function __construct($params)
	{
		$this->params = $params;
		$this->position = 'menu';
		$this->load_pos = $this->params->get('menu_load_pos', 'default');
	}

	/**
	 * Render the menu features
	 *
	 * @return	string
	 * @since	1.0.0
	 */
	public function renderFeature()
	{

		$menu_type = $this->params->get('menu_type');
		$offcanvas_position = $this->params->get('offcanvas_position', 'right');

		$output = '';

		if ($menu_type === 'mega_offcanvas')
		{
			$output .= '<nav class="sp-megamenu-wrapper">';

			if($offcanvas_position === 'right')
			{
				$output .= '<a id="offcanvas-toggler" aria-label="' . JText::_('HELIX_ULTIMATE_NAVIGATION') . '" class="offcanvas-toggler-right" href="#"><i class="fas fa-bars" aria-hidden="true" title="' . JText::_('HELIX_ULTIMATE_NAVIGATION') . '"></i></a>';
			}

			$menu = new HelixultimateMenu('d-none d-lg-block', '');
			$output .= $menu->render();
			$output .= '</nav>';
		}
		elseif ($menu_type === 'mega')
		{
			$output .= '<nav class="sp-megamenu-wrapper">';

			if ($offcanvas_position === 'right')
			{
				$output .= '<a id="offcanvas-toggler" aria-label="' . JText::_('HELIX_ULTIMATE_NAVIGATION') . '" class="offcanvas-toggler-right d-block d-lg-none" href="#"><i class="fas fa-bars" aria-hidden="true" title="' . JText::_('HELIX_ULTIMATE_NAVIGATION') . '"></i></a>';
			}

			$menu = new HelixultimateMenu('d-none d-lg-block', '');
			$output .= $menu->render();
			$output .= '</nav>';
		}
		else
		{
			if($offcanvas_position === 'right')
			{
				$output .= '<a id="offcanvas-toggler" aria-label="' . JText::_('HELIX_ULTIMATE_NAVIGATION') . '" class="offcanvas-toggler-right" href="#"><i class="fas fa-bars" aria-hidden="true" title="' . JText::_('HELIX_ULTIMATE_NAVIGATION') . '"></i></a>';
			}
		}

		return $output;

	}
}
