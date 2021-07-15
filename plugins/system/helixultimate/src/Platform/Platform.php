<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace HelixUltimate\Framework\Platform;

defined('_JEXEC') or die();

use HelixUltimate\Framework\Platform\Helper;
use HelixUltimate\Framework\Platform\Request;
use HelixUltimate\Framework\System\HelixDocument;
use HelixUltimate\Framework\System\JoomlaBridge;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
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
	 * Constructor function for platform.
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
		if ($this->option === 'com_ajax' && $this->helix === 'ultimate' && $this->id && $this->permission)
		{
			$app = Factory::getApplication();
			$id = (int) $app->input->get('id', 0, 'INT');
			$style = Helper::getTemplateStyle($id);

			$layoutData = array(
				'style' => $style,
				'id' 	=> $this->id,
				'version' 	=> $this->version,
				'view' 		=> $this->view,
				'iframe'	=> [
					'url' => Uri::root(true) . '/index.php?templateStyle=' . $style->id . "&helixMode=edit",
					'width' => '100%',
					'height' => '100%'
				]
			);

			return LayoutHelper::render('display', $layoutData, HELIX_LAYOUTS_PATH);
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
		$request = new Request;

		if ($this->option === 'com_ajax' && $this->helix === 'ultimate' && $this->request === 'task')
		{
			$request->initialize();
			exit;
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
		$helixDocument = new HelixDocument;
		$style_id = (int) $app->input->get('id', 0, 'INT');

		$template = Helper::loadTemplateData();
		$helix_plg_uri = Uri::root(true) . '/plugins/system/helixultimate';
		$helix_assets_url = Uri::root() . 'plugins/system/helixultimate/assets';

		Factory::getLanguage()->load('tpl_' . $template->template, JPATH_SITE, null, true);
		self::registerLanguageScripts();

		/** Set meta information. */
		$doc->setTitle("Helix Ultimate Framework");
		$doc->setGenerator('Helix Ultimate - The Best Joomla Template Framework!');
		$doc->addFavicon($helix_plg_uri . '/assets/images/favicon.ico');
		$doc->setMetaData('viewport', 'width=device-width, initial-scale=1.0');

		$helixDocument->addInlineScript('var helixUltimateStyleId = ' . $style_id . ';');

		/** System defined assets */
		$helixDocument->useScript('jquery')
			->useScript('jquery-noconflict')
			->useScript('jquery-migrate')
			->registerAndUseScript('cms', '', ['version' => 'auto', 'relative' => true])
			->registerAndUseScript('script.bootstrap', '', ['version' => 'auto', 'relative' => true])
			->registerAndUseStyle('style.bootstrap', $helix_assets_url . '/css/bootstrap.min.css', ['version' => 'auto', 'relative' => true])
			->useScript('keepalive')
			->registerAndUseScript('script.chosen', '', ['version' => 'auto', 'relative' => true])
			->registerAndUseScript('script.colorPicker', '', ['version' => 'auto', 'relative' => true]);

		HTMLHelper::_('jquery.token');

		if (JoomlaBridge::getVersion('major') >= 4)
		{
			$helixDocument->useScript('core');
		}

		
		/** Framework defined assets */
		$helixDocument->registerAndUseStyle('style.chosen', '', ['version' => 'auto', 'relative' => true])
			->registerAndUseStyle('style.colorPicker', '', ['version' => 'auto', 'relative' => true])
			->registerAndUseStyle('helix.jquery.ui', $helix_assets_url . '/css/admin/jquery-ui.min.css', ['version' => 'auto', 'relative' => true])
			->registerAndUseStyle('helix.ultimate', $helix_assets_url . '/css/admin/helix-ultimate.css', ['version' => 'auto', 'relative' => true])
			->registerAndUseStyle('helix.modal', $helix_assets_url . '/css/admin/modal.css', ['version' => 'auto', 'relative' => true])
			->registerAndUseStyle('helix.fontAwesome', Uri::root() . 'templates/' . $template->template . '/css/font-awesome.min.css')
			->registerAndUseStyle('helix.device-field', $helix_assets_url . '/css/admin/devices-field.css', ['version' => 'auto', 'relative' => true])
			->registerAndUseStyle('style.helix.menuBuilder', $helix_assets_url . '/css/admin/menu-builder.css', ['version' => 'auto', 'relative' => true])
			->registerAndUseStyle('style.helix.megaMenu', $helix_assets_url . '/css/admin/megamenu.css', ['version' => 'auto', 'relative' => true])
			->registerAndUseStyle('style.helix.toaster', $helix_assets_url . '/css/admin/toaster.css', ['version' => 'auto', 'relative' => false]);

		$helixDocument->registerAndUseScript('helix.jquery.ui', $helix_assets_url . '/js/admin/jquery-ui.min.js', ['version' => 'auto', 'relative' => true], ['defer' => true])
			->registerAndUseScript('helix.toaster', $helix_assets_url . '/js/admin/toaster.js', ['version' => 'auto', 'relative' => false], ['defer' => false])
			->registerAndUseScript('helix.utils', $helix_assets_url . '/js/admin/utils.js', ['version' => 'auto', 'relative' => true], ['defer' => false])
			->registerAndUseScript('helix.fields', $helix_assets_url . '/js/admin/fields.js', ['version' => 'auto', 'relative' => true], ['defer' => true])
			->registerAndUseScript('helix.ultimate', $helix_assets_url . '/js/admin/helix-ultimate.js', ['version' => 'auto', 'relative' => true], ['defer' => true])
			->registerAndUseScript('helix.webFont', $helix_assets_url . '/js/admin/webfont.js', ['version' => 'auto', 'relative' => true], ['defer' => true])
			->registerAndUseScript('helix.modal', $helix_assets_url . '/js/admin/modal.js', ['version' => 'auto', 'relative' => true], ['defer' => true])
			->registerAndUseScript('helix.layout', $helix_assets_url . '/js/admin/layout.js', ['version' => 'auto', 'relative' => true], ['defer' => true])
			->registerAndUseScript('helix.media', $helix_assets_url . '/js/admin/media.js', ['version' => 'auto', 'relative' => true], ['defer' => true])
			->registerAndUseScript('helix.device-field', $helix_assets_url . '/js/admin/devices-field.js', ['version' => 'auto', 'relative' => true], ['defer' => true])
			->registerAndUseScript('helix.presets', $helix_assets_url . '/js/admin/presets.js', ['version' => 'auto', 'relative' => true], ['defer' => true])
			->registerAndUseScript('helix.treeSortable', $helix_assets_url . '/js/admin/treeSortable.js', ['version' => 'auto', 'relative' => true], ['defer' => true])
			->registerAndUseScript('helix.menubuilder', $helix_assets_url . '/js/admin/menubuilder.js', ['version' => 'auto', 'relative' => true], ['defer' => true])
			->registerAndUseScript('helix.megamenu', $helix_assets_url . '/js/admin/megamenu.js', ['version' => 'auto', 'relative' => true], ['defer' => true]);

		// Pass important data to Joomla variable for javascript
		$meta = array(
			'base' => rtrim(Uri::root(), '/'),
			'activeMenu' => $template->params->get('menu', 'mainmenu', 'STRING')
		);

		$doc->addScriptOptions('meta', $meta);
		$doc->setBuffer((new self)->initialize(), 'component');
	}

	/**
	 * Register the framework language strings for JavaScript.
	 * i.e by Joomla.Text._()
	 *
	 * @return	void
	 * @since	2.0.0
	 */
	private static function registerLanguageScripts()
	{
		Text::script('HELIX_ULTIMATE_SELECT_ICON_LABEL');
	}
}
