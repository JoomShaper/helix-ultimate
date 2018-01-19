<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die('Restricted access');

class HelixUltimateFeatureSocial
{

	private $params;

	public function __construct( $params )
	{
		$this->params = $params;
		$this->position = $this->params->get('social_position');
		$this->load_pos = $this->params->get('social_position');
	}

	public function renderFeature()
	{

		$facebook = $this->params->get('facebook');
		$twitter = $this->params->get('twitter');
		$googleplus = $this->params->get('googleplus');
		$pinterest = $this->params->get('pinterest');
		$youtube = $this->params->get('youtube');
		$linkedin = $this->params->get('linkedin');
		$dribbble = $this->params->get('dribbble');
		$behance = $this->params->get('behance');
		$skype = $this->params->get('skype');
		$whatsapp = $this->params->get('whatsapp');
		$flickr = $this->params->get('flickr');
		$vk = $this->params->get('vk');
		$custom = $this->params->get('custom');

		if( $this->params->get('show_social_icons') && ( $facebook || $twitter || $googleplus || $pinterest || $youtube || $linkedin || $dribbble || $behance || $skype || $flickr || $vk ) ) {
			$html  = '<ul class="social-icons">';

			if( $facebook ) {
				$html .= '<li><a target="_blank" href="'. $facebook .'"><span class="fa fa-facebook"></span></a></li>';
			}
			if( $twitter ) {
				$html .= '<li><a target="_blank" href="'. $twitter .'"><span class="fa fa-twitter"></span></a></li>';
			}
			if( $googleplus ) {
				$html .= '<li><a target="_blank" href="'. $googleplus .'"><span class="fa fa-google-plus"></span></a></li>';
			}
			if( $pinterest ) {
				$html .= '<li><a target="_blank" href="'. $pinterest .'"><span class="fa fa-pinterest"></span></a></li>';
			}
			if( $youtube ) {
				$html .= '<li><a target="_blank" href="'. $youtube .'"><span class="fa fa-youtube"></span></a></li>';
			}
			if( $linkedin ) {
				$html .= '<li><a target="_blank" href="'. $linkedin .'"><span class="fa fa-linkedin"></span></a></li>';
			}
			if( $dribbble ) {
				$html .= '<li><a target="_blank" href="'. $dribbble .'"><span class="fa fa-dribbble"></span></a></li>';
			}
			if( $behance ) {
				$html .= '<li><a target="_blank" href="'. $behance .'"><span class="fa fa-behance"></span></a></li>';
			}
			if( $flickr ) {
				$html .= '<li><a target="_blank" href="'. $flickr .'"><span class="fa fa-flickr"></span></a></li>';
			}
			if( $vk ) {
				$html .= '<li><a target="_blank" href="'. $vk .'"><span class="fa fa-vk"></span></a></li>';
			}
			if( $skype ) {
				$html .= '<li><a href="skype:'. $skype .'?chat"><span class="fa fa-skype"></span></a></li>';
			}
			if( $whatsapp ) {
				$html .= '<li><a href="whatsapp://send?abid='. $whatsapp .'&text=Hi"><span class="fa fa-whatsapp"></span></a></li>';
			}
			if( $custom ) {
				$explt_custom = explode(' ', $custom);
				$html .= '<li><a target="_blank" href="'. $explt_custom[1] .'"><span class="fa '. $explt_custom[0] .'"></span></a></li>';
			}

			$html .= '</ul>';

			return $html;
		}

	}
}
