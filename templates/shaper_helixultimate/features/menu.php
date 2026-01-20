<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use HelixUltimate\Framework\Core\Classes\HelixultimateMenu;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Helper\ModuleHelper;
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
	 * Position to load the feature
	 *
	 * @var		string	$position	The position name
	 * @since	1.0.0
	 */
	public $position;

	/**
	 * Load position
	 *
	 * @var		string $load_pos	The load position
	 * @since	1.0.0
	 */
	public $load_pos;

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
		$html = '';

		$predefinedHeader = $this->params->get('predefined_header', 'none');

		if ($offcanvas_position === 'right' && $predefinedHeader === 'none' && $menu_type !== 'mega_offcanvas')
		{
			if ($menu_type === 'mega')
			{
				$html .= '<a id="offcanvas-toggler" aria-label="' . Text::_('HELIX_ULTIMATE_NAVIGATION') . '" class="offcanvas-toggler-right offcanvas-toggler-custom d-lg-none" href="#" title="' . Text::_('HELIX_ULTIMATE_NAVIGATION') . '"><div class="burger-icon" aria-hidden="true"><span></span><span></span><span></span></div></a>';
			}
			else
			{
				$html .= '<a id="offcanvas-toggler" aria-label="' . Text::_('HELIX_ULTIMATE_NAVIGATION') . '" class="offcanvas-toggler-right offcanvas-toggler-custom align-items-center" href="#" title="' . Text::_('HELIX_ULTIMATE_NAVIGATION') . '"><div class="burger-icon" aria-hidden="true"><span></span><span></span><span></span></div></a>';
			}
		}


	    if ($menu_type === 'mega_offcanvas' || $menu_type === 'mega')
	    {
	        $output  = '<nav class="sp-megamenu-wrapper d-flex" role="navigation" aria-label="' . Text::_('HELIX_ULTIMATE_AIRA_NAVIGATION') . '">';
	        $menu    = new HelixultimateMenu('d-none d-lg-block', ''); // desktop only
	        $output .= $menu->render();
			$output .= $html;
	        $output .= '</nav>';
			

	        return $output;
	    }
		
	    return $html;
	}


	/**
	 * Render login/sign in option in header
	 *
	 * @return	string	The login HTML string.
	 * @since	2.0.0
	 */
	public function renderLogin()
	{
		$user = Factory::getApplication()->getIdentity();

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

			$modules= ModuleHelper::getModules('logged-in-usermenu');

			if (!empty($modules)) {
				$html[] = '<li class="custom_user_login_menu">'.ModuleHelper::renderModule($modules[0], ['style' => 'none']).'</li>';
			}

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
