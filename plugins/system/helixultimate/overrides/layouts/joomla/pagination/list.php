<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('JPATH_BASE') or die;

$list = $displayData['list'];

$startDisabled = $list['start']['active'] ? '' : ' disabled'; 
$prevDisabled  = $list['previous']['active'] ? '' : ' disabled'; 
$nextDisabled  = $list['next']['active'] ? '' : ' disabled'; 
$endDisabled   = $list['end']['active'] ? '' : ' disabled'; 

?>
<ul class="pagination ms-0 mb-4">
	<?php echo $list['start']['data']; ?>
	<?php echo $list['previous']['data']; ?>

	<?php foreach ($list['pages'] as $page) : ?>
		<?php echo $page['data']; ?>
	<?php endforeach; ?>

	<?php echo $list['next']['data']; ?>
	<?php echo $list['end']['data']; ?>
</ul>