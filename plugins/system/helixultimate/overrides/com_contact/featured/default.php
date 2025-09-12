<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('_JEXEC') or die;

?>
<div class="com-contact-featured blog-featured">
<?php if ($this->params->get('show_page_heading') != 0) : ?>
    <h1>
        <?php echo $this->escape($this->params->get('page_heading')); ?>
    </h1>
<?php endif; ?>

<?php echo $this->loadTemplate('items'); ?>

<?php if ($this->params->def('show_pagination', 2) == 1  || ($this->params->get('show_pagination') == 2 && $this->pagination->pagesTotal > 1)) : ?>
    <div class="com-contact-featured__pagination w-100">
        <?php if ($this->params->def('show_pagination_results', 1)) : ?>
            <p class="counter float-end pt-3 pe-2">
                <?php echo $this->pagination->getPagesCounter(); ?>
            </p>
        <?php endif; ?>

        <?php echo $this->pagination->getPagesLinks(); ?>
    </div>
<?php endif; ?>
</div>
