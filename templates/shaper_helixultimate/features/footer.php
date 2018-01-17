<?php
/**
 * @package Helix3 Framework
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2015 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die('Restricted access');

class HelixUltimateFeatureFooter {

	private $params;

	public function __construct($params){
		$this->params = $params;
		$this->position = $this->params->get('copyright_position');
		$this->load_pos = $this->params->get('copyright_load_pos');
	}

	public function renderFeature() {

		if($this->params->get('enabled_copyright')) {
			$output = '';
			//Copyright
			if( $this->params->get('copyright') ) {
				$output .= '<span class="sp-copyright">' . str_ireplace('{year}',date('Y'), $this->params->get('copyright')) . '</span>';
			}

			return $output;
		}

	}
}
