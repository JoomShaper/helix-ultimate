<?php

/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

?>
<span class="createdby">
    <span class="icon-user icon-fw" aria-hidden="true"></span>
    <?php $author = ($displayData['item']->created_by_alias ?: $displayData['item']->author); ?>
    <?php $author = '<span>' . $author . '</span>'; ?>
    <?php if (!empty($displayData['item']->contact_link) && $displayData['params']->get('link_author') == true) : ?>
        <?php echo Text::sprintf('COM_CONTENT_WRITTEN_BY', HTMLHelper::_('link', $displayData['item']->contact_link, $author)); ?>
    <?php else : ?>
        <?php echo Text::sprintf('COM_CONTENT_WRITTEN_BY', $author); ?>
    <?php endif; ?>
</span>
