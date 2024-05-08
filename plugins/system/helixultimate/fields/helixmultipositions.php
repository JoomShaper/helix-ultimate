<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use HelixUltimate\Framework\Platform\Helper;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Version;

FormHelper::loadFieldClass('list');

$version = new Version();
$JoomlaVersion = $version->getShortVersion();

if (version_compare($JoomlaVersion, '4.0.0', '>='))
{
	JLoader::registerAlias('JFormFieldList', 'Joomla\CMS\Form\Field\ListField');
}

/**
 * Form field for Helix positions
 *
 * @since 	1.0.0
 */
class JFormFieldHelixmultipositions extends JFormFieldList
{
	/**
	 * Field type
	 *
	 * @var		string	$type
	 * @since	1.0.0
	 */
	protected $type = 'Helixmultipositions';

	/**
	 * Override getOptions function
	 *
	 * @return	array
	 * @since	1.0.0
	 */
	protected function getOptions()
	{
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
			if (empty($positions->position)) {
				continue;
			}

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

		$optionsArray = [];

		foreach ($options as $key => $item) {
			$optionsArray[] = HTMLHelper::_('select.option', $key, $item . ' (' . $key . ')');
	  	}

	  return array_merge(parent::getOptions(), $optionsArray);
	}
}
