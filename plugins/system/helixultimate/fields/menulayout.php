<?php
/**
 * @package Helix3 Framework
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2015 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

//no direct accees
defined ('_JEXEC') or die ('resticted aceess');

$current_menu_id = $this->form->getValue('id');
$menu_items = menuItems();
$JMenuSite = new JMenuSite;


function create_col_menu($current_menu_id){
    $items = menuItems();
    $menus = new JMenuSite;

    if (isset($items[$current_menu_id])) {
        $items = $items[$current_menu_id];
        foreach ($items as $key => $item_id) {
            $html  ='<div class="widget" data-mod_id="'.$item_id.'">';
            $html .= '<div class="widget-top">';
            $html .= '<div class="widget-title"> <h3>'.$menus->getItem($item_id)->title.'</h3> </div>';
            $html .= '</div>';
            $html .= '</div>';

            echo $html;
        }
    }
}

function unique_menu_items($current_menu_id, $layout){
    $saved_menu_items = array();
    if (! empty($layout) && count($layout)) {
        foreach ($layout as $key => $row) {
            if (! empty($row->attr) && count($row->attr)) {
                foreach ($row->attr as $col_key => $col) {
                    if ( ! empty($col->items) && count($col->items)){
                        foreach ($col->items as $item){
                            if ($item->type === 'menu_item'){
                                $saved_menu_items[] = $item->item_id;
                            }
                        }
                    }

                }
            }
        }
    }

    $items = menuItems();
    $menus = new JMenuSite;

    $unique_item_id = array();
    if (isset($items[$current_menu_id])) {
        $items = $items[$current_menu_id];
        foreach ($items as $key => $item_id) {

            if ( ! in_array($item_id,$saved_menu_items)){
                $unique_item_id[] = $item_id;
            }

        }
    }
    return $unique_item_id;
}

function create_menu($current_menu_id)
{
    $items = menuItems();
    $menus = new JMenuSite;

    if (isset($items[$current_menu_id]))
    {
        $item = $items[$current_menu_id];
        foreach ($item as $key => $item_id)
        {
            echo '<li>';
            echo $menus->getItem($item_id)->title;
            echo '</li>';
        }
    }
}

function menuItems()
{
    $menus = new JMenuSite;
    $menus = $menus->getMenu();
    $new = array();
    foreach ($menus as $item) {
        $new[$item->parent_id][] = $item->id;
    }
    return $new;
}

function getModuleNameId($id = 'all')
{
    $db = JFactory::getDBO();

    if ($id == 'all') {
        $query = 'SELECT id, title FROM `#__modules` WHERE ( `published` !=-2 AND `published` !=0 ) AND client_id = 0';
    } else {
        $query = 'SELECT id, title FROM `#__modules` WHERE ( `published` !=-2 AND `published` !=0 ) AND id = ' . $id;
    }

    $db->setQuery($query);

    return $db->loadObjectList();
}

$modules = getModuleNameId();
?>

<?php
$menu_width = 600;
$align = 'right';
$layout = '';

if (isset($menu_data->width))
{
    $menu_width = $menu_data->width;
}

if (isset($menu_data->menuAlign))
{
    $align = $menu_data->menuAlign;
}

if (isset($menu_data->layout))
{
    $layout = $menu_data->layout;
}
?>

<?php
$items = menuItems();
$item = array();
if (isset($items[$current_menu_id]) && !empty($items[$current_menu_id])) {
    $item = $items[$current_menu_id];
}

$menuItems = new JMenuSite;

$no_child = true;
$count = 0;
$x_key = 0;
$y_key = 0;
$check_child = 0;
$item_array = array();

foreach ($item as $key => $id)
{
    $status = 0;
    if (isset($items[$id]) && is_array($items[$id])) {
        $no_child = false;
        $count = $count + 1;
        $check_child = $check_child+1;
        $status = 1;
    }

    if ($check_child === 2) {
        $y_key = 0;
        $x_key = $x_key + 1;
        $check_child = 1;
    }

    $item_array[$x_key][$y_key] = array($id,$status);
    $y_key = $y_key + 1;
}

if ($no_child === true)
{
    $count = 1;
}

if($count > 4 && $count != 6)
{
    $count = 4;
}
?>

<div class="row-fluid">

    <div class="span2">
        <h3 class="sidebar-title"> <i class="fa fa-bars"></i> <?php echo JText::_('HELIX_MENU_DRAG_MODULE'); ?></h3>
        <div class="modules-list">
            <?php
            $modules = getModuleNameId();
            if($modules) {
                foreach($modules as $module){
                    echo '<div class="draggable-module" data-mod_id="' . $module->id . '" data-type="module">' . $module->title . '<i class="fa fa-arrows"></i></div>';
                }
            }?>
        </div>
    </div>

    <div class="span10">

        <div class="helixfw-megamenu-wrap">
            <div class="action-bar">
                <ul>
                    <li>
                        <strong><?php echo JText::_('HELIX_MENU_SUB_WIDTH'); ?></strong> <input type="number" id="menuWidth" name="width" value="<?php echo $menu_width; ?>">
                    </li>
                    <li class="btn-group">
                        <a class="alignment btn <?php echo ($align == 'left')?'active':''; ?>" data-al_flag="left" href="#"><?php echo JText::_('HELIX_GLOBAL_LEFT'); ?></a>
                        <a class="alignment btn <?php echo ($align == 'center')?'active':''; ?>" data-al_flag="center" href="#"><?php echo JText::_('HELIX_GLOBAL_CENTER'); ?></a>
                        <a class="alignment btn <?php echo ($align == 'right')?'active':''; ?>" data-al_flag="right" href="#"><?php echo JText::_('HELIX_GLOBAL_RIGHT'); ?></a>
                        <a class="alignment btn <?php echo ($align == 'full')?'active':''; ?>" data-al_flag="full" href="#"><?php echo JText::_('HELIX_GLOBAL_FULL'); ?></a>
                    </li>
                </ul>
            </div>

            <div id="hfwmm-layout" class="hfwmm-layout" data-width="<?php echo $menu_width; ?>" data-menu_item="<?php echo $count; ?>" data-menu_align="<?php echo $align; ?>">

                <?php

                if (! empty($layout) && count($layout)) {
                    $col_increment = 0;

                    foreach ($layout as $key => $row) {

                        ?>
                        <div class="hfwmm-row">
                            <div class="hfwmm-row-actions">
                                <p class="hfwmm-row-left hfwmmRowSortingIcon"><i class="fa fa-sort"></i> Row </p>
                                <p class="hfwmm-row-right">
                                    <span class="hfwmmRowDeleteIcon"><i class="fa fa-trash-o"></i> </span>
                                </p>
                                <div class="clearfix"></div>
                            </div>

                            <?php
                            if (! empty($row->attr) && count($row->attr)) {
                                foreach ($row->attr as $col_key => $col) {
                                    $col_increment++;
                                    ?>
                                    <div class="hfwmm-col hfwmm-col-<?php echo $col->colGrid; ?>" data-grid="<?php echo $col->colGrid; ?>">
                                        <div class="hfwmm-item-wrap">
                                            <div class="hfwmm-column-actions">
                                                <span class="hfwmmColSortingIcon"><i class="fa fa-arrows"></i> Column</span>
                                            </div>

                                            <?php
                                            $modId = $col->moduleId;
                                            if ( ! empty($col->items) && count($col->items)){
                                                foreach ($col->items as $item){
                                                    if ($item->type === 'module'){
                                                        $modules = getModuleNameId($item->item_id);
                                                        $title = '<span class="pull-left">'.$modules[0]->title.'</span> <a href="javascript:;" class="pull-right helixfw_mod_remove text-warning"><i class="fa fa-remove"></i></a>';
                                                    }elseif ($item->type === 'menu_item'){
                                                        $title = $JMenuSite->getItem($item->item_id)->title;
                                                    }
                                                    ?>
                                                    <div class="widget" data-mod_id="<?php echo $item->item_id; ?>" data-type="<?php echo $item->type; ?>">
                                                        <div class="widget-top">
                                                            <div class="widget-title"> <h3><?php echo $title; ?></h3> </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                            }
                                            ?>

                                            <?php
                                            if ($col_increment == 1){
                                                $unique_menu_items = unique_menu_items($current_menu_id, $layout);
                                                if ( count($unique_menu_items)){
                                                    foreach ($unique_menu_items as $key => $item_id) {
                                                        $html  ='<div class="widget" data-mod_id="'.$item_id.'" data-type="menu_item">';
                                                        $html .= '<div class="widget-top">';
                                                        $html .= '<div class="widget-title"> <h3>'.$JMenuSite->getItem($item_id)->title.'</h3> </div>';
                                                        $html .= '</div>';
                                                        $html .= '</div>';

                                                        echo $html;
                                                    }
                                                }
                                            }
                                            //create_col_menu($current_menu_id);
                                            ?>

                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            ?>

                        </div>

                        <?php
                    }
                }
                ?>

            </div>
        </div>


        <div class="hfwmm-addrow-btn-wrap">
            <button id="choose_layout" class="choose_layout" name="choose_layout"> <i class="fa fa-plus-circle"></i> Add Row </button>
            <div class="hfwmm-modal in" id="hfwmm-layout-modal" style="display: none;" >
                <ul class="menu-layout-list clearfix">
                    <li>
                        <a href="#" class="layout12" data-layout="12" data-design="layout12">
                            <div class="first-grid last-grid grid-design"></div>
                        </a>
                    </li>

                    <li>
                        <a href="#" class="layout66" data-layout="6,6" data-design="layout66">
                            <div class="first-grid middle-grid grid-design grid-design33"></div>
                            <div class="last-grid grid-design grid-design33"></div>
                        </a>
                    </li>

                    <li>
                        <a href="#" class="layout444" data-layout="4,4,4" data-design="layout444">
                            <div class="first-grid grid-design grid-design444"></div>
                            <div class="grid-design middle-grid grid-design444"></div>
                            <div class="last-grid grid-design grid-design444"></div>
                        </a>
                    </li>

                    <li>
                        <a href="#" class="layout3333" data-layout="3,3,3,3" data-design="layout3333">
                            <div class="first-grid grid-design grid-design3333"></div>
                            <div class="grid-design middle-grid grid-design3333"></div>
                            <div class="grid-design middle-grid-left grid-design3333"></div>
                            <div class="last-grid grid-design grid-design3333"></div>
                        </a>
                    </li>

                    <li>
                        <a href="#" class="layout222222" data-layout="2,2,2,2,2,2" data-design="layout222222">
                            <div class="first-grid grid-design grid-design6"></div>
                            <div class="grid-design middle-grid grid-design6"></div>
                            <div class="grid-design middle-grid-left grid-design6"></div>
                            <div class="grid-design middle-grid-left grid-design6"></div>
                            <div class="grid-design middle-grid-left grid-design6"></div>
                            <div class="last-grid grid-design grid-design6"></div>
                        </a>
                    </li>

                    <li>
                        <a href="#" class="layout48" data-layout="4,8" data-design="layout48">
                            <div class="first-grid middle-grid grid-design grid-design24"></div>
                            <div class="last-grid grid-design grid-design24"></div>
                        </a>
                    </li>

                    <li>
                        <a href="#" class="layout84" data-layout="8,4" data-design="layout84">
                            <div class="first-grid middle-grid grid-design grid-design42"></div>
                            <div class="last-grid grid-design grid-design42"></div>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="layout210" data-layout="2,10" data-design="layout210">
                            <div class="first-grid middle-grid grid-design grid-design15"></div>
                            <div class="last-grid grid-design grid-design15"></div>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="layout102" data-layout="10,2" data-design="layout102">
                            <div class="first-grid middle-grid grid-design grid-design51"></div>
                            <div class="last-grid grid-design grid-design51"></div>
                        </a>
                    </li>
                </ul>

                <div class="clearfix"></div>

            </div>
        </div>

    </div>
</div>
