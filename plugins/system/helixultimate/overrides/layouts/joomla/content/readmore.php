<?php

/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$params    = $displayData['params'];
$item      = $displayData['item'];
$direction = Factory::getLanguage()->isRtl() ? 'left' : 'right';
?>

<p class="readmore">
    <?php if (!$params->get('access-view')) : ?>
        <a href="<?php echo $displayData['link']; ?>" aria-label="<?php echo Text::_('JGLOBAL_REGISTER_TO_READ_MORE') . ' ' . $this->escape($item->title); ?>">
            <?php echo Text::_('JGLOBAL_REGISTER_TO_READ_MORE'); ?>
        </a>
    <?php elseif ($readmore = $item->alternative_readmore) : ?>
        <a href="<?php echo $displayData['link']; ?>" aria-label="<?php echo $this->escape($readmore . ' ' . $item->title); ?>">
            <?php echo $readmore; ?>
            <?php if ($params->get('show_readmore_title', 0) != 0) : ?>
                <?php echo HTMLHelper::_('string.truncate', $item->title, $params->get('readmore_limit')); ?>
            <?php endif; ?>
        </a>
    <?php elseif ($params->get('show_readmore_title', 0) == 0) : ?>
        <a href="<?php echo $displayData['link']; ?>" aria-label="<?php echo Text::sprintf('JGLOBAL_READ_MORE_TITLE', $this->escape($item->title)); ?>">
            <?php echo Text::_('JGLOBAL_READ_MORE'); ?>
        </a>
    <?php else : ?>
        <a href="<?php echo $displayData['link']; ?>" aria-label="<?php echo Text::sprintf('JGLOBAL_READ_MORE_TITLE', $this->escape($item->title)); ?>">
            <?php echo Text::sprintf('JGLOBAL_READ_MORE_TITLE', HTMLHelper::_('string.truncate', $item->title, $params->get('readmore_limit'))); ?>
        </a>
    <?php endif; ?>
</p>
