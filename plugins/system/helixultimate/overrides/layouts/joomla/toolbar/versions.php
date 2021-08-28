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
use Joomla\CMS\Session\Session;

extract($displayData);

echo HTMLHelper::_(
	'bootstrap.renderModal',
	'versionsModal',
	array(
		'url'    => "index.php?option=com_contenthistory&amp;view=history&amp;layout=modal&amp;tmpl=component&amp;item_id="
			. (int) $displayData['itemId']. "&amp;type_id=" . $displayData['typeId'] . "&amp;type_alias=" . $displayData['typeAlias']
			. "&amp;" . Session::getFormToken() . "=1",
		'title'  => $displayData['title'],
		'height' => '100%',
		'width'  => '100%',
		'modalWidth'  => '80',
		'bodyHeight'  => '60',
		'footer' => '<a type="button" class="btn btn-secondary" data-dismiss="modal" aria-hidden="true">'
			. Text::_("JLIB_HTML_BEHAVIOR_CLOSE") . '</a>'
	)
);

$id = isset($displayData['id']) ? $displayData['id'] : '';

?>
<button<?php echo $id; ?> onclick="jQuery('#versionsModal').modal('show')" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" title="<?php echo $displayData['title']; ?>">
	<span class="icon-archive" aria-hidden="true"></span><?php echo $displayData['title']; ?>
</button>
