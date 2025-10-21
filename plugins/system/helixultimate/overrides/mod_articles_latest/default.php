<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;

if (!$list) {
    return;
}

?>
<ul class="mod-articleslatest latestnews mod-list">
<?php foreach ($list as $item) : ?>
    <li itemscope itemtype="https://schema.org/Article">
        <a href="<?php echo $item->link; ?>" itemprop="url">
            <span itemprop="name">
                <?php echo $item->title; ?>
            </span>
            <span><?php echo HTMLHelper::_('date', $item->created, 'DATE_FORMAT_LC3'); ?></span>
        </a>
    </li>
<?php endforeach; ?>
</ul>
