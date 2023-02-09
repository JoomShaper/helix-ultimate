<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

Namespace HelixUltimate\Framework\Core;

defined('_JEXEC') or die();

use HelixUltimate\Framework\Platform\Helper;
use HelixUltimate\Framework\System\JoomlaBridge;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;
use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\OutputStyle;

/**
 * Initiator class for viewing
 * template.
 *
 * @since	1.0.0
 */
class HelixUltimate
{
	/**
	 * Template params.
	 *
	 * @var		object		$params		The helix params.
	 * @since	1.0.0
	 */
	public $params;

	/**
	 * The document object
	 *
	 * @var		JDocument
	 * @since	1.0.0
	 */
	private $doc;

	/**
	 * Joomla! app instance.
	 *
	 * @var		CMSApplication		$app	The CMS application instance.
	 * @since	1.0.0
	 */
	public $app;

	/**
	 * Input instance
	 *
	 * @var		JInput
	 * @since	1.0.0
	 */
	public $input;

	/**
	 * Get active template.
	 *
	 * @var		object	$template
	 * @since	1.0.0
	 */
	public $template;

	/**
	 * Template folder url.
	 *
	 * @var		string
	 * @since	1.0.0
	 */
	public $template_folder_url;

	/**
	 * In positions
	 *
	 * @var		array
	 * @since	1.0.0
	 */
	private $in_positions = array();

	/**
	 * Load feature
	 *
	 * @var		array
	 * @since	1.0.0
	 */
	public $loadFeature = array();

	/**
	 * Constructor function.
	 *
	 * @since	1.0.0
	 */
	public function __construct()
	{
		$this->app      = Factory::getApplication();
		$this->input    = $this->app->input;
		$this->doc      = Factory::getDocument();

		/**
		 * Load template data from cache or database
		 * for initializing the template
		 */
		$this->template = Helper::loadTemplateData();
		$this->params   = $this->template->params;
		$this->get_template_uri();
	}

	/**
	 * Call magic method for handling custom assets
	 *
	 * @return	void
	 * @since	2.0.0
	 */
	public function __call($method, $args)
	{
		$type = '';

		if (\strpos($method, 'addCustom') !== false)
		{
			$type = \strtolower(\substr($method, 9));
		}
		else
		{
			throw new \Exception(sprintf('Method "%s" does not exists in the class "%s"', $method, __CLASS__));
		}
		
		if (!\in_array($type, ['css', 'scss', 'js']))
		{
			throw new \Exception(sprintf('Type "%s" does not found! Only allowed types are "css", "scss", and "js"', $type));
		}

		$this->addCustomAssets($type);
	}

	/**
	 * Generate body class.
	 *
	 * @param	string	$class	Body class.
	 *
	 * @return	string
	 * @since	1.0.0
	 */
	public function bodyClass($class = '')
	{
		$menu = $this->app->getMenu()->getActive();
		$menuParams = empty($menu) ? new Registry : $menu->getParams();


		$stickyHeader 	= $this->params->get('sticky_header', 0) ? ' sticky-header' : '';
		$stickyHeader 	= $this->params->get('sticky_header_sm', 0) ? $stickyHeader . ' sticky-header-md' : $stickyHeader;
		$stickyHeader 	= $this->params->get('sticky_header_xs', 0) ? $stickyHeader . ' sticky-header-sm' : $stickyHeader;

		$compClass = $this->input->get('option', '', 'STRING');
		$compClassDash = str_replace('_', '-', $compClass);

		$bodyClass       = 'site helix-ultimate hu ' . htmlspecialchars($compClass) . ' ' . $compClassDash;
		$bodyClass      .= ' view-' . htmlspecialchars($this->input->get('view', '', 'STRING'));
		$bodyClass      .= ' layout-' . htmlspecialchars($this->input->get('layout', 'default', 'STRING'));
		$bodyClass      .= ' task-' . htmlspecialchars($this->input->get('task', 'none', 'STRING'));
		$bodyClass      .= ' itemid-' . (int) $this->input->get('Itemid', '', 'INT');
		$bodyClass      .= ($this->doc->language) ? ' ' . $this->doc->language : '';
		$bodyClass      .= ($this->doc->direction) ? ' ' . $this->doc->direction : '';
		$bodyClass 		.= $stickyHeader;
		$bodyClass      .= ($this->params->get('boxed_layout', 0)) ? ' layout-boxed' : ' layout-fluid';
		$bodyClass		.= ($this->params->get('blog_details_remove_container', 0)) ? ' remove-container' : "";
		$bodyClass      .= ' offcanvas-init offcanvs-position-' . $this->params->get('offcanvas_position', 'right');

		if (isset($menu) && $menu)
		{
			if ($menuParams->get('pageclass_sfx'))
			{
				$bodyClass .= ' ' . $menuParams->get('pageclass_sfx');
			}
		}

		$bodyClass .= (!empty($class)) ? ' ' . $class : '';

		return $bodyClass;
	}

	public function googleAnalytics()
	{
		$code = $this->params->get('ga_code', null);
		$method = $this->params->get('ga_tracking_method', 'gst');
		$script = '';

		if (!empty($code))
		{
			$code = preg_replace("@\s+@", '', $code);
		}

		if ($method === 'gst' && !empty($code))
		{
			$script = "
			<!-- add google analytics -->
			<!-- Global site tag (gtag.js) - Google Analytics -->
			<script async src='https://www.googletagmanager.com/gtag/js?id={$code}'></script>
			<script>
				window.dataLayer = window.dataLayer || [];
				function gtag(){dataLayer.push(arguments);}
				gtag('js', new Date());

				gtag('config', '{$code}');
			</script>
			";
		}
		elseif ($method === 'ua' && !empty($code))
		{
			$script = "
			<!-- Google Tag Manager -->
			<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
			new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
			j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
			'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
			})(window,document,'script','dataLayer','{$code}');</script>
			<!-- End Google Tag Manager -->
			";
		}

		return $script;
	}

	/**
	 * Config header of the template.
	 *
	 * @return	void
	 * @since	1.0.0
	 */
	public function head()
	{
		$option = $this->input->get('option', '', 'STRING');
		$view 	= $this->input->get('view', '', 'STRING');
		$layout = $this->input->get('layout', 'default', 'STRING');

		HTMLHelper::_('jquery.framework');
		HTMLHelper::_('bootstrap.framework');

		if (JVERSION < 4)
		{
			if(isset($this->doc->_scripts[Uri::base(true) . '/media/jui/js/bootstrap.min.js']))
			{
				unset($this->doc->_scripts[Uri::base(true) . '/media/jui/js/bootstrap.min.js']);
			}

			if(isset($this->doc->_scripts[Uri::base(true) . '/media/jui/js/bootstrap-tooltip-extended.min.js']))
			{
				unset($this->doc->_scripts[Uri::base(true) . '/media/jui/js/bootstrap-tooltip-extended.min.js']);
			}
		}

		$webfonts = array();

		if ($this->params->get('enable_body_font'))
		{
			$webfonts['body'] = $this->params->get('body_font');
		}

		if ($this->params->get('enable_h1_font'))
		{
			$webfonts['h1'] = $this->params->get('h1_font');
		}

		if ($this->params->get('enable_h2_font'))
		{
			$webfonts['h2'] = $this->params->get('h2_font');
		}

		if ($this->params->get('enable_h3_font'))
		{
			$webfonts['h3'] = $this->params->get('h3_font');
		}

		if ($this->params->get('enable_h4_font'))
		{
			$webfonts['h4'] = $this->params->get('h4_font');
		}

		if ($this->params->get('enable_h5_font'))
		{
			$webfonts['h5'] = $this->params->get('h5_font');
		}

		if ($this->params->get('enable_h6_font'))
		{
			$webfonts['h6'] = $this->params->get('h6_font');
		}

		if ($this->params->get('enable_navigation_font'))
		{
			$webfonts['.sp-megamenu-parent > li > a, .sp-megamenu-parent > li > span, .sp-megamenu-parent .sp-dropdown li.sp-menu-item > a'] = $this->params->get('navigation_font');
		}

		if ($this->params->get('enable_custom_font') && $this->params->get('custom_font_selectors'))
		{
			$webfonts[$this->params->get('custom_font_selectors')] = $this->params->get('custom_font');
		}

		// Favicon
		if ($favicon = $this->params->get('favicon'))
		{
			$this->doc->addFavicon(Uri::base(true) . '/' . $favicon);
		}
		else
		{
			$this->doc->addFavicon($this->template_folder_url . '/images/favicon.ico');
		}

		$this->addGoogleFont($webfonts);

		$this->doc->addScriptdeclaration('template="' . $this->template->template . '";');

		$generatorText = Text::_('HELIX_ULTIMATE_GENERATOR_TEXT');

		if (!empty($generatorText))
		{
			$this->doc->setGenerator($generatorText);
		}

		if (JVERSION < 4)
		{
			echo '<jdoc:include type="head" />';
		}
		else
		{
			echo '<jdoc:include type="metas" />';
			echo '<jdoc:include type="styles" />';
			echo '<jdoc:include type="scripts" />';
		}

		$this->add_css('bootstrap.min.css');

		if ($view === 'form' && $layout === 'edit')
		{
			$this->doc->addStylesheet(Uri::root(true) . '/plugins/system/helixultimate/assets/css/frontend-edit.css');
		}
		
		if (JVERSION >= 4)
		{
			$this->doc->getWebAssetManager()->useScript('showon');
		}
		else
		{
			$bsBundleJSPath = JPATH_ROOT . '/templates/' . $this->template->template . '/js/bootstrap.bundle.min.js';
			$bsJsPath = JPATH_ROOT . '/templates/' . $this->template->template . '/js/bootstrap.min.js';
			
			if (\file_exists($bsBundleJSPath))
			{
				$this->add_js('bootstrap.bundle.min.js');
			}
			elseif (\file_exists($bsJsPath))
			{
				$this->add_js('popper.min.js, bootstrap.min.js');
			}
		}

		$app = Factory::getApplication();
		$user = $app->getIdentity();
		if (JVERSION >= 4)
		{
			$this->add_css('system-j4.min.css');
			if ($user->id)
			{
				$this->doc->addStylesheet(Uri::root(true) . '/plugins/system/helixultimate/assets/css/choices.css');
			}
		}
		else
		{
			$this->add_css('system-j3.min.css');
		}
	}

	/**
	 * Add css files at header.
	 *
	 * @param	string		$css_files	Css files seperated by comma.
	 * @param	array		$options	Stylesheet options
	 * @param	array		$attribs	Tag attributes
	 *
	 * @return	void
	 * @since	1.0.0
	 */
	public function add_css($css_files = '', $options = array(), $attribs = array())
	{
		$files = array(
			'resource' => $css_files,
			'options'  => $options,
			'attribs'  => $attribs
		);

		$this->put_css_js_file($files, 'css');
	}

	/**
	 * Add javascript file to head.
	 *
	 * @param	string	$js_files	Javascript files separated by comma.
	 * @param	array	$options	Script options.
	 * @param	array	$attribs	Script tag attributes.
	 *
	 * @return	void
	 * @since	1.0.0
	 */
	public function add_js($js_files = '', $options = array(), $attribs = array())
	{
		$files = array(
			'resource' => $js_files,
			'options'  => $options,
			'attribs'  => $attribs
		);

		$this->put_css_js_file($files, 'js');
	}

	/**
	 * Put css and js files into header.
	 *
	 * @param	array	$files		The files array containing the file paths, doc options, and tag attributes.
	 * @param	string	$folder	Type of the file to add into header. @availables are (js, css)
	 *
	 * @return	void
	 * @since	1.0.0
	 */
	private function put_css_js_file($files = array(), $folder = '')
	{
		$asset_path = JPATH_THEMES . "/{$this->template->template}/{$folder}/";
		$file_list = explode(',', $files['resource']);

		foreach ($file_list as $file)
		{
			if (empty($file))
			{
				continue;
			}

			$file = trim($file);
			$file_path = $asset_path . $file;

			if (!Helper::endsWith($file_path, $folder))
			{
				$file_path .= '.' . $folder;
			}

			if (File::exists($file_path))
			{
				$file_url = Uri::base(true) . '/templates/' . $this->template->template . '/' . $folder . '/' . (Helper::endsWith($file, $folder) ? $file : $file . '.' . $folder);
			}
			elseif (File::exists($file))
			{
				$file_url = Helper::endsWith($file, $folder) ? $file : $file . '.' . $folder;
			}
			else
			{
				/** If asset not exists inside the template path then try to load from plugin's asset path. */
				$uri = '/plugins/system/helixultimate/assets/' . $folder . '/' . (Helper::endsWith($file, $folder) ? $file : $file . '.' . $folder);

				if (\file_exists(JPATH_ROOT . $uri))
				{
					$file_url = Uri::base(true) . $uri;
				}
				else
				{
					continue;
				}
			}

			if ($folder === 'js')
			{
				$this->doc->addScript($file_url, $files['options'], $files['attribs']);
			}
			else
			{
				$this->doc->addStyleSheet($file_url, $files['options'], $files['attribs']);
			}
		}
	}

	/**
	 * Load font awesome font for J3 & J4 separately.
	 *
	 * @return	void
	 * @since 	2.0.3
	 */
	public function loadFontAwesome()
	{
		if ($this->params->get('enable_fontawesome'))
		{
			if (JVERSION < 4)
			{
				$this->add_css('font-awesome.min.css');
				$this->add_css('v4-shims.min.css');
			}
			else
			{
				$this->doc->addStyleSheet(Uri::root(true) . '/media/system/css/joomla-fontawesome.min.css', ['relative' => false, 'version' => 'auto']);
			}
		}
	}

	/**
	 * Get template URI.
	 *
	 * @return	void
	 * @since	1.0.0
	 */
	private function get_template_uri()
	{
		$this->template_folder_url = Uri::base(true) . '/templates/' . $this->template->template;
	}

	/**
	 * Include features.
	 *
	 * @return	void
	 * @since	1.0.0
	 */
	private function include_features()
	{
		$folder_path = JPATH_THEMES . '/' . $this->template->template . '/features';

		if (Folder::exists($folder_path))
		{
			$files = Folder::files($folder_path, '.php');

			if (!empty($files))
			{
				foreach ($files as $key => $file)
				{
					include_once $folder_path . '/' . $file;

					$file_name = File::stripExt($file);
					$class = 'HelixUltimateFeature' . ucfirst($file_name);
					$feature_obj = new $class($this->params);
					$position = $feature_obj->position;
					$load_pos = (isset($feature_obj->load_pos) && $feature_obj->load_pos) ? $feature_obj->load_pos : '';

					$this->in_positions[] = $position;

					if (!empty($position))
					{
						$this->loadFeature[$position][$key]['feature'] = $feature_obj->renderFeature();
						$this->loadFeature[$position][$key]['load_pos'] = $load_pos;
					}
				}
			}
		}
	}

	/**
	 * Render Layout
	 *
	 * @return	void
	 * @since	1.0.0
	 */
	public function render_layout()
	{
		// $this->add_css('custom.css');
		// $this->add_js('custom.js');
		$this->include_features();

		$layout = ($this->params->get('layout')) ? $this->params->get('layout') : [];

		if (!empty($layout))
		{
			$rows = json_decode($layout);
		}
		else
		{
			$layout_file = JPATH_SITE . '/templates/' . $this->template->template . '/options.json';

			if (!File::exists($layout_file))
			{
				die('Default Layout file is not exists! Please goto to template manager and create a new layout first.');
			}

			$layout_data = json_decode(file_get_contents($layout_file));
			$rows = json_decode($layout_data->layout);
		}

		$output = $this->get_recursive_layout($rows);	

		echo $output;
	}

	private function get_recursive_layout($rows = array())
	{
		if (empty($rows) || !is_array($rows))
		{
			return;
		}

		$option      = $this->app->input->getCmd('option', '');
		$view        = $this->app->input->getCmd('view', '');

		$pagebuilder = false;
		$output = '';
		$modified_row = new \stdClass;

		if ($option === 'com_sppagebuilder')
		{
			$pagebuilder = true;
		}

		$themepath      = JPATH_THEMES . '/' . $this->template->template;
		$carea_file     = $themepath . '/html/layouts/helixultimate/frontend/conponentarea.php';
		$module_file    = $themepath . '/html/layouts/helixultimate/frontend/modules.php';
		$lyt_thm_path   = $themepath . '/html/layouts/helixultimate/';

		$layout_path_carea  = (file_exists($carea_file)) ? $lyt_thm_path : JPATH_ROOT . '/plugins/system/helixultimate/layouts';
		$layout_path_module = (file_exists($module_file)) ? $lyt_thm_path : JPATH_ROOT . '/plugins/system/helixultimate/layouts';

		foreach ($rows as $key => $row)
		{
			$modified_row = $this->get_current_row($row);
			$columns = $modified_row->attr;

			if ($columns)
			{
				$componentArea = false;

				if (isset($modified_row->has_component) && $modified_row->has_component)
				{
					$componentArea = true;
				}

				$fluidrow = false;

				if (isset($modified_row->settings->fluidrow) && $modified_row->settings->fluidrow)
				{
					$fluidrow = $modified_row->settings->fluidrow;
				}

				$id = (isset($modified_row->settings->name) && $modified_row->settings->name) ? 'sp-' . \JFilterOutput::stringURLSafe($modified_row->settings->name) : 'sp-section-' . ($key + 1);
				$row_class = $this->build_row_class($modified_row->settings);
				$this->add_row_styles($modified_row->settings, $id);
				$sematic = (isset($modified_row->settings->name) && $modified_row->settings->name) ? strtolower($modified_row->settings->name) : 'section';

				

				switch ($sematic)
				{
					case "header":
						$sematic = 'header';
						break;

					case "footer":
						$sematic = 'footer';
						break;

					default:
						$sematic = 'section';
						break;
				}

				$data = array(
					'sematic' 			=> $sematic,
					'id' 				=> $id,
					'row_class' 		=> $row_class,
					'componentArea' 	=> $componentArea,
					'pagebuilder' 		=> $pagebuilder,
					'fluidrow' 			=> $fluidrow,
					'rowColumns' 		=> $columns,
					'loadFeature'       => $this->loadFeature
				);

				$layout_path  = JPATH_ROOT . '/plugins/system/helixultimate/layouts';
				$getLayout = new FileLayout('frontend.generate', $layout_path);

				/**
				 * If a section is named as `header` that means the section is for
				 * the page header or site menu header.
				 * But if the predefined_header option is enabled then
				 * render the predefined header instead of the header section.
				 */
				if ($sematic === 'header')
				{
					if (!$this->params->get('predefined_header'))
					{
						$output .= $getLayout->render($data);
					}
				}
				else
				{
					$output .= $getLayout->render($data);
				}
			}
		}

		return $output;
	}

	/**
	 * Get current row
	 *
	 * @param	\stdClass	$row	layout rows
	 *
	 * @return	\stdClass			Updated rows.
	 * @since	1.0.0
	 */
	private function get_current_row($row)
	{
		// Absence span
		$inactive_col   = 0;
		$has_component  = false;

		foreach ($row->attr as $key => &$column)
		{
			$column->settings->disable_modules = isset($column->settings->name) ? $this->disable_details_page_modules($column->settings->name) : false;

			if (!$column->settings->column_type)
			{
				if (!$this->count_modules($column->settings->name))
				{
					$inactive_col += $column->settings->grid_size;
					unset($row->attr[$key]);
				}

				if ($column->settings->disable_modules && $this->count_modules($column->settings->name))
				{
					$inactive_col += $column->settings->grid_size;
					unset($row->attr[$key]);
				}
			}
			else
			{
				$row->has_component = true;
				$has_component = true;
			}
		}

		foreach ($row->attr as &$column)
		{
			$options = $column->settings;
			$col_grid_size = $options->grid_size;
			$className = '';

			if (!$has_component)
			{
				if (end($row->attr) === $column)
				{
					$col_grid_size += $inactive_col;
				}
			}
			else
			{
				if (!empty($options->column_type))
				{
					$col_grid_size += $inactive_col;
				}
			}

			if (isset($options->lg_col) && $options->lg_col)
			{
				$className = $className . ' col-lg-' . $options->lg_col;
			}
			else
			{
				$className = 'col-lg-' . $col_grid_size;
			}

			if (isset($options->xl_col) && $options->xl_col)
			{
				$className = $className . ' col-xl-' . $options->xl_col;
			}

			if (isset($options->md_col) && $options->md_col)
			{
				$className = 'col-md-' . $options->md_col . ' ' . $className;
			}

			if (isset($options->sm_col) && $options->sm_col)
			{
				$className = 'col-sm-' . $options->sm_col . ' ' . $className;
			}

			if (isset($options->xs_col) && $options->xs_col)
			{
				$className = 'col-' . $options->xs_col . ' ' . $className;
			}

			$device_class = $this->get_device_class($options);
			$column->settings->className = $className . ' ' . $device_class;
		}

		return $row;
	}

	/**
	 * Add row styles.
	 *
	 * @param	object	$options	Row style options.
	 * @param	integer	$id			Row ID.
	 *
	 * @return	void
	 * @since	1.0.0
	 */
	private function add_row_styles($options, $id)
	{
		$row_css = '';

		if (isset($options->background_image) && $options->background_image)
		{
			$row_css .= 'background-image:url("' . Uri::base(true) . '/' . $options->background_image . '");';

			if (isset($options->background_repeat) && $options->background_repeat)
			{
				$row_css .= 'background-repeat:' . $options->background_repeat . ';';
			}

			if (isset($options->background_size) && $options->background_size)
			{
				$row_css .= 'background-size:' . $options->background_size . ';';
			}

			if (isset($options->background_attachment) && $options->background_attachment)
			{
				$row_css .= 'background-attachment:' . $options->background_attachment . ';';
			}

			if (isset($options->background_position) && $options->background_position)
			{
				$row_css .= 'background-position:' . $options->background_position . ';';
			}
		}

		if (isset($options->background_color) && $options->background_color)
		{
			$row_css .= 'background-color:' . $options->background_color . ';';
		}

		if (isset($options->color) && $options->color)
		{
			$row_css .= 'color:' . $options->color . ';';
		}

		if (isset($options->padding) && $options->padding)
		{
			$row_css .= 'padding:' . $options->padding . ';';
		}

		if (isset($options->margin) && $options->margin)
		{
			$row_css .= 'margin:' . $options->margin . ';';
		}

		if ($row_css)
		{
			$this->doc->addStyledeclaration('#' . $id . '{ ' . $row_css . ' }');
		}

		if (isset($options->link_color) && $options->link_color)
		{
			$this->doc->addStyledeclaration('#' . $id . ' a{color:' . $options->link_color . ';}');
		}

		if (isset($options->link_hover_color) && $options->link_hover_color)
		{
			$this->doc->addStyledeclaration('#' . $id . ' a:hover{color:' . $options->link_hover_color . ';}');
		}
	}

	/**
	 * Generate the class of the row.
	 *
	 * @param	object	$options	Row options.
	 *
	 * @return	string	The classes of the row.
	 * @since	1.0.0
	 */
	private function build_row_class($options)
	{
		$row_class = '';

		if (isset($options->custom_class) && $options->custom_class)
		{
			$row_class .= $options->custom_class;
		}

		$device_class = $this->get_device_class($options);

		if ($device_class)
		{
			$row_class .= ' ' . $device_class;
		}

		if ($row_class)
		{
			$row_class = 'class="' . $row_class . '"';
		}

		return $row_class;
	}

	/**
	 * Get device class for responsiveness.
	 *
	 * @param	object 	$options	Options object.
	 *
	 * @return	string	Device classes.
	 * @since	1.0.0
	 */
	private function get_device_class($options)
	{
		$device_class = '';

		if (isset($options->hide_on_phone) && $options->hide_on_phone)
		{
			$device_class = 'd-none d-sm-block';
		}

		if (isset($options->hide_on_large_phone) && $options->hide_on_large_phone)
		{
			$device_class = $this->reshape_device_class('sm', $device_class);
			$device_class .= ' d-sm-none d-md-block';
		}

		if (isset($options->hide_on_tablet) && $options->hide_on_tablet)
		{
			$device_class = $this->reshape_device_class('md', $device_class);
			$device_class .= ' d-md-none d-lg-block';
		}

		if (isset($options->hide_on_small_desktop) && $options->hide_on_small_desktop)
		{
			$device_class = $this->reshape_device_class('lg', $device_class);
			$device_class .= ' d-lg-none d-xl-block';
		}

		if (isset($options->hide_on_desktop) && $options->hide_on_desktop)
		{
			$device_class = $this->reshape_device_class('xl', $device_class);
			$device_class .= ' d-xl-none';
		}

		return $device_class;
	}

	/**
	 * Reshape the device classes for responsiveness.
	 *
	 * @param	string	$device		The device indicator.
	 * @param	string	$class		The existing class.
	 *
	 * @return	string	The updated class
	 * @since	1.0.0
	 */
	private function reshape_device_class($device = '', $class = '')
	{
		$search = 'd-' . $device . '-block';
		$class = str_replace($search, '', $class);
		$class = trim($class, ' ');

		return $class;
	}

	/**
	 * Count the number of modules of a position.
	 *
	 * @param	string	$position	Module position.
	 *
	 * @return	integer	The number of modules.
	 * @since	1.0.0
	 */
	public function count_modules($position)
	{
		$position = Helper::CheckNull($position);
		return ($this->doc->countModules($position) || $this->has_feature($position));
	}

	/**
	 * Disable module only from article page.
	 *
	 * @param	string	$position	Module position.
	 *
	 * @return	boolean
	 * @since	1.0.0
	 */
	private function disable_details_page_modules( $position )
	{
		$article_and_disable = ($this->app->input->get('view') === 'article' && $this->params->get('disable_module'));
		$match_positions = $position === 'left' || $position === 'right';

		return ($article_and_disable && $match_positions);
	}

	/**
	 * If the position has feature.
	 *
	 * @param	string	$position	The module position.
	 *
	 * @return	boolean	True on success, false otherwise.
	 * @since	1.0.0
	 */
	private function has_feature($position)
	{
		if (in_array($position, $this->in_positions))
		{
			return true;
		}

		return false;
	}

	/**
	 * Perform after body expressions.
	 *
	 * @return	string
	 * @since	1.0.0
	 */
	public function after_body()
	{
		if ($before_body = $this->params->get('before_body'))
		{
			echo $before_body . "\n";
		}
	}

	/**
	 * Add scss file with options.
	 *
	 * @param	string	$scss			The scss file name.
	 * @param	array	$vars			The variables array.
	 * @param	string	$css			The css file name.
	 * @param	boolean	$forceCompile	Compile the scss to css by force
	 *
	 * @return	void
	 * @since	1.0.0
	 */
	public function add_scss($scss, $vars = array(), $css = '', $forceCompile = false, $path = '')
	{
		$scss = File::stripExt($scss);

		if (!empty($css))
		{
			$css = File::stripExt($css) . '.css';
		}
		else
		{
			$css = $scss . '.css';
		}

		if ($this->params->get('scssoption'))
		{
			$needsCompile = $this->needScssCompile($scss, $vars);

			if ($forceCompile || $needsCompile)
			{
				$compiler = new Compiler;
				$template = Helper::loadTemplateData()->template;
				$scss_path = JPATH_THEMES . '/' . $template . '/scss';
				$css_path = JPATH_THEMES . '/' . $template . '/css';

				if (file_exists($scss_path . '/' . $scss . '.scss'))
				{
					$out = $css_path . '/' . $css;
					$compiler->setOutputStyle(OutputStyle::COMPRESSED);
					$compiler->setImportPaths($scss_path);

					if (!empty($vars))
					{
						$compiler->addVariables($vars);
					}

					$compiledCss = $compiler->compileString('@import "' . $scss . '.scss"');
					File::write($out, $compiledCss->getCss());

					$cache_path = JPATH_ROOT . '/cache/com_templates/templates/' . $template . '/' . $scss . '.scss.cache';
					$scssCache = array();
					$scssCache['imports'] = $this->parseIncludedFiles($compiledCss->getIncludedFiles());
					$scssCache['vars'] = $compiler->getVariables();

					File::write($cache_path, json_encode($scssCache));
				}
			}
		}

		$this->add_css($css);
	}

	/**
	 * Parse the included scss files and get the filemtime.
	 *
	 * @param 	array 	$files	The files path array.
	 *
	 * @return 	array	The new array with filepath and the modified time.
	 * @since 	2.0.5
	 */
	private function parseIncludedFiles(array $files) : array
	{
		$parsedFiles = [];

		foreach ($files as $file)
		{
			if (!empty($file) && \file_exists($file))
			{
				$parsedFiles[realpath($file)] = filemtime($file);
			}
		}

		return $parsedFiles;
	}

	/**
	 * If it is needed to compile the scss.
	 *
	 * @param	string	$scss	The scss file name.
	 * @param	array	$vars	Scss variables.
	 *
	 * @return	boolean
	 * @since	1.0.0
	 */
	public function needScssCompile($scss, $vars = array())
	{
		$cache_path = JPATH_ROOT . '/cache/com_templates/templates/' . $this->template->template . '/' . $scss . '.scss.cache';
		
		if (file_exists($cache_path))
		{
			$cache_file = json_decode(file_get_contents($cache_path));
			$imports = (isset($cache_file->imports) && $cache_file->imports) ? $cache_file->imports : array();
			$cached_vars = (isset($cache_file->vars) && $cache_file->vars) ? (array) $cache_file->vars : array();

			if (array_diff_assoc($vars, $cached_vars))
			{
				return true;
			}

			if (!empty($imports))
			{
				foreach ($imports as $import => $mtime)
				{
					if (file_exists($import))
					{
						$existModificationTime = filemtime($import);

						if ($existModificationTime != $mtime)
						{
							return true;
						}
					}
					else
					{
						return true;
					}
				}

				return false;
			}
			else
			{
				return true;
			}

			return false;
		}

		return true;
	}

	/**
	 * Add google fonts.
	 *
	 * @param	array	$fonts	Google fonts.
	 *
	 * @return	void
	 * @since	1.0.0
	 */
	public function addGoogleFont($fonts)
	{
		// $doc = Factory::getDocument();

		$systemFonts = array(
			'Arial',
			'Tahoma',
			'Verdana',
			'Helvetica',
			'Times New Roman',
			'Trebuchet MS',
			'Georgia'
		);

		if (is_array($fonts))
		{
			foreach ($fonts as $key => $font)
			{
				$font = json_decode($font);

				if (!in_array($font->fontFamily, $systemFonts))
				{
					$fontUrl = '//fonts.googleapis.com/css?family=' . $font->fontFamily . ':100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i';

					if (!empty(trim($font->fontSubset)))
					{
						$fontUrl .= '&subset=' . $font->fontSubset;
					}

					$fontUrl .= '&display=swap';

					$this->doc->addStylesheet($fontUrl, ['version' => 'auto'], ['media' => 'none', 'onload' => 'media="all"']);
				}

				$fontCSS = $key . "{";
				$fontCSS .= "font-family: '" . $font->fontFamily . "', sans-serif;";

				if (isset($font->fontSize) && $font->fontSize)
				{
					$fontCSS .= 'font-size: ' . $font->fontSize . (!preg_match("@(px|em|rem|%)$@", $font->fontSize) ? 'px;' : ';');
				}

				if (isset($font->fontWeight) && $font->fontWeight)
				{
					$fontCSS .= 'font-weight: ' . $font->fontWeight . ';';
				}

				if (isset($font->fontStyle) && $font->fontStyle)
				{
					$fontCSS .= 'font-style: ' . $font->fontStyle . ';';
				}

				if (!empty($font->fontColor))
				{
					$fontCSS .= 'color: ' . $font->fontColor . ';';
				}

				if (!empty($font->fontLineHeight))
				{
					$fontCSS .= 'line-height: ' . $font->fontLineHeight . ';';
				}

				if (!empty($font->fontLetterSpacing))
				{
					$fontCSS .= 'letter-spacing: ' . $font->fontLetterSpacing . ';';
				}

				if (!empty($font->textDecoration))
				{
					$fontCSS .= 'text-decoration: ' . $font->textDecoration . ';';
				}

				if (!empty($font->textAlign))
				{
					$fontCSS .= 'text-align: ' . $font->textAlign . ';';
				}

				$fontCSS .= "}\n";

				if (isset($font->fontSize_sm) && $font->fontSize_sm)
				{
					$fontCSS .= '@media (min-width:768px) and (max-width:991px){';
					$fontCSS .= $key . "{";
					$fontCSS .= 'font-size: ' . $font->fontSize_sm . (!preg_match("@(px|em|rem|%)$@", $font->fontSize_sm) ? 'px;' : ';');
					$fontCSS .= "}\n}\n";
				}

				if (isset($font->fontSize_xs) && $font->fontSize_xs)
				{
					$fontCSS .= '@media (max-width:767px){';
					$fontCSS .= $key . "{";
					$fontCSS .= 'font-size: ' . $font->fontSize_xs . (!preg_match("@(px|em|rem|%)$@", $font->fontSize_xs) ? 'px;' : ';');
					$fontCSS .= "}\n}\n";
				}

				$this->doc->addStyledeclaration($fontCSS);
			}
		}
	}

	/**
	 * Exclude js files and return the other js.
	 *
	 * @param	string	$key		The key
	 * @param	string	$excludes	The files to excludes with comma seperated.
	 *
	 * @return	boolean
	 * @since	1.0.0
	 */
	private function exclude_js($key, $excludes)
	{
		$match = false;

		if ($excludes)
		{
			$excludes = explode(',', $excludes);

			foreach ($excludes as $exclude)
			{
				if (basename($key) == trim($exclude))
				{
					$match = true;
				}
			}
		}

		return $match;
	}

	/**
	 * Check if the contents of the assets are changed.
	 * If the contents are changed then the filesize must be changed.
	 *
	 * @param	string	$cachedFile		File path
	 * @param	string	$currentContent	The contents
	 *
	 * @return	bool
	 * @since	2.0.0
	 */
	private function contentsChanged($cachedFile, $currentContent)
	{
		$temp = tmpfile();
		fwrite($temp, $currentContent);
		fseek($temp, 0);
		$tempFileSize = filesize(stream_get_meta_data($temp)['uri']);
		fclose($temp);

		return filesize($cachedFile) !== $tempFileSize;
	}

	/**
	 * Check if the file is minified or not.
	 * This is getting the file contents and counting
	 * the number of lines in the file.
	 * If there is only one line that means this is a minified file.
	 * On the other hand, if the percentage of the ratio of the
	 * ($numberOfLines:$contentLength) is less then 1 that means there may
	 * have a few number of lines but that could be negligible.
	 *
	 * @param	string	$file	The file url
	 *
	 * @return	boolean	True if minified, false otherwise.
	 * @since	2.0.0
	 */
	private function isMinified($file)
	{
		$content = file_get_contents($file);
		$contentLength = strlen($content);
		$numberOfLines = preg_match_all("@[\r\n]@", $content);

		return ($numberOfLines === 1)
			|| (($numberOfLines * 100 / $contentLength) < 1);
	}

	/**
	 * Compress javascript.
	 *
	 * @param	string		$excludes	If any js to exclude from compressing.
	 *
	 * @return	void
	 * @since	1.0.0
	 */
	public function compress_js($excludes = '')
	{
			$app       = Factory::getApplication();
			$view      = $app->input->get('view');
			$layout    = $app->input->get('layout');
			
			// disable js compress for edit view
			if($view == 'form' || $layout == 'edit')
			{
				return;
			}
			
			$cachetime = $app->get('cachetime', 15);

			$all_scripts  = $this->doc->_scripts;
			$cache_path   = JPATH_ROOT . '/cache/com_templates/templates/' . $this->template->template;
			$scripts      = array();
			$root_url     = Uri::root(true);
			$minifiedCode = '';
			$md5sum       = '';

			$excludeScripts = ['validate.js', 'tinymce.min.js', 'tiny_mce.js', 'editor.min.js'];
			$excludedScriptPaths = [];
			$remoteScripts = [];

			// Check all local scripts
			foreach ($all_scripts as $key => $value)
			{
				$js_file = str_replace($root_url, JPATH_ROOT, $key);

				// disable js compress for sp_pagebuilder
				if(strpos($js_file, 'com_sppagebuilder')) {
					continue;
				}

				if (strpos($js_file, JPATH_ROOT) === false)
				{
					$js_file = JPATH_ROOT . $key;
				}

				$fullPath = $js_file;

				if (\stripos($js_file, '?') !== false)
				{
					$js_file = \substr($js_file, 0, \stripos($js_file, '?'));
				}

				$ext = \strtolower(\pathinfo($js_file, PATHINFO_EXTENSION));

				if ($ext !== 'js')
				{
					$remoteScripts[] = $fullPath;
					unset($this->doc->_scripts[$key]);
					continue;
				}

				/**
				 * Exclude the scripts which are crating trouble while minifying,
				 * and searching scripts with relative path inside the script e.g. tinymce.
				*/
				if (JVERSION < 4 && \in_array(basename($js_file), $excludeScripts)) {
					$excludedScriptPaths[] = $js_file;
					unset($this->doc->_scripts[$key]);
					continue;
				}

				if (\file_exists($js_file))
				{
					if (!$this->exclude_js($key, $excludes))
					{
						$scripts[] = $key;
						$md5sum .= md5($key);
						$compressed = \JShrink\Minifier::minify(file_get_contents($js_file), array('flaggedComments' => false));
						$minifiedCode .= "/*------ " . basename($js_file) . " ------*/\n" . $compressed . "\n\n"; //add file name to compressed JS
						unset($this->doc->_scripts[$key]); // Remove scripts
					}
				}
			}

			// Compress All scripts
			if ($minifiedCode)
			{
				if (!Folder::exists($cache_path))
				{
					Folder::create($cache_path, 0755);
				}
				else
				{
					$file = $cache_path . '/' . md5($md5sum) . '.js';

					if (!\file_exists($file))
					{
						File::write($file, $minifiedCode);
					}
					else
					{
						if (filesize($file) == 0 || ((filemtime($file) + $cachetime * 60) < time()))
						{
							File::write($file, $minifiedCode);
						}
					}
					$this->doc->addScript(Uri::root(true) . '/cache/com_templates/templates/' . $this->template->template . '/' . md5($md5sum) . '.js');
				}
			}

			$excludedScriptPaths = array_merge($excludedScriptPaths, $remoteScripts);

			/** Add the script paths excluded earlier. */
			if (!empty($excludedScriptPaths))
			{
				foreach ($excludedScriptPaths as $path)
				{
					$path = Path::clean($path);

					if (\stripos($path, JPATH_ROOT) === 0)
					{
						$path = str_replace(JPATH_ROOT, '', $path);
					}

					$this->doc->addScript(Uri::root(true) . $path);
				}
			}

			return;
	}

	/**
	 * Get preloader of specific type
	 *
	 * @param	string	$type	Loader Type
	 *
	 * @return	string	Loader HTML string
	 * @since	2.0.0
	 */
	public function getPreloader($type)
	{
		$loader = array();

		switch ($type)
		{
			case 'circle':
				$loader[] = "<div class='sp-loader-circle'></div>";
			break;

			case 'bubble-loop':
				$loader[] = "<div class='sp-loader-bubble-loop'></div>";
			break;

			case 'wave-two':
				$loader[] = "<div class='wave-two-wrap'>";
				$loader[] = "<ul class='wave-two'>";
				$loader[] = str_repeat("<li></li>", 6);
				$loader[] = "</ul>";
				$loader[] = "</div>";
			break;

			case 'audio-wave':
				$loader[] = "<div class='sp-loader-audio-wave'></div>";
			break;

			case 'circle-two':
				$loader[] = "<div class='circle-two'><span></span></div>";
			break;

			case 'clock':
				$loader[] = "<div class='sp-loader-clock'></div>";
			break;

			case 'logo':
				$src = $this->params->get('logo_type') === 'image' ? Uri::root() . $this->params->get('logo_image') : null;

				$loader[] = "<div class='sp-loader-with-logo'>";
				$loader[] = "<div class='logo'>";
				$loader[] = $src ? "<img src='" . $src . "' />" : "Loading...";
				$loader[] = "</div>";
				$loader[] = "<div class='line' id='line-load'></div>";
				$loader[] = "</div>";
			break;

			default:
				$loader[] = "<div class='sp-preloader'></div>";
			break;
		}

		return implode("\n", $loader);
	}

	/**
	 * Get header style.
	 *
	 * @return	void
	 * @since	1.0.0
	 */
	public function getHeaderStyle()
	{
		$pre_header 	= $this->params->get('predefined_header');
		$header_style 	= $this->params->get('header_style');

		if (!$pre_header || !$header_style)
		{
			return;
		}

		$options = new \stdClass;
		$options->template 	= $this->template;
		$options->params 	= $this->params;
		$template 			= $options->template->template;

		$tmpl_file_location = JPATH_ROOT . '/templates/' . $template . '/headers';

		if (File::exists($tmpl_file_location . '/' . $header_style . '/header.php'))
		{
			$getLayout = new FileLayout($header_style . '.header', $tmpl_file_location);

			return $getLayout->render($options);
		}
	}

	/**
	 * Get offcanvas styles
	 *
	 * @return	string	The offcanvas layout HTML string.
	 * @since	2.0.0
	 */
	public function getOffcanvasStyle()
	{
		$offCanvasStyle = $this->params->get('offcanvas_style', '');

		if (empty($offCanvasStyle))
		{
			return '';
		}

		$options = new \stdClass;
		$options->template 	= $this->template;
		$options->params 	= $this->params;
		$template 			= $options->template->template;

		$offCanvasDirectory = JPATH_ROOT . '/templates/' . $template . '/offcanvas';

		if (\file_exists($offCanvasDirectory . '/' . $offCanvasStyle . '/canvas.php'))
		{
			$getLayout = new FileLayout($offCanvasStyle . '.canvas', $offCanvasDirectory);

			return $getLayout->render($options);
		}

		return '';
	}

	/**
	 * Minify CSS code.
	 *
	 * @param	string	$css_code	The css code snippet.
	 *
	 * @return	string	The minified code
	 * @since	1.0.0
	 */
	public function minifyCss($css_code)
	{
		// Remove comments
		$css_code = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css_code);

		// Remove space after colons
		$css_code = str_replace(': ', ':', $css_code);

		// Remove whitespace
		$css_code = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css_code);

		// Remove Empty Selectors without any properties
		$css_code = preg_replace('/(?:(?:[^\r\n{}]+)\s?{[\s]*})/', '', $css_code);

		// Remove Empty Media Selectors without any properties or selector
		$css_code = preg_replace('/@media\s?\((?:[^\r\n,{}]+)\s?{[\s]*}/', '', $css_code);

		return $css_code;
	}

	/**
	 * Compress css files.
	 *
	 * @return	void
	 * @since	1.0.0
	 */
	public function compress_css()
	{
			$app             = Factory::getApplication();
			$cachetime       = $app->get('cachetime', 15);
			$all_stylesheets = $this->doc->_styleSheets;
			$cache_path      = \JPATH_ROOT . '/cache/com_templates/templates/' . $this->template->template;
			$stylesheets     = [];
			$root_url        = Uri::root(true);
			$minifiedCode    = '';
			$md5sum          = '';

			// Check all local stylesheets
			foreach ($all_stylesheets as $key => $value)
			{
				$css_file = str_replace($root_url, \JPATH_ROOT, $key);
				
				// disable css compress for sp_pagebuilder
				if(strpos($css_file, 'com_sppagebuilder')) {
					continue;
				}

				if (strpos($css_file, \JPATH_ROOT) === false)
				{
					$css_file = \JPATH_ROOT . $key;
				}

				global $absolute_url;
				$absolute_url = $key;            

				if (\file_exists($css_file))
				{
					$stylesheets[] = $key;
					$md5sum .= md5($key);
					$compressed = $this->minifyCss(\file_get_contents($css_file));

					$fixUrl = preg_replace_callback('/url\(([^\):]*)\)/', function ($matches) {

						global $absolute_url;
					
						$url = str_replace(array('"', '\''), '', $matches[1]);
						$base = dirname($absolute_url);
						while (preg_match('/^\.\.\//', $url))
						{
							$base = dirname($base);
							$url  = substr($url, 3);
						}
						$url = $base . '/' . $url;
						$url = str_replace('//', '/', $url); // For fixing double slash '//' in url for fontawesome
						return "url('$url')";
					}, $compressed);

					$minifiedCode .= "/*------ " . basename($css_file) . " ------*/\n" . $fixUrl . "\n\n"; //add file name to compressed css

					unset($this->doc->_styleSheets[$key]); //Remove stylesheets
				}
			}

			//Compress All stylesheets
			if ($minifiedCode)
			{
					if (!Folder::exists($cache_path))
					{
							Folder::create($cache_path, 0755);
					}
					else
					{
							$file = $cache_path . '/' . md5($md5sum) . '.css';

							if (!\file_exists($file))
							{
									File::write($file, $minifiedCode);
							}
							else
							{
									if (filesize($file) == 0 || ((filemtime($file) + $cachetime * 60) < time()))
									{
											File::write($file, $minifiedCode);
									}
							}
							$this->doc->addStylesheet(Uri::root(true) . '/cache/com_templates/templates/' . $this->template->template . '/' . md5($md5sum) . '.css');
					}
			}

			return;
	}
	/**
	 * Get related articles.
	 *
	 * @param	object 	$params		Article params.
	 *
	 * @return	array	Articles
	 * @since	1.0.0
	 */
	public static function getRelatedArticles($params)
	{
		$user   = Factory::getUser();
		$userId = $user->id;
		$groups = $user->getAuthorisedViewLevels();
		$authorised = Access::getAuthorisedViewLevels($userId);

		$db = Factory::getDbo();
		$app = Factory::getApplication();
		$nullDate = $db->quote($db->getNullDate());
		$nowDate  = $db->quote(Factory::getDate()->toSql());
		$item_id = $params['item_id'];
		$maximum = isset($params['maximum']) ? (int) $params['maximum'] : 5;
		$maximum = $maximum < 1 ? 5 : $maximum;
		$catId = isset($params['catId']) ? (int) $params['catId'] : null;
		$tagids = [];

		if (isset($params['itemTags']) && count($params['itemTags']))
		{
			$itemTags = $params['itemTags'];

			foreach ($itemTags as $tag)
			{
				array_push($tagids, $tag->id);
			}
		}

		// Category filter
		$catItemIds = $tagItemIds = $itemIds = [];

		if ($catId !== null)
		{
			$catQuery = $db->getQuery(true)
				->clear()
				->select('id')
				->from($db->quoteName('#__content'))
				->where($db->quoteName('catid') . " = " . $catId)
				->setLimit($maximum + 1);

			$db->setQuery($catQuery);
			$catItemIds = $db->loadColumn();
		}

		// Tags filter
		if (is_array($tagids) && count($tagids))
		{
			$tagId = implode(',', ArrayHelper::toInteger($tagids));

			if ($tagId)
			{
				$subQuery = $db->getQuery(true)
					->clear()
					->select('DISTINCT content_item_id as id')
					->from($db->quoteName('#__contentitem_tag_map'))
					->where('tag_id IN (' . $tagId . ')')
					->where('type_alias = ' . $db->quote('com_content.article'));

				$db->setQuery($subQuery);
				$tagItemIds = $db->loadColumn();
			}
		}

		$itemIds = array_unique(array_merge($catItemIds, $tagItemIds));

		if (count($itemIds) < 1)
		{
			return [];
		}

		$itemIds = implode(',', ArrayHelper::toInteger($itemIds));
		$query = $db->getQuery(true);

		$query->clear()
			->select('a.*')
			->select('a.alias as slug')
			->from($db->quoteName('#__content', 'a'))
			->select($db->quoteName('b.alias', 'category_alias'))
			->select($db->quoteName('b.title', 'category'))
			->select($db->quoteName('b.access', 'category_access'))
			->select($db->quoteName('u.name', 'author'))
			->join('LEFT', $db->quoteName('#__categories', 'b') . ' ON (' . $db->quoteName('a.catid') . ' = ' . $db->quoteName('b.id') . ')')
			->join('LEFT', $db->quoteName('#__users', 'u') . ' ON (' . $db->quoteName('a.created_by') . ' = ' . $db->quoteName('u.id') . ')')
			->where($db->quoteName('a.access') . " IN (" . implode(',', $authorised) . ")")
			->where('a.id IN (' . $itemIds . ')')
			->where('a.id != ' . (int) $item_id);

		// Language filter
		if ($app->getLanguageFilter())
		{
			$query->where('a.language IN (' . $db->Quote(Factory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')');
		}

		$query->where('(a.publish_down IS NULL OR a.publish_down >= ' . $nowDate . ')');
		$query->where($db->quoteName('a.state') . ' = ' . $db->quote(1));
		$query->order($db->quoteName('a.created') . ' DESC')
			->setLimit($maximum);

		$db->setQuery($query);
		$items = $db->loadObjectList();

		foreach ($items as &$item)
		{
			$item->slug    	= $item->id . ':' . $item->slug;
			$item->catslug 	= $item->catid . ':' . $item->category_alias;
			$item->params = ComponentHelper::getParams('com_content');
			$access = (isset($item->access) && $item->access) ? $item->access : true;

			if ($access)
			{
				$item->params->set('access-view', true);
			}
			else
			{
				if ($item->catid == 0 || $item->category_access === null)
				{
					$item->params->set('access-view', in_array($item->access, $groups));
				}
				else
				{
					$item->params->set('access-view', in_array($item->access, $groups) && in_array($item->category_access, $groups));
				}
			}
		}

		return $items;
	}

	/**
	 * Generate the SCSS variables from the preset settings.
	 *
	 * @return 	array
	 * @since 	2.0.5
	 */
	public function getSCSSVariables() : array
	{
		$custom_style = $this->params->get('custom_style');
		$preset = $this->params->get('preset');

		if($custom_style || !$preset)
		{
			$scssVars = array(
				'preset' => 'default',
				'text_color' => $this->params->get('text_color'),
				'bg_color' => $this->params->get('bg_color'),
				'link_color' => $this->params->get('link_color'),
				'link_hover_color' => $this->params->get('link_hover_color'),
				'header_bg_color' => $this->params->get('header_bg_color'),
				'logo_text_color' => $this->params->get('logo_text_color'),
				'menu_text_color' => $this->params->get('menu_text_color'),
				'menu_text_hover_color' => $this->params->get('menu_text_hover_color'),
				'menu_text_active_color' => $this->params->get('menu_text_active_color'),
				'menu_dropdown_bg_color' => $this->params->get('menu_dropdown_bg_color'),
				'menu_dropdown_text_color' => $this->params->get('menu_dropdown_text_color'),
				'menu_dropdown_text_hover_color' => $this->params->get('menu_dropdown_text_hover_color'),
				'menu_dropdown_text_active_color' => $this->params->get('menu_dropdown_text_active_color'),
				'footer_bg_color' => $this->params->get('footer_bg_color'),
				'footer_text_color' => $this->params->get('footer_text_color'),
				'footer_link_color' => $this->params->get('footer_link_color'),
				'footer_link_hover_color' => $this->params->get('footer_link_hover_color'),
				'topbar_bg_color' => $this->params->get('topbar_bg_color'),
				'topbar_text_color' => $this->params->get('topbar_text_color'),
				'offcanvas_menu_icon_color' => $this->params->get('offcanvas_menu_icon_color'),
				'offcanvas_menu_bg_color' => $this->params->get('offcanvas_menu_bg_color'),
				'offcanvas_menu_items_and_items_color' => $this->params->get('offcanvas_menu_items_and_items_color'),
				'offcanvas_menu_active_menu_item_color' => $this->params->get('offcanvas_menu_active_menu_item_color')
			);
		}
		else
		{
			$scssVars = (array) json_decode($this->params->get('preset'));

			$scssVars['offcanvas_menu_icon_color'] = '#000000';
			$scssVars['offcanvas_menu_bg_color'] = $this->params->get('menu_dropdown_bg_color');
			$scssVars['offcanvas_menu_items_and_items_color'] = $this->params->get('menu_dropdown_text_color');
			$scssVars['offcanvas_menu_active_menu_item_color'] = $scssVars['menu_text_active_color'];
		}

		$scssVars['header_height'] 		= $this->params->get('header_height', '60px');
		$scssVars['header_height_sm'] 	= $this->params->get('header_height_sm', '60px');
		$scssVars['header_height_xs'] 	= $this->params->get('header_height_xs', '50px');
		$scssVars['offcanvas_width'] 	= $this->params->get('offcanvas_width', '300') . 'px';

		return $scssVars;
	}

	/**
	 * If user put their own JS or CSS files into `templates/{template}/js/custom`
	 * or `templates/{template}/css/custom` directory respectively then,
	 * those files would be added automatically to the template.
	 *
	 * @param	string	$type	The asset type
	 *
	 * @return	void
	 * @since	2.0.0
	 */
	public function addCustomAssets($type)
	{
		$template = Helper::loadTemplateData()->template;
		$directory = JPATH_ROOT . '/templates/' . $template . '/' . strtolower($type) . '/custom';
		$path = Uri::root(true) . '/templates/' . $template . '/' . strtolower($type) . '/custom';

		if (!\file_exists($directory) || !\is_dir($directory))
		{
			return;
		}

		$files = Folder::files($directory);
		
		if (!empty($files))
		{
			foreach ($files as $file)
			{
				if ($type === 'css')
				{
					if (preg_match("@\.css$@", $file))
					{
						$this->doc->addStylesheet($path . '/' . $file);
					}
				}
				elseif ($type === 'scss')
				{
					if (preg_match("@\.scss$@", $file))
					{
						$vars = $this->getSCSSVariables();
						$this->add_scss('custom/' . $file, $vars);
					}
				}
				elseif ($type === 'js')
				{
					if (preg_match("@\.js$@", $file))
					{
						$this->doc->addScript($path . '/' . $file, [], ['defer' => true]);
					}
				}
			}
		}
		
	}
}
