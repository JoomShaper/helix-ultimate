<?php
/**
 * @package Helix3 Framework
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2014 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

//no direct accees
defined ('_JEXEC') or die('resticted aceess');

class HelixUltimateFeaturePreloader {

	private $helixUltimate;

	public function __construct($helixUltimate){
		$this->helixUltimate = $helixUltimate;
		$this->position = 'helixpreloader';
	}

	public function renderFeature() {

		$app = JFactory::getApplication();

		$output = '';
		if ($this->helixUltimate->getParam('preloader')) {
           	//Pre-loader -->
            $output .= '<div class="sp-pre-loader">';
                if ($this->helixUltimate->getParam('preloader_animation') == 'double-loop') {
                    // Bubble Loop loader
                    $output .= '<div class="sp-loader-bubble-loop"></div>';
                } elseif ($this->helixUltimate->getParam('preloader_animation') == 'wave-two') {
                    // Audio Wave 2 loader
                    $output .= '<div class="wave-two-wrap">';
                        $output .= '<ul class="wave-two">';
                            $output .= '<li></li>';
                            $output .= '<li></li>';
                            $output .= '<li></li>';
                            $output .= '<li></li>';
                            $output .= '<li></li>';
                            $output .= '<li></li>';
                        $output .= '</ul>'; //<!-- /.Audio Wave 2 loader -->
                    $output .= '</div> >'; // <!-- /.wave-two-wrap -->

                } elseif ($this->helixUltimate->getParam('preloader_animation') == 'audio-wave') {
                    // Audio Wave loader
                    $output .= '<div class="sp-loader-audio-wave"> </div>';
                } elseif ($this->helixUltimate->getParam('preloader_animation') == 'circle-two') {
                    // Circle two Loader
                    $output .= '<div class="circle-two">';
                        $output .= '<span></span>';
                    $output .= '</div>'; // /.Circle two loader
                } elseif ($this->helixUltimate->getParam('preloader_animation') == 'clock') {
                    //Clock loader
                    $output .= '<div class="sp-loader-clock"></div>';
                } elseif ($this->helixUltimate->getParam('preloader_animation') == 'logo') {

                    if ($this->helixUltimate->getParam('logo_image')) {
                        $logo = JUri::root() . '/' . $this->helixUltimate->getParam('logo_image');
                    } else {
                        $logo = JUri::root() . '/templates/' . $app->getTemplate() . '/images/presets/' . $this->helixUltimate->Preset() . '/logo.png';
                    }

                    // Line loader with logo
                    $output .= '<div class="sp-loader-with-logo">';
                        $output .= '<div class="logo">';
                            $output .= '<img src="' . $logo . '" alt="">';
                        $output .= '</div>';
                        $output .= '<div class="line" id="line-load"></div>';
                    $output .= '</div>'; // /.Line loader with logo

                } else {
                    // Circle loader
                    $output .= '<div class="sp-loader-circle"></div>'; // /.Circular loader
                }
            $output .= '</div>'; // /.Pre-loader

        } // if enable preloader

        echo $output;
	} //renderFeature
}
