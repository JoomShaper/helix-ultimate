<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;

?>
<?php if ($this->params->get('show_articles')) : ?>
<div class="com-contact__articles contact-articles">
    <ul class="list-unstyled">
        <?php foreach ($this->item->articles as $article) : ?>
            <li>
                <?php echo HTMLHelper::_('link', Route::_(RouteHelper::getArticleRoute($article->slug, $article->catid, $article->language)), htmlspecialchars($article->title, ENT_COMPAT, 'UTF-8')); ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>
