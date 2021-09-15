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
use HelixUltimate\Framework\Platform\Settings;
use HelixUltimate\Framework\Platform\Helper;
/**
 * Form field for helix presets.
 *
 * @since	1.0.0
 */
class JFormFieldHelixpresets extends FormField
{
	/**
	 * Field type
	 *
	 * @var		string	$type
	 * @since	1.0.0
	 */
	protected $type = 'Helixpresets';

	/**
	 * Preset field.
	 *
	 * @var		string	Preset field.
	 * @since	1.0.0
	 */
	protected $presetfiled = '';

	/**
	 * Preset List.
	 *
	 * @var		string	Preset list.
	 * @since	1.0.0
	 */
	protected $presetList = '';

	/**
	 * Override getInput function form FormField
	 *
	 * @return	string	Field HTML string
	 * @since	1.0.0
	 */
	protected function getInput()
	{
		$children = $this->element->children();
		$presetXml = array();

		foreach ($children as $child)
		{
			$presetXml[(string) $child['name']] = $this->getDefaultDataFromXML($child);
		}

		$html = '<div class="hu-presets clearfix">';

		$templateData = Helper::loadTemplateData();

		if (empty($templateData))
		{
			throw new \Exception(sprintf('Something went wrong! Template data not found.'));
		}

		$params = $templateData->params;
		$data = $params->get('presets-data', null);
		$presetsData = $this->createPresetData($data, array_keys($presetXml), $children);

		if (!empty($presetsData))
		{
			list ($data, $htmlString) = $this->generateFieldFromParamsData($presetsData, $this->value);
		}
		else
		{
			list ($data, $htmlString) = $this->generateFieldFromXmlData($children, $this->value);
		}

		$data = json_encode($data);

		$html .= $htmlString;
		$html .= '<input id="default-values" type="hidden" class="default-values" value=\'' . json_encode($presetXml) . '\' />';
		$html .= '<input id="presets-data" type="hidden" name="presets-data" class="hu-presets-data" value=\'' . $data . '\' />';
		$html .= '<input id="' . $this->id . '" type="hidden" name="' . $this->name . '" class="hu-input-preset" value=\'' . $this->value . '\' />';
		$html .= '</div>';
		

		return $html;
	}

	/**
	 * Check if the options.xml is updated with new preset values or not.
	 * If any new preset added to the xml then update the json and add the
	 * new one. Same for removing a preset at xml.
	 *
	 * @param 	string 		$json	The JSON preset value string.
	 * @param	array		$names	The preset names array.
	 * @param 	SimpleXML	$presets	The simple XML children of the presets.
	 *
	 * @return 	\stdClass | null
	 * @since 	2.0.2
	 */
	private function createPresetData($json, $names, $presets)
	{
		if (empty($json))
		{
			return null;
		}

		if (\is_string($json))
		{
			$json = \json_decode($json);
		}

		$keys = \array_keys((array) $json);
		$names = \array_flip($names);

		foreach ($json as $presetName => $presetData)
		{
			if (!isset($names[$presetName]))
			{
				unset($json->$presetName);
			}
			else
			{
				unset($names[$presetName]);
			}
		}

		foreach ($presets as $child)
		{
			$elementName = (string) $child['name'];

			if (isset($names[$elementName]))
			{
				$json->$elementName = array(
					'label' => isset($child['label']) ? (string) $child['label'] : '',
					'default' => isset($child['default']) ? (string) $child['default'] : '',
					'description' => isset($child['description']) ? $child['description'] : '',
					'data' => (object) $this->getDefaultDataFromXML($child)
				);
				$json->$elementName = (object) $json->$elementName;
			}
		}
		
		return $json;
	}


	private function getDefaultDataFromXML($presets)
	{
		$data = array();

		foreach ($presets->children() as $preset)
		{
			$data[(string) $preset['name']] = (string) $preset['value'];
		}

		$data['preset'] = (string) $presets['name'];

		return $data;
	}

	/**
	 * Make setting panel or modal from saved
	 * data into database
	 *
	 * @param	string	$json	Preset json string.
	 * @param	string	$value	Field value
	 *
	 * @return	array
	 * @since	2.0.0
	 */
	private function generateFieldFromParamsData($json, $value)
	{
		$data = array();
		$html = '';

		if (\is_string($json) && strlen($json) > 0)
		{
			$json = json_decode($json);
		}

		$preset = json_decode($value);

		foreach ($json as $name => $child)
		{
			$class = '';

			if (isset($preset->preset) && $preset->preset === $name)
			{
				$class = ' active';
			}

			$html_data_attr = 'data-preset="' . $name . '"';

			$presetData = array(
				'name' => $name,
				'data' => array()
			);

			foreach ($child->data as $prop => $val)
			{
				if ($prop !== 'preset')
				{
					$html_data_attr .= ' data-' . $prop . '="' . $val . '"';

					// Generate preset data for editing
					$presetData['data'][$prop] = $val;
				}
			}

			$html .= '<div class="hu-preset ' . $class . '" style="background-color: ' . $child->default . '" ' . $html_data_attr . '>';
			
			// Edit preset
			$html .= '<a type="button" role="button" class="hu-edit-preset" data-preset="' . $name . '" style="color: ' . $child->default . '; border-top-right-radius: 3px;" data-preset_data=\'' . json_encode($presetData) . '\'><span class="fas fa-pen" aria-hidden="true"></span></a>';

			$html .= Settings::preparePresetEditForm($presetData, $name);

			$html .= '<div class="hu-preset-title">' . $child->label . '</div>';
			$html .= '<div class="hu-preset-contents">';
			$html .= '</div>';
			$html .= '</div>';
		}

		return [$json, $html];
	}

	/**
	 * Make setting panel or modal from XML
	 *
	 *
	 * @param	array	$children	Preset fields
	 * @param	string	$value		Field value
	 *
	 * @return	array
	 * @since	2.0.0
	 */
	private function generateFieldFromXmlData($children, $value)
	{
		$data = array();

		$html = '';

		foreach ($children as $child)
		{
			$data[(string) $child['name']] = array(
				'label' => isset($child['label']) ? (string) $child['label'] : '',
				'default' => isset($child['default']) ? (string) $child['default'] : '',
				'description' => isset($child['description']) ? $child['description'] : '',
				'data' => array()
			);

			$preset = json_decode($value);

			$class = '';

			if (isset($preset->preset) && $preset->preset === $child['name'])
			{
				$class = ' active';
			}

			$childName = $child->getName();

			if ($childName === 'preset')
			{
				$html_data_attr = 'data-preset="' . $child['name'] . '"';

				$presetData = array(
					'name' => (string) $child['name'],
					'data' => array()
				);

				foreach ($child->children() as $preset)
				{
					$html_data_attr .= ' data-' . $preset['name'] . '="' . $preset['value'] . '"';

					// Generate preset data for editing
					$presetData['data'][(string) $preset['name']] = (string) $preset['value'];
					$presetData['data']['preset'] = (string) $child['name'];

					$data[(string) $child['name']]['data'][(string) $preset['name']] = (string) $preset['value'];
					$data[(string) $child['name']]['data']['preset'] = (string) $child['name'];
				}

				$html .= '<div class="hu-preset' . $class . '" style="background-color: ' . $child['default'] . '" ' . $html_data_attr . '  class="hu-preset">';

				// Edit preset
				$html .= '<a type="button" role="button" class="hu-edit-preset" data-preset="' . $child['name'] . '" style="color: ' . $child['default'] . '" data-preset_data=\'' . json_encode($presetData) . '\'><span class="fas fa-pen" aria-hidden="true"></span></a>';

				$html .= Settings::preparePresetEditForm($presetData, $child['name']);

				$html .= '<div class="hu-preset-title">' . $child['label'] . '</div>';
				$html .= '<div class="hu-preset-contents">';
				$html .= '</div>';
				$html .= '</div>';
			}
			else
			{
				throw new UnexpectedValueException(sprintf('Unsupported element %s in JFormFieldGroupedList', $child->getName()), 500);
			}
		}

		return [$data, $html];
	}
}
