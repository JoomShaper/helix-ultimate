<?php
global $tpl_layout_data, $is_administrator;
//Checking is current page is administrator page

if ( empty($tpl_layout_data)){
    //This is frontend
    $params = JFactory::getApplication()->getTemplate(true)->params;
    $rows   = json_decode($params->get('layout'));
    $tpl_layout_data = $rows;
}

if ( ! empty($ajaxTplLayoutData)){
    $tpl_layout_data = $ajaxTplLayoutData;
}

$bg_color = '';
$logo = '';

$header_settings = null;

foreach ($tpl_layout_data as $layout_value){
    if ( ! empty($layout_value->header)){
        foreach ($layout_value->header as $header_settings);

        if ( ! empty($header_settings->settings->background_image)){
            $logo = $header_settings->settings->background_image;
        }
        if ( ! empty($header_settings->settings->background_color)){
            $bg_color = $header_settings->settings->background_color;
        }

    }
}

$rowSettingsData = null;
if ($header_settings && $is_administrator){
    if ( ! class_exists('RowColumnSettings')){
        require_once JPATH_PLUGINS.'/system/helix3/layout/layout-settings/row-column-settings.php';
    }
    $rowSettingsData = RowColumnSettings::getSettings($header_settings->settings);
}

//die(str_replace(JPATH_ROOT.'/', '', JPATH_BASE));
?>

<div class="helix-4-header-area" style="background-color: <?php echo $bg_color ?> ;" <?php echo $rowSettingsData ?> >
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="col-md-3">

                <div class="header-logo-wrap">
                    <a href="#" class="helix-4-logo">
                        <?php
                        if ( ! empty($logo)){
                            echo '<img src="'.JURI::root(true).'/'.$logo.'" alt="Helix-4">';
                        }else{
                            echo '<img src="https://image.ibb.co/cwGhUQ/helix_4_logo.png" alt="Helix-4">';
                        }
                        ?>

                    </a>
                </div>

            </div>
            <div class="col-md-9">
                <nav class="navigation">
                    <ul class="helix-4-menu">
                        <li><a href="#">Home</a></li>
                        <li><a href="#">About</a></li>
                        <li><a href="#">Classes</a></li>
                        <li><a href="#">Trainers</a></li>
                        <li><a href="#">Pricing</a></li>
                        <li><a href="#">Events</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>