<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * Copyright (c) 2010 - 2025 JoomShaper
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

	/**
	 * Position to load the feature
	 *
	 * @var		string	$position	The position name
	 * @since	1.0.0
	 */
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
		$this->params = $params;
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
				$rawHeading      = strtolower(trim((string) $params->get('helixultimate_page_title_heading', 'h2')));
				$allowedHeadings = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div'];
				$page_heading    = in_array($rawHeading, $allowedHeadings, true) ? $rawHeading : 'h2';

				if ($page_heading === 'h1')
				{
					$page_sub_heading = 'h2';
				}
				else
				{
					$page_sub_heading = 'h3';
				}

				$page_title     = (string) $menuitem->title;
				$page_title_alt = $params->get('helixultimate_page_title_alt');

				if (!empty($page_title_alt))
				{
					$page_title = (string) $page_title_alt;
				}

				$page_title = htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8');

				$page_subtitle = $params->get('helixultimate_page_subtitle');

				if (!empty($page_subtitle))
				{
					$page_subtitle = htmlspecialchars((string) $page_subtitle, ENT_QUOTES, 'UTF-8');
				}

				// Validate background color
				$rawBgColor  = trim((string) $params->get('helixultimate_page_title_bg_color'));
				$safeBgColor = '';

				if ($rawBgColor !== '')
				{
					if (preg_match('/^#([0-9a-fA-F]{3,4}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/', $rawBgColor)
						|| preg_match('/^(rgb|rgba|hsl|hsla)\(\s*[\d\.\%,\s\/]+\s*\)$/i', $rawBgColor)
						|| preg_match('/^[a-zA-Z]+$/', $rawBgColor))
					{
						$safeBgColor = $rawBgColor;
					}
				}

				// Validate background image
				$rawBgImage     = trim((string) $params->get('helixultimate_page_title_bg_image'));
				$safeBgImageUrl = '';

				if ($rawBgImage !== '')
				{
					if (strpos($rawBgImage, '..') === false && strpos($rawBgImage, "\0") === false && !preg_match('/["\'<>]/', $rawBgImage))
					{
						$safeBgImageUrl = Uri::root(true) . '/' . ltrim($rawBgImage, '/');
					}
				}

				$styleDeclarations = [];

				if ($safeBgColor !== '')
				{
					$styleDeclarations[] = 'background-color: ' . $safeBgColor . ';';
				}

				if ($safeBgImageUrl !== '')
				{
					$styleDeclarations[] = 'background-image: url(\'' . addcslashes($safeBgImageUrl, "'\\") . '\');';
				}

				$styleAttr = '';

				if (!empty($styleDeclarations))
				{
					$styleAttr = ' style="' . htmlspecialchars(implode(' ', $styleDeclarations), ENT_QUOTES, 'UTF-8') . '"';
				}

				$output = '';
				$output .= '<div class="sp-page-title"' . $styleAttr . '>';
				$output .= '<div class="container">';
				$output .= '<' . $page_heading . ' class="sp-page-title-heading">' . $page_title . '</' . $page_heading . '>';

				if (!empty($page_subtitle))
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
