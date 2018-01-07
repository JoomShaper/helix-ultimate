<?php
/**
 * @package Helix3 Framework
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2014 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/
//no direct accees
defined ('_JEXEC') or die('resticted aceess');

class HelixUltimateFeatureContact {

	private $helixUltimate;

	public function __construct($helixUltimate){
		$this->helixUltimate = $helixUltimate;
		$this->position = $this->helixUltimate->getParam('contact_position');
	}

	public function renderFeature() {

		if($this->helixUltimate->getParam('enable_contactinfo')) {

			$output = '<ul class="sp-contact-info">';
			if($this->helixUltimate->getParam('contact_phone')) $output .= '<li class="sp-contact-phone"><i class="fa fa-phone"></i> <a href="tel:' . str_replace(' ', '', $this->helixUltimate->getParam('contact_phone')) . '">' . $this->helixUltimate->getParam('contact_phone') . '</a></li>';
			if($this->helixUltimate->getParam('contact_mobile')) $output .= '<li class="sp-contact-mobile"><i class="fa fa-mobile"></i> <a href="tel:'. str_replace(' ', '', $this->helixUltimate->getParam('contact_mobile')) .'">' . $this->helixUltimate->getParam('contact_mobile') . '</a></li>';
			if($this->helixUltimate->getParam('contact_email')) $output .= '<li class="sp-contact-email"><i class="fa fa-envelope"></i> <a href="mailto:'. $this->helixUltimate->getParam('contact_email') .'">' . $this->helixUltimate->getParam('contact_email') . '</a></li>';
			if($this->helixUltimate->getParam('contact_time')) $output .= '<li class="sp-contact-time"><i class="fa fa-clock-o"></i>' . $this->helixUltimate->getParam('contact_time') . '</li>';
			$output .= '</ul>';

			return $output;
		}

	}
}
