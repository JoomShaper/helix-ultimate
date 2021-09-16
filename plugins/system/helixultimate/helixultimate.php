<?php
/**
 * @package 	Helix_Ultimate_Framework
 * @author 		JoomShaper <joomshaper@js.com>
 * @copyright 	Copyright (c) 2010 - 2018 JoomShaper
 * @license 	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

/**
 * Bootstrap php file.
 * This is responsible for auto-loading php classes.
 *
 * @since 2.0.0
 */
require_once __DIR__ . '/bootstrap.php';


use HelixUltimate\Framework\Core\HelixUltimate;
use HelixUltimate\Framework\Platform\Blog;
use HelixUltimate\Framework\Platform\Helper;
use HelixUltimate\Framework\Platform\Media;
use HelixUltimate\Framework\Platform\Platform;
use HelixUltimate\Framework\System\JoomlaBridge;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Helper\MediaHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

// Constant definition
define('HELIX_LAYOUTS_PATH', JPATH_PLUGINS . '/system/helixultimate/layouts');
define('HELIX_LAYOUT_PATH', JPATH_PLUGINS . '/system/helixultimate/layout');

/**
 * Class for System Plugin HelixUltimate.
 *
 * @since 1.0.0
 */
class  PlgSystemHelixultimate extends JPlugin
{
	/**
	 * Is autoload language.
	 *
	 * @var		boolean		$autoloadLanguage
	 * @since	1.0.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * Joomla! app instance.
	 *
	 * @var		CMSApplication		$app	The CMS application instance.
	 * @since	1.0.0
	 */
	protected $app;

	/**
	 * Handle the event hook onAfterInitialize.
	 * Here we can override the HTML functions.
	 *
	 * @return	void
	 * @since	2.0.0
	 */
	public function onAfterInitialise()
	{
		$template = Helper::loadTemplateData();

		if (isset($template->template) && !empty($template->template))
		{
			$bootstrapPath = JPATH_ROOT . '/plugins/system/helixultimate/html/layouts/libraries/cms/html/bootstrap.php';

			if ($this->app->isClient('site') && \file_exists($bootstrapPath))
			{
				if (!class_exists('HelixBootstrap'))
				{
					require_once $bootstrapPath;
				}

				HTMLHelper::register('bootstrap.tooltip', ['HelixBootstrap', 'tooltip']);
				HTMLHelper::register('bootstrap.popover', ['HelixBootstrap', 'popover']);
			}
		}
	}

	/**
	 * The form event. Load additional parameters when available into the field form.
	 * Only when the type of the form is of interest.
	 *
	 * @param	Form		$form	The form.
	 * @param	stdClass	$data	The data.
	 *
	 * @return	void
	 * @since	1.0.0
	 */
	public function onContentPrepareForm(Form $form, $data)
	{
		$doc = Factory::getDocument();
		$template = Helper::loadTemplateData();

		$plg_path = Uri::root(true) . '/plugins/system/helixultimate';
		$tmpl_path = Uri::root(true) . '/templates/' . $template->template;

		Form::addFormPath(JPATH_PLUGINS . '/system/helixultimate/params');

		if ($form->getName() === 'com_menus.item')
		{
			HTMLHelper::_('jquery.framework');
			$helix_plg_url = Uri::root(true) . '/plugins/system/helixultimate';
			$doc->addScript($helix_plg_url . '/assets/js/admin/jquery-ui.min.js');

			$doc->addStyleSheet($tmpl_path . '/css/font-awesome.min.css');
			$doc->addStyleSheet($plg_path . '/assets/css/admin/modal.css');
			$doc->addScript($plg_path . '/assets/js/admin/modal.js');

			$form->loadFile('megamenu', false);
		}

		// Article Post format
		if ($form->getName() === 'com_content.article')
		{
			$doc->addStyleSheet($tmpl_path . '/css/font-awesome.min.css');
			$tpl_path = JPATH_ROOT . '/templates/' . $this->getTemplateName()->template;

			HTMLHelper::_('jquery.framework');
			HTMLHelper::_('jquery.token');

			$doc->addStyleSheet($plg_path . '/assets/css/admin/blog-options.css');
			$doc->addScript($plg_path . '/assets/js/admin/blog-options.js', ['version' => 'auto', 'relative' => false]);

			if (File::exists($tpl_path . '/blog-options.xml'))
			{
				Form::addFormPath($tpl_path);
			}
			else
			{
				Form::addFormPath(JPATH_PLUGINS . '/system/helixultimate/params');
			}

			$form->loadFile('blog-options', false);
		}
	}

	/**
	 * On Saving extensions logging method
	 * Method is called when an extension is being saved
	 *
	 * @param   string   $context  The extension
	 * @param   JTable   $table    DataBase Table object
	 * @param   boolean  $isNew    If the extension is new or not
	 *
	 * @return	void
	 * @since	1.0.0
	 */
	public function onExtensionAfterSave($context, $table, $isNew)
	{
		if ($context === 'com_templates.style' && !empty($table->id))
		{
			$params = new Registry;
			$params->loadString($table->params);

			$email       = $params->get('joomshaper_email');
			$license_key = $params->get('joomshaper_license_key');
			$template    = trim($table->template);

			if (!empty($email) && !empty($license_key))
			{
				$extra_query = 'joomshaper_email=' . urlencode($email);
				$extra_query .= '&amp;joomshaper_license_key=' . urlencode($license_key);

				$db = Factory::getDbo();
				$fields = array(
					$db->quoteName('extra_query') . ' = ' . $db->quote($extra_query),
					$db->quoteName('last_check_timestamp') . ' = 0'
				);

				$query = $db->getQuery(true)
					->update($db->quoteName('#__update_sites'))
					->set($fields)
					->where($db->quoteName('name') . ' = ' . $db->quote($template));
				$db->setQuery($query);
				$db->execute();
			}
		}
	}

	/**
	 * Attach the joomla web asset JSON file to the registry.
	 * From Joomla!4, the new web asset manager comes into the account.
	 * The templates might contains a joomla.asset.json file for managing
	 * the web assets.
	 *
	 * @return 	void
	 * @since 	2.0.5
	 */
	private function attachWebAsset() : void
	{
		$activeMenu = $this->app->getMenu()->getActive();
		$template = !empty($activeMenu) && $activeMenu->template_style_id > 0
			? Helper::getTemplateStyle($activeMenu->template_style_id)
			: Helper::loadTemplateData();

		$webAssetUri = '/templates/' . $template->template . '/joomla.asset.json';

		if(\file_exists(JPATH_ROOT . $webAssetUri))
		{
			Factory::getDocument()->getWebAssetManager()->getRegistry()->addRegistryFile($webAssetUri);
		}
	}

	/**
	 * After route.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function onAfterRoute()
	{
		$option     = $this->app->input->get('option', '', 'STRING');
		$helix      = $this->app->input->get('helix', '', 'STRING');
		$view       = $this->app->input->get('view', '', 'STRING');
		$task       = $this->app->input->get('task', '', 'STRING');
		$request    = $this->app->input->get('request', '', 'STRING');
		$action     = $this->app->input->get('action', '', 'STRING');
		$id         = $this->app->input->get('id', 0, 'INT');
		$tmpl		= $this->app->input->get('tmpl', '', 'STRING');
		$helixReturn= $this->app->input->get('helixreturn', '', 'STRING');

		$this->attachWebAsset();

		$this->app->input->set('helix_id', 9);

		if ($this->app->isClient('administrator') && $option === 'com_ajax' && $helix === 'ultimate' && !empty($id))
		{
			$this->app->input->set('tmpl', 'component');

			if ($this->app->input->get('format', '', 'STRING') !== 'html')
			{
				$this->app->input->set('format', 'html');
			}
		}

		if ($this->app->isClient('administrator') && $option === 'com_ajax'
			&& $helix === 'ultimate' && !Factory::getUser()->id)
		{
			// Redirect to the login page
			$return = urlencode(base64_encode('index.php?option=com_ajax&helix=ultimate&id=' . $id));
			$this->app->redirect(Route::_('index.php?helixreturn=' . $return, false));
		}

		/** If `helixreturn` query exists in the url then redirect to the return url. */
		if (Factory::getUser()->id && !empty($helixReturn))
		{
			$this->app->redirect(base64_decode($helixReturn));
		}

		if ($this->app->isClient('administrator'))
		{
			if ($option === 'com_ajax' && $helix === 'ultimate')
			{
				Helper::flushSettingsDataToJs();

				if ($task === 'export' && !empty($id))
				{
					$template = $this->getTemplateName($id);

					header('Content-Description: File Transfer');
					header('Content-type: application/txt');
					header('Content-Disposition: attachment; filename="' . $template->template . '_settings_' . date('d-m-Y') . '.json"');
					header('Content-Transfer-Encoding: binary');
					header('Expires: 0');
					header('Cache-Control: must-revalidate');
					header('Pragma: public');
					echo $template->params;
					exit();
				}

				/**
				 * Trigger the onAfterRespond event in every ajax route hit.
				 */
				if (!empty($request))
				{
					$this->app->triggerEvent('onAfterRespond');
				}
			}
		}

		if ($this->app->isClient('site'))
		{
			$option     = $this->app->input->get('option', '', 'STRING');
			$helix      = $this->app->input->get('helix', '', 'STRING');
			$request    = $this->app->input->get('request', '', 'STRING');
			$action     = $this->app->input->get('action', '', 'STRING');

			if ($option === 'com_ajax' && $helix === 'ultimate' && $request === 'task' && $action !== '')
			{
				switch ($action)
				{
					case 'upload-blog-image':
						Blog::upload_image();
						break;
					case 'remove-blog-image':
						Blog::remove_image();
						break;
					case 'view-media':
						Media::getFolders();
						break;
					case 'delete-media':
						Media::deleteMedia();
						break;
					case 'upload-media':
						Media::uploadMedia();
						break;
				}
			}
		}
	}

	/**
	 * Event on after respond.
	 * On this event initialize the platform.
	 *
	 * @return	void
	 * @since 	1.0.0
	 */
	public function onAfterRespond()
	{
		$request = $this->app->input->get('request', '', 'STRING');

		if ($this->app->isClient('administrator') && !empty($request))
		{
			/**
			 * On every ajax request handle the request from here.
			 */
			(new Platform)->handleRequests();
		}
	}

	/**
	 * Method to catch the onAfterDispatch event.
	 * This event is responsible for rendering the framework settings.
	 *
	 * @return	void
	 * @since	1.0.0
	 */
	public function onAfterDispatch()
	{
		$option     = $this->app->input->get('option', '', 'STRING');
		$helix      = $this->app->input->get('helix', '', 'STRING');
		$view       = $this->app->input->get('view', '', 'STRING');
		$task       = $this->app->input->get('task', '', 'STRING');
		$request    = $this->app->input->get('request', '', 'STRING');
		$action     = $this->app->input->get('action', '', 'STRING');
		$id         = $this->app->input->get('id', 0, 'INT');

		if ($this->app->isClient('administrator')
			&& $option === 'com_ajax'
			&& $helix === 'ultimate'
			&& !empty($id)
			&& empty($request))
		{
			Platform::loadFrameworkSystem();
		}

		if ($this->app->isClient('site'))
		{
			$activeMenu = $this->app->getMenu()->getActive();

			if (is_null($activeMenu))
			{
				$template_style_id = 0;
			}
			else
			{
				$template_style_id = (int) $activeMenu->template_style_id;
			}

			if ($template_style_id > 0)
			{
				if (JoomlaBridge::getVersion('major') < 4)
				{
					Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_templates/tables');
					$style = Table::getInstance('Style', 'TemplatesTable');
				}
				else
				{
					$style = new Joomla\Component\Templates\Administrator\Table\StyleTable(Factory::getContainer()->get('DatabaseDriver'));
				}

				$style->load($template_style_id);

				if (!empty($style->template))
				{
					$this->app->setTemplate($style->template, $style->params);
				}
			}
		}
	}

	public function onBeforeCompileHead()
	{
		$template = Helper::loadTemplateData();
		$params = $template->params;

		if ($this->app->isClient('administrator') && $this->app->input->get('option') === 'com_ajax' && $this->app->input->get('helix') === 'ultimate')
		{
			// Generating method `sanitizeAssetsForJ3` or `sanitizeAssetsForJ4` according to the Joomla major version.
			$sanitizeMethod = 'sanitizeAssetsForJ' . JoomlaBridge::getVersion('major');
			$this->$sanitizeMethod();
		}

		if ($this->app->isClient('site'))
		{
			$theme = new HelixUltimate;

			if ($params->get('compress_css'))
			{
				$theme->compress_css();
			}

			if ($params->get('compress_js'))
			{
				$theme->compress_js($params->get('exclude_js'));
			}

			if ($params->get('image_lazy_loading', 0))
			{
				$theme->add_js('lazysizes.min.js');
			}

			/**
			 * Adding custom directory for the assets.
			 * If anyone put any file inside the `templates/{template}/css/custom`
			 * or `templates/{template}/js/custom` directory then the files
			 * would be added to the site.
			 */
			$theme->addCustomCSS();
			$theme->addCustomSCSS();
			$theme->addCustomJS();
		}
	}

	/**
	 * Sanitize the assets i.e. scripts and stylesheets before adding to the head.
	 * This function is applicable for Joomla 3.
	 * @note This method is using dynamically.
	 *
	 * @return 	void
	 * @since	2.0.0
	 */
	private function sanitizeAssetsForJ3()
	{
		$headData = Factory::getDocument()->getHeadData();
		$styles = $headData['styleSheets'];
		$scripts = $headData['scripts'];

		if (!empty($styles))
		{
			foreach ($styles as $url => $style)
			{
				$paths = explode('/', $url);

				if ($paths[count($paths) - 1] === 'template.css')
				{
					unset($styles[$url]);
				}
			}
		}

		if (!empty($scripts))
		{
			foreach ($scripts as $url => $script)
			{
				$paths = explode('/', $url);

				if ($paths[count($paths) - 1] === 'template.js')
				{
					unset($scripts[$url]);
				}
			}
		}

		$headData['styleSheets'] = $styles;
		$headData['scripts'] = $scripts;

		Factory::getDocument()->setHeadData($headData);
	}

	/**
	 * Sanitize the assets i.e. scripts and stylesheets before adding to the head.
	 * This function is applicable for Joomla 4.
	 * @note This method is using dynamically.
	 *
	 * @return	void
	 * @since	2.0.0
	 */
	private function sanitizeAssetsForJ4()
	{
		$doc = Factory::getDocument();
		$wa = $doc->getWebAssetManager();

		/**
		 * Disable the atum specific styles and scripts.
		 */
		$assets = [
			'style' => ['template.atum.base', 'template.atum', 'template.active', 'template.active.language', 'template.user', 'template.atum.ltr', 'template.atum.rtl'],
			'script' => ['choicesjs', 'dragula']
		];

		foreach ($assets as $type => $names)
		{
			foreach ($names as $name)
			{
				if ($wa->assetExists($type, $name))
				{
					$methodName = 'disable' . ucfirst($type);
					$wa->$methodName($name);
				}
			}
		}
	}

	public function onBeforeRender()
	{
		$option     = $this->app->input->get('option', '', 'STRING');
		$helix      = $this->app->input->get('helix', '', 'STRING');
		$id         = $this->app->input->get('id', 0, 'INT');

		if ($option === 'com_ajax' && $helix === 'ultimate' && $id)
		{
			if ($this->app->isClient('site'))
			{
				$template = Helper::loadTemplateData();
				$this->app->setTemplate($template->template, $template->params);
			}
		}
	}

	public function onAfterRender()
	{
		$template = Helper::loadTemplateData();
		$params = $template->params;

		if ($this->app->isClient('site') && $params->get('image_lazy_loading', 0))
		{
			$srcRegex = "@<img[^>]*src=[\"\']([^\"\']*)[\"\'][^>]*>@";
			$classRegex = "@<img[^>]*class=[\"\']([^\"\']*)[\"\'][^>]*>@";

			$body = $content = $this->app->getBody();
			$find = [];

			/** Get all the images tags. */
			preg_match_all($srcRegex, $body, $matches);

			if (!empty($matches))
			{
				/**
				 * Update the relative path (starts with (/)images/../)
				 * by absolute path i.e. path `images/headers/raindrops.jpg`
				 * with `/path/to/the/project/images/headers/raindrops.jpg`
				 */
				foreach ($matches[1] as $key => $match)
				{
					$find[] = $matches[0][$key];

					/** Cleanup the image src. */
					$_match = JVERSION >= 4
						? MediaHelper::getCleanMediaFieldValue($match)
						: $match;
					
					if (preg_match("@(^images\/|^\/+images\/).*$@", $match))
					{
						$update = Uri::base() . $_match;
						$regex = "@" . \preg_quote($match, '/') . "@";
						$matches[0][$key] = preg_replace($regex, $update, $matches[0][$key]);
					}
					else
					{
						if ($match !== $_match)
						{
							$regex = "@" . \preg_quote($match, '/') . "@";
							$matches[0][$key] = preg_replace($regex, $_match, $matches[0][$key]);
						}
					}
				}

				/** Loop through the full matches. */
				foreach ($matches[0] as $key => $match)
				{
					$imageElement = $match;

					/**
					 * If there has a src attributes
					 * then replace them with data-src.
					 */
					if (preg_match("@src=[\"\']([^\"\']*)[\"\']@", $imageElement))
					{
						$imageElement = preg_replace("@src(?=\=[\"\']([^\"\']*)[\"\'])@", "data-src", $imageElement);
					}

					/**
					 * If srcset exists in the img element then
					 * replace the srcset with the data-srcset and add a new
					 * data-size='auto' attribute value for maintaining size
					 */
					if (preg_match("@srcset=[\"\']([^\"\']*)[\"\']@", $imageElement))
					{
						$imageElement = preg_replace("@srcset(?=\=[\"\']([^\"\']*)[\"\'])@", "data-srcset", $imageElement);
						$dataSize = 'data-size="auto" />';
						$imageElement = preg_replace("@(<img[^>]*?)(\/?>)@", "$1 " . $dataSize, $imageElement);
					}

					/** Check if there is any class attribute at the image element. */
					if (preg_match($classRegex, $imageElement, $classMatches))
					{
						/**
						 * If there is a class attribute then take the class
						 * names and append a class 'lazyload' with the existing
						 * classes and replace the previous class attribute with
						 * updating one.
						 */
						if (!empty($classMatches))
						{
							$newClass = 'class="' . $classMatches[1] . ' lazyload"';
							$imageElement = preg_replace("@class=[\"\']([^\"\']*)[\"\']@", $newClass, $imageElement);
						}
					}
					else
					{
						/** If no class attribute exists then add a class attribute. */
						$newClass = 'class="lazyload" />';
						$imageElement = preg_replace("@(<img[^>]*?)(\/?>)@", "$1 " . $newClass, $imageElement);
					}

					/** Update the content with updated images. */
					$content = str_replace($find[$key], $imageElement, $content);
				}
			}

			/**
			 * Set the body content with updated images
			 */
			$this->app->setBody($content);
		}
	}

	/**
	 * Get template object by it's ID.
	 *
	 * @param	int		$id		The template ID.
	 *
	 * @return	object			Template object.
	 * @since	1.0.0
	 */
	private function getTemplateName($id = 0)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__template_styles'));
		$query->where($db->quoteName('client_id') . ' = 0');

		if (empty($id))
		{
			$query->where($db->quoteName('home') . ' = 1');
		}
		else
		{
			$query->where($db->quoteName('id') . ' = ' . (int) $id);
		}

		$db->setQuery($query);

		return $db->loadObject();
	}

	public function onAjaxHelixultimate()
	{
		$app 		= Factory::getApplication();
		$input 		= $app->input;

		$task = $input->get('task', '', 'STRING');

		// If no task provided to the request then it close with a 403 bad request error.
		if (empty($task))
		{
			$app->setHeader('status', 403, true);
			$app->sendHeaders();
			echo new JsonResponse('You missed to pass task at your requested URL.');
			$app->close();
		}

		// Destructing the Class and method from the task
		$namespace = "HelixUltimate\\Framework\\HttpResponse\\";
		$class = "Response";
		$method = '';

		$classMethod = explode('.', $task);

		if (count($classMethod) === 1)
		{
			$method = $classMethod[0];
		}
		elseif (count($classMethod) === 2)
		{
			$class = ucfirst($classMethod[0]);
			$method = $classMethod[1];
		}
		else
		{
			$app->setHeader('status', 500, true);
			$app->sendHeaders();
			echo new JsonResponse('task is not in a proper format. Use "className.method" or only "method" format without quote.');
			$app->close();
		}

		$class = $namespace . $class;

		// Check if the class is exists or not
		if (!\class_exists($class))
		{
			$app->setHeader('status', 500, true);
			$app->sendHeaders();
			echo new JsonResponse('The class "' . $class . '" does not exist!');
			$app->close();
		}

		// Check if the method exists
		if (!\method_exists($class, $method))
		{
			$app->setHeader('status', 500, true);
			$app->sendHeaders();
			echo new JsonResponse('Method "' . $method . '" inside the class "' . $class . '" does not exist!');
			$app->close();
		}

		// $instance = new $class();
		$response = $class::$method();

		$app->setHeader('status', 200, true);
		$app->sendHeaders();
		echo new JsonResponse($response);
		$app->close();
	}
}
