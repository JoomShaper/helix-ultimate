<?php
/**
 * @package Helix_Ultimate_Framework
 * @version 1.0.0
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
		$this->params = $params; // اضافه شده برای تنظیم پارامترها
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
		$menuitem = $app->getMenu()->getActive();

		if ($menuitem)
		{
			$params = $menuitem->getParams();

			if ($params->get('helixultimate_enable_page_title', 0))
			{
				$page_title = $menuitem->title;
				$page_heading = $params->get('helixultimate_page_title_heading', 'h2');
				$page_title_alt = $params->get('helixultimate_page_title_alt');
				$page_subtitle = $params->get('helixultimate_page_subtitle');
				$page_title_bg_color = $params->get('helixultimate_page_title_bg_color');
				$page_title_bg_image = $params->get('helixultimate_page_title_bg_image');

				if ($page_heading == 'h1')
				{
					$page_sub_heading = 'h2';
				}
				else
				{
					$page_sub_heading = 'h3';
				}

				$style = '';

				if ($page_title_bg_color)
				{
					$style .= 'background-color: ' . $page_title_bg_color . ';';
				}

				if ($page_title_bg_image)
				{
					$style .= 'background-image: url(' . Uri::root(true) . '/' . $page_title_bg_image . ');';
				}

				if ($style)
				{
					$style = 'style="' . $style . '"';
				}

				if ($page_title_alt)
				{
					$page_title = $page_title_alt;
				}

				// بررسی اگر صفحه جاری یک صفحه مطلب است و تنظیم عنوان مطلب
				$input = $app->input;
				$option = $input->getCmd('option');
				$view = $input->getCmd('view');
				$id = $input->getInt('id');

				if ($option == 'com_content' && $view == 'article' && $id)
				{
					// دریافت عنوان مطلب از جوملا
					$db = Factory::getDbo();
					$query = $db->getQuery(true)
						->select($db->quoteName('title'))
						->from($db->quoteName('#__content'))
						->where($db->quoteName('id') . ' = ' . (int) $id);
					$db->setQuery($query);
					$article_title = $db->loadResult();

					if ($article_title)
					{
						$page_title = $article_title;
					}
				}

				$output = '';

				$output .= '<div class="sp-page-title" ' . $style . '>';
				$output .= '<div class="container">';

				$output .= '<' . $page_heading . ' class="sp-page-title-heading">' . $page_title . '</' . $page_heading . '>';

				if ($page_subtitle)
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
