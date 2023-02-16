<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/


defined('JPATH_BASE') or die;

use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('behavior.core');

$id     = isset($displayData['id']) ? $displayData['id'] : '';
$doTask = isset($displayData['onclick']) ? $displayData['onclick'] : $displayData['doTask'];
$text   = $displayData['text'];
?>
<button id="<?php echo $id; ?>" onclick="<?php echo $doTask; ?>" rel="help" class="btn btn-outline-info btn-sm">
	<span class="icon-question-sign" aria-hidden="true"></span>
	<?php echo $text; ?>
</button>
