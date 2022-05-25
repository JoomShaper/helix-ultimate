<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/


defined('JPATH_BASE') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

HTMLHelper::_('behavior.core');

$id    = isset($displayData['id']) ? $displayData['id'] : '';
$title = $displayData['title'];
Text::script('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST');
Text::script('ERROR');
$message = "{'error': [Joomla.JText._('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST')]}";
$alert = "Joomla.renderMessages(" . $message . ")";
?>
<button id="<?php echo $id; ?>" data-bs-toggle="modal" onclick="if (document.adminForm.boxchecked.value==0){<?php echo $alert; ?>}else{jQuery( '#collapseModal' ).modal('show'); return true;}" class="btn btn-outline-primary btn-sm">
	<span class="icon-checkbox-partial" aria-hidden="true" title="<?php echo $title; ?>"></span>
	<?php echo $title; ?>
</button>
