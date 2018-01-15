<?php
/**
 * @package Helix3 Framework
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2015 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/
//no direct accees
defined ('_JEXEC') or die('resticted aceess');

class HelixUltimateFeatureLogo {

	private $params;

	public function __construct( $params )
	{
		$this->params = $params;
		$this->position = 'logo';
	}

	public function renderFeature()
	{

		$template_name = JFactory::getApplication()->getTemplate();

		//Retina Image
		if( $this->params->get('logo_type') == 'image' ) {
			jimport('joomla.image.image');

			if( $this->params->get('logo_image') ) {
				$path = \JPATH_ROOT . '/' . $this->params->get('logo_image');
			} else {
				$path = \JPATH_ROOT . '/templates/' . $template_name . '/images/presets/' . $this->params->get('preset') . '/logo.png';
			}

			$ext = \JFile::getExt($path);

			if(file_exists($path) && $ext != 'svg') {
				$image = new \JImage( $path );
				$width 	= $image->getWidth();
				$height = $image->getHeight();
			} else {
				$width 	= '';
				$height = '';
			}
		}

		$html  = '';
		$custom_logo_class = '';
		$sitename = \JFactory::getApplication()->get('sitename');

		if( $this->params->get('mobile_logo') ) {
			$custom_logo_class = ' hidden-xs';
		}

		if( $this->params->get('logo_type') == 'image' ) {
			if( $this->params->get('logo_image') ) {
				$html .= '<div class="logo">';
				$html .= '<a href="' . \JURI::base(true) . '/">';

					if($ext != 'svg') {
						if( $this->params->get('logo_image_2x') ) {
							$html .= '<img class="sp-normal-logo'. $custom_logo_class .'" src="' . $this->params->get('logo_image') . '" alt="'. $sitename .'">';
							$html .= '<img class="sp-retina-logo'. $custom_logo_class .'" src="' . $this->params->get('logo_image_2x') . '" alt="'. $sitename .'" width="' . $width . '" height="' . $height . '">';
						} else {
							$html .= '<img class="sp-default-logo'. $custom_logo_class .'" src="' . $this->params->get('logo_image') . '" alt="'. $sitename .'">';
						}
					} else {
						$html .= '<img class="sp-default-logo sp-logo-svg'. $custom_logo_class .'" src="' . $this->params->get('logo_image') . '" alt="'. $sitename .'">';
					}

					if( $this->params->get('mobile_logo') ) {
						$html .= '<img class="sp-default-logo visible-xs" src="' . $this->params->get('mobile_logo') . '" alt="'. $sitename .'">';
					}

				$html .= '</a>';

				$html .= '</div>';
			} else {
				$html .= '<div class="logo">';
					$html .= '<a href="' . \JURI::base(true) . '/">';

						$html .= '<img class="sp-default-logo sp-logo-svg'. $custom_logo_class .'" src="' . JURI::base(true) . '/templates/'. $template_name . '/images/presets/' . $this->params->get('preset') . '/logo.svg" alt="'. $sitename .'">';

						if( $this->params->get('mobile_logo') ) {
							$html .= '<img class="sp-default-logo visible-xs" src="' . $this->params->get('mobile_logo') . '" alt="'. $sitename .'">';
						}
					$html .= '</a>';
				$html .= '</div>';
			}

		} else {
			if( $this->params->get('logo_text') ) {
				$html .= '<span class="logo"><a href="' . \JURI::base(true) . '/">' . $this->params->get('logo_text') . '</a></span>';
			} else {
				$html .= '<span class="logo"><a href="' . \JURI::base(true) . '/">' . $sitename . '</a></span>';
			}

			if( $this->params->get('logo_slogan') ) {
				$html .= '<span class="logo-slogan">' . $this->params->get('logo_slogan') . '</span>';
			}
		}

		return $html;
	}

}
