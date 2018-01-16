<?php
/**
* @package Helix Ultimate Framework
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2018 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die ('resticted aceess');

require_once dirname(__DIR__) . '/core/lib/fa.php';

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
$enable_megamenu = 0;
$show_title = 1;
$custom_class = '';
$fa_icon = '';

if (isset($menu_data->megamenu))
{
  $enable_megamenu = $menu_data->megamenu;
}

if (isset($menu_data->width))
{
  $menu_width = $menu_data->width;
}

if (isset($menu_data->menualign))
{
  $align = $menu_data->menualign;
}

if (isset($menu_data->layout))
{
  $layout = $menu_data->layout;
}

if (isset($menu_data->showtitle))
{
  $show_title = $menu_data->showtitle;
}

if (isset($menu_data->faicon))
{
  $fa_icon = $menu_data->faicon;
}

if (isset($menu_data->customclass))
{
  $custom_class = $menu_data->customclass;
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

<div class="helix-ultimate-row">

  <div class="helix-ultimate-col-sm-9">

    <div class="helix-ultimate-megamenu-wrap">
      <div class="helix-ultimate-megamenu-actions">
        <div>
          <span class="helix-ultimate-megamenu-label"><?php echo JText::_('HELIX_ULTIMATE_MENU_ENABLED'); ?></span>
          <input type="checkbox" class="helix-ultimate-checkbox" id="helix-ultimate-megamenu-toggler" <?php echo ($enable_megamenu) ? 'checked' : ''; ?> />
        </div>
        <div class="helix-ultimate-megamenu-field-control<?php echo ($enable_megamenu != 1)?' hide-menu-builder':''?>">
          <span class="helix-ultimate-megamenu-label"><?php echo JText::_('HELIX_ULTIMATE_MENU_SUB_WIDTH'); ?></span>
          <input type="number" id="helix-ultimate-megamenu-width" value="<?php echo $menu_width; ?>" placeholder="400">
        </div>
        <div class="helix-ultimate-megamenu-field-control<?php echo ($enable_megamenu != 1)?' hide-menu-builder':''?>">
          <span class="helix-ultimate-megamenu-label"><?php echo JText::_('HELIX_ULTIMATE_MENU_SUB_ALIGNMENT'); ?></span>
          <div class="helix-ultimate-megamenu-alignment">
            <select id="helix-ultimate-megamenu-alignment">
              <option value="left" <?php echo ($align == 'left') ? 'selected' : ''; ?>><?php echo JText::_('HELIX_ULTIMATE_GLOBAL_LEFT'); ?></option>
              <option value="center" <?php echo ($align == 'center') ? 'selected' : ''; ?>><?php echo JText::_('HELIX_ULTIMATE_GLOBAL_CENTER'); ?></option>
              <option value="right" <?php echo ($align == 'right') ? 'selected' : ''; ?>><?php echo JText::_('HELIX_ULTIMATE_GLOBAL_RIGHT'); ?></option>
              <option value="full" <?php echo ($align == 'full') ? 'selected' : ''; ?>><?php echo JText::_('HELIX_ULTIMATE_GLOBAL_FULL'); ?></option>
            </select>
          </div>
        </div>
        <div>
          <span class="helix-ultimate-megamenu-label"><?php echo JText::_('HELIX_ULTIMATE_MENU_SHOW_TITLE'); ?></span>
          <input type="checkbox" class="helix-ultimate-checkbox" id="helix-ultimate-megamenu-title-toggler" <?php echo ($show_title) ? 'checked' : ''; ?> />
        </div>
        <div>
          <span class="helix-ultimate-megamenu-label"><?php echo JText::_('HELIX_ULTIMATE_MENU_ICON'); ?></span>
          <select id="helix-ultimate-megamenu-fa-icon">
            <option value=""><?php echo JText::_('HELIX_ULTIMATE_GLOBAL_SELECT'); ?></option>
            <?php foreach ($fa_list as $key => $fa) { ?>
              <option value="<?php echo $fa; ?>" <?php echo ($fa_icon == $fa) ? 'selected' : ''; ?>><?php echo $fa; ?></option>
            <?php } ?>
          </select>
        </div>
        <div>
          <span class="helix-ultimate-megamenu-label"><?php echo JText::_('HELIX_ULTIMATE_MENU_CUSTOM_CLASS'); ?></span>
          <input type="text" id="helix-ultimate-megamenu-custom-class" placeholder="custom-class" value="<?php echo ($custom_class); ?>" />
        </div>
      </div>

      <div id="helix-ultimate-megamenu-layout" class="helix-ultimate-megamenu-layout helix-ultimate-megamenu-field-control<?php echo ($enable_megamenu != 1)?' hide-menu-builder':''?>" data-megamenu="<?php echo $enable_megamenu; ?>" data-width="<?php echo $menu_width; ?>" data-menuitem="<?php echo $count; ?>" data-menualign="<?php echo $align; ?>" data-showtitle="<?php echo $show_title; ?>" data-customclass="<?php echo $custom_class; ?>">
        <?php

        if (! empty($layout) && count($layout)) {
          $col_increment = 0;

          foreach ($layout as $key => $row) {

            ?>
            <div class="helix-ultimate-megamenu-row">
              <div class="helix-ultimate-megamenu-row-actions clearfix">
                <div class="helix-ultimate-action-move-row">
                  <span class="fa fa-sort"></span> Row
                </div>
                <a href="#" class="helix-ultimate-action-detele-row"><span class="fa fa-trash-o"></span></a>
              </div>

              <div class="helix-ultimate-row">
                <?php
                if (! empty($row->attr) && count($row->attr))
                {
                  foreach ($row->attr as $col_key => $col)
                  {
                    $col_increment++;
                    ?>
                    <div class="helix-ultimate-megmenu-col helix-ultimate-col-sm-<?php echo $col->colGrid; ?>" data-grid="<?php echo $col->colGrid; ?>">
                      <div class="helix-ultimate-megamenu-column">
                        <div class="helix-ultimate-megamenu-column-actions">
                          <span class="helix-ultimate-action-move-column"><span class="fa fa-arrows"></span> Column</span>
                        </div>

                        <div class="helix-ultimate-megamenu-item-list"><?php
                          $modId = $col->moduleId;
                          if ( ! empty($col->items) && count($col->items))
                          {
                            foreach ($col->items as $item)
                            {
                              if ($item->type === 'module')
                              {
                                $modules = getModuleNameId($item->item_id);
                                $title = $modules[0]->title . '<a href="javascript:;" class="helix-ultimate-megamenu-remove-module"><span class="fa fa-remove"></span></a>';
                              }
                              elseif ($item->type === 'menu_item')
                              {
                                $title = $JMenuSite->getItem($item->item_id)->title;
                              }
                              ?>
                              <div class="helix-ultimate-megamenu-item" data-mod_id="<?php echo $item->item_id; ?>" data-type="<?php echo $item->type; ?>">
                                <div class="helix-ultimate-megamenu-item-module">
                                  <div class="helix-ultimate-megamenu-item-module-title"><?php echo $title; ?></div>
                                </div>
                              </div>
                              <?php
                            }
                          }

                          if ($col_increment == 1)
                          {
                            $unique_menu_items = unique_menu_items($current_menu_id, $layout);
                            if ( count($unique_menu_items))
                            {
                              foreach ($unique_menu_items as $key => $item_id)
                              {
                                $html  ='<div class="helix-ultimate-megamenu-item" data-mod_id="'.$item_id.'" data-type="menu_item">';
                                $html .= '<div class="helix-ultimate-megamenu-item-module">';
                                $html .= '<div class="helix-ultimate-megamenu-item-module-title">'.$JMenuSite->getItem($item_id)->title.'</div>';
                                $html .= '</div>';
                                $html .= '</div>';
                                echo $html;
                              }
                            }
                          }
                          ?></div>
                      </div>
                    </div>
                    <?php
                  }
                }
                ?>
              </div>
            </div>
            <?php
          }
        }
        ?>
      </div>
    </div>

    <div class="helix-ultimate-megamenu-add-row helix-ultimate-megamenu-field-control clearfix<?php echo ($enable_megamenu != 1)?' hide-menu-builder':''?>">
      <button id="helix-ultimate-choose-megamenu-layout" class="helix-ultimate-choose-megamenu-layout"><span class="fa fa-plus-circle"></span> Add New Row</button>

      <div class="helix-ultimate-megamenu-modal" id="helix-ultimate-megamenu-layout-modal" style="display: none;" >
        <div class="helix-ultimate-row">

          <div class="helix-ultimate-col-sm-4">
            <div class="helix-ultimate-megamenu-grids" data-layout="12">
              <div class="helix-ultimate-row">
                <div class="helix-ultimate-col-sm-12"><div>12</div></div>
              </div>
            </div>
          </div>

          <div class="helix-ultimate-col-sm-4">
            <div class="helix-ultimate-megamenu-grids" data-layout="6+6">
              <div class="helix-ultimate-row">
                <div class="helix-ultimate-col-sm-6"><div>6</div></div>
                <div class="helix-ultimate-col-sm-6"><div>6</div></div>
              </div>
            </div>
          </div>

          <div class="helix-ultimate-col-sm-4">
            <div class="helix-ultimate-megamenu-grids" data-layout="4+4+4">
              <div class="helix-ultimate-row">
                <div class="helix-ultimate-col-sm-4"><div>4</div></div>
                <div class="helix-ultimate-col-sm-4"><div>4</div></div>
                <div class="helix-ultimate-col-sm-4"><div>4</div></div>
              </div>
            </div>
          </div>

          <div class="helix-ultimate-col-sm-4">
            <div class="helix-ultimate-megamenu-grids" data-layout="3+3+3+3">
              <div class="helix-ultimate-row">
                <div class="helix-ultimate-col-sm-3"><div>3</div></div>
                <div class="helix-ultimate-col-sm-3"><div>3</div></div>
                <div class="helix-ultimate-col-sm-3"><div>3</div></div>
                <div class="helix-ultimate-col-sm-3"><div>3</div></div>
              </div>
            </div>
          </div>

          <div class="helix-ultimate-col-sm-4">
            <div class="helix-ultimate-megamenu-grids" data-layout="2+2+2+2+2+2">
              <div class="helix-ultimate-row">
                <div class="helix-ultimate-col-sm-2"><div>2</div></div>
                <div class="helix-ultimate-col-sm-2"><div>2</div></div>
                <div class="helix-ultimate-col-sm-2"><div>2</div></div>
                <div class="helix-ultimate-col-sm-2"><div>2</div></div>
                <div class="helix-ultimate-col-sm-2"><div>2</div></div>
                <div class="helix-ultimate-col-sm-2"><div>2</div></div>
              </div>
            </div>
          </div>

          <div class="helix-ultimate-col-sm-4">
            <div class="helix-ultimate-megamenu-grids" data-layout="5+7">
              <div class="helix-ultimate-row">
                <div class="helix-ultimate-col-sm-5"><div>5</div></div>
                <div class="helix-ultimate-col-sm-7"><div>7</div></div>
              </div>
            </div>
          </div>

          <div class="helix-ultimate-col-sm-4">
            <div class="helix-ultimate-megamenu-grids" data-layout="4+8">
              <div class="helix-ultimate-row">
                <div class="helix-ultimate-col-sm-4"><div>4</div></div>
                <div class="helix-ultimate-col-sm-8"><div>8</div></div>
              </div>
            </div>
          </div>

          <div class="helix-ultimate-col-sm-4">
            <div class="helix-ultimate-megamenu-grids" data-layout="3+9">
              <div class="helix-ultimate-row">
                <div class="helix-ultimate-col-sm-3"><div>3</div></div>
                <div class="helix-ultimate-col-sm-9"><div>9</div></div>
              </div>
            </div>
          </div>

          <div class="helix-ultimate-col-sm-4">
            <div class="helix-ultimate-megamenu-grids" data-layout="2+10">
              <div class="helix-ultimate-row">
                <div class="helix-ultimate-col-sm-2"><div>2</div></div>
                <div class="helix-ultimate-col-sm-10"><div>10</div></div>
              </div>
            </div>
          </div>

        </div>
        </ul>
      </div>
    </div>

  </div>

  <div class="helix-ultimate-col-sm-3">
    <div class="helix-ultimate-megamenu-sidebar <?php echo ($enable_megamenu != 1)?' hide-menu-builder':''?>">
      <h3><span class="fa fa-bars"></span> <?php echo JText::_('HELIX_ULTIMATE_MENU_MODULE_LIST'); ?></h3>
      <div class="helix-ultimate-megamenu-module-list">
        <?php
        $modules = getModuleNameId();
        if($modules) {
          foreach($modules as $module){
            echo '<div class="helix-ultimate-megamenu-draggable-module" data-mod_id="' . $module->id . '" data-type="module"><span class="fa fa-arrows"></span> ' . $module->title . '</div>';
          }
        }?>
      </div>
    </div>
  </div>
</div>
