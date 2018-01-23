<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$article = $displayData['article'];
$overlib = $displayData['overlib'];

$icon = $article->state ? 'pencil-square-o' : 'eye-slash';

if (strtotime($article->publish_up) > strtotime(Factory::getDate()) || ((strtotime($article->publish_down) < strtotime(Factory::getDate())) && $article->publish_down != Factory::getDbo()->getNullDate()))
{
	$icon = 'eye-slash';
}

?>
<div class="clearfix mb-2">
	<span class="hasTooltip fa fa-<?php echo $icon; ?>" title="<?php echo HTMLHelper::tooltipText(Text::_('COM_CONTENT_EDIT_ITEM'), $overlib, 0, 0); ?>"></span>
	<?php echo Text::_('JGLOBAL_EDIT'); ?>
</div>
