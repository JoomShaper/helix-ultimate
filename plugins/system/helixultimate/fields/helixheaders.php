<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Filesystem\Folder;

defined('_JEXEC') or die();

/**
 * Form field for Helix headers.
 *
 * @since		1.0.0
 * @deprecated	3.0		Use the Same Class from the src/fields instead.
 */
class JFormFieldHelixheaders extends FormField
{
	/**
	 * Field type
	 *
	 * @var		string	$type
	 * @since	1.0.0
	 */
	protected $type = 'Helixheaders';

	/**
	 * Override getInput function form FormField
	 *
	 * @return	string	Field HTML string
	 * @since	1.0.0
	 */
	protected function getInput()
	{
		$input  = Factory::getApplication()->input;
		$id = $input->get('id', 0, 'INT');
		$template = $this->getTemplateName($id);

		$headers_src = JPATH_ROOT . '/templates/' . $template . '/headers';
		$thumb_url = Uri::root() . 'templates/' . $template . '/headers';

		$html = '';

		if (Folder::exists($headers_src))
		{
			$headers = Folder::folders($headers_src);

			if (!empty($headers))
			{
				$html = '<div class="hu-predefined-headers">';
				$html .= '<ul class="hu-header-list clearfix" data-name="' . $this->name . '">';

				foreach ($headers as $header)
				{
					$html .= '<li class="hu-header-item' . (($this->value === $header) ? ' active' : '') . '" data-style="' . $header . '">';

					if (file_exists($headers_src . '/' . $header . '/thumb.svg'))
					{
						$html .= '<span><img src="' . $thumb_url . '/' . $header . '/thumb.svg" alt="' . $header . '"</span>';
					}
					else
					{
						$html .= '<span><img src="' . $thumb_url . '/' . $header . '/thumb.jpg" alt="' . $header . '"</span>';
					}

					$html .= '</li>';
				}

				$html .= '<input type="hidden" name="' . $this->name . '" value=\'' . $this->value . '\' id="' . $this->id . '">';
				$html .= '</div>';
			}
		}

		return $html;
	}

	/**
	 * Get template name.
	 *
	 * @param	integer		$id		The template ID.
	 *
	 * @return	object
	 * @since	1.0.0
	 */
	private function getTemplateName($id = 0)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from($db->quoteName('#__template_styles'));
		$query->where($db->quoteName('client_id') . ' = 0');
		$query->where($db->quoteName('id') . ' = ' . (int) $id);

		$db->setQuery($query);
		$result = $db->loadObject();

		if (!empty($result))
		{
			return $result->template;
		}

		return;
	}
}
