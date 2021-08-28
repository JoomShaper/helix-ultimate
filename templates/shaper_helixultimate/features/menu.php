<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use HelixUltimate\Framework\Core\Classes\HelixultimateMenu;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

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
			$output .= '<nav class="sp-megamenu-wrapper d-flex" role="' . Text::_('HELIX_ULTIMATE_AIRA_NAVIGATION') . '">';
			$menu = new HelixultimateMenu('d-none d-lg-block', '');
			$output .= $menu->render();
			
			if($offcanvas_position === 'right')
			{
				$output .= '<a id="offcanvas-toggler" aria-label="' . Text::_('HELIX_ULTIMATE_NAVIGATION') . '" class="offcanvas-toggler-right" href="#"><div class="burger-icon" aria-hidden="true"><span></span><span></span><span></span></div></a>';
			}
			$output .= '</nav>';
		}
		elseif ($menu_type === 'mega')
		{
			$output .= '<nav class="sp-megamenu-wrapper d-flex" role="' . Text::_('HELIX_ULTIMATE_AIRA_NAVIGATION') . '">';

			if ($offcanvas_position === 'right')
			{
				$output .= '<a id="offcanvas-toggler" aria-label="' . Text::_('HELIX_ULTIMATE_NAVIGATION') . '" class="offcanvas-toggler-right d-flex d-lg-none" href="#"><div class="burger-icon" aria-hidden="true"><span></span><span></span><span></span></div></a>';
			}

			$menu = new HelixultimateMenu('d-none d-lg-block', '');
			$output .= $menu->render();
			$output .= '</nav>';
		}
		else
		{
			if($offcanvas_position === 'right')
			{
				$output .= '<a id="offcanvas-toggler" aria-label="' . Text::_('HELIX_ULTIMATE_NAVIGATION') . '"  class="offcanvas-toggler-right" href="#"><div class="burger-icon" aria-hidden="true"><span></span><span></span><span></span></div></a>';
			}
		}

		return $output;

	}

	/**
	 * Render login/sign in option in header
	 *
	 * @return	string	The login HTML string.
	 * @since	2.0.0
	 */
	public function renderLogin()
	{
		$user = Factory::getUser();

		$html = [];
		$html[] = '<div class="sp-module">';

		if ($user->id === 0)
		{
			$html[] = '<a class="sp-sign-in" href="' . Route::_('index.php?option=com_users&view=login') . '" ><span class="far fa-user me-1" aria-hidden="true"></span><span class="signin-text d-none d-lg-inline-block">' . Text::_('HELIX_ULTIMATE_SIGN_IN_MENU') . '</span></a>';
		}
		else
		{
			$html[] = '<div class="sp-profile-wrapper">';
			$html[] = '<a href="#" class="sp-sign-in"><i class="fas fa-user-circle" aria-hidden="true"></i> <span class="user-text d-none d-xl-inline-block"> ' . ($user->name ?? '') . '</span> <i class="fas fa-chevron-down arrow-icon" aria-hidden="true"></i></a>';
			$html[] = '<ul class="sp-profile-dropdown">';
			$html[] = '	<li class="sp-profile-dropdown-item">';
			$html[] = '		<a href="' . Route::_('index.php?option=com_users&view=profile') . '">' . Text::_('HELIX_ULTIMATE_USER_PROFILE') . '</a>';
			$html[] = '	</li>';
			$html[] = '	<li class="sp-profile-dropdown-item">';
			$html[] = '		<a href="' . Route::_('index.php?option=com_users&view=login&layout=logout') . '">' . Text::_('HELIX_ULTIMATE_USER_LOGOUT') . '</a>';
			$html[] = '	</li>';
			$html[] = '</ul>';
			$html[] = '</div>';
		}

		$html[] = '</div>';

		return implode("\n", $html);
	}
}
