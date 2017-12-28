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

$footer_settings = null;
foreach ($tpl_layout_data as $layout_value){
    if ( ! empty($layout_value->footer)){
        foreach ($layout_value->footer as $footer_settings);

        if ( ! empty($footer_settings->settings->footer_logo)){
            $logo = $footer_settings->settings->footer_logo;
        }
        if ( ! empty($footer_settings->settings->color)){
            $bg_color = $footer_settings->settings->color;
        }

    }
}

$rowSettingsData = null;
if ($footer_settings && $is_administrator){
    if ( ! class_exists('RowColumnSettings')){
        require_once JPATH_PLUGINS.'/system/helix3/layout/layout-settings/row-column-settings.php';
    }
    $rowSettingsData = RowColumnSettings::getSettings($footer_settings->settings);
}
?>




<footer class="helix-4-footer-area container-fluid" style="background-color: <?php echo $bg_color ?> ;" <?php echo $rowSettingsData ?> >
    <div class="row-fluid">


        <div class="col-md-3 footer-widget">
            <div class="clearfix">
                <h4 class="widget-title">Brand</h4>
                <ul class="footer-link">
                    <li><a href="#">Privacy</a></li>
                    <li><a href="#">Shop</a></li>
                    <li><a href="#">Testimonials</a></li>
                    <li><a href="#">Brand</a></li>
                    <li><a href="#">Advertise</a></li>
                </ul>
            </div>
        </div>


        <div class="col-md-3 footer-widget">
            <div class="clearfix">
                <h4 class="widget-title">Resources</h4>
                <ul class="footer-link">
                    <li><a href="#">Support</a></li>
                    <li><a href="#">Contact</a></li>
                    <li><a href="#">Privacy & terms</a></li>
                    <li><a href="#">Guidelines</a></li>
                    <li><a href="#">Integrations</a></li>
                </ul>
            </div>
        </div>

        <div class="col-md-3 footer-widget">
            <div class="clearfix">
                <h4 class="widget-title">Company</h4>
                <ul class="footer-link">
                    <li><a href="#">About</a></li>
                    <li><a href="#">Customers</a></li>
                    <li><a href="#">Jobs</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Press</a></li>
                </ul>
            </div>
        </div>

        <div class="col-md-3 footer-widget">
            <div class="clearfix">
                <a href="#" class="footer-logo">
                    <?php
                    if ( ! empty($logo)){
                        echo '<img src="'.JURI::root(true).'/'.$logo.'" alt="Helix-4">';
                    }else{
                        echo '<img src="https://image.ibb.co/cwGhUQ/helix_4_logo.png" alt="Helix-4">';
                    }
                    ?>
                </a>
                <p>Design ideate hacker. Venture <br>capital.</p>
                <div class="helix-4-footer-social">
                    <a href="#" class="fa fa-facebook"></a>
                    <a href="#" class="fa fa-twitter"></a>
                    <a href="#" class="fa fa-linkedin"></a>
                    <a href="#" class="fa fa-pinterest"></a>
                </div>
            </div>
        </div>


    </div>
    <div class="row-fluid">
        <div class="col-md-12 text-center copyright-text">
            <p> &copy; <?php echo date('Y') ?>. Designed with <i class="fa fa-heart"></i> by JoomShaper</p>
        </div>
    </div>
</footer>