<?php
/**
 * @package Helix3 Framework
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2015 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/
//no direct accees
defined ('_JEXEC') or die('resticted aceess');

class HelixUltimateFeatureFooter {

	private $helixUltimate;

	public function __construct($helixUltimate){
		$this->helixUltimate = $helixUltimate;
		$this->position = $this->helixUltimate->getParam('copyright_position');
		$this->load_pos = $this->helixUltimate->getParam('copyright_load_pos');
	}

	public function renderFeature() {

		if($this->helixUltimate->getParam('enabled_copyright')) {
			$output = '';
			//Copyright
			if( $this->helixUltimate->getParam('copyright') ) {
				$output .= '<span class="sp-copyright">' . str_ireplace('{year}',date('Y'), $this->helixUltimate->getParam('copyright')) . '</span>';
			}

			return $output;
		}

	}
}
