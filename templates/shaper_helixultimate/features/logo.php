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

	private $helixUltimate;
	public $position;

	public function __construct( $helixUltimate ){
		$this->helixUltimate = $helixUltimate;
		$this->position = $this->helixUltimate->getParam('logo_position', 'logo');
		$this->load_pos = $this->helixUltimate->getParam('logo_load_pos');
	}

	public function renderFeature() {

		//Retina Image
		if( $this->helixUltimate->getParam('logo_type') == 'image' ) {
			jimport('joomla.image.image');

			if( $this->helixUltimate->getParam('logo_image') ) {
				$path = JPATH_ROOT . '/' . $this->helixUltimate->getParam('logo_image');
			} else {
				$path = JPATH_ROOT . '/templates/' . $this->helixUltimate->getTemplate() . '/images/presets/' . $this->helixUltimate->Preset() . '/logo.png';
			}

			if(file_exists($path)) {
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

		if( $this->helixUltimate->getParam('mobile_logo') ) {
			$custom_logo_class = ' hidden-xs';
		}

		if( $this->helixUltimate->getParam('logo_type') == 'image' ) {
			if( $this->helixUltimate->getParam('logo_image') ) {
				$html .= '<div class="logo">';
				$html .= '<a href="' . \JURI::base(true) . '/">';
					$html .= '<img class="sp-default-logo'. $custom_logo_class .'" src="' . $this->helixUltimate->getParam('logo_image') . '" alt="'. $sitename .'">';
					if( $this->helixUltimate->getParam('logo_image_2x') ) {
						$html .= '<img class="sp-retina-logo'. $custom_logo_class .'" src="' . $this->helixUltimate->getParam('logo_image_2x') . '" alt="'. $sitename .'" width="' . $width . '" height="' . $height . '">';
					}

					if( $this->helixUltimate->getParam('mobile_logo') ) {
						$html .= '<img class="sp-default-logo visible-xs" src="' . $this->helixUltimate->getParam('mobile_logo') . '" alt="'. $sitename .'">';
					}

				$html .= '</a>';

				$html .= '</div>';
			} else {
				$html .= '<div class="logo">';
					$html .= '<a href="' . \JURI::base(true) . '/">';
						$html .= '<img class="sp-default-logo'. $custom_logo_class .'" src="' . $this->helixUltimate->getTemplateUri() . '/images/presets/' . $this->helixUltimate->Preset() . '/logo.png" alt="'. $sitename .'">';
						$html .= '<img class="sp-retina-logo'. $custom_logo_class .'" src="' . $this->helixUltimate->getTemplateUri() . '/images/presets/' . $this->helixUltimate->Preset() . '/logo@2x.png" alt="'. $sitename .'" width="' . $width . '" height="' . $height . '">';

						if( $this->helixUltimate->getParam('mobile_logo') ) {
							$html .= '<img class="sp-default-logo visible-xs" src="' . $this->helixUltimate->getParam('mobile_logo') . '" alt="'. $sitename .'">';
						}
					$html .= '</a>';
				$html .= '</div>';
			}

		} else {
			if( $this->helixUltimate->getParam('logo_text') ) {
				$html .= '<h1 class="logo"> <a href="' . \JURI::base(true) . '/">' . $this->helixUltimate->getParam('logo_text') . '</a></h1>';
			} else {
				$html .= '<h1 class="logo"> <a href="' . \JURI::base(true) . '/">' . $sitename . '</a></h1>';
			}

			if( $this->helixUltimate->getParam('logo_slogan') ) {
				$html .= '<p class="logo-slogan">' . $this->helixUltimate->getParam('logo_slogan') . '</p>';
			}
		}

		return $html;
	}

}
