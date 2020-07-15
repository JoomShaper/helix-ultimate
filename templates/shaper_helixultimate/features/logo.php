<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2020 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

/**
 * Helix Ultimate Site Logo.
 *
 * @since	1.0.0
 */
class HelixUltimateFeatureLogo
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
		$this->position = 'logo';
		$this->load_pos = $this->params->get('logo_load_pos', 'default');
	}

	/**
	 * Render the logo features.
	 *
	 * @return	string
	 * @since	1.0.0
	 */
	public function renderFeature()
	{

		$template_name = HelixUltimate\Framework\Platform\Helper::loadTemplateData()->template;

		$menu_type = $this->params->get('menu_type');
		$offcanvas_position = $this->params->get('offcanvas_position', 'right');

		$doc = Factory::getDocument();

		$presetVars = (array) json_decode($this->params->get('preset'));
		$preset = (isset($presetVars['preset']) && $presetVars['preset']) ? $presetVars['preset'] : 'default';

		if ($this->params->get('logo_type') === 'image')
		{
			if ($this->params->get('logo_image'))
			{
				$path = \JPATH_ROOT . '/' . $this->params->get('logo_image');
			}
			else
			{
				$path = \JPATH_ROOT . '/templates/' . $template_name . '/images/presets/' . $preset . '/logo.svg';
			}
		}

		$html = '';

		if ($offcanvas_position === 'left')
		{
			if ($menu_type === 'mega')
			{
				$html .= '<a id="offcanvas-toggler" aria-label="' . JText::_('HELIX_ULTIMATE_NAVIGATION') . '" class="offcanvas-toggler-left d-block d-lg-none" href="#"><span class="fas fa-bars" aria-hidden="true" title="' . JText::_('HELIX_ULTIMATE_NAVIGATION') . '"></span></a>';
			}
			else
			{
				$html .= '<a id="offcanvas-toggler" aria-label="' . JText::_('HELIX_ULTIMATE_NAVIGATION') . '" class="offcanvas-toggler-left" href="#"><span class="fas fa-bars" aria-hidden="true" title="' . JText::_('HELIX_ULTIMATE_NAVIGATION') . '"></span></a>';
			}
		}

		$custom_logo_class = '';
		$sitename = Factory::getApplication()->get('sitename');

		if ($this->params->get('mobile_logo'))
		{
			$custom_logo_class = ' d-none d-lg-inline-block';
		}

		if ($this->params->get('logo_type') === 'image')
		{
			$altText = $this->params->get('logo_alt', $sitename);	

			if ($this->params->get('logo_image'))
			{
				$html .= '<div class="logo">';
				$html .= '<a href="' . Uri::base(true) . '/">';

				$defaultLogo = $this->params->get('logo_image', null);
				$retinaLogo	= $this->params->get('retina_logo', null);
				$srcset = '';

				if (file_exists($defaultLogo))
				{
					$srcset .= Uri::root() . $defaultLogo . ' 1x, ';
				}

				if (!is_null($retinaLogo) && file_exists($retinaLogo))
				{
					$srcset .= Uri::root() . $retinaLogo . ' 2x';
				}

				$siteLogo = "
				<img class='logo-image {$custom_logo_class}'
					srcset='{$srcset}'
					src='{$defaultLogo}'
					alt='{$altText}'
				/>
				";

				$html .= $siteLogo;

				if ($this->params->get('mobile_logo'))
				{
					$html .= '<img class="logo-image-phone d-inline-block d-lg-none" src="' .
						$this->params->get('mobile_logo') . '" alt="' . $altText . '" />';
				}

				$html .= '</a>';

				$html .= '</div>';
			}
			else
			{
				$html .= '<div class="logo">';
				$html .= '<a href="' . Uri::base(true) . '/">';

				$html .= '<img class="logo-image' . $custom_logo_class .
					'" src="' . Uri::base(true) . '/templates/' .
					$template_name . '/images/presets/' . $preset . '/logo.svg" alt="' . $altText . '" />';

				if ($this->params->get('mobile_logo'))
				{
					$html .= '<img class="logo-image-phone d-inline-block d-lg-none" src="' . $this->params->get('mobile_logo') . '" alt="' . $altText . '" />';
				}

				$html .= '</a>';
				$html .= '</div>';
			}

			if ($logo_height = $this->params->get('logo_height'))
			{
				$logoStyle = '.logo-image {height:' . $logo_height . 'px;}';
				$logoStyle .= '.logo-image-phone {height:' . $logo_height . 'px;}';

				$doc->addStyleDeclaration($logoStyle);
			}
		}
		else
		{
			if ($this->params->get('logo_text'))
			{
				$html .= '<span class="logo"><a href="' . Uri::base(true) . '/">' . $this->params->get('logo_text') . '</a></span>';
			}
			else
			{
				$html .= '<span class="logo"><a href="' . Uri::base(true) . '/">' . $sitename . '</a></span>';
			}

			if ($this->params->get('logo_slogan'))
			{
				$html .= '<span class="logo-slogan">' . $this->params->get('logo_slogan') . '</span>';
			}
		}

		return $html;
	}
}
