<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use HelixUltimate\Framework\Platform\Helper;


/**
 * Form field for Helix positions
 *
 * @since 		1.0.0
 * @deprecated	3.0		Use the Same Class from the src/fields instead.
 */
class JFormFieldHelixpositions extends FormField
{
	/**
	 * Field type
	 *
	 * @var		string	$type
	 * @since	1.0.0
	 */
	protected $type = 'Helixpositions';

	/**
	 * Override getInput function form FormField
	 *
	 * @return	string	Field HTML string
	 * @since	1.0.0
	 */
	protected function getInput()
	{
		$html = array();
		$attr = '';
		$input  = Factory::getApplication()->input;
		$style_id = $input->get('id', 0, 'INT');
		$style = Helper::getTemplateStyle($style_id);

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('position'));
		$query->from($db->quoteName('#__modules'));
		$query->where($db->quoteName('client_id') . ' = 0');
		$query->where($db->quoteName('published') . ' = 1');
		$query->group('position');
		$query->order('position ASC');
		$db->setQuery($query);
		$dbpositions = $db->loadObjectList();

		$templateXML = JPATH_SITE . '/templates/' . $style->template . '/templateDetails.xml';
		$template = simplexml_load_file($templateXML);
		$options = array();

		foreach ($dbpositions as $positions)
		{
			$options[] = $positions->position;
		}

		foreach ($template->positions[0] as $position)
		{
			$options[] = (string) $position;
		}

		ksort($options);

		$opts = array_unique($options);

		$options = array();

		foreach ($opts as $opt)
		{
			$options[$opt] = $opt;
		}

		$html[] = HTMLHelper::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);

		return implode($html);
	}
}
