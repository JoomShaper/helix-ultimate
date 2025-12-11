<?php

/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;

// Create a shortcut for params.
$params  = $displayData->params;
$canEdit = $displayData->params->get('access-edit');
$heading = $displayData->heading ?? 'h2';

$currentDate = Factory::getDate()->format('Y-m-d H:i:s');
$link = RouteHelper::getArticleRoute($displayData->slug, $displayData->catid, $displayData->language);
?>
<?php if ($displayData->state == 0 || $params->get('show_title') || ($params->get('show_author') && !empty($displayData->author))) : ?>
    <div class="article-header">
        <?php if ($params->get('show_title')) : ?>
            <<?php echo $heading; ?>>
                <?php if ($params->get('link_titles') && ($params->get('access-view') || $params->get('show_noauth', '0') == '1')) : ?>
                    <a href="<?php echo Route::_($link); ?>">
                        <?php echo $this->escape($displayData->title); ?>
                    </a>
                <?php else : ?>
                    <?php echo $this->escape($displayData->title); ?>
                <?php endif; ?>
            </<?php echo $heading; ?>>
        <?php endif; ?>

        <?php if ($displayData->state == 0) : ?>
            <span class="badge bg-warning"><?php echo Text::_('JUNPUBLISHED'); ?></span>
        <?php endif; ?>

        <?php if ($displayData->publish_up > $currentDate) : ?>
            <span class="badge bg-warning"><?php echo Text::_('JNOTPUBLISHEDYET'); ?></span>
        <?php endif; ?>

        <?php if ($displayData->publish_down !== null && $displayData->publish_down < $currentDate) : ?>
            <span class="badge bg-warning"><?php echo Text::_('JEXPIRED'); ?></span>
        <?php endif; ?>
    </div>
<?php endif; ?>
