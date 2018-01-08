<?php
/**
* @package Helix Ultimate Framework
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2018 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die ('resticted access');

$fields = JFolder::files( dirname( __FILE__ ) . '/fields', '\.php$', false, true);
foreach ($fields as $field) {
    require_once $field;
}

// require_once 'layout-settings/fields-helper.php';
require_once 'settings/settings.php';

echo RowColumnSettings::getRowSettings($rowSettings);
echo RowColumnSettings::getColumnSettings($columnSettings);

$colGrid = array(
    '12'        => '12',
    '66'        => '6+6',
    '444'       => '4+4+4',
    '3333'      => '3+3+3+3',
    '48'        => '4+8',
    '39'        => '3+9',
    '363'       => '3+6+3',
    '264'       => '2+6+4',
    '210'       => '2+10',
    '57'        => '5+7',
    '237'       => '2+3+7',
    '255'       => '2+5+5',
    '282'       => '2+8+2',
    '2442'      => '2+4+4+2',
);

?>
<div class="hidden">
    <div class="save-box">
        <div class="form-group">
            <label><?php echo JText::_('HELIX_ENTER_LAYOUT_NAME'); ?></label>
            <input class="form-control addon-input addon-name" type="text" data-attrname="layout_name" value="" placeholder="">
        </div>
    </div>
</div>

<div class="hidden">
    <div id="helix-ultimate-layout-section">
    <div class="helix-ultimate-section-settings clearfix">

        <div class="pull-left">
        <a class="helix-ultimate-move-row" href="#"><i class="fa fa-arrows"></i></a>
        <strong class="helix-ultimate-section-title"><?php echo JText::_('HELIX_SECTION_TITLE'); ?></strong>
        </div>

        <div class="pull-right">
        <ul class="helix-ultimate-row-option-list">
            <li>
            <a class="helix-ultimate-add-columns" href="#"><span class="fa fa-columns"></span></a>
            <ul class="helix-ultimate-column-list">
                <?php
                $active = '';
                foreach ($colGrid as $key => $grid){
                if($key == 12){
                    $active = 'active';
                }
                $cols = explode('+', $grid);
                $col_output = '';
                foreach ($cols as $col) {
                    $col_output .= '<span class="helix-ultimate-column-layout-col-'. $col .'"><span>'. $col .'</span></span>';
                }
                echo '<li><a href="#" class="helix-ultimate-column-layout hasTooltip helix-ultimate-column-layout-' .$key. ' '.$active.'" data-layout="'.$grid.'" data-original-title="<strong>'.$grid.'</strong>">'. $col_output .'</a></li>';
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
        <div class="helix-ultimate-layout-column col-md-12" data-grid_size="12">
            <div class="helix-ultimate-column clearfix">
            <span class="helix-ultimate-column-title"><?php echo JText::_('HELIX_NONE'); ?></span>
            <a class="helix-ultimate-column-options" href="#" ><i class="fa fa-gear"></i></a>
            </div>
        </div>
        </div>
    </div>
    <a class="helix-ultimate-add-row" href="#"><i class="fa fa-plus"></i></a>
    </div>
</div>

<div class="clearfix"></div>

<!-- Layout Builder Section -->
<div id="helix-ultimate-layout-builder" >
<?php
    if ($layout_data)
    {
        foreach ($layout_data as $row)
        {
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
                <strong class="helix-ultimate-section-title"><?php echo $name; ?></strong>
            </div>

            <div class="pull-right">
                <ul class="helix-ultimate-row-option-list">
                    <li>
                        <a class="helix-ultimate-add-columns" href="#"><span class="fa fa-columns"></span></a>
                        <ul class="helix-ultimate-column-list">
                            <?php
                                $active = '';
                                foreach ($colGrid as $key => $grid)
                                {
                                    if ($key == $row->layout)
                                    {
                                        $active = 'active';
                                    }
                                    $cols = explode('+', $grid);

                                    $col_output = '';
                                    foreach ($cols as $col) {
                                        $col_output .= '<span class="helix-ultimate-column-layout-col-'. $col .'"><span>'. $col .'</span></span>';
                                    }
                                    echo '<li><a href="#" class="helix-ultimate-column-layout hasTooltip helix-ultimate-column-layout-' .$key. ' '.$active.'" data-layout="'.$grid.'" data-original-title="<strong>'.$grid.'</strong>">'. $col_output .'</a></li>';
                                    $active ='';
                                }

                                $customLayout = '';
                                if (!isset($colGrid[$row->layout]))
                                {
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
            <?php
                foreach ($row->attr as $column)
                {
                    $colSettings = RowColumnSettings::getSettings($column->settings);
            ?>
                <div class="helix-ultimate-layout-column col-md-<?php echo $column->settings->grid_size; ?>" <?php echo $colSettings; ?>>
                    <div class="helix-ultimate-column<?php echo (isset($column->settings->column_type) && $column->settings->column_type) ? ' helix-ultimate-column-component' : ''; ?> clearfix">
                    <?php
                        if (isset($column->settings->column_type) && $column->settings->column_type)
                        {
                            echo '<span class="helix-ultimate-column-title">Component</span>';
                        }
                        else
                        {
                            if (!isset($column->settings->name))
                            {
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
