<?php

if (! function_exists('print_row')){
    function print_row($source){
        echo '<pre>';
        print_r($source);
        echo '</pre>';
    }
}

function helixfw_get_template(){

    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    $query->select($db->quoteName(array('template', 'params')));
    $query->from($db->quoteName('#__template_styles'));
    $query->where($db->quoteName('client_id') . ' = '. $db->quote(0));
    $query->where($db->quoteName('home') . ' = '. $db->quote(1));
    $db->setQuery($query);

    return $db->loadObject();
}

/**
 * Update option
 *
 * @param null $option_name
 * @param null $option_value
 * @return bool
 */
function helixfw_update_option($option_name = null, $option_value = null){
    if ($option_name) {
        $template = helixfw_get_template()->template;
        $layoutPath = JPATH_SITE . '/templates/' . $template . '/layout/';
        $option_path = $layoutPath . 'options.json';

        $options = array($option_name => $option_value);
        if (file_exists($option_path)) {
            $options = json_decode(file_get_contents($option_path), true);
            $options[$option_name] = $option_value;
        }
        $options = json_encode($options);

        //Write new options here
        $file = fopen($option_path, 'wb');
        fwrite($file, $options);
        fclose($file);

        return $option_value;
    }
    return false;
}

/**
 * get_option from saved json file
 *
 * @param null $option_name
 * @return bool
 */
function helixfw_get_option($option_name = null){
    if ($option_name) {
        $template = helixfw_get_template()->template;
        $option_path = JPATH_SITE . '/templates/' . $template . '/layout/options.json';

        if (file_exists($option_path)) {
            $options = file_get_contents($option_path);
            $options = json_decode($options, true);
            if (key_exists($option_name, $options)){
                return $options[$option_name];
            }
        }
    }

    return false;
}

function helix4_builder_header(){
    $headers =  helix4_builder_header_data();

    foreach ($headers as $key => $value){
        $callback = $value['callback'];

        if (function_exists($callback)){
            $callback();
        }else{
            trigger_error($callback.'() is not exists', E_USER_WARNING);
        }
    }
}

function helix4_builder_footer(){
    $footers =  helix4_builder_footer_data();

    foreach ($footers as $key => $value){
        $callback = $value['callback'];

        if (function_exists($callback)){
            $callback();
        }else{
            trigger_error($callback.'() does not exists', E_USER_WARNING);
        }
    }
}

if ( ! function_exists('helix4_builder_header_data')){
    function helix4_builder_header_data(){
        $header_presets = array(
            'default'   => array(
                'name' => 'Default Header',
                'callback'  => 'helix4_default_header_callback'
            ),
            'classic'   => array(
                'name' => 'Classic Header',
                'callback'  => 'helix4_classic_header_callback'
            ),
        );

        return $header_presets;
    }
}

if ( ! function_exists('helix4_builder_footer_data')){
    function helix4_builder_footer_data(){
        $footer_presets = array(
            'default'   => array(
                'name' => 'Default Footer',
                'callback'  => 'helix4_default_footer_callback'
            )
        );
        return $footer_presets;
    }
}

function helix4_default_header_callback(){

    echo '<h4>Header 4</h4>';
}

function helix4_classic_header_callback(){
    ?>
    <style type="text/css">
        .classic-header-h4{
            color: green;
        }
    </style>
    <?php
    echo '<h4 class="classic-header-h4">Helix4 Classic Header</h4>';
}

function helix4_default_footer_callback(){
    ?>
    <style type="text/css">
        .classic-header-h4{
            color: orange;
        }

        .default-footer-wrapper{
            background: #4a4a4a;
        }
    </style>

    <div class="default-footer-wrapper">


    </div>
    <?php
    echo '<h4 class="classic-header-h4">Helix4 Default Footer</h4>';
}

function helixfw_header_config(){
    $config = array(
        'type'=>'general',
        'title'=>'',
        'attr'=>array(
            'background_color' => array(
                'type'		=> 'color',
                'title'		=> JText::_('HELIX_SECTION_BACKGROUND_COLOR'),
                'desc'		=> JText::_('HELIX_SECTION_BACKGROUND_COLOR_DESC')
            ),
            'background_image' => array(
                'type'		=> 'media',
                'title'		=> 'Logo',
                'desc'		=> 'Switch Logo',
                'std'		=> '',
            ),
        )
    );


    return $config;
}


function helixfw_footer_config(){
    $config = array(
        'type'=>'general',
        'title'=>'',
        'attr'=>array(
            'color' => array(
                'type'		=> 'color',
                'title'		=> JText::_('HELIX_SECTION_TEXT_COLOR'),
                'desc'		=> JText::_('HELIX_SECTION_TEXT_COLOR_DESC')
            ),
            'footer_logo' => array(
                'type'		=> 'media',
                'title'		=> 'Footer Logo',
                'desc'		=> 'Set logo in your footer for branding.',
                'std'		=> '',
            ),
/*
            'footer_about_us' => array(
                'type'		=> 'text',
                'title'		=> 'Footer About Us',
                'desc'		=> 'Footer About Us short description',
                'std'		=> 'This is about us page',
            ),

            'footer_social_links' => array(
                'type'		=> 'text',
                'title'		=> 'Footer Social Links',
                'desc'		=> htmlspecialchars(json_encode(
                    array(
                        array('url' => '#', 'title' => 'facebook'),
                        array('url' => '#', 'title' => 'twitter'),
                        array('url' => '#', 'title' => 'youtube'),
                        array('url' => '#', 'title' => 'instagram'),
                    )
                )),
                'std'		=> htmlspecialchars(json_encode(
                    array(
                        array('url' => '#', 'title' => 'facebook'),
                        array('url' => '#', 'title' => 'twitter'),
                        array('url' => '#', 'title' => 'youtube'),
                        array('url' => '#', 'title' => 'instagram'),
                    )
                )),
            ),

            'footer_links_one' => array(
                'type'		=> 'text',
                'title'		=> 'Footer Links group one',
                'desc'		=> htmlspecialchars(json_encode(
                    array(
                        array('url' => '#', 'title' => 'Home'),
                        array('url' => '#', 'title' => 'About'),
                        array('url' => '#', 'title' => 'Classes'),
                    )
                )),
                'std'		=> htmlspecialchars(json_encode(
                    array(
                        array('url' => '#', 'title' => 'Home'),
                        array('url' => '#', 'title' => 'About'),
                        array('url' => '#', 'title' => 'Classes'),
                    )
                )),
            ),

            'footer_links_one_heading' => array(
                'type'		=> 'text',
                'title'		=> 'Links goup one heading',
                'desc'		=> '',
                'std'		=> 'Home',
            ),

            'footer_links_two' => array(
                'type'		=> 'text',
                'title'		=> 'Footer Links group Two',
                'desc'		=> htmlspecialchars(json_encode(
                    array(
                        array('url' => '#', 'title' => 'Home'),
                        array('url' => '#', 'title' => 'About'),
                        array('url' => '#', 'title' => 'Classes'),
                    )
                )),
                'std'		=> htmlspecialchars(json_encode(
                    array(
                        array('url' => '#', 'title' => 'Home'),
                        array('url' => '#', 'title' => 'About'),
                        array('url' => '#', 'title' => 'Classes'),
                    )
                )),
            ),
            'footer_links_two_heading' => array(
                'type'		=> 'text',
                'title'		=> 'Links goup two heading',
                'desc'		=> '',
                'std'		=> 'Resources',
            ),
            'footer_links_three' => array(
                'type'		=> 'text',
                'title'		=> 'Footer Links group Three',
                'desc'		=> htmlspecialchars(json_encode(
                    array(
                        array('url' => '#', 'title' => 'Home'),
                        array('url' => '#', 'title' => 'About'),
                        array('url' => '#', 'title' => 'Classes'),
                    )
                )),
                'std'		=> htmlspecialchars(json_encode(
                    array(
                        array('url' => '#', 'title' => 'Home'),
                        array('url' => '#', 'title' => 'About'),
                        array('url' => '#', 'title' => 'Classes'),
                    )
                )),
            ),
            'footer_links_three_heading' => array(
                'type'		=> 'text',
                'title'		=> 'Links goup three heading',
                'desc'		=> '',
                'std'		=> 'Brand',
            ),*/
        )
    );


    return $config;
}

function helixfw_logo_block_config(){
    $config = array(
        'type'=>'general',
        'title'=>'',
        'attr'=>array(

            'footer_logo' => array(
                'type'		=> 'media',
                'title'		=> 'Footer Logo',
                'desc'		=> 'Set logo in your footer for branding.',
                'std'		=> '',
            ),
            'helixfw_module_list' 		=> array(
                'type'		=> 'select',
                'title'		=> 'Select a Module',
                'desc'		=> 'For this block, you can select a module, So it will be rendered here',
                'values'	=> array(
                    '' => "",
                    'header_logo'               => 'Header',
                    'header_menu_links'         => 'Menu Links',
                    'footer_about_us_social'    => 'Footer About Us and Social URL',
                    'footer_block_2'            => 'Footer Block 2',
                    'footer_block_3'            => 'Footer Block 3',
                    'footer_block_4'            => 'Footer Block 4',
                    'header2_block3'            => 'Header 2 block 3',
                ),
                'std'		=> '',
            ),

        )
    );
    return $config;
}


function helixfw_header_variation(){
    $args = array(
        'header1'   => array(
            'name' => 'Header One',
            'blocks' => array(
                array(
                    'title'                 => 'Logo Option',
                    'class'                 => 'span4',
                    'wrap_class'            => 'helixfwLogoWrap',
                    'selector_class'        => 'helixfwLogoOptionLogo',
                    'ajax_output_class'     => 'header-logo-block-wrap',
                ),
                array(
                    'title'                 => 'Menu Option',
                    'class'                 => 'span8',
                    'wrap_class'            => 'helixfwHeaderMenu',
                    'selector_class'        => 'helixfwMenuOptionLogo',
                    'ajax_output_class'     => 'header-menu-block-wrap',
                )

            )
        ),
        'header2'   => array(
            'name' => 'Header Two',
            'blocks' => array(
                array(
                    'title'                 => 'Logo Two Option',
                    'class'                 => 'span4',
                    'wrap_class'            => 'helixfwLogoWrap',
                    'selector_class'        => 'helixfwLogoOptionLogo',
                    'ajax_output_class'     => 'header-logo-block-wrap',
                ),
                array(
                    'title'                 => 'Menu Two Option',
                    'class'                 => 'span4',
                    'wrap_class'            => 'helixfwHeaderMenu',
                    'selector_class'        => 'helixfwMenuOptionLogo',
                    'ajax_output_class'     => 'header-menu-block-wrap',
                ),
                array(
                    'title'                 => 'Menu Two Option',
                    'class'                 => 'span4',
                    'wrap_class'            => 'header-block-3',
                    'selector_class'        => 'block3Selector',
                    'ajax_output_class'     => 'header2-block3-wrap',
                ),

            )
        )
    );

    return $args;
}



function helixfw_footer_variation(){
    $args = array(
        'footer1'   => array(
            'name' => 'Footer One',
            'blocks' => array(
                array(
                    'title'                 => 'Footer Block 1',
                    'class'                 => 'span3',
                    'wrap_class'            => 'footerBlockOneWrap',
                    'selector_class'        => 'helixfwFooterAboutUsSocialOption',
                    'ajax_output_class'     => 'footer-about-us-social-block-wrap',
                ),
                array(
                    'title'                 => 'Footer Block 2',
                    'class'                 => 'span3',
                    'wrap_class'            => 'footerBlockTwoWrap',
                    'selector_class'        => 'footerBlock2',
                    'ajax_output_class'     => 'footer-block-2-block-wrap',
                ),
                array(
                    'title'                 => 'Footer Block 3',
                    'class'                 => 'span3',
                    'wrap_class'            => 'footerBlockThreeWrap',
                    'selector_class'        => 'footerBlock3',
                    'ajax_output_class'     => 'footer-block-3-block-wrap',
                ),
                array(
                    'title'                 => 'Footer Block 4',
                    'class'                 => 'span3',
                    'wrap_class'            => 'footerBlockThreeWrap',
                    'selector_class'        => 'footerBlock4',
                    'ajax_output_class'     => 'footer-block-4-block-wrap',
                ),
            )
        ),
        'footer2'   => array(
            'name' => 'Footer Two',
            'blocks' => array(
                array(
                    'title'                 => 'Footer Block 1',
                    'class'                 => 'span4',
                    'wrap_class'            => 'footerBlockOneWrap',
                    'selector_class'        => 'helixfwFooterAboutUsSocialOption',
                    'ajax_output_class'     => 'footer-about-us-social-block-wrap',
                ),
                array(
                    'title'                 => 'Footer Block 2',
                    'class'                 => 'span4',
                    'wrap_class'            => 'footerBlockTwoWrap',
                    'selector_class'        => 'footerBlock2',
                    'ajax_output_class'     => 'footer-block-2-block-wrap',
                ),
                array(
                    'title'                 => 'Footer Block 3',
                    'class'                 => 'span4',
                    'wrap_class'            => 'footerBlockThreeWrap',
                    'selector_class'        => 'footerBlock3',
                    'ajax_output_class'     => 'footer-block-3-block-wrap',
                ),
            )
        )
    );

    return $args;
}


/**
 * @param null $position
 * @param bool $echo
 * @return string
 */

if ( ! function_exists('render_module_by_position')){
    function render_module_by_position($position = null, $echo = true){
        if ($position){
            // define module position
            jimport( 'joomla.application.module.helper' );

            $user		= JFactory::getUser();
            $groups		= implode(',', $user->getAuthorisedViewLevels());
            $lang 		= JFactory::getLanguage()->getTag();
            $clientId 	= 0;

            $db	= JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('id, title, module, position, content, showtitle, params');
            $query->from('#__modules AS m');
            $query->where('m.published = 1');

            $query->where('m.position = "bottom1"');

            $date = JFactory::getDate();
            $now = $date->toSql();
            $nullDate = $db->getNullDate();
            $query->where('(m.publish_up = '.$db->Quote($nullDate).' OR m.publish_up <= '.$db->Quote($now).')');
            $query->where('(m.publish_down = '.$db->Quote($nullDate).' OR m.publish_down >= '.$db->Quote($now).')');

            $query->where('m.access IN ('.$groups.')');
            $query->where('m.client_id = '. $clientId);

            // Filter by language
            $query->order('position, ordering');

            // Set the query
            $db->setQuery($query);

            $modules = $db->loadObjectList();

            $rendered = '';
            foreach ($modules as $module) { //loop through the array and render their output
                $_options = array( 'style' => 'raw' );
                $rendered .= JModuleHelper::renderModule( $module, $_options );
            }

            if ($echo){
                echo $rendered;
            }else{
                return $rendered;
            }

        }
    }
}

/**
 * @param null $header
 * @param bool $echo
 * @return string
 */

if ( ! function_exists('helixfw_render_header')){
    function helixfw_render_header($header = null, $echo = true){
        $template = $template = helixfw_get_template()->template;
        $path     = JPATH_SITE . '/templates/' . $template . '/layout/inc/headers/';
        $header = $path.$header.'.php';

        $output = '';
        if (file_exists($header)){
            ob_start();
            include $header;
            $output .= ob_get_clean();
        }

        if ($echo){
            echo $output;
            return;
        }
        return $output;
    }
}

if ( ! function_exists('helixfw_render_footer')){
    function helixfw_render_footer($footer = null, $echo = true){
        $template = $template = helixfw_get_template()->template;
        $path     = JPATH_SITE . '/templates/' . $template . '/layout/inc/footers/';
        $footer = $path.$footer.'.php';

        $output = '';
        if (file_exists($footer)){
            ob_start();
            include $footer;
            $output .= ob_get_clean();
        }

        if ($echo){
            echo $output;
            return;
        }
        return $output;
    }
}

/**
 * @param null $directory
 * @return array|null
 */

if ( ! function_exists('helixfw_inc_tpl_lists')){
    function helixfw_inc_tpl_lists($directory = null){
        if ($directory){
            $template = $template = helixfw_get_template()->template;
            $path     = JPATH_SITE . '/templates/' . $template . '/layout/inc/'.$directory;
            $get_headers_files = glob($path.'/*.php');

            $tpl = array();
            if (count($get_headers_files)){
                foreach ($get_headers_files as $file){
                    $get_file_name = str_replace(dirname($file).'/', '', $file);
                    $header_file = str_replace('.php', '', $get_file_name);
                    $file_name = ucwords(str_replace('_', ' ', $header_file));

                    $tpl['files'][] = $get_file_name;
                    $tpl['names'][$header_file] = $file_name;
                }
            }

            return $tpl;
        }

        return null;
    }
}


$GLOBALS['is_administrator'] = false;
$current_screen = str_replace(JPATH_ROOT.'/', '', JPATH_BASE);
if ($current_screen === 'administrator'){
    $GLOBALS['is_administrator'] = true;
}