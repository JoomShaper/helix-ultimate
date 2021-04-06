<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

use HelixUltimate\Framework\Platform\Helper;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Filesystem\Folder;

defined('_JEXEC') or die();

/**
 * Form field for Helix headers.
 *
 * @since	1.0.0
 */
class JFormFieldHelixOffcanvas extends FormField
{
    /**
	 * Field type
	 *
	 * @var		string	$type
	 * @since	1.0.0
	 */
	protected $type = 'HelixOffcanvas';

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
		$template = Helper::loadTemplateData();
		$templateName = $template->template;

		$offCanvasDir = JPATH_ROOT . '/templates/' . $templateName . '/offcanvas';
		$thumb_url = Uri::root() . 'templates/' . $templateName . '/offcanvas';

		$html = '';

		if (Folder::exists($offCanvasDir))
		{
			$offCanvases = Folder::folders($offCanvasDir);

			if (!empty($offCanvases))
			{
				$html = '<div class="hu-predefined-offcanvas">';
				$html .= '<ul class="hu-offcanvas-list clearfix" data-name="' . $this->name . '">';

				foreach ($offCanvases as $key => $canvas)
				{
					$canvasName = preg_replace("@(^\d+-)(.+)@", "$2", $canvas);
					$canvasName = preg_split("@(?=[A-Z])@", $canvasName);
					$canvasName = implode(' ', $canvasName);

					$html .= '<li class="hu-offcanvas-item' . (($this->value === $canvas) ? ' active' : '') . '" data-style="' . $canvas . '">';

					if (file_exists($offCanvasDir . '/' . $canvas . '/thumb.svg'))
					{
						$html .= '<span class="img-wrap"><img src="' . $thumb_url . '/' . $canvas . '/thumb.svg" alt="' . $canvas . '"></span>';
					}
					else
					{
						$html .= '<span class="img-wrap"><img src="' . $thumb_url . '/' . $canvas . '/thumb.jpg" alt="' . $canvas . '"></span>';
					}

					$html .= '<span class="hu-predefined-offcanvas-title">' . $canvasName . '</span>';
					$html .= '</li>';
				}

				$html .= '<input type="hidden" name="' . $this->name . '" value=\'' . $this->value . '\' id="' . $this->id . '">';
				$html .= '</div>';
			}
		}

		return $html;
	}
}