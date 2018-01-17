<?php
/**
 * @package Helix3 Framework
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2014 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die('Restricted access');

class HelixUltimateFeatureContact {

	private $params;

	public function __construct($params){
		$this->params = $params;
		$this->position = $this->params->get('contact_position');
	}

	public function renderFeature() {

		if($this->params->get('enable_contactinfo')) {

			$output = '<ul class="sp-contact-info">';
			if($this->params->get('contact_phone')) $output .= '<li class="sp-contact-phone"><i class="fa fa-phone"></i> <a href="tel:' . str_replace(' ', '', $this->params->get('contact_phone')) . '">' . $this->params->get('contact_phone') . '</a></li>';
			if($this->params->get('contact_mobile')) $output .= '<li class="sp-contact-mobile"><i class="fa fa-mobile"></i> <a href="tel:'. str_replace(' ', '', $this->params->get('contact_mobile')) .'">' . $this->params->get('contact_mobile') . '</a></li>';
			if($this->params->get('contact_email')) $output .= '<li class="sp-contact-email"><i class="fa fa-envelope"></i> <a href="mailto:'. $this->params->get('contact_email') .'">' . $this->params->get('contact_email') . '</a></li>';
			if($this->params->get('contact_time')) $output .= '<li class="sp-contact-time"><i class="fa fa-clock-o"></i>' . $this->params->get('contact_time') . '</li>';
			$output .= '</ul>';

			return $output;
		}

	}
}
