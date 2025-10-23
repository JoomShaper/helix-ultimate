<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;

?>
<ul class="com-content-blog__links">
    <?php foreach ($this->link_items as $item) : ?>
        <li class="com-content-blog__link">
            <a href="<?php echo Route::_(RouteHelper::getArticleRoute($item->slug, $item->catid, $item->language)); ?>">
                <?php echo $item->title; ?></a>
        </li>
    <?php endforeach; ?>
</ul>
