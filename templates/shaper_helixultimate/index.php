<?php
/**
 * @package Helix3 Framework
 * Template Name - Shaper Helix3
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2015 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

defined('_JEXEC') or die('Restricted access');

$doc = JFactory::getDocument();
$app = JFactory::getApplication();
$menu = $app->getMenu()->getActive();

JHtml::_('jquery.framework');
JHtml::_('bootstrap.framework'); //Force load Bootstrap
unset($doc->_scripts[$this->baseurl . '/media/jui/js/bootstrap.min.js']); // Remove joomla core bootstrap
//Load Helix
$helix_path = JPATH_PLUGINS . '/system/helixultimate/core/helixultimate.php';

if (file_exists($helix_path)) {
    require_once($helix_path);
    $theme = new helixUltimate;
} else {
    die('Please install and activate helix plugin');
}

//Coming Soon
if ($this->params->get('comingsoon_mode'))
{
  header("Location: " . $this->baseUrl . "?tmpl=comingsoon");
}

//Class Classes
$body_classes = '';
if ($this->params->get('sticky_header')) {
    $body_classes .= ' sticky-header';
}

$body_classes .= ($this->params->get('boxed_layout', 0)) ? ' layout-boxed' : ' layout-fluid';

if (isset($menu) && $menu) {
    if ($menu->params->get('pageclass_sfx')) {
        $body_classes .= ' ' . $menu->params->get('pageclass_sfx');
    }
}

// Offcanvas
$body_classes .= ' offcanvas-init offcanvs-position-' . $this->params->get('offcanvas_position', 'right');

//Body Background Image
if ($bg_image = $this->params->get('body_bg_image')) {

    $body_style = 'background-image: url(' . JURI::base(true) . '/' . $bg_image . ');';
    $body_style .= 'background-repeat: ' . $this->params->get('body_bg_repeat') . ';';
    $body_style .= 'background-size: ' . $this->params->get('body_bg_size') . ';';
    $body_style .= 'background-attachment: ' . $this->params->get('body_bg_attachment') . ';';
    $body_style .= 'background-position: ' . $this->params->get('body_bg_position') . ';';
    $body_style = 'body.site {' . $body_style . '}';

    $doc->addStyledeclaration($body_style);
}

//Body Font
$webfonts = array();

if ($this->params->get('enable_body_font')) {
    $webfonts['body'] = $this->params->get('body_font');
}

//Heading1 Font
if ($this->params->get('enable_h1_font')) {
    $webfonts['h1'] = $this->params->get('h1_font');
}

//Heading2 Font
if ($this->params->get('enable_h2_font')) {
    $webfonts['h2'] = $this->params->get('h2_font');
}

//Heading3 Font
if ($this->params->get('enable_h3_font')) {
    $webfonts['h3'] = $this->params->get('h3_font');
}

//Heading4 Font
if ($this->params->get('enable_h4_font')) {
    $webfonts['h4'] = $this->params->get('h4_font');
}

//Heading5 Font
if ($this->params->get('enable_h5_font')) {
    $webfonts['h5'] = $this->params->get('h5_font');
}

//Heading6 Font
if ($this->params->get('enable_h6_font')) {
    $webfonts['h6'] = $this->params->get('h6_font');
}

//Navigation Font
if ($this->params->get('enable_navigation_font')) {
    $webfonts['.sp-megamenu-parent'] = $this->params->get('navigation_font');
}

//Custom Font
if ($this->params->get('enable_custom_font') && $this->params->get('custom_font_selectors')) {
    $webfonts[$this->params->get('custom_font_selectors')] = $this->params->get('custom_font');
}

$theme->addGoogleFont($webfonts);

//Custom CSS
if ($custom_css = $this->params->get('custom_css')) {
    $doc->addStyledeclaration($custom_css);
}

//Custom JS
if ($custom_js = $this->params->get('custom_js')) {
    $doc->addScriptdeclaration($custom_js);
}

//preloader & goto top
$doc->addScriptdeclaration("\nvar sp_gotop = '" . $this->params->get('goto_top') . "';\n");
$doc->addScriptdeclaration("\nvar sp_offanimation = '" . $this->params->get('offcanvas_animation') . "';\n");
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
    <link rel="canonical" href="<?php echo JUri::current(); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php
    if ($favicon = $this->params->get('favicon')) {
        $doc->addFavicon(JURI::base(true) . '/' . $favicon);
    } else {
        $doc->addFavicon($theme->template_folder_url . '/images/favicon.ico');
    }
    ?>
    <!-- head -->
    <jdoc:include type="head" />
    <?php
    // load css, less and js
    $theme->add_css('bootstrap.min.css, font-awesome.min.css'); // CSS Files
    $theme->add_js('popper.min.js, bootstrap.min.js, jquery.sticky.js, main.js'); // JS Files

    $scssVars = array(
        'preset' => $this->params->get('preset', 'preset1'),
        'header_height' => $this->params->get('header_height', '60px'),
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
        'topbar_text_color' => $this->params->get('topbar_text_color')
    );

    $theme->addSCSS('master', $scssVars, 'template');
    $theme->addSCSS('presets', $scssVars, 'presets/' . $this->params->get('preset', 'preset1'));

    //Before Head
    if ($before_head = $this->params->get('before_head'))
    {
        echo $before_head . "\n";
    }
    ?>
</head>
<body class="<?php echo $theme->bodyClass($body_classes); ?>">
    <?php if($this->params->get('preloader')) : ?>
        <div class="sp-preloader"><div></div></div>
    <?php endif; ?>

    <div class="body-wrapper">
        <div class="body-innerwrapper">
            <?php $theme->render_layout(); ?>
        </div>
    </div>

    <!-- Off Canvas Menu -->
    <div class="offcanvas-overlay"></div>
    <div class="offcanvas-menu">
        <a href="#" class="close-offcanvas"><span class="fa fa-remove"></span></a>
        <div class="offcanvas-inner">
            <?php if ($theme->count_modules('offcanvas')) { ?>
                <jdoc:include type="modules" name="offcanvas" style="sp_xhtml" />
            <?php } else { ?>
                <p class="alert alert-warning">
                    <?php echo JText::_('HELIX_NO_MODULE_OFFCANVAS'); ?>
                </p>
            <?php } ?>
        </div>
    </div>

    <?php
    if ($this->params->get('compress_css'))
    {
        $theme->compressCSS();
    }

    $tempOption    = $app->input->get('option');

    if ( $this->params->get('compress_js') && $tempOption != 'com_config' )
    {
        $theme->compressJS($this->params->get('exclude_js'));
    }

    //before body
    if ($before_body = $this->params->get('before_body'))
    {
        echo $before_body . "\n";
    } ?>

    <jdoc:include type="modules" name="debug" />
    <!-- Preloader -->
    <jdoc:include type="modules" name="helixpreloader" />
    <!-- Go to top -->
    <?php if ($this->params->get('goto_top')) { ?>
        <a href="javascript:void(0)" class="scrollup">&nbsp;</a>
    <?php } ?>

</body>
</html>
