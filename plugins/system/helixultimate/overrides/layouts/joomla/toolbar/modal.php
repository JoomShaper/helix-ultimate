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

$selector = $displayData['selector'];
$id       = isset($displayData['id']) ? $displayData['id'] : '';
$class    = isset($displayData['class']) ? $displayData['class'] : 'btn btn-secondary btn-sm';
$icon     = isset($displayData['icon']) ? $displayData['icon'] : 'fas fa-download';
$text     = isset($displayData['text']) ? $displayData['text'] : '';

// Render the modal
echo HTMLHelper::_('bootstrap.renderModal',
	'modal_'. $selector,
	array(
		'url'         => $displayData['doTask'],
		'title'       => $text,
		'height'      => '100%',
		'width'       => '100%',
		'modalWidth'  => 80,
		'bodyHeight'  => 60,
		'closeButton' => true,
		'footer'      => '<a class="btn btn-secondary" data-bs-dismiss="modal" type="button"'
						. ' onclick="window.parent.jQuery(\'#modal_downloadModal\').modal(\'hide\');">'
						. Text::_("COM_BANNERS_CANCEL") . '</a>'
						. '<button class="btn btn-success" type="button"'
						. ' onclick="jQuery(\'#modal_downloadModal iframe\').contents().find(\'#exportBtn\').click();">'
						. Text::_("COM_BANNERS_TRACKS_EXPORT") . '</button>',
	)
);
?>
<button id="<?php echo $id; ?>" onclick="jQuery('#modal_<?php echo $selector; ?>').modal('show')" class="<?php echo $class; ?>" data-bs-toggle="modal" title="<?php echo $text; ?>">
	<span class="icon-<?php echo $icon; ?>" aria-hidden="true"></span><?php echo $text; ?>
</button>
