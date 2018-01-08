<?php
/**
 * @package Helix3 Framework
 * Template Name - Shaper Helix3
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2015 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
//no direct accees
defined('_JEXEC') or die('resticted aceess');

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
    $this->helixUltimate = helixUltimate::getInstance();
} else {
    die('Please install and activate helix plugin');
}

//Coming Soon
if ($this->helixUltimate->getParam('comingsoon_mode'))
    header("Location: " . $this->baseUrl . "?tmpl=comingsoon");

//Class Classes
$body_classes = '';
if ($this->helixUltimate->getParam('sticky_header')) {
    $body_classes .= ' sticky-header';
}

$body_classes .= ($this->helixUltimate->getParam('boxed_layout', 0)) ? ' layout-boxed' : ' layout-fluid';

if (isset($menu) && $menu) {
    if ($menu->params->get('pageclass_sfx')) {
        $body_classes .= ' ' . $menu->params->get('pageclass_sfx');
    }
}

//Body Background Image
if ($bg_image = $this->helixUltimate->getParam('body_bg_image')) {

    $body_style = 'background-image: url(' . JURI::base(true) . '/' . $bg_image . ');';
    $body_style .= 'background-repeat: ' . $this->helixUltimate->getParam('body_bg_repeat') . ';';
    $body_style .= 'background-size: ' . $this->helixUltimate->getParam('body_bg_size') . ';';
    $body_style .= 'background-attachment: ' . $this->helixUltimate->getParam('body_bg_attachment') . ';';
    $body_style .= 'background-position: ' . $this->helixUltimate->getParam('body_bg_position') . ';';
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

$this->helixUltimate->addGoogleFont($webfonts);

//Custom CSS
if ($custom_css = $this->helixUltimate->getParam('custom_css')) {
    $doc->addStyledeclaration($custom_css);
}

//Custom JS
if ($custom_js = $this->helixUltimate->getParam('custom_js')) {
    $doc->addScriptdeclaration($custom_js);
}

//preloader & goto top
$doc->addScriptdeclaration("\nvar sp_preloader = '" . $this->params->get('preloader') . "';\n");
$doc->addScriptdeclaration("\nvar sp_gotop = '" . $this->params->get('goto_top') . "';\n");
$doc->addScriptdeclaration("\nvar sp_offanimation = '" . $this->params->get('offcanvas_animation') . "';\n");


$doc->addStyleSheet($this->helixUltimate->getTemplateUri().'/layout/inc/header_footer.css');
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" amp>
<head>
    <meta charset="utf-8">
    <script async src="https://cdn.ampproject.org/v0.js"></script>
    <link rel="canonical" href="<?php echo JUri::current(); ?>">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php
    if ($favicon = $this->helixUltimate->getParam('favicon')) {
        $doc->addFavicon(JURI::base(true) . '/' . $favicon);
    } else {
        $doc->addFavicon($this->helixUltimate->getTemplateUri() . '/images/favicon.ico');
    }
    ?>
    <!-- head -->
    <jdoc:include type="head" />
    <?php
    $megabgcolor = ($this->helixUltimate->PresetParam('_megabg')) ? $this->helixUltimate->PresetParam('_megabg') : '#ffffff';
    $megabgtx = ($this->helixUltimate->PresetParam('_megatx')) ? $this->helixUltimate->PresetParam('_megatx') : '#333333';

    $preloader_bg = ($this->helixUltimate->getParam('preloader_bg')) ? $this->helixUltimate->getParam('preloader_bg') : '#f5f5f5';
    $preloader_tx = ($this->helixUltimate->getParam('preloader_tx')) ? $this->helixUltimate->getParam('preloader_tx') : '#f5f5f5';
    // echo $this->params->get('major_color'); die;
    // load css, less and js
    $this->helixUltimate->addCSS('bootstrap.min.css, font-awesome.min.css') // CSS Files
    ->addJS('popper.min.js, bootstrap.min.js, jquery.sticky.js, main.js'); // JS Files

    $scssVars = array(
      'preset' => $this->helixUltimate->Preset(),
      'bg_color' => $this->params->get('bg_color'),
      'text_color' => $this->params->get('major_color'),
      'major_color' => $this->params->get('major_color'),
      'megabg_color' => $megabgcolor,
      'megatx_color' => $megabgtx,
      'preloader_bg' => $preloader_bg,
      'preloader_tx' => $preloader_tx
    );

    $this->helixUltimate->addSCSS('theme', $scssVars, 'template');
    $this->helixUltimate->addSCSS('presets', $scssVars, 'presets/' . $this->helixUltimate->Preset());

    //Before Head
    if ($before_head = $this->helixUltimate->getParam('before_head')) {
        echo $before_head . "\n";
    }
    ?>
</head>
<body class="<?php echo $this->helixUltimate->bodyClass($body_classes); ?> off-canvas-menu-init">


<?php

$helperFile = '/plugins/system/helixultimate/layout/settings/helpers.php';
require_once JPATH_BASE.$helperFile;
$selected_header = helixfw_get_option('selected_header');
if ( ! empty($selected_header)){
    helixfw_render_header($selected_header);
}
?>

<div class="body-wrapper">
    <div class="body-innerwrapper">
        <?php $this->helixUltimate->generatelayout(); ?>
    </div> <!-- /.body-innerwrapper -->
</div> <!-- /.body-innerwrapper -->

<?php

$selected_footer = helixfw_get_option('selected_footer');
if ( ! empty($selected_footer)){
    helixfw_render_footer($selected_footer);
}

?>


<!-- Off Canvas Menu -->
<div class="offcanvas-menu">
    <a href="#" class="close-offcanvas"><i class="fa fa-remove"></i></a>
    <div class="offcanvas-inner">
        <?php if ($this->helixUltimate->countModules('offcanvas')) { ?>
            <jdoc:include type="modules" name="offcanvas" style="sp_xhtml" />
        <?php } else { ?>
            <p class="alert alert-warning">
                <?php echo JText::_('HELIX_NO_MODULE_OFFCANVAS'); ?>
            </p>
        <?php } ?>
    </div> <!-- /.offcanvas-inner -->
</div> <!-- /.offcanvas-menu -->

<?php
if ($this->params->get('compress_css')) {
    $this->helixUltimate->compressCSS();
}

$tempOption    = $app->input->get('option');
// $tempView       = $app->input->get('view');

if ( $this->params->get('compress_js') && $tempOption != 'com_config' ) {
    $this->helixUltimate->compressJS($this->params->get('exclude_js'));
}

//before body
if ($before_body = $this->helixUltimate->getParam('before_body')) {
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
