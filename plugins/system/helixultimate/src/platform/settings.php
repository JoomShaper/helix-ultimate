<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace HelixUltimate\Framework\Platform;

defined('_JEXEC') or die();

use HelixUltimate\Framework\Platform\Helper;
use HelixUltimate\Framework\System\HelixCache;
use Joomla\CMS\Factory;
use Joomla\CMS\Form;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\Registry\Registry;

/**
 * Settings class responsible for left sidebar settings
 * Helix framework's option sidebar.
 *
 * @since   1.0.0
 */
class Settings
{
	/**
	 * Joomla! app instance.
	 *
	 * @var		CMSApplication		$app	The CMS application instance.
	 * @since	1.0.0
	 */
	private $app;

	/**
	 * Component name. Invoke from input option.
	 *
	 * @var		string	$option		The option query string value.
	 * @since	1.0.0
	 */
	private $option;

	/**
	 * Helix value.
	 *
	 * @var		string	$helix	The helix value from query string.
	 * @since	1.0.0
	 */
	private $helix;

	/**
	 * View name.
	 *
	 * @var		string	$view	The view name from query string.
	 * @since	1.0.0
	 */
	private $view;

	/**
	 * Template ID.
	 *
	 * @var		integer		$id	 The helix template ID.
	 * @since	1.0.0
	 */
	private $id;

	/**
	 * Request value.
	 *
	 * @var		string	$request	The request value from query string.
	 * @since	1.0.0
	 */
	private $request;

	/**
	 * The Input object
	 *
	 * @var		JInput	$input	Joomla Request input.
	 * @since	1.0.0
	 */
	private $input;

	/**
	 * Template Form.
	 *
	 * @var		Form	$form	Joomla Form instance.
	 * @sine	1.0.0
	 */
	private	$form;

	/**
	 * Constructor function for class Options.
	 *
	 * @return	void
	 * @since	1.0.0
	 */
	public function __construct()
	{
		$this->app 		= Factory::getApplication();
		$this->input 	= $this->app->input;

		$this->form 	= new Form\Form('template');

		$this->option 	= $this->input->get('option', '', 'STRING');
		$this->id		= $this->input->get('id', 0, 'INT');
		$this->view 	= $this->input->get('view', '', 'STRING');
		$this->helix	= $this->input->get('helix', '', 'STRING');
		$this->request	= $this->input->get('request', '', 'STRING');

		if ($this->option === 'com_ajax' && $this->helix === 'ultimate' && $this->id !== 0)
		{
			HTMLHelper::_('jquery.framework');
			HTMLHelper::_('script', 'jui/cms.js', array('version' => 'auto', 'relative' => true));
		}
	}

	/**
	 * Prepare form data for the XML
	 *
	 * @return	Registry	$formData
	 * @since	2.0.0
	 */
	protected function prepareSettingsFormData()
	{
		$templateStyle = Helper::getTemplateStyle($this->id);

		$this->form->loadFile(JPATH_ROOT . '/templates/' . $templateStyle->template . '/options.xml');

		$formData = array();

		if (!empty($templateStyle->params))
		{
			$formData = json_decode($templateStyle->params);
		}

		if (empty($formData))
		{
			$layout_file = JPATH_ROOT . '/templates/' . $templateStyle->template . '/options.json';
			$formData = file_get_contents($layout_file);
			$formData = json_decode($formData);
		}

		// Set custom field data for social share button
		if (empty($formData->social_share_lists))
		{
			$formData->social_share_lists = array('facebook', 'twitter', 'linkedin');
		}

		// Store into cache before return
		if (!empty($formData))
		{
			$keyOptions = [
				'option' => 'com_ajax',
				'helix' => 'ultimate',
				'status' => 'init'
			];

			$key = Helper::generateKey($keyOptions);
			$formData = new Registry($formData);
			$templateStyle->params = $formData;
			$cache = new HelixCache($key);
			$cache->cleanCache()->storeCache($templateStyle);
		}

		return $formData;
	}

	/**
	 * Prepare Preset Edit Form for rendering
	 *
	 * @param	object	$presetData		Preset Default data
	 * @param	string	$presetName		The preset name
	 *
	 * @return	string	Presets HTML string
	 * @since	2.0.0
	 */
	public static function preparePresetEditForm($presetData, $presetName)
	{
		$presetForm = new Form\Form('preset');
		$presetForm->loadFile(JPATH_PLUGINS . '/system/helixultimate/src/form/preset.xml');

		if (!empty($presetData['data']))
		{
			$formData = new Registry($presetData['data']);
			$presetForm->bind($formData);
		}

		$fieldset = $presetForm->getFieldset('colors');

		/**
		 * Make the field id unique by adding
		 * the preset name prefix
		 */
		foreach ($fieldset as &$presetField)
		{
			$presetField->id = $presetName . '-' . $presetField->id;
		}

		$html = '<div id="' . $presetData['name'] . '" class="helix-ultimate-preset-container" style="display: none;">';
		$html .= '<div  class="' . $presetData['name'] . '">';

		$html .= LayoutHelper::render(
			'cpanel.control-board.fieldset.fields',
			['group' => 'no-group', 'fields' => $fieldset],
			HELIX_LAYOUTS_PATH
		);

		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}

	/**
	 * Get Field sets
	 *
	 * @return	array
	 * @since	2.0.0
	 */
	private function getFieldsets()
	{
		$formData = $this->prepareSettingsFormData();

		if (!empty($formData))
		{
			$this->form->bind($formData);
		}
		else
		{
			return;
		}

		$fieldsets = $this->form->getFieldsets();

		return $fieldsets;
	}

	/**
	 * Render Field sets contents
	 *
	 * @return	string	Fieldset HTML String
	 * @since	2.0.0
	 */
	public function renderFieldsetContents()
	{
		$fieldsets = $this->getFieldsets();
		$panelHTML = '';

		foreach ($fieldsets as $key => $fieldset)
		{
			$layoutData = array(
				'fieldset' => $fieldset,
				'form' => $this->form,
				'key' => $key
			);
			$panelHTML .= LayoutHelper::render('cpanel.control-board.fieldset.panel', $layoutData, HELIX_LAYOUTS_PATH);
		}

		return $panelHTML;
	}

	/**
	 * Render HelixUltimate admin sidebar.
	 *
	 * @return	string	Sidebar HTML string.
	 * @since 	1.0.0
	 */
	public function renderBuilderControlBoard()
	{
		$fieldsets = $this->getFieldsets();

		$layoutData = array(
			'fieldsets' => $fieldsets,
			'form' => $this->form
		);

		return LayoutHelper::render('cpanel.control-board.settings', $layoutData, HELIX_LAYOUTS_PATH);
	}

	/**
	 * Handling showon conditions form XML form.
	 *
	 * @param	string		$showOn			Showon conditions.
	 * @param	string		$formControl	Form Control.
	 * @param	string		$group			Form group.
	 *
	 * @return	array		Showon data array.
	 * @since	1.0.0
	 */
	public static function parseShowOnConditions($showOn, $formControl = null, $group = null)
	{
		// Process the showon data.
		if (!$showOn)
		{
			return array();
		}

		$formPath = $formControl ?: '';

		if ($group)
		{
			$groups = explode('.', $group);

			/**
			 * An empty formControl leads to invalid shown property
			 * Use the 1st part of the group instead to avoid.
			 */
			if (empty($formPath) && isset($groups[0]))
			{
				$formPath = $groups[0];
				array_shift($groups);
			}

			foreach ($groups as $group)
			{
				$formPath .= '[' . $group . ']';
			}
		}

		$showOnData  = array();
		$showOnParts = preg_split('#(\[AND\]|\[OR\])#', $showOn, -1, PREG_SPLIT_DELIM_CAPTURE);
		$op          = '';

		foreach ($showOnParts as $showOnPart)
		{
			if (($showOnPart === '[AND]') || $showOnPart === '[OR]')
			{
				$op = trim($showOnPart, '[]');
				continue;
			}

			$compareEqual     = strpos($showOnPart, '!:') === false;
			$showOnPartBlocks = explode(($compareEqual ? ':' : '!:'), $showOnPart, 2);

			$showOnData[] = array(
				'field'  => $formPath ? $formPath . '[' . $showOnPartBlocks[0] . ']' : $showOnPartBlocks[0],
				'values' => explode(',', $showOnPartBlocks[1]),
				'sign'   => $compareEqual === true ? '=' : '!=',
				'op'     => $op,
			);

			if ($op !== '')
			{
				$op = '';
			}
		}

		return $showOnData;
	}
}
