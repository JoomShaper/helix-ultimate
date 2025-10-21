<?php

/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;

?>
<span class="parent-category-name">
    <?php echo LayoutHelper::render('joomla.icon.iconclass', ['icon' => 'icon-folder icon-fw']); ?>
    <?php $title = $this->escape($displayData['item']->parent_title); ?>
    <?php if ($displayData['params']->get('link_parent_category') && !empty($displayData['item']->parent_id)) : ?>
        <?php $url = '<a href="' . Route::_(
            RouteHelper::getCategoryRoute($displayData['item']->parent_id, $displayData['item']->parent_language)
        )
            . '">' . $title . '</a>'; ?>
        <?php echo Text::sprintf('COM_CONTENT_PARENT', $url); ?>
    <?php else : ?>
        <?php echo Text::sprintf('COM_CONTENT_PARENT', '<span>' . $title . '</span>'); ?>
    <?php endif; ?>
</span>
