<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('JPATH_BASE') or die;

$btnClass = $displayData['class'];
?>
<button
	type="button"
	class="btn btn-sm <?php echo $btnClass; ?> dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"
	data-bs-auto-close="true"
	aria-haspopup="true"
	aria-expanded="false"></button>
<div class="dropdown-menu">
