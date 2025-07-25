<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('_JEXEC') or die;

$selector = empty($displayData['selector']) ? '' : $displayData['selector'];
?>

<ul class="joomla-tabs nav nav-tabs" id="<?php echo preg_replace('/^[\.#]/', '', $selector); ?>Tabs" role="tablist"></ul>
<div class="tab-content" id="<?php echo $selector; ?>Content">
