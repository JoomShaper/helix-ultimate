<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace HelixUltimate\Framework\Platform;

defined('_JEXEC') or die();

use HelixUltimate\Framework\HttpResponse\Response;
use HelixUltimate\Framework\Platform\Blog;
use HelixUltimate\Framework\Platform\Helper;
use HelixUltimate\Framework\Platform\Media;
use HelixUltimate\Framework\System\HelixCache;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Http\Http;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\Registry\Registry;

/**
 * Request class where the ajax requests
 * take place.
 *
 * @since   1.0.0
 */
class Request
{
	/**
	 * Joomla! app instance.
	 *
	 * @var     CMSApplication  $app
	 * @since   1.0.0
	 */
	protected $app;

	/**
	 * ID.
	 *
	 * @var		integer		$id
	 * @since	1.0.0
	 */
	protected $id;

	/**
	 * Request action.
	 *
	 * @var		string		$action
	 * @since	1.0.0
	 */
	protected $action;

	/**
	 * Request data.
	 *
	 * @var		array|object	$data
	 * @since	1.0.0
	 */
	protected $data;

	/**
	 * Layout name.
	 *
	 * @var		string		$layout_name
	 * @since	1.0.0
	 */
	protected $layout_name = '';

	/**
	 * Request reporting.
	 *
	 * @var		array	$report
	 * @since	1.0.0
	 */
	protected $report = array();

	/**
	 * Constructor function for request.
	 *
	 * @return	void
	 * @since	1.0.0
	 */
	public function __construct()
	{
		$this->app = Factory::getApplication();
		$input = $this->app->input;

		$this->id       = $input->get('id', null, 'INT');
		$this->action   = $input->get('action', '');
		$this->data     = $input->get('data', array(), 'ARRAY');
		$this->report   = array( 'status' => false, 'message' => 'Unexpected error occurs');

	}

	/**
	 * Initialize the request.
	 *
	 * @return	void
	 * @since	1.0.0
	 */
	public function initialize()
	{
		switch ($this->action)
		{
			case 'save-tmpl-style':
				$this->saveTemplateStyle();
				break;

			case 'draft-tmpl-style':
				$this->draftTemplateStyle();
				break;

			case 'reset-drafted-settings':
				$this->resetDraftedSettings();
				break;

			case 'save-layout':
				$this->copyTemplateLayout();
				break;

			case 'render-layout':
				$this->renderTemplateLayout();
				break;

			case 'remove-layout-file':
				$this->removeLayoutFile();
				break;

			case 'view-media':
				Media::getFolders();
				break;

			case 'delete-media':
				Media::deleteMedia();
				break;

			case 'create-folder':
				Media::createFolder();
				break;

			case 'upload-media':
				Media::uploadMedia();

			case 'import-tmpl-style':
				$this->importTemplateStyle();
				break;

			case 'update-font-list':
				$this->updateGoogleFontList();
				break;

			case 'fontVariants':
				$this->changeFontVariants();
				break;

			case 'upload-blog-image':
				Blog::upload_image();
				break;

			case 'remove-blog-image':
				Blog::remove_image();
				break;

			case 'purge-css-file':
				$this->purgeCssFiles();
				break;

			case 'getMenuItems':
				$this->report = Response::getMenuItems();
				break;

			case 'parentAdoption':
				$this->report = Response::parentAdoption();
				break;

			case 'rebuildMenu':
				$this->report = Response::rebuildMenu();
				break;

			case 'generateMegaMenuBody':
				$this->report = Response::generateMegaMenuBody();
				break;
			
			case 'saveMegaMenuSettings':
				$this->report = Response::saveMegaMenuSettings();
				break;

			case 'updateRowLayout':
				$this->report = Response::updateRowLayout();
				break;

			case 'generateRow':
				$this->report = Response::generateRow();
				break;

			case 'generatePopoverContents':
				$this->report = Response::generatePopoverContents();
				break;

			case 'generateNewCell':
				$this->report = Response::generateNewCell();
				break;

			case 'getModuleList':
				$this->report = Response::getModuleList();
				break;
		}

		echo json_encode($this->report);
	}

	/**
	 * Save template style.
	 *
	 * @return	void
	 * @since	1.0.0
	 */
	private function saveTemplateStyle()
	{
		$this->report['status'] = false;
		$this->report['message'] = Text::_('JINVALID_TOKEN');

		Session::checkToken() or die(json_encode($this->report));

		$data = $_POST;
		$inputs = $this->filterInputs($data);

		if (!$this->id || !is_int($this->id))
		{
			return;
		}

		$update = Helper::updateTemplateStyle($this->id, $inputs);

		$keyOptions = [
			'option' => 'com_ajax',
			'helix' => 'ultimate',
			'status' => 'draft',
			'id' => $this->id
		];

		$key = Helper::generateKey($keyOptions);
		$cache = new HelixCache($key);

		if ($cache->contains())
		{
			$cache->removeCache();
		}

		if ($update)
		{
			$this->report['status'] = true;
			$this->report['message'] = 'Style changed successfully';
			$this->report['isDrafted'] = Helper::isDrafted();
		}
	}

	private function draftTemplateStyle()
	{
		$this->report['status'] = false;
		$this->report['message'] = Text::_('JINVALID_TOKEN');

		Session::checkToken() or die(json_encode($this->report));

		$data = $_POST;
		$inputs = $this->filterInputs($data);

		$storeData = array();

		if (isset($inputs['id']))
		{
			$storeData['id'] = (int) $inputs['id'];
			unset($inputs['id']);
		}

		if (isset($inputs['template']))
		{
			$storeData['template'] = $inputs['template'];
			unset($inputs['template']);
		}

		if (isset($inputs['client_id']))
		{
			$storeData['client_id'] = (int) $inputs['client_id'];
			unset($inputs['client_id']);
		}

		if (isset($inputs['home']))
		{
			$storeData['home'] = (int) $inputs['home'];
			unset($inputs['home']);
		}

		if (isset($inputs['title']))
		{
			$storeData['title'] = $inputs['title'];
			unset($inputs['title']);
		}

		$params = new Registry($inputs);
		$storeData['params'] = $params;

		if (!$this->id || !is_int($this->id))
		{
			return;
		}

		$keyOptions = [
			'option' => 'com_ajax',
			'helix' => 'ultimate',
			'status' => 'draft',
			'id' => $this->id
		];

		try
		{
			$key = Helper::generateKey($keyOptions);
			$cache = new HelixCache($key);

			if ($cache->contains())
			{
				$cache->removeCache()->storeCache((object) $storeData);
			}
			else
			{
				$cache->storeCache((object) $storeData);
			}

			$this->report['status'] = true;
			$this->report['message'] = 'Style drafted successfully';
			$this->report['isDrafted'] = Helper::isDrafted();
		}
		catch (\Exception $e)
		{
			$this->report['status'] = false;
			$this->report['message'] = $e->getMessage();
			$this->report['isDrafted'] = Helper::isDrafted();
		}
	}

	private function resetDraftedSettings()
	{
		$keyOptions = [
			'option' => 'com_ajax',
			'helix' => 'ultimate',
			'status' => 'draft',
			'id' => $this->id
		];

		try
		{
			$key = Helper::generateKey($keyOptions);
			$cache = new HelixCache($key);

			if ($cache->contains())
			{
				$cache->removeCache();
			}

			$this->report['status'] = true;
			$this->report['message'] = 'Draft resets successfully';
			$this->report['isDrafted'] = Helper::isDrafted();
		}
		catch (\Exception $e)
		{
			$this->report['status'] = false;
			$this->report['message'] = $e->getMessage();
			$this->report['isDrafted'] = Helper::isDrafted();
		}
	}

	/**
	 * Filter inputs.
	 *
	 * @param	array	$inputs		Inputs to filter.
	 *
	 * @return	array	The filtered input
	 * @since	1.0.0
	 */
	private function filterInputs($inputs)
	{
		foreach ($inputs as &$input)
		{
			if (is_string($input))
			{
				$input = trim($input);
			}
		}

		return $inputs;
	}

	/**
	 * Copy template layout.
	 *
	 * @return	void
	 * @since	1.0.0
	 */
	private function copyTemplateLayout()
	{
		$this->setLayoutParams();
		$content = '';

		if (isset($this->data['content']))
		{
			$content = $this->data['content'];
		}

		if ($this->layout_name && $content)
		{
			$file_name = $this->layout_file_path . '.json';

			$file = fopen($file_name, 'wb');
			fwrite($file, $content);
			fclose($file);

			$this->report['status'] = true;
			$this->report['message'] = 'Files copy created as you saved';
			$this->report['layout'] = Folder::files($this->layouts_folder_path, '.json');
		}
	}

	/**
	 * Render template layout
	 *
	 * @return	void
	 * @since	1.0.0
	 */
	private function renderTemplateLayout()
	{
		$this->setLayoutParams();

		if (file_exists($this->layout_file_path))
		{
			$content = file_get_contents($this->layout_file_path);

			if (isset($content) && $content)
			{
				$layoutHtml = $this->generateLayoutHTML(json_decode($content));

				$this->report['status'] = true;
				$this->report['message'] = 'Files content rendered';
				$this->report['layoutHtml'] = $layoutHtml;
			}
		}
	}

	/**
	 * Remove layout file.
	 *
	 * @return	void
	 * @since	1.0.0
	 */
	private function removeLayoutFile()
	{
		$this->setLayoutParams();

		if (file_exists($this->layout_file_path))
		{
			unlink($this->layout_file_path);
			$this->report['status'] = true;
			$this->report['message'] = 'File removed';
			$this->report['layout'] = \JFolder::files($this->layouts_folder_path, '.json');
		}
	}

	/**
	 * Purge CSS files.
	 *
	 * @return	void
	 * @since	1.0.0
	 */
	private function purgeCssFiles()
	{
		try
		{
			$data = $_POST;
			$inputs = $this->filterInputs($data);

			if (!$this->id || !is_int($this->id))
			{
				throw new Exception('Page ID required!');
			}

			$templateStyle = Helper::getTemplateStyle($this->id);
			$cache_path    = JPATH_SITE . '/cache/com_templates/templates/' . $templateStyle->template;

			if (Folder::exists($cache_path))
			{
				$files = scandir($cache_path);

				if (count($files) > 0)
				{
					foreach ($files as $file)
					{
						$ext  = explode('.', $file);
						$cache = count($ext) > 2 ? $ext[1] : '';

						if (end($ext) === 'css' || $cache === 'scss')
						{
							File::delete($cache_path . '/' . $file);
						}
					}
				}
			}

			$this->report['status']  = true;
			$this->report['message'] = 'CSS purge success';
		}
		catch (Exception $e)
		{
			$this->report['status']  = false;
			$this->report['message'] = $e->getMessage();
		}
	}

	/**
	 * Import template style.
	 *
	 * @return 	void
	 * @since	1.0.0
	 */
	private function importTemplateStyle()
	{
		if (!$this->id || !is_int($this->id))
		{
			return;
		}

		$settings 	= $this->data['settings'];
		$data 		= json_decode($settings);

		if (json_last_error() === JSON_ERROR_NONE)
		{
			$update = Helper::updateTemplateStyle($this->id, $data);

			if ($update)
			{
				$this->report['status'] = true;
				$this->report['message'] = 'Settings imported successfully';
			}
		}
	}

	/**
	 * Update Google font list.
	 *
	 * @return	void
	 * @since	1.0.0
	 */
	private function updateGoogleFontList()
	{
		$tmpl_style = Helper::loadTemplateData();
		$template   = $tmpl_style->template;

		$template_path = JPATH_SITE . '/templates/' . $template . '/webfonts';

		if (!Folder::exists($template_path))
		{
			Folder::create($template_path, 0755);
		}

		$params = is_string($tmpl_style->params)
			? new Registry($tmpl_style->params)
			: $tmpl_style->params;

		$apiKey = $params->get('gfont_api', '');

		$url  = 'https://www.googleapis.com/webfonts/v1/webfonts?key=' . $apiKey;
		$http = new Http;
		$str  = $http->get($url);

		if ($str->code === 200)
		{
			if (File::write($template_path . '/webfonts.json', $str->body))
			{
				$this->report['status']  = true;
				$this->report['message'] = '<p class="font-update-success">Google Webfonts list successfully updated! Please refresh your browser.</p>';
			}
			else
			{
				$this->report['message'] = '<p class="font-update-failed">Google Webfonts update failed. Please make sure that your template folder is writable.</p>';
			}
		}
		elseif ($str->code === 403)
		{
			$this->report['status']  = true;
			$decode_msg = json_decode($str->body);

			if (isset(json_decode($str->body)->error->message) && $get_msg = json_decode($str->body)->error->message)
			{
				$this->report['message'] = "<p class='font-update-failed'>" . $get_msg . "</p>";
			}
		}
	}

	/**
	 * Change font variants.
	 *
	 * @return 	void
	 * @since	1.0.0
	 */
	private function changeFontVariants()
	{
		$tmpl_style = Helper::getTemplateStyle($this->id);
		$template   = $tmpl_style->template;
		$font_name  = $this->data['fontName'];

		$template_path = JPATH_SITE . '/templates/' . $template . '/webfonts/webfonts.json';
		$plugin_path   = JPATH_PLUGINS . '/system/helixultimate/assets/webfonts/webfonts.json';

		if (File::exists($template_path))
		{
			// $json = File::read($template_path);
			$json = file_get_contents($template_path);
		}
		else
		{
			// $json = File::read($plugin_path);
			$json = file_get_contents($plugin_path);
		}

		$webfonts   = json_decode($json);
		$items      = $webfonts->items;

		foreach ($items as $item)
		{
			if ($item->family == $font_name)
			{
				$fontVariants = '';
				$fontSubsets = '';

				// Variants
				foreach ($item->variants as $variant)
				{
					$fontVariants .= '<option value="' . $variant . '">' . $variant . '</option>';
				}

				// Subsets
				foreach ($item->subsets as $subset)
				{
					$fontSubsets .= '<option value="' . $subset . '">' . $subset . '</option>';
				}

				$this->report['status']     = true;
				$this->report['message']    = 'Font Style Changed';
				$this->report['variants']   = $fontVariants;
				$this->report['subsets']    = $fontSubsets;
				break;
			}
		}
	}

	/**
	 * Set layout params.
	 *
	 * @return	void
	 * @since	1.0.0
	 */
	private function setLayoutParams()
	{
		$tmpl_style = Helper::getTemplateStyle($this->id);
		$this->template   = $tmpl_style->template;

		if (isset($this->data['layoutName']))
		{
			$this->layout_name = $this->data['layoutName'];
		}

		$this->layouts_folder_path  = JPATH_SITE . '/templates/' . $this->template . '/layout/';
		$this->layout_file_path     = $this->layouts_folder_path . $this->layout_name;
	}

	/**
	 * Generate Layout HTML.
	 *
	 * @param	object		$content 	Layout grid rows.
	 *
	 * @return	string		Layout HTML.
	 * @since	1.0.0
	 */
	private function generateLayoutHTML($content = array())
	{
		$lang = \JFactory::getLanguage();
		$lang->load('tpl_' . $this->template, JPATH_SITE, $lang->getName(), true);

		$colGrid = array(
			'12'        => '12',
			'66'        => '6,6',
			'444'       => '4,4,4',
			'3333'      => '3,3,3,3',
			'48'        => '4,8',
			'39'        => '3,9',
			'363'       => '3,6,3',
			'264'       => '2,6,4',
			'210'       => '2,10',
			'57'        => '5,7',
			'237'       => '2,3,7',
			'255'       => '2,5,5',
			'282'       => '2,8,2',
			'2442'      => '2,4,4,2',
		);

		$html = '';

		if (!empty($content))
		{
			foreach ($content as $row)
			{
				$rowSettings = $this->getSettings($row->settings);
				$name = Text::_('HELIX_SECTION_TITLE');

				if (isset($row->settings->name))
				{
					$name = $row->settings->name;
				}

				$html .= '<div class="layoutbuilder-section" ' . $rowSettings . '>';
				$html .= '<div class="settings-section clearfix">';
				$html .= '<div class="settings-left pull-left">';
				$html .= '<a class="row-move" href="#"><i class="fas fa-arrows-alt" aria-hidden="true"></i></a>';
				$html .= '<strong class="section-title">' . $name . '</strong>';
				$html .= '</div>';
				$html .= '<div class="settings-right pull-right">';
				$html .= '<ul class="button-group">';
				$html .= '<li>';
				$html .= '<a class="btn btn-small add-columns" href="#"><i class="fas fa-columns" aria-hidden="true"></i></a>';
				$html .= '<ul class="column-list">';

				$_active = '';

				foreach ($colGrid as $key => $grid)
				{
					if ($key === $row->layout)
					{
						$_active = 'active';
					}

					$html .= '<li><a href="#" class="column-layout column-layout-' . $key . ' ' . $_active . '" data-layout="' . $grid . '"></a></li>';
					$_active = '';
				}

				$active = '';
				$customLayout = '';

				if (!isset($colGrid[$row->layout]))
				{
					$active = 'active';
					$split = str_split($row->layout);
					$customLayout = implode(',', $split);
				}

				$html .= '<li>';
				$html .= '<a href="#" class="hasTooltip column-layout-custom column-layout custom ' . $active . '" data-layout="' . $customLayout . '" data-type="custom" data-original-title="<strong>Custom Layout</strong>"></a>';
				$html .= '</li>';
				$html .= '</ul>';
				$html .= '</li>';
				$html .= '<li><a class="btn btn-small add-row" href="#"><i class="fas fa-bars" aria-hidden="true"></i></a></li>';
				$html .= '<li><a class="btn btn-small row-ops-set" href="#"><i class="fas fa-cogs" aria-hidden="true"></i></a></li>';
				$html .= '<li><a class="btn btn-danger btn-small remove-row" href="#"><i class="fas fa-times" aria-hidden="true"></i></a></li>';
				$html .= '</ul>';
				$html .= '</div>';
				$html .= '</div>';
				$html .= '<div class="row ui-sortable">';

				foreach ($row->attr as $column)
				{
					$colSettings = $this->getSettings($column->settings);

					$html .= '<div class="' . $column->className . '" ' . $colSettings . '>';
					$html .= '<div class="column">';

					if (isset($column->settings->column_type) && $column->settings->column_type)
					{
						$html .= '<h6 class="col-title pull-left">Component</h6>';
					}
					else
					{
						if (!isset($column->settings->name))
						{
							$column->settings->name = 'none';
						}

						$html .= '<h6 class="col-title pull-left">' . $column->settings->name . '</h6>';
					}

					$html .= '<a class="col-ops-set pull-right" href="#" ><i class="fas fa-cogs" aria-hidden="true"></i></a>';
					$html .= '</div>';
					$html .= '</div>';
				}

				$html .= '</div>';
				$html .= '</div>';
			}
		}

		return $html;
	}

	/**
	 * Get settings.
	 *
	 * @param	array	$config		The configuration array.
	 *
	 * @return	string	Settings string.
	 * @since	1.0.0
	 */
	private function getSettings($config = null)
	{
		$data = '';

		if (!empty($config))
		{
			foreach ($config as $key => $value)
			{
				$data .= ' data-' . $key . '="' . $value . '"';
			}
		}

		return $data;
	}
}
