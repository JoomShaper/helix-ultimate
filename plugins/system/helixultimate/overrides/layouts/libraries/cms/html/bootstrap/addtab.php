<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('_JEXEC') or die;

$id       = empty($displayData['id']) ? '' : $displayData['id'];
$active   = empty($displayData['active']) ? '' : $displayData['active'];
$title    = empty($displayData['title']) ? '' : $displayData['title'];
?>
<div id="<?php echo preg_replace('/^[\.#]/', '', $id); ?>"
    class="tab-pane<?php echo $active; ?>"
    data-active="<?php echo trim(htmlspecialchars($active, ENT_COMPAT, 'UTF-8')); ?>"
    data-id="<?php echo  htmlspecialchars($id, ENT_COMPAT, 'UTF-8'); ?>"
    data-title="<?php echo htmlspecialchars($title, ENT_COMPAT, 'UTF-8'); ?>">
