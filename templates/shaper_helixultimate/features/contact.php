<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

/**
 * Helix Ultimate contact information.
 *
 * @since	1.0.0
 */
class HelixUltimateFeatureContact
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
		$this->position = $this->params->get('contact_position', 'top1');
		$this->load_pos = $this->params->get('social_load_pos', 'default');
	}

	/**
	 * Render the contact features
	 *
	 * @return	string
	 * @since	1.0.0
	 */
	public function renderFeature()
	{
		$conditions = $this->params->get('contactinfo') && ($this->params->get('contact_phone') || $this->params->get('contact_mobile') || $this->params->get('contact_email') || $this->params->get('contact_time'));

		if($conditions)
		{
			$output = '<ul class="sp-contact-info">';

			if($this->params->get('contact_phone'))
			{
				$output .= '<li class="sp-contact-phone"><span class="fas fa-phone" aria-hidden="true"></span> <a href="tel:' . str_replace(array(')', '(', ' ', '-'), array('', '', '', ''), $this->params->get('contact_phone')) . '">' . $this->params->get('contact_phone') . '</a></li>';
			}

			if($this->params->get('contact_mobile'))
			{
				$output .= '<li class="sp-contact-mobile"><span class="fas fa-mobile-alt" aria-hidden="true"></span> <a href="tel:' . str_replace(array(')', '(', ' ', '-'), array('', '', '', ''), $this->params->get('contact_mobile')) . '">' . $this->params->get('contact_mobile') . '</a></li>';
			}

			if($this->params->get('contact_email'))
			{
				$output .= '<li class="sp-contact-email"><span class="far fa-envelope" aria-hidden="true"></span> <a href="mailto:'. $this->params->get('contact_email') .'">' . $this->params->get('contact_email') . '</a></li>';
			}

			if($this->params->get('contact_time'))
			{
				$output .= '<li class="sp-contact-time"><span class="far fa-clock" aria-hidden="true"></span> ' . $this->params->get('contact_time') . '</li>';
			}

			$output .= '</ul>';

			return $output;
		}
	}
}
