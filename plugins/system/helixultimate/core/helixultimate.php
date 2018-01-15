<?php
/**
 * @package   Helix3 Framework
 * @author    JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2015 JoomShaper
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die ('resticted aceess');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.filter.filteroutput');

class HelixUltimate
{
    public $params;

    private $doc;

    public $app;

    public $template;

    public $template_folder_url;

    private $in_positions = array();

    public $loadFeature = array();

    public function __construct()
    {
        $this->app      = JFactory::getApplication();
        $this->doc      = JFactory::getDocument();
        $this->template = $this->app->getTemplate(true);
        $this->params   = $this->template->params;
        $this->get_template_uri();
    }

    public function bodyClass($class = '')
    {
        $language  = $this->doc->language;
        $direction = $this->doc->direction;
        $option    = str_replace('_', '-', $this->app->input->getCmd('option', ''));
        $view      = $this->app->input->getCmd('view', '');
        $layout    = $this->app->input->getCmd('layout', '');
        $task      = $this->app->input->getCmd('task', '');
        $itemid    = $this->app->input->getCmd('Itemid', '');
        $sitename  = $this->app->get('sitename');

        if ($view == 'modules')
        {
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

    public function add_css($css_files = '', $options = array(), $attribs = array())
    {
        $files = array(
                'resource' => $css_files,
                'options'  => $options,
                'attribs'  => $attribs
            );

        $this->put_css_js_file($files,'css');
    }

    public function add_js($js_files = '', $options = array(), $attribs = array())
    {
        $files = array(
                'resource' => $js_files,
                'options'  => $options,
                'attribs'  => $attribs
            );

        $this->put_css_js_file($files,'js');
    }


    private function put_css_js_file($files = array(), $file_type = '')
    {
        $files_folder_path = JPATH_THEMES . '/' . $this->template->template . '/'. $file_type .'/';
        $file_list = explode(',',$files['resource']);

        foreach( $file_list as $file )
        {
            if (empty($file)) continue;
            $file = trim($file);
            $file_path = $files_folder_path . $file;

            if (JFile::exists($file_path))
            {
                $file_url = JURI::base(true) . '/templates/' . $this->template->template . '/'. $file_type .'/' . $file;
            }
            else if (JFile::exists($file))
            {
                $file_url = $file;
            }
            else
            {
                continue;
            }

            if($file_type == 'js')
            {
                $this->doc->addScript($file_url, $files['options'], $files['attribs']);
            }
            else
            {
                $this->doc->addStyleSheet($file_url, $files['options'], $files['attribs']);
            }
        }
    }

    private function get_template_uri()
    {
        $this->template_folder_url = JURI::base(true) . '/templates/' . $this->template->template;
    }

    private function include_features()
    {
        $folder_path     = JPATH_THEMES . '/' . $this->template->template . '/features';

        if (JFolder::exists($folder_path))
        {
            $files = JFolder::files($folder_path, '.php');

            if (count($files))
            {
                foreach ($files as $key => $file)
                {
                    include_once $folder_path . '/' . $file;

                    $file_name = JFile::stripExt($file);
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

    public function render_layout()
    {
        $this->add_css('custom.css');
        $this->add_js('custom.js');
        $this->include_features();

        $layout = ($this->params->get('layout'))? $this->params->get('layout') : [];
        $rows   = json_decode($layout);

        if (empty($rows))
        {
            $layout_file = JPATH_SITE . '/templates/' . $this->template->template . '/layout/default.json';
            if (!JFile::exists($layout_file))
            {
                die('Default Layout file is not exists! Please goto to template manager and create a new layout first.');
            }
            $layout_data = json_decode(JFile::read($layout_file));
            $rows = $layout_data->layout;
        }

        $output = $this->get_recursive_layout($rows);

        echo $output;
    }

    private function get_recursive_layout($rows = array())
    {
        if(empty($rows) || !is_array($rows))
        {
            return;
        }

        $option      = $this->app->input->getCmd('option', '');
        $view        = $this->app->input->getCmd('view', '');
        $pagebuilder = false;
        $output = '';

        if ($option == 'com_sppagebuilder')
        {
            $pagebuilder = true;
        }

        $themepath      = JPATH_THEMES . '/' . $this->template->template;
        $carea_file     = $themepath . '/html/layouts/helixultimate/frontend/conponentarea.php';
        $module_file    = $themepath . '/html/layouts/helixultimate/frontend/modules.php';
        $lyt_thm_path   = $themepath . '/html/layouts/helixultimate/';

        $layout_path_carea  = (file_exists($carea_file)) ? $lyt_thm_path : JPATH_ROOT .'/plugins/system/helixultimate/layouts';
        $layout_path_module = (file_exists($module_file)) ? $lyt_thm_path : JPATH_ROOT .'/plugins/system/helixultimate/layouts';

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

                $id = (isset($modified_row->settings->name) && $modified_row->settings->name) ? 'sp-section-' . ($key + 1) : 'sp-' . JFilterOutput::stringURLSafe($modified_row->settings->name);
                $row_class = $this->build_row_class($modified_row->settings);
                $this->add_row_styles($modified_row->settings, $id);
                $sematic = (isset($modified_row->settings->name) && $modified_row->settings->name) ? strtolower($modified_row->settings->name) : 'section';

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
                    'rowColumns' 		=> $columns,
                    'loadFeature'       => $this->loadFeature
                );

                $layout_path  = JPATH_ROOT .'/plugins/system/helixultimate/layouts';
                $getLayout = new JLayoutFile('frontend.generate', $layout_path );
                $output .= $getLayout->render($data);
            }
        }

        return $output;
    }

    private function get_current_row($row)
    {
        $inactive_col   = 0; //absence span
        $has_component  = false;

        foreach ($row->attr as $key => &$column)
        {
            if (!$column->settings->column_type)
            {
                if (!$this->count_modules($column->settings->name))
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
            $col_grid_size = $column->settings->grid_size;
            if (!$has_component && end($row->attr) === $column)
            {
                $col_grid_size = $col_grid_size + $inactive_col;
            }

            if ($column->settings->column_type)
            {
                $col_grid_size = $col_grid_size + $inactive_col;
                $column->className = 'col-md-' . $col_grid_size . ' col-lg-' . $col_grid_size;
            }
            else
            {
                $column->className = 'col-md-' . $col_grid_size . ' col-lg-' . $col_grid_size;
            }
        }

        return $row;
    }

    private function add_row_styles($options, $id)
    {
        $row_css = '';

        if (isset($options->background_image) && $options->background_image)
        {
            $row_css .= 'background-image:url("' . JURI::base(true) . '/' . $options->background_image . '");';
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
            $doc->addStyledeclaration('#' . $id . '{ ' . $row_css . ' }');
        }


        if (isset($options->link_color) && $options->link_color)
        {
            $doc->addStyledeclaration('#' . $id . ' a{color:' . $options->link_color . ';}');
        }

        if (isset($options->link_hover_color) && $options->link_hover_color) {
            $doc->addStyledeclaration('#' . $id . ' a:hover{color:' . $options->link_hover_color . ';}');
        }
    }

    private function build_row_class($options)
    {
        $row_class = '';
        if (isset($options->custom_class) && $options->custom_class)
        {
            $row_class .= $options->custom_class;
        }

        if (isset($options->hidden_xs) && $options->hidden_xs)
        {
            $row_class .= ' hidden-xs';
        }

        if (isset($options->hidden_sm) && $options->hidden_sm)
        {
            $row_class .= ' hidden-sm';
        }

        if (isset($options->hidden_md) && $options->hidden_md)
        {
            $row_class .= ' hidden-md';
        }


        if($row_class)
        {
            $row_class = 'class="' . $row_class . '"';
        }

        return $row_class;
    }

    public function count_modules($position)
    {
        return ($this->doc->countModules($position) or $this->has_feature($position));
    }

    private function has_feature($position)
    {
        if (in_array($position, $this->in_positions))
        {
            return true;
        }
        return false;
    }


    /**
     * Add Inline Javascript
     *
     * @param mixed $code
     *
     * @return self
     */
    public function addInlineJS($code){
        $this->doc->addScriptDeclaration($code);
    }

    /**
     * Add Inline CSS
     *
     * @param mixed $code
     *
     * @return self
     */
    public function addInlineCSS($code){
        $this->doc->addStyleDeclaration($code);
    }

    public function scssInit()
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

    public function addSCSS($scss, $vars = array(), $css = '')
    {
        $scss = JFile::stripExt($scss);

        if(!empty($css))
        {
            $css = JFile::stripExt($css) . '.css';
        }
        else
        {
            $css = $scss . '.css';
        }

        $needsCompile = $this->needScssCompile($scss, $vars);
        if ($needsCompile)
        {
            $scssInit = $this->scssInit();
            $template  = JFactory::getApplication()->getTemplate();
            $scss_path = JPATH_THEMES . '/' . $template . '/scss';
            $css_path = JPATH_THEMES . '/' . $template . '/css';

            if (file_exists($scss_path . '/'. $scss . '.scss'))
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

        $this->add_css($css);
    }

    private function needScssCompile($scss, $existvars = array())
    {
      $cache_path = JPATH_CACHE . '/com_templates/templates/' . $this->template->template . '/' . $scss . '.scss.cache';

      $return = false;

      if(file_exists($cache_path))
      {
        $cache_file = json_decode(file_get_contents($cache_path));
        $imports = (isset($cache_file->imports) && $cache_file->imports) ? $cache_file->imports : array();
        $vars = (isset($cache_file->vars) && $cache_file->vars) ? (array) $cache_file->vars : array();

        if(array_diff($vars, $existvars))
        {
          $return = true;
        }

        if(count($imports))
        {
          foreach ($imports as $import => $mtime)
          {
            if(file_exists($import))
            {
              $existmtime = filemtime($import);
              if($existmtime != $mtime)
              {
                $return = true;
              }
            }
            else
            {
              $return = true;
            }
          }
        }
        else
        {
          $return = true;
        }
      } else {
        $return = true;
      }

      return $return;
    }

    private static function resetCookie($name){
        if (JRequest::getVar('reset', '', 'get') == 1)
        {
            setcookie($name, '', time() - 3600, '/');
        }
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
    public function addGoogleFont($fonts){
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
    private function excludeJS($key, $excludes){
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

    public function compressJS($excludes = ''){
        //function to compress js files

        require_once(__DIR__ . '/classes/Minifier.php');

        $doc       = JFactory::getDocument();
        $app       = JFactory::getApplication();
        $cachetime = $app->get('cachetime', 15);

        $all_scripts  = $doc->_scripts;
        $cache_path   = JPATH_CACHE . '/com_templates/templates/' . $this->template->template;
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
                if (!$this->excludeJS($key, $excludes))
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
                $doc->addScript(JURI::base(true) . '/cache/com_templates/templates/' . $this->template->template . '/' . md5($md5sum) . '.js');
            }
        }

        return;
    }

    //Compress CSS files
    public function compressCSS(){
        //function to compress css files

        require_once(__DIR__ . '/classes/cssmin.php');

        $doc             = JFactory::getDocument();
        $app             = JFactory::getApplication();
        $cachetime       = $app->get('cachetime', 15);
        $all_stylesheets = $doc->_styleSheets;
        $cache_path      = JPATH_CACHE . '/com_templates/templates/' . $this->template->template;
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
                $doc->addStylesheet(JURI::base(true) . '/cache/com_templates/templates/' . $this->template->template . '/' . md5($md5sum) . '.css');
            }
        }

        return;
    }


}
