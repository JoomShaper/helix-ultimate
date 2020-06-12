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
use HelixUltimate\Framework\Platform\Request;
use HelixUltimate\Framework\System\HelixCache;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

/**
 * Platform management class.
 *
 * @since   1.0.0
 */
class Platform
{
	/**
	 * Joomla! app instance.
	 *
	 * @var		CMSApplication		$app	The CMS application instance.
	 * @since	1.0.0
	 */
	protected $app;

	/**
	 * Component name. Invoke from input option.
	 *
	 * @var		string	$option		The option query string value.
	 * @since	1.0.0
	 */
	protected $option;

	/**
	 * Helix value.
	 *
	 * @var		string	$helix	The helix value from query string.
	 * @since	1.0.0
	 */
	protected $helix;

	/**
	 * View name.
	 *
	 * @var		string	$view	The view name from query string.
	 * @since	1.0.0
	 */
	protected $view;

	/**
	 * Template ID value.
	 *
	 * @var		integer		$id		The template ID.
	 * @since	1.0.0
	 */
	protected $id;

	/**
	 * Request value.
	 *
	 * @var		string	$request	The request value from query string.
	 * @since	1.0.0
	 */
	protected $request;

	/**
	 * Helix Version.
	 *
	 * @var		string		$version	The helix version.
	 * @sine	1.0.0
	 */
	protected $version;

	/**
	 * The users array.
	 *
	 * @var		object	$user		The users.
	 * @since	1.0.0
	 */
	protected $user = null;

	/**
	 * If the user has the permission.
	 *
	 * @var		boolean		$permission		The permission value.
	 * @since	1.0.0
	 */
	protected $permission = false;

	/**
	 * Constructor functioln for platform.
	 *
	 * @return 	void
	 * @since	1.0.0
	 */
	public function __construct()
	{
		$this->user = Factory::getUser();
		$this->app  = Factory::getApplication();
		$input 		= $this->app->input;

		$this->version    = Helper::getVersion();

		$this->option     = $input->get('option', '', 'STRING');
		$this->helix      = $input->get('helix', '', 'STRING');
		$this->view       = $input->get('view', '', 'STRING');
		$this->id         = $input->get('id', null, 'INT');
		$this->request    = $input->get('request', '', 'STRING');

		$this->userTmplEditPermission();
	}

	/**
	 * Initialize the platform
	 *
	 * @return 	void
	 * @since	1.0.0
	 */
	public function initialize()
	{
		if ($this->option === 'com_ajax' && $this->helix === 'ultimate' && $this->request === 'task')
		{
			$request = new Request;
			$request->initialize();
		}
		elseif ($this->option === 'com_ajax' && $this->helix === 'ultimate' && $this->id && $this->permission)
		{
			$app = Factory::getApplication();
			$id = (int) $app->input->get('id', 0, 'INT');
			$style = Helper::getTemplateStyle($id);

			$layoutData = array(
				'style' => $style,
				'id' 	=> $this->id,
				'version' 	=> $this->version,
				'view' 		=> $this->view,
				'iframe'	=> ['url' => Uri::root(true) . '/index.php?template=' . $style->template]
			);

			echo LayoutHelper::render('display', $layoutData, HELIX_LAYOUTS_PATH);
		}
	}

	/**
	 * Handle the task requests.
	 * This function is responsible for handling the API requests
	 * which are made as task or subtask
	 *
	 * @return	void
	 * @since	2.0.0
	 */
	public function handleRequests()
	{
		if ($this->option === 'com_ajax' && $this->helix === 'ultimate' && $this->request === 'task')
		{
			$request = new Request;
			$request->initialize();
		}
	}

	/**
	 * Check user template edit permission.
	 *
	 * @return	void
	 * @since	1.0.0
	 */
	private function userTmplEditPermission()
	{
		if ($this->user->id && $this->user->authorise('core.edit', 'com_templates'))
		{
			$this->permission = true;
		}
	}

	/**
	 * Load framework system.
	 *
	 * @return	void
	 * @since	1.0.0
	 */
	public static function loadFrameworkSystem()
	{
		$app = Factory::getApplication();
		$doc = Factory::getDocument();
		$style_id = (int) $app->input->get('id', 0, 'INT');

		$template = Helper::loadTemplateData();

		$helix_plg_url = Uri::root(true) . '/plugins/system/helixultimate';

		Factory::getLanguage()->load('tpl_' . $template->template, JPATH_SITE, null, true);

		$doc->setTitle("Helix Ultimate Framework");
		$doc->addFavicon($helix_plg_url . '/assets/images/favicon.ico');

		$doc->addScriptDeclaration('var helixUltimateStyleId = ' . $style_id . ';');

		HTMLHelper::_('jquery.framework');
		HTMLHelper::_('script', 'jui/cms.js', array('version' => 'auto', 'relative' => true));

		HTMLHelper::_('jquery.ui', array('core', 'sortable'));
		HTMLHelper::_('bootstrap.framework');
		HTMLHelper::_('behavior.formvalidator');
		HTMLHelper::_('behavior.keepalive');
		HTMLHelper::_('formbehavior.chosen', 'select');
		HTMLHelper::_('behavior.colorpicker');
		HTMLHelper::_('jquery.token');

		$doc->setMetaData('viewport', 'width=device-width, initial-scale=1.0');

		$doc->addStyleSheet($helix_plg_url . '/assets/css/admin/helix-ultimate.css');
		$doc->addStyleSheet($helix_plg_url . '/assets/css/admin/jquery-ui.min.css');
		$doc->addStyleSheet($helix_plg_url . '/assets/css/admin/modal.css');
		$doc->addStyleSheet($helix_plg_url . '/assets/css/font-awesome.min.css');
		$doc->addStyleSheet($helix_plg_url . '/assets/css/admin/devices-field.css');

		$doc->addScript($helix_plg_url . '/assets/js/admin/helix-ultimate.js');
		$doc->addScript($helix_plg_url . '/assets/js/admin/jquery-ui.min.js');
		$doc->addScript($helix_plg_url . '/assets/js/admin/webfont.js');
		$doc->addScript($helix_plg_url . '/assets/js/admin/modal.js');
		$doc->addScript($helix_plg_url . '/assets/js/admin/layout.js');
		$doc->addScript($helix_plg_url . '/assets/js/admin/media.js');
		$doc->addScript($helix_plg_url . '/assets/js/admin/devices-field.js');
		$doc->addScript($helix_plg_url . '/assets/js/admin/presets.js');

		/**
		 * Push the platform contents inside
		 * the body part of the backend template,
		 * or more specifically into the component
		 * <jdoc:include /> section
		 */
		// $content = (new self)->initialize();
		// $doc->setBuffer($content, 'component');

		// Pass important data to Joomla variable for javascript
		$meta = array(
			'base' => rtrim(Uri::root(), '/')
		);
		$doc->addScriptOptions('meta', $meta);

		echo $doc->render(
			false,
			[
				'file' => 'component.php',
				'template' => 'HelixUltimate',
			]
		);
	}
}
