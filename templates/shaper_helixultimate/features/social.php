<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

/**
 * Helix Ultimate social media information.
 *
 * @since	1.0.0
 */
class HelixUltimateFeatureSocial
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
		$this->position = $this->params->get('social_position');
		$this->load_pos = $this->params->get('social_load_pos', 'default');
	}

	/**
	 * Render the social media features
	 *
	 * @return	string
	 * @since	1.0.0
	 */
	public function renderFeature()
	{
		$socials = array(
			'facebook' 	=> $this->params->get('facebook'),
			'twitter' 	=> $this->params->get('twitter'),
			'pinterest' => $this->params->get('pinterest'),
			'youtube' 	=> $this->params->get('youtube'),
			'linkedin' 	=> $this->params->get('linkedin'),
			'dribbble' 	=> $this->params->get('dribbble'),
			'instagram' => $this->params->get('instagram'),
			'behance' 	=> $this->params->get('behance'),
			'skype' 	=> $this->params->get('skype'),
			'whatsapp' 	=> $this->params->get('whatsapp'),
			'flickr' 	=> $this->params->get('flickr'),
			'vk' 		=> $this->params->get('vk'),
			'custom' 	=> $this->params->get('custom'),
		);

		$iconPrefix = 'fab';

		$hasAnySocialLink = array_reduce($socials,
			function ($acc, $curr) {
				return $acc || !empty($curr);
			},
			false
		);

		if ($this->params->get('show_social_icons') && $hasAnySocialLink)
		{
			$html  = '<ul class="social-icons">';

			foreach ($socials as $name => $link)
			{
				/** Modify links and name if needed. */
				if (!empty($link))
				{
					$iconName = 'fa-' . $name;

					switch($name)
					{
						case 'skype':
							$link = 'skype:' . $link . '?chat';
						break;

						case 'whatsapp':
							$link = 'https://wa.me/' . $link . '?text=Hi';
						break;

						case 'custom':
							$array = explode(' ', preg_replace("@\s+@", ' ', trim($link)));

							if (!empty($array) && count($array) > 1)
							{
								$chunks = count($array);

								if ($chunks === 2)
								{
									list($iconName, $link) = $array;
								}
								elseif ($chunks === 3)
								{
									list($iconPrefix, $iconName, $link) = $array;
								}
							}
						break;

						default:
							$link = $link;
						break;
					}
				}

				/** Generate link after modification.*/
				if (!empty($link))
				{
					$iconClass = $iconPrefix . ' ' . $iconName;
					$html .= '<li class="social-icon-' . $name . '"><a target="_blank" rel="noopener noreferrer" href="' . $link . '" aria-label="' . ucfirst($name) . '"><span class="' . $iconClass . '" aria-hidden="true"></span></a></li>';
				}
			}

			$html .= '</ul>';

			return $html;
		}

	}
}
