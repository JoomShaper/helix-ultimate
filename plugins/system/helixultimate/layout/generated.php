<?php
/**
* @package Helix3 Framework
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2017 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

//no direct accees
defined ('_JEXEC') or die ('resticted aceess');

$types = JFolder::files( dirname( __FILE__ ) . '/types', '\.php$', false, true);


foreach ($types as $type) {
  require_once $type;
}

// require_once 'layout-settings/fields-helper.php';
require_once 'settings/settings.php';

echo RowColumnSettings::getRowSettings($rowSettings);
echo RowColumnSettings::getColumnSettings($columnSettings);

$colGrid = array(
  '12'        => '12',
  '66'        => '6,6',
  '444'       => '4,4,4',
  '3333'      => '3,3,3,3',
  '48'        => '4,8',
  '39'        => '3,9',
  '363'       => '3,6,3',
  '264'       => '2,6,4',
  '210'       => '2,10',
  '57'        => '5,7',
  '237'       => '2,3,7',
  '255'       => '2,5,5',
  '282'       => '2,8,2',
  '2442'      => '2,4,4,2',
);

?>
<div class="hidden">
  <div class="save-box">
    <div class="form-group">
      <label>
        <?php echo JText::_('HELIX_ENTER_LAYOUT_NAME'); ?>
        <input class="form-control addon-input addon-name" type="text" data-attrname="layout_name" value="" placeholder="">
      </label>
    </div>
  </div>
</div>

<!-- Modal for all -->

<div class="sp-modal" id="layout-modal" tabindex="-1" role="dialog" aria-labelledby="modal-label" aria-hidden="true">
  <div class="sp-modal-dialog">
    <div class="sp-modal-content">
      <div class="sp-modal-header">
        <button type="button" class="close" data-dismiss="spmodal" aria-hidden="true">&times;</button>
        <h3 class="sp-modal-title" id="modal-label"></h3>
      </div>
      <div class="sp-modal-body"></div>
      <div class="sp-modal-footer clearfix">
        <a href="javascript:void(0)" class="sppb-btn sppb-btn-success pull-left" id="save-settings" data-dismiss="spmodal"><?php echo JText::_('HELIX_APPLY'); ?></a>
        <button class="sppb-btn sppb-btn-danger pull-left" data-dismiss="spmodal" aria-hidden="true"><?php echo JText::_('HELIX_CANCEL'); ?></button>
      </div>
    </div>
  </div>
</div>

<div class="hidden">
  <div id="helix-ultimate-layout-section">
    <div class="helix-ultimate-section-settings clearfix">

      <div class="pull-left">
        <a class="helix-ultimate-move-row" href="#"><i class="fa fa-arrows"></i></a>
        <strong class="helicx-ultimate-section-title"><?php echo JText::_('HELIX_SECTION_TITLE'); ?></strong>
      </div>

      <div class="pull-right">
        <ul class="helix-ultimate-btn-group">
          <li>
            <a class="btn btn-small helix-ultimate-add-columns" href="#"><i class="fa fa-columns"></i></a>
            <ul class="helix-ultimate-column-list">
              <?php
              foreach ($colGrid as $key => $grid){
                $active = ($key==12) ? ' active' : '';
                echo '<li><a href="#" class="column-layout hasTooltip column-layout-' .$key. $active .'" data-layout="'.$grid.'" data-original-title="<strong>'.$grid.'</strong>"></a></li>';
                $active = '';
              }
              ?>
              <li><a href="#" class="hasTooltip column-layout-custom column-layout custom <?php echo $active; ?>" data-layout="" data-type='custom' data-original-title="<strong>Custom Layout</strong>"></a></li>
            </ul>
          </li>
          <li><a class="btn btn-small helix-ultimate-add-row" href="#"><i class="fa fa-bars"></i></a></li>
          <li><a class="btn btn-small helix-ultimate-row-options" href="#"><i class="fa fa-gears"></i></a></li>
          <li><a class="btn btn-danger btn-small helix-ultimate-remove-row" href="#"><i class="fa fa-times"></i></a></li>
        </ul>
      </div>

    </div>
    <div class="row ui-sortable">
      <div class="helix-ultimate-layout-column col-sm-12">
        <div class="helix-ultimate-column">
          <h6 class="helix-ultimate-column-title"><?php echo JText::_('HELIX_NONE'); ?></h6>
          <a class="helix-ultimate-column-options" href="#" ><i class="fa fa-gear"></i></a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="clearfix"></div>

<!-- Layout Builder Section -->
<div id="helix-ultimate-layout-builder" >
  <?php

  if ($layout_data) {
    foreach ($layout_data as $row) {
      $rowSettings = RowColumnSettings::getSettings($row->settings);
      $name = JText::_('HELIX_SECTION_TITLE');

      if (isset($row->settings->name)) {
        $name = $row->settings->name;
      }
      ?>
      <div class="helix-ultimate-layout-section" <?php echo $rowSettings; ?>>
        <div class="helix-ultimate-section-settings clearfix">
          <div class="pull-left">
            <a class="helix-ultimate-move-row" href="#"><i class="fa fa-arrows"></i></a>
            <strong class="helicx-ultimate-section-title"><?php echo $name; ?></strong>
          </div>

          <div class="pull-right">
            <ul class="helix-ultimate-row-option-list">
              <li>
                <a class="helix-ultimate-add-columns" href="#"><i class="fa fa-columns"></i></a>
                <ul class="helix-ultimate-column-list">
                  <?php
                  $active = '';
                  foreach ($colGrid as $key => $grid){
                    if($key == $row->layout){
                      $active = 'active';
                    }
                    echo '<li><a href="#" class="helix-ultimate-column-layout hasTooltip helix-ultimate-column-layout-' .$key. ' '.$active.'" data-layout="'.$grid.'" data-original-title="<strong>'.$grid.'</strong>"></a></li>';
                    $active ='';
                  } ?>

                  <?php
                  $customLayout = '';
                  if (!isset($colGrid[$row->layout])) {
                    $active = 'active';
                    $split = str_split($row->layout);
                    $customLayout = implode(',',$split);
                  }
                  ?>
                  <li><a href="#" class="hasTooltip helix-ultimate-column-layout-custom helix-ultimate-column-layout helix-ultimate-custom <?php echo $active; ?>" data-layout="<?php echo $customLayout; ?>" data-type='custom' data-original-title="<strong>Custom Layout</strong>"></a></li>
                </ul>
              </li>
              <li><a class="helix-ultimate-row-options" href="#"><i class="fa fa-gears"></i></a></li>
              <li><a class="helix-ultimate-remove-row" href="#"><i class="fa fa-trash"></i></a></li>
            </ul>
          </div>
        </div>

        <div class="helix-ultimate-row-container ui-sortable">
          <div class="row ui-sortable">
            <?php foreach ($row->attr as $column) { $colSettings = RowColumnSettings::getSettings($column->settings); ?>
              <div class="<?php echo $column->className; ?>" <?php echo $colSettings; ?>>
                <div class="helix-ultimate-column<?php echo (isset($column->settings->column_type) && $column->settings->column_type) ? ' helix-ultimate-column-component' : ''; ?> clearfix">
                  <?php if (isset($column->settings->column_type) && $column->settings->column_type) {
                    echo '<span class="helix-ultimate-column-title">Component</span>';
                  }else{
                    if (!isset($column->settings->name)) {
                      $column->settings->name = 'none';
                    }
                    echo '<span class="helix-ultimate-column-title">'.$column->settings->name.'</span>';
                  }
                  ?>
                  <a class="helix-ultimate-column-options" href="#" ><i class="fa fa-gear"></i></a>
                </div>
              </div>
            <?php } ?>
          </div>
        </div>

        <a class="helix-ultimate-add-row" href="#"><i class="fa fa-plus"></i></a>

      </div>
      <?php
    }
  }
  ?>
</div>

<div class="clearfix"></div>
