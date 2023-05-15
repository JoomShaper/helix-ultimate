<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use HelixUltimate\Framework\Platform\Helper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
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

		$presetVars = (array) json_decode(Helper::CheckNull($this->params->get('preset')));
		$preset = (isset($presetVars['preset']) && $presetVars['preset']) ? $presetVars['preset'] : 'default';

		$html = '';

		if ($offcanvas_position === 'left')
		{
			if ($menu_type === 'mega')
			{
				$html .= '<a id="offcanvas-toggler" aria-label="' . Text::_('HELIX_ULTIMATE_NAVIGATION') . '" class="offcanvas-toggler-left d-flex d-lg-none" href="#" aria-hidden="true" title="' . Text::_('HELIX_ULTIMATE_NAVIGATION') . '"><div class="burger-icon"><span></span><span></span><span></span></div></a>';
			}
			else
			{
				$html .= '<a id="offcanvas-toggler" aria-label="' . Text::_('HELIX_ULTIMATE_NAVIGATION') . '" class="offcanvas-toggler-left d-flex align-items-center" href="#" aria-hidden="true" title="' . Text::_('HELIX_ULTIMATE_NAVIGATION') . '"><div class="burger-icon"><span></span><span></span><span></span></div></a>';
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
				$logoUrl = $this->params->get('logo_custom_link') ?? Uri::base(true) . '/';

				$html .= '<div class="logo">';
				$html .= '<a href="' . $logoUrl . '">';

				$defaultLogo = $this->params->get('logo_image', null);
				$retinaLogo	= $this->params->get('retina_logo', null);
				$srcset = '';
				if (file_exists($defaultLogo))
				{
					$srcset .= Uri::root() . $defaultLogo . ' 1x';
				}
				if (file_exists($defaultLogo) && (!is_null($retinaLogo) && file_exists($retinaLogo)))
				{
					$srcset .= ', ';
				}
				if (!is_null($retinaLogo) && file_exists($retinaLogo))
				{
					$srcset .= Uri::root() . $retinaLogo . ' 2x';
				}
				$logoWithUrl = Uri::root() . $defaultLogo;
				$attrLogoHeight = $this->params->get('logo_height', '') ?? '0px';
				$siteLogo = "
				<img class='logo-image {$custom_logo_class}'
					srcset='{$srcset}'
					src='{$logoWithUrl}'
					height='{$attrLogoHeight}'
					alt='{$altText}'
				/>
				";

				$html .= $siteLogo;

				if ($this->params->get('mobile_logo'))
				{
					$html .= '<img class="logo-image-phone d-inline-block d-lg-none" src="' .
						Uri::root() .$this->params->get('mobile_logo') . '" alt="' . $altText . '" />';
				}

				$html .= '</a>';

				$html .= '</div>';
			}
			else
			{
				$html .= '<div class="logo">';
				$html .= '<a href="' . Uri::base(true) . '/">';

				$html .= '<img class="logo-image' . $custom_logo_class .
					'" src="' . Uri::base() . 'templates/' .
					$template_name . '/images/presets/' . $preset . '/logo.svg" alt="' . $altText . '" />';

				if ($this->params->get('mobile_logo'))
				{
					$html .= '<img class="logo-image-phone d-inline-block d-lg-none" src="' . $this->params->get('mobile_logo') . '" alt="' . $altText . '" />';
				}

				$html .= '</a>';
				$html .= '</div>';
			}

			if ($logo_height = $this->params->get('logo_height', ''))
			{
				$logo_height = preg_match("@(px|em|rem|%)$@", $logo_height) ? $logo_height : $logo_height . 'px';

				$logoStyle = '.logo-image {height:' . $logo_height . ';}';
				$logoStyle .= '.logo-image-phone {height:' . $logo_height . ';}';

				$doc->addStyleDeclaration($logoStyle);
			}

			/**
			 * If responsive logo height is provided then add the height
			 * to the media query.
			 */
			if ($logo_height_sm = $this->params->get('logo_height_sm', ''))
			{
				$logo_height_sm = preg_match("@(px|em|rem|%)$@", $logo_height_sm) ? $logo_height_sm : $logo_height_sm . 'px';

				$logoStyleSm = '@media(max-width: 992px) {';
				$logoStyleSm .= '.logo-image {height: ' . $logo_height_sm . ';}';
				$logoStyleSm .= '.logo-image-phone {height: ' . $logo_height_sm . ';}';
				$logoStyleSm .= '}';

				$doc->addStyleDeclaration($logoStyleSm);
			}
			
			if ($logo_height_xs = $this->params->get('logo_height_xs', ''))
			{
				$logo_height_xs = preg_match("@(px|em|rem|%)$@", $logo_height_xs) ? $logo_height_xs : $logo_height_xs . 'px';

				$logoStyleXs = '@media(max-width: 576px) {';
				$logoStyleXs .= '.logo-image {height: ' . $logo_height_xs . ';}';
				$logoStyleXs .= '.logo-image-phone {height: ' . $logo_height_xs . ';}';
				$logoStyleXs .= '}';

				$doc->addStyleDeclaration($logoStyleXs);
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
