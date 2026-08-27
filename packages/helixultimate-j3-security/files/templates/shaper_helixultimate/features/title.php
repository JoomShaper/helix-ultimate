<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */


defined ('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

/**
 * Helix Ultimate Site Title.
 *
 * @since	1.0.0
 */
class HelixUltimateFeatureTitle
{
	/**
	 * Template parameters
	 *
	 * @var		object	$params		The parameters object
	 * @since	1.0.0
	 */
	private $params;
	public $position;

	/**
	 * Constructor function
	 *
	 * @param	object	$params		The template parameters
	 *
	 * @since	1.0.0
	 */
	public function __construct($params)
	{
		$this->position = 'title';
	}

	/**
	 * Render the logo features.
	 *
	 * @return	string
	 * @since	1.0.0
	 */
	public function renderFeature()
	{

		$app = Factory::getApplication();
		$menuitem   = $app->getMenu()->getActive();

		if($menuitem)
		{

			$params = $menuitem->getParams();

			if ($params->get('helixultimate_enable_page_title', 0))
			{
				$page_title          = $menuitem->title;
				$page_heading        = $params->get('helixultimate_page_title_heading', 'h2');
				$allowedHeadings     = array('h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p');
				$page_heading        = in_array(strtolower((string) $page_heading), $allowedHeadings, true) ? strtolower((string) $page_heading) : 'h2';
				$page_title_alt      = $params->get('helixultimate_page_title_alt');
				$page_subtitle       = $params->get('helixultimate_page_subtitle');
				$page_title_bg_color = $params->get('helixultimate_page_title_bg_color');
				$page_title_bg_image = $params->get('helixultimate_page_title_bg_image');

				$page_sub_heading = ($page_heading === 'h1') ? 'h2' : 'h3';
				if (!in_array($page_sub_heading, $allowedHeadings, true))
				{
					$page_sub_heading = 'h3';
				}

				$style = '';

				if ($page_title_bg_color && preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/', $page_title_bg_color))
				{
					$style .= 'background-color: ' . $page_title_bg_color . ';';
				}

				if ($page_title_bg_image)
				{
					$cleanBgImage = htmlspecialchars(trim(strip_tags((string) $page_title_bg_image)), ENT_QUOTES, 'UTF-8');
					$bgUrl        = htmlspecialchars(Uri::root(true) . '/' . ltrim($cleanBgImage, '/'), ENT_QUOTES, 'UTF-8');
					$style .= "background-image: url('" . $bgUrl . "');";
				}

				if ($style)
				{
					$style = 'style="' . $style . '"';
				}

				if ($page_title_alt)
				{
					$page_title = $page_title_alt;
				}

				$page_title    = htmlspecialchars((string) $page_title, ENT_QUOTES, 'UTF-8');
				$page_subtitle = $page_subtitle !== null ? htmlspecialchars((string) $page_subtitle, ENT_QUOTES, 'UTF-8') : '';

				$output = '';

				$output .= '<div class="sp-page-title"' . $style . '>';
				$output .= '<div class="container">';

				$output .= '<' . $page_heading . ' class="sp-page-title-heading">' . $page_title . '</' . $page_heading . '>';

				if ($page_subtitle !== '')
				{
					$output .= '<' . $page_sub_heading . ' class="sp-page-title-sub-heading">' . $page_subtitle . '</' . $page_sub_heading . '>';
				}

				$output .= '<jdoc:include type="modules" name="breadcrumb" style="none" />';

				$output .= '</div>';
				$output .= '</div>';

				return $output;
			}

		}

	}
}
