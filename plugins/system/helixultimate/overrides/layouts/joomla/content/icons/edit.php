<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('JPATH_BASE') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$article = $displayData['article'];
$tooltip = JVERSION < 4 ? $displayData['overlib'] : $displayData['tooltip'];

$icon 			= $article->state ? 'edit' : 'eye-slash';
$currentDate   	= Factory::getDate()->format('Y-m-d H:i:s');
$isUnpublished 	= JVERSION < 4
	? strtotime($article->publish_up) > strtotime(Factory::getDate()) || ((strtotime($article->publish_down) < strtotime(Factory::getDate())) && $article->publish_down != Factory::getDbo()->getNullDate())
	: ($article->publish_up > $currentDate) || !is_null($article->publish_down) && ($article->publish_down < $currentDate);

if ($isUnpublished)
{
	$icon = 'eye-slash';
}
?>
<SPAN class="link-edit-article">
	<span class="hasTooltip fas fa-<?php echo $icon; ?>" title="<?php echo HTMLHelper::tooltipText(Text::_('COM_CONTENT_EDIT_ITEM'), $tooltip, 0, 0); ?>"></span>
	<?php echo Text::_('JGLOBAL_EDIT'); ?>
</SPAN>
