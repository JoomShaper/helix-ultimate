<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('_JEXEC') or die;

$this->document->getWebAssetManager()
    ->useStyle('com_finder.finder')
    ->useScript('com_finder.finder');

?>
<div class="com-finder finder">
    <?php if ($this->params->get('show_page_heading')) : ?>
        <h1>
            <?php if ($this->escape($this->params->get('page_heading'))) : ?>
                <?php echo $this->escape($this->params->get('page_heading')); ?>
            <?php else : ?>
                <?php echo $this->escape($this->params->get('page_title')); ?>
            <?php endif; ?>
        </h1>
    <?php endif; ?>
    <div id="search-form" class="com-finder__form">
        <?php echo $this->loadTemplate('form'); ?>
    </div>
    <?php // Load the search results layout if we are performing a search. ?>
    <?php if ($this->query->search === true) : ?>
        <div id="search-results" class="com-finder__results">
            <?php echo $this->loadTemplate('results'); ?>
        </div>
    <?php endif; ?>
</div>
