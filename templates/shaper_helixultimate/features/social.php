<?php
/**
 * @package Helix3 Framework
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2015 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/
//no direct accees
defined ('_JEXEC') or die('resticted aceess');

class HelixUltimateFeatureSocial {

	private $helixUltimate;
	public $position;

	public function __construct( $helixUltimate ){
		$this->helixUltimate = $helixUltimate;
		$this->position = $this->helixUltimate->getParam('social_position');
		$this->load_pos = $this->helixUltimate->getParam('social_load_pos');
	}

	public function renderFeature() {

		$facebook 	= $this->helixUltimate->getParam('facebook');
		$twitter  	= $this->helixUltimate->getParam('twitter');
		$googleplus = $this->helixUltimate->getParam('googleplus');
		$pinterest 	= $this->helixUltimate->getParam('pinterest');
		$youtube 		= $this->helixUltimate->getParam('youtube');
		$linkedin 	= $this->helixUltimate->getParam('linkedin');
		$dribbble 	= $this->helixUltimate->getParam('dribbble');
		$behance 		= $this->helixUltimate->getParam('behance');
		$skype 			= $this->helixUltimate->getParam('skype');
		$whatsapp 	= $this->helixUltimate->getParam('whatsapp');
		$flickr 		= $this->helixUltimate->getParam('flickr');
		$vk 				= $this->helixUltimate->getParam('vk');
		$custom 		= $this->helixUltimate->getParam('custom');

		if( $this->helixUltimate->getParam('show_social_icons') && ( $facebook || $twitter || $googleplus || $pinterest || $youtube || $linkedin || $dribbble || $behance || $skype || $flickr || $vk ) ) {
			$html  = '<ul class="social-icons">';

			if( $facebook ) {
				$html .= '<li><a target="_blank" href="'. $facebook .'"><i class="fa fa-facebook"></i></a></li>';
			}
			if( $twitter ) {
				$html .= '<li><a target="_blank" href="'. $twitter .'"><i class="fa fa-twitter"></i></a></li>';
			}
			if( $googleplus ) {
				$html .= '<li><a target="_blank" href="'. $googleplus .'"><i class="fa fa-google-plus"></i></a></li>';
			}
			if( $pinterest ) {
				$html .= '<li><a target="_blank" href="'. $pinterest .'"><i class="fa fa-pinterest"></i></a></li>';
			}
			if( $youtube ) {
				$html .= '<li><a target="_blank" href="'. $youtube .'"><i class="fa fa-youtube"></i></a></li>';
			}
			if( $linkedin ) {
				$html .= '<li><a target="_blank" href="'. $linkedin .'"><i class="fa fa-linkedin"></i></a></li>';
			}
			if( $dribbble ) {
				$html .= '<li><a target="_blank" href="'. $dribbble .'"><i class="fa fa-dribbble"></i></a></li>';
			}
			if( $behance ) {
				$html .= '<li><a target="_blank" href="'. $behance .'"><i class="fa fa-behance"></i></a></li>';
			}
			if( $flickr ) {
				$html .= '<li><a target="_blank" href="'. $flickr .'"><i class="fa fa-flickr"></i></a></li>';
			}
			if( $vk ) {
				$html .= '<li><a target="_blank" href="'. $vk .'"><i class="fa fa-vk"></i></a></li>';
			}
			if( $skype ) {
				$html .= '<li><a href="skype:'. $skype .'?chat"><i class="fa fa-skype"></i></a></li>';
			}
			if( $whatsapp ) {
				$html .= '<li><a href="whatsapp://send?abid='. $whatsapp .'&text=Hi"><i class="fa fa-whatsapp"></i></a></li>';
			}
			if( $custom ) {
				$explt_custom = explode(' ', $custom);
				$html .= '<li><a target="_blank" href="'. $explt_custom[1] .'"><i class="fa '. $explt_custom[0] .'"></i></a></li>';
			}

			$html .= '</ul>';

			return $html;
		}

	}
}
