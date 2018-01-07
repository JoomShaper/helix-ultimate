<?php
/**
 * @package   Helix3 Framework
 * @author    JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2015 JoomShaper
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

//no direct accees
defined('_JEXEC') or die ('resticted aceess');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.filter.filteroutput');

class HelixUltimate{
    private static $_instance;
    //private $document;
    //private $importedFiles = array();

    private $load_pos;

    //initialize
    public function __construct(){

    }

    /**
     * making self object for singleton method
     *
     */
    final public static function getInstance(){
        if (!self::$_instance) {
            self::$_instance = new self();
            self::getInstance()->getDocument();
        }
        return self::$_instance;
    }

    /**
     * Get Document
     *
     * @param string $key
     */
    public static function getDocument($key = false){
        self::getInstance()->document = JFactory::getDocument();
        $doc = self::getInstance()->document;
        if (is_string($key)) {
            return $doc->$key;
        }
        return $doc;
    }

    public static function getParam($key){
        $params = JFactory::getApplication()->getTemplate(true)->params;
        return $params->get($key);
    }

    //Body Class
    public static function bodyClass($class = ''){
        $app       = JFactory::getApplication();
        $doc       = JFactory::getDocument();
        $language  = $doc->language;
        $direction = $doc->direction;
        $option    = str_replace('_', '-', $app->input->getCmd('option', ''));
        $view      = $app->input->getCmd('view', '');
        $layout    = $app->input->getCmd('layout', '');
        $task      = $app->input->getCmd('task', '');
        $itemid    = $app->input->getCmd('Itemid', '');
        $sitename  = $app->get('sitename');

        if ($view == 'modules') {
            $layout = 'edit';
        }

        return 'site ' . $option
            . ' view-' . $view
            . ($layout ? ' layout-' . $layout : ' no-layout')
            . ($task ? ' task-' . $task : ' no-task')
            . ($itemid ? ' itemid-' . $itemid : '')
            . ($language ? ' ' . $language : '')
            . ($direction ? ' ' . $direction : '')
            . ($class ? ' ' . $class : '');
    }

    //Get view
    public static function view($class = ''){
        $app    = JFactory::getApplication();
        $view   = $app->input->getCmd('view', '');
        $layout = $app->input->getCmd('layout', '');

        if (($view == 'modules'))
        {
            $layout = 'edit';
        }

        return $layout;
    }

    //Get Template name
    public static function getTemplate()
    {
        return JFactory::getApplication()->getTemplate();
    }

    //Get Template URI
    public static function getTemplateUri(){
        return JURI::base(true) . '/templates/' . self::getTemplate();
    }

    /**
     * Get or set Template param. If value not setted params get and return,
     * else set params
     *
     * @param string $name
     * @param mixed  $value
     */
    public static function Param($name = true, $value = null){
        // if $name = true, this will return all param data
        if (is_bool($name) and $name == true)
        {
            return JFactory::getApplication()->getTemplate(true)->params;
        }
        // if $value = null, this will return specific param data
        if (is_null($value))
        {
            return JFactory::getApplication()->getTemplate(true)->params->get($name);
        }
        // if $value not = null, this will set a value in specific name.

        $data = JFactory::getApplication()->getTemplate(true)->params->get($name);

        if (is_null($data) or !isset($data))
        {
            JFactory::getApplication()->getTemplate(true)->params->set($name, $value);

            return $value;
        }
        else
        {
            return $data;
        }
    }

    /**
     * Importing features
     *
     * @access private
     */
    private $inPositions = array();
    public $loadFeature = array();

    private static function importFeatures(){
        $template = JFactory::getApplication()->getTemplate();
        $path     = JPATH_THEMES . '/' . $template . '/features';

        if (file_exists($path)) {
            $files = JFolder::files($path, '.php');

            if (count($files)) {
                foreach ($files as $key => $file) {
                    include_once $path . '/' . $file;
                    $name = JFile::stripExt($file);

                    $class = 'HelixUltimateFeature' . ucfirst($name);
                    $class = new $class(self::getInstance());

                    $position = $class->position;
                    $load_pos = (isset($class->load_pos) && $class->load_pos) ? $class->load_pos : '';

                    self::getInstance()->inPositions[] = $position;

                    if (!empty($position)) {
                        self::getInstance()->loadFeature[$position][$key]['feature'] = $class->renderFeature();
                        self::getInstance()->loadFeature[$position][$key]['load_pos'] = $load_pos;
                    }
                }
            }
        }

        return self::getInstance();
    }

    /**
     * get number from col-xs
     *
     * @param string $col_name
     */
    public static function getColXsNo($col_name){
        //Remove Classes name
        $class_remove = array('layout-column', 'column-active', 'col-sm-', 'span', 'builder-col');
        $col_number = trim(str_replace($class_remove, '', $col_name));
        return $col_number;
    }

    public static function generatelayout(){

        self::getInstance()->addCSS('custom.css');
        self::getInstance()->addJS('custom.js');

        $doc         = JFactory::getDocument();
        $app         = JFactory::getApplication();
        $option      = $app->input->getCmd('option', '');
        $view        = $app->input->getCmd('view', '');
        $pagebuilder = false;

        if ($option == 'com_sppagebuilder') {
            $doc->addStylesheet( JURI::base(true) . '/plugins/system/helixultimate/assets/css/pagebuilder.css' );
            $pagebuilder = true;
        }

        //Import Features
        self::importFeatures();

        $params = JFactory::getApplication()->getTemplate(true)->params;
        $rows   = json_decode($params->get('layout'));

        //die(print_row($rows));
        //Load from file if not exists in database
        if (empty($rows)) {
            $layout_file = JPATH_SITE . '/templates/' . self::getTemplate() . '/layout/default.json';
            if (!JFile::exists($layout_file)) {
                die('Default Layout file is not exists! Please goto to template manager and create a new layout first.');
            }
            $rows = json_decode(JFile::read($layout_file));
        }

        $output = '';
        $output .= self::get_recursive_layout($rows, 1);
        echo $output;
    }

    public static function get_recursive_layout($rows = array(), $row_level){
        $doc         = JFactory::getDocument();
        $app         = JFactory::getApplication();
        $option      = $app->input->getCmd('option', '');
        $view        = $app->input->getCmd('view', '');
        $pagebuilder = false;

        if ($option == 'com_sppagebuilder') {
            $doc->addStylesheet( JURI::base(true) . '/plugins/system/helixultimate/assets/css/pagebuilder.css' );
            $pagebuilder = true;
        }

        //helper & model
        $menu_class   = JPATH_ROOT . '/plugins/system/helixultimate/core/classes/helixultimate.php';

        if (file_exists($menu_class)) {
            require_once($menu_class);
        }
        $template       = JFactory::getApplication()->getTemplate();
        $themepath      = JPATH_THEMES . '/' . $template;
        $carea_file     = $themepath . '/html/layouts/helixultimate/frontend/conponentarea.php';
        $module_file    = $themepath . '/html/layouts/helixultimate/frontend/modules.php';
        $lyt_thm_path   = $themepath . '/html/layouts/helixultimate/';

        $layout_path_carea  = (file_exists($carea_file)) ? $lyt_thm_path : JPATH_ROOT .'/plugins/system/helixultimate/layouts';
        $layout_path_module = (file_exists($module_file)) ? $lyt_thm_path : JPATH_ROOT .'/plugins/system/helixultimate/layouts';

        $output = '';
        if (! empty($rows) && count($rows)){
            foreach ($rows as $key => $row) {
                //Skip header footer configuration
                if ( ! empty($row->header)){
                    break;
                }

                $rowColumns = self::rowColumns($row->attr);
                if (!empty($rowColumns)) {
                    $componentArea = false;

                    if (self::hasComponent($rowColumns)) {
                        $componentArea = true;
                    }

                    $fluidrow = false;
                    if (!empty($row->settings->fluidrow)) {
                        $fluidrow = $row->settings->fluidrow;
                    }

                    $id = (empty($row->settings->name)) ? 'sp-section-' . ($key + 1) : 'sp-' . JFilterOutput::stringURLSafe($row->settings->name);

                    if ($row_level > 1){
                        $id = $id.'-'.$row_level;
                    }

                    $row_class = '';

                    if (!empty($row->settings->custom_class)) {
                        $row_class .= $row->settings->custom_class;
                    }
                    if (!empty($row->settings->hidden_xs)) {
                        $row_class .= ' hidden-xs';
                    }
                    if (!empty($row->settings->hidden_sm)) {
                        $row_class .= ' hidden-sm';
                    }
                    if (!empty($row->settings->hidden_md)) {
                        $row_class .= ' hidden-md';
                    }
                    if ($row_class) {
                        $row_class = ' class="' . $row_class . '"';
                    }
                    else {
                        $row_class = '';
                    }

                    //css
                    $row_css = '';

                    if (!empty($row->settings->background_image)) {
                        $row_css .= 'background-image:url("' . JURI::base(true) . '/' . $row->settings->background_image . '");';
                        if (!empty($row->settings->background_repeat)) {
                            $row_css .= 'background-repeat:' . $row->settings->background_repeat . ';';
                        }
                        if (!empty($row->settings->background_size)) {
                            $row_css .= 'background-size:' . $row->settings->background_size . ';';
                        }
                        if (!empty($row->settings->background_attachment)) {
                            $row_css .= 'background-attachment:' . $row->settings->background_attachment . ';';
                        }
                        if (!empty($row->settings->background_position)) {
                            $row_css .= 'background-position:' . $row->settings->background_position . ';';
                        }
                    }

                    if (!empty($row->settings->background_color)) {
                        $row_css .= 'background-color:' . $row->settings->background_color . ';';
                    }
                    if (!empty($row->settings->color)) {
                        $row_css .= 'color:' . $row->settings->color . ';';
                    }
                    if (!empty($row->settings->padding)) {
                        $row_css .= 'padding:' . $row->settings->padding . ';';
                    }
                    if (!empty($row->settings->margin)) {
                        $row_css .= 'margin:' . $row->settings->margin . ';';
                    }
                    if ($row_css) {
                        $doc->addStyledeclaration('#' . $id . '{ ' . $row_css . ' }');
                    }
                    //Link Color
                    if (!empty($row->settings->link_color)) {
                        $doc->addStyledeclaration('#' . $id . ' a{color:' . $row->settings->link_color . ';}');
                    }
                    //Link Hover Color
                    if (!empty($row->settings->link_hover_color)) {
                        $doc->addStyledeclaration('#' . $id . ' a:hover{color:' . $row->settings->link_hover_color . ';}');
                    }

                    // set html5 stracture
                    $sematic = (!empty($row->settings->name)) ? strtolower($row->settings->name) : 'section';

                    switch ($sematic) {
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
                        'rowColumns' 		=> $rowColumns,
                    );

                    if (count($row->attr)){
                        //Segment and any HTML5 mark will be available for first level item
                        if ($row_level == 1) {
                            $output .= '<' . $data['sematic'] . ' id="' . $data['id'] . '"' . $data['row_class'] . '>';
                            if ($data['componentArea']) {
                                if (!$data['pagebuilder']) {
                                    $output .= '<div class="container">';
                                }
                            } else {
                                if (!$data['fluidrow']) {
                                    $output .= '<div class="container">';
                                }
                            }
                            $output .= '<div class="row">';
                        }elseif ($row_level > 1){
                            $output .= '<div id="nested-' . $data['id']. '"' . $data['row_class'] . '>';
                        }

                        foreach ($row->attr as $col){
                            //End Responsive Utilities
                            if ($col->settings->column_type){ //Component
                                $getLayout = new JLayoutFile('frontend.conponentarea', $layout_path_carea );
                                $output .= $getLayout->render($col);
                            }
                            else { // Module
                                $getLayout = new JLayoutFile('frontend.modules', $layout_path_module );
                                $output .= $getLayout->render($col);
                            }

                            //Recursive
                            if ( ! empty($col->attr) && count($col->attr)){
                                $output .=self::get_recursive_layout($col->attr, $row_level + 1);
                            }
                        }

                        //Segment and any HTML5 mark will be available for first level item
                        if ($row_level == 1){
                            $output .= '</div>';

                            if ($data['componentArea']){
                                if (!$data['pagebuilder']){
                                    $output .= '</div>';
                                }
                            }
                            else{
                                if (!$data['fluidrow']){
                                    $output .= '</div>';
                                }
                            }
                            $output .= '</' . $data['sematic'] . '>';
                        }elseif ($row_level > 1){
                            $output .= '</div>';
                        }
                    }
                }
            }
        }

        return $output;
    }

    /* Detect component row */
    private static function hasComponent($rowColumns){
        $hasComponent = false;

        foreach ($rowColumns as $key => $column) {
            if ($column->settings->column_type) { /* Component */
                $hasComponent = true;
            }
        }

        return $hasComponent;
    }

    //Get Active Columns
    private static function rowColumns($columns){
        $doc  = JFactory::getDocument();
        $cols = array();

        //Inactive
        $absspan        = 0; //   absence span
        $col_i          = 1;
        $totalPublished = count($columns); // total publish children
        $hasComponent   = false;

        foreach ($columns as &$column) {

            $column->settings->name         = (!empty($column->settings->name)) ? $column->settings->name : 'none_empty';
            $column->settings->column_type  = (!empty($column->settings->column_type)) ? $column->settings->column_type : 0;
            $column->settings->custom_class = (!empty($column->settings->custom_class)) ? $column->settings->custom_class : '';

            if (!$column->settings->column_type) {
                if (!self::countModules($column->settings->name)) {
                    $col_xs_no = (int) self::getColXsNo($column->className);
                    $absspan += $col_xs_no;
                    $totalPublished--;
                }
            }
            else
            {
                $hasComponent = true;
            }
        }

        //Active
        foreach ($columns as &$column) {
            //print_r($column->attr);
            $col_xs_no = (int) self::getColXsNo($column->className);

            if ($column->settings->column_type) {
                $column->className = 'col-sm-' . ($col_xs_no + $absspan) . ' col-md-' . ($col_xs_no + $absspan);
                $cols[]            = $column;
                $col_i++;
            }
            else {

                if (self::countModules($column->settings->name)) {
                    $last_col = ($totalPublished == $col_i) ? $absspan : 0;
                    $col_grid = (int) self::getColXsNo($column->className);
                    if ($hasComponent) {
                        $column->className = 'col-sm-' . $col_grid . ' col-md-' . $col_grid;
                    }
                    else {
                        $column->className = 'col-sm-' . ($col_grid + $last_col) . ' col-md-' . ($col_grid + $last_col);
                    }

                    $cols[] = $column;
                    $col_i++;
                }
            }
        }

        return $cols;
    }

    //Count Modules
    public static function countModules($position){
        $doc = JFactory::getDocument();
        return ($doc->countModules($position) or self::hasFeature($position));
    }

    /**
     * Has feature
     *
     * @param string $position
     */

    public static function hasFeature($position){
        if (in_array($position, self::getInstance()->inPositions)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Add stylesheet
     *
     * @param mixed $sources . string or array
     *
     * @return self
     */
    public static function addCSS($sources, $attribs = array()){
        $template = JFactory::getApplication()->getTemplate();
        $path     = JPATH_THEMES . '/' . $template . '/css/';

        $srcs = array();

        if (is_string($sources)) {
            $sources = explode(',', $sources);
        }
        if (!is_array($sources)) {
            $sources = array($sources);
        }

        foreach ((array) $sources as $source)
            $srcs[] = trim($source);

        foreach ($srcs as $src) {
            if (file_exists($path . $src)) {
                self::getInstance()->document->addStyleSheet(JURI::base(true) . '/templates/' . $template . '/css/' . $src, 'text/css', null, $attribs);
            }
            else {
                if ($src != 'custom.css') {
                    self::getInstance()->document->addStyleSheet($src, 'text/css', null, $attribs);
                }
            }
        }

        return self::getInstance();
    }

    /**
     * Add javascript
     *
     * @param mixed  $sources   . string or array
     * @param string $seperator . default is , (comma)
     *
     * @return self
     */
    public static function addJS($sources, $seperator = ','){
        $srcs = array();

        $template = JFactory::getApplication()->getTemplate();
        $path     = JPATH_THEMES . '/' . $template . '/js/';

        if (is_string($sources)) {
            $sources = explode($seperator, $sources);
        }
        if (!is_array($sources)) {
            $sources = array($sources);
        }

        foreach ((array) $sources as $source)
            $srcs[] = trim($source);

        foreach ($srcs as $src) {
            if (file_exists($path . $src)) {
                self::getInstance()->document->addScript(JURI::base(true) . '/templates/' . $template . '/js/' . $src);
            } else {
                if ($src != 'custom.js')
                {
                    self::getInstance()->document->addScript($src);
                }
            }
        }

        return self::getInstance();
    }

    /**
     * Add Inline Javascript
     *
     * @param mixed $code
     *
     * @return self
     */
    public function addInlineJS($code){
        self::getInstance()->document->addScriptDeclaration($code);
        return self::getInstance();
    }

    /**
     * Add Inline CSS
     *
     * @param mixed $code
     *
     * @return self
     */
    public function addInlineCSS($code){
        self::getInstance()->document->addStyleDeclaration($code);
        return self::getInstance();
    }

    public static function scssInit()
    {
      if (!class_exists('Leafo\ScssPhp\Version', false))
      {
        include_once __DIR__ . '/classes/scss/Base/Range.php';
        include_once __DIR__ . '/classes/scss/Block.php';
        include_once __DIR__ . '/classes/scss/Colors.php';
        include_once __DIR__ . '/classes/scss/Compiler.php';
        include_once __DIR__ . '/classes/scss/Compiler/Environment.php';
        include_once __DIR__ . '/classes/scss/Exception/CompilerException.php';
        include_once __DIR__ . '/classes/scss/Exception/ParserException.php';
        include_once __DIR__ . '/classes/scss/Exception/ServerException.php';
        include_once __DIR__ . '/classes/scss/Formatter.php';
        include_once __DIR__ . '/classes/scss/Formatter/Compact.php';
        include_once __DIR__ . '/classes/scss/Formatter/Compressed.php';
        include_once __DIR__ . '/classes/scss/Formatter/Crunched.php';
        include_once __DIR__ . '/classes/scss/Formatter/Debug.php';
        include_once __DIR__ . '/classes/scss/Formatter/Expanded.php';
        include_once __DIR__ . '/classes/scss/Formatter/Nested.php';
        include_once __DIR__ . '/classes/scss/Formatter/OutputBlock.php';
        include_once __DIR__ . '/classes/scss/Node.php';
        include_once __DIR__ . '/classes/scss/Node/Number.php';
        include_once __DIR__ . '/classes/scss/Parser.php';
        include_once __DIR__ . '/classes/scss/Type.php';
        include_once __DIR__ . '/classes/scss/Util.php';
        include_once __DIR__ . '/classes/scss/Version.php';

        return new Leafo\ScssPhp\Compiler();
      }
    }

    public static function addSCSS($scss, $vars = array(), $css = '')
    {
      jimport('joomla.filesystem.file');
      $scss = \JFile::stripExt($scss);

      if(!empty($css))
      {
        $css = \JFile::stripExt($css) . '.css';
      }
      else
      {
        $css = $scss . '.css';
      }

      $needsCompile = self::needScssCompile($scss, $vars);
      if($needsCompile) {
        $scssInit = self::scssInit();
        $template  = \JFactory::getApplication()->getTemplate();
        $scss_path = JPATH_THEMES . '/' . $template . '/scss';
        $css_path = JPATH_THEMES . '/' . $template . '/css';

        if(file_exists($scss_path . '/'. $scss . '.scss'))
        {
          $out = $css_path . '/' . $css;
          $scssInit->setFormatter('Leafo\ScssPhp\Formatter\Expanded');
          $scssInit->setImportPaths($scss_path);
          if(count($vars))
          {
            $scssInit->setVariables($vars);
          }
          $compiledCss = $scssInit->compile('@import "'. $scss .'.scss"');
          JFile::write($out, $compiledCss);

          $cache_path = \JPATH_CACHE . '/com_templates/templates/' . $template . '/' . $scss . '.scss.cache';
          $scssCache = array();
          $scssCache['imports'] = $scssInit->getParsedFiles();
          $scssCache['vars'] = $scssInit->getVariables();
          JFile::write($cache_path, json_encode($scssCache));
        }
      }

      self::addCSS($css);
    }

    private static function needScssCompile($scss, $existvars = array())
    {
      $template  = \JFactory::getApplication()->getTemplate();
      $cache_path = \JPATH_CACHE . '/com_templates/templates/' . $template . '/' . $scss . '.scss.cache';

      if(file_exists($cache_path))
      {
        $cache_file = json_decode(file_get_contents($cache_path));
        $imports = (isset($cache_file->imports) && $cache_file->imports) ? $cache_file->imports : array();
        $vars = (isset($cache_file->vars) && $cache_file->vars) ? (array) $cache_file->vars : array();

        if(array_diff($vars, $existvars))
        {
          return true;
        }

        if(count($imports))
        {
          foreach ($imports as $import => $mtime)
          {
            if(file_exists($import))
            {
              $existmtime = filemtime($import);
              if($existmtime > $mtime)
              {
                return true;
              }
              else
              {
                return false;
              }
            }
            else
            {
              return true;
            }
          }
        }
        else
        {
          return true;
        }
      }

      return true;
    }

    private static function resetCookie($name){
        if (JRequest::getVar('reset', '', 'get') == 1)
        {
            setcookie($name, '', time() - 3600, '/');
        }
    }

    /**
     * Preset
     *
     */
    public static function Preset(){
        $template = JFactory::getApplication()->getTemplate();
        $name     = $template . '_preset';

        if (isset($_COOKIE[$name])) {
            $current = $_COOKIE[$name];
        }
        else {
            $current = self::getParam('preset');
        }
        return $current;
    }

    public static function PresetParam($name){
        return self::getParam(self::getInstance()->Preset() . $name);
    }

    /**
     * Load Menu
     *
     * @since    1.0
     */
    public static function loadMegaMenu($class = "", $name = ''){
        require_once __DIR__ . '/classes/menu.php';
        return new Helix3Menu($class, $name);
    }


    /**
     * Convert object to array
     *
     */
    public static function object_to_array($obj) {
        if(is_object($obj)) $obj = (array) $obj;
        if(is_array($obj)) {
            $new = array();
            foreach($obj as $key => $val) {
                $new[$key] = self::object_to_array($val);
            }
        }
        else $new = $obj;
        return $new;
    }

    /**
     * Convert object to array
     *
     */
    public static function font_key_search($font, $fonts) {

        foreach ($fonts as $key => $value) {
            if($value['family'] == $font) {
                return $key;
            }
        }

        return 0;
    }

    /**
     * Add Google Fonts
     *
     * @param string $name  . Name of font. Ex: Yanone+Kaffeesatz:400,700,300,200 or Yanone+Kaffeesatz  or Yanone
     *                      Kaffeesatz
     * @param string $field . Applied selector. Ex: h1, h2, #id, .classname
     */
    public static function addGoogleFont($fonts){
        $doc = JFactory::getDocument();
        $webfonts = '';
        $tpl_path = JPATH_BASE . '/templates/' . JFactory::getApplication()->getTemplate() . '/webfonts/webfonts.json';
        $plg_path = JPATH_BASE . '/plugins/system/helixultimate/assets/webfonts/webfonts.json';

        if(file_exists($tpl_path)) {
            $webfonts = JFile::read($tpl_path);
        } else if (file_exists($plg_path)) {
            $webfonts = JFile::read($plg_path);
        }

        //Families
        $families = array();
        foreach ($fonts as $key => $value) {
            $value = json_decode($value);
            if (isset($value->fontWeight) && $value->fontWeight) {
                $families[$value->fontFamily]['weight'][] = $value->fontWeight;
            }

            if (isset($value->fontSubset) && $value->fontSubset) {
                $families[$value->fontFamily]['subset'][] = $value->fontSubset;
            }
        }

        //Selectors
        $selectors = array();
        foreach ($fonts as $key => $value) {
            $value = json_decode($value);

            if (isset($value->fontFamily) && $value->fontFamily) {
                $selectors[$key]['family'] = $value->fontFamily;
            }

            if (isset($value->fontSize) && $value->fontSize) {
                $selectors[$key]['size'] = $value->fontSize;
            }

            if (isset($value->fontWeight) && $value->fontWeight) {
                $selectors[$key]['weight'] = $value->fontWeight;
            }
        }

        //Add Google Font URL
        foreach ($families as $key => $value) {
            $output = str_replace(' ', '+', $key);

            // Weight
            if($webfonts) {
                $fonts_array = self::object_to_array(json_decode($webfonts));
                $font_key = self::font_key_search($key, $fonts_array['items']);
                $weight_array = $fonts_array['items'][$font_key]['variants'];
                $output .= ':' . implode(',', $weight_array);
            } else {
                $weight = array_unique($value['weight']);
                if (isset($weight) && $weight)
                {
                    $output .= ':' . implode(',', $weight);
                }
            }

            // Subset
            $subset = array_unique($value['subset']);
            if (isset($subset) && $subset) {
                $output .= '&amp;subset=' . implode(',', $subset);
            }

            $doc->addStylesheet('//fonts.googleapis.com/css?family=' . $output);
        }

        //Add font to Selector
        foreach ($selectors as $key => $value) {
            if (isset($value['family']) && $value['family']) {
                $output = 'font-family:' . $value['family'] . ', sans-serif; ';

                if (isset($value['size']) && $value['size']) {
                    $output .= 'font-size:' . $value['size'] . 'px; ';
                }

                if (isset($value['weight']) && $value['weight']) {
                    $output .= 'font-weight:' . str_replace('regular', 'normal', $value['weight']) . '; ';
                }

                $selectors = explode(',', $key);

                foreach ($selectors as $selector) {
                    $style = $selector . '{' . $output . '}';
                    $doc->addStyledeclaration($style);
                }
            }
        }
    }

    //Exclude js and return others js
    private static function excludeJS($key, $excludes){
        $match = false;
        if ($excludes) {
            $excludes = explode(',', $excludes);
            foreach ($excludes as $exclude) {
                if (JFile::getName($key) == trim($exclude))
                {
                    $match = true;
                }
            }
        }

        return $match;
    }

    public static function compressJS($excludes = ''){
        //function to compress js files

        require_once(__DIR__ . '/classes/Minifier.php');

        $doc       = JFactory::getDocument();
        $app       = JFactory::getApplication();
        $cachetime = $app->get('cachetime', 15);

        $all_scripts  = $doc->_scripts;
        $cache_path   = JPATH_CACHE . '/com_templates/templates/' . self::getTemplate();
        $scripts      = array();
        $root_url     = JURI::root(true);
        $minifiedCode = '';
        $md5sum       = '';

        //Check all local scripts
        foreach ($all_scripts as $key => $value) {
            $js_file = str_replace($root_url, JPATH_ROOT, $key);

            if (strpos($js_file, JPATH_ROOT) === false) {
                $js_file = JPATH_ROOT . $key;
            }

            if (JFile::exists($js_file)) {
                if (!self::excludeJS($key, $excludes))
                {
                    $scripts[] = $key;
                    $md5sum .= md5($key);
                    $compressed = \JShrink\Minifier::minify(JFile::read($js_file), array('flaggedComments' => false));
                    $minifiedCode .= "/*------ " . JFile::getName($js_file) . " ------*/\n" . $compressed . "\n\n";//add file name to compressed JS

                    unset($doc->_scripts[$key]); //Remove sripts
                }
            }
        }

        //Compress All scripts
        if ($minifiedCode) {
            if (!JFolder::exists($cache_path)) {
                JFolder::create($cache_path, 0755);
            }
            else {
                $file = $cache_path . '/' . md5($md5sum) . '.js';

                if (!JFile::exists($file)) {
                    JFile::write($file, $minifiedCode);
                }
                else {
                    if (filesize($file) == 0 || ((filemtime($file) + $cachetime * 60) < time())) {
                        JFile::write($file, $minifiedCode);
                    }
                }
                $doc->addScript(JURI::base(true) . '/cache/com_templates/templates/' . self::getTemplate() . '/' . md5($md5sum) . '.js');
            }
        }

        return;
    }

    //Compress CSS files
    public static function compressCSS(){
        //function to compress css files

        require_once(__DIR__ . '/classes/cssmin.php');

        $doc             = JFactory::getDocument();
        $app             = JFactory::getApplication();
        $cachetime       = $app->get('cachetime', 15);
        $all_stylesheets = $doc->_styleSheets;
        $cache_path      = JPATH_CACHE . '/com_templates/templates/' . self::getTemplate();
        $stylesheets     = array();
        $root_url        = JURI::root(true);
        $minifiedCode    = '';
        $md5sum          = '';

        //Check all local stylesheets
        foreach ($all_stylesheets as $key => $value) {
            $css_file = str_replace($root_url, JPATH_ROOT, $key);

            if (strpos($css_file, JPATH_ROOT) === false) {
                $css_file = JPATH_ROOT . $key;
            }

            global $absolute_url;
            $absolute_url = $key;//absoulte path of each css file

            if (JFile::exists($css_file)) {
                $stylesheets[] = $key;
                $md5sum .= md5($key);
                $compressed = CSSMinify::process(JFile::read($css_file));

                $fixUrl = preg_replace_callback('/url\(([^\)]*)\)/',
                    function ($matches)
                    {
                        $url = str_replace(array('"', '\''), '', $matches[1]);

                        global $absolute_url;
                        $base = dirname($absolute_url);
                        while (preg_match('/^\.\.\//', $url))
                        {
                            $base = dirname($base);
                            $url  = substr($url, 3);
                        }
                        $url = $base . '/' . $url;

                        return "url('$url')";
                    }, $compressed);

                $minifiedCode .= "/*------ " . JFile::getName($css_file) . " ------*/\n" . $fixUrl . "\n\n";//add file name to compressed css

                unset($doc->_styleSheets[$key]); //Remove scripts
            }
        }

        //Compress All stylesheets
        if ($minifiedCode) {
            if (!JFolder::exists($cache_path)) {
                JFolder::create($cache_path, 0755);
            }
            else {
                $file = $cache_path . '/' . md5($md5sum) . '.css';

                if (!JFile::exists($file)) {
                    JFile::write($file, $minifiedCode);
                }
                else {
                    if (filesize($file) == 0 || ((filemtime($file) + $cachetime * 60) < time())) {
                        JFile::write($file, $minifiedCode);
                    }
                }
                $doc->addStylesheet(JURI::base(true) . '/cache/com_templates/templates/' . self::getTemplate() . '/' . md5($md5sum) . '.css');
            }
        }

        return;
    }


}
