<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

?>
<div class="com-content-archive archive">
<?php if ($this->params->get('show_page_heading')) : ?>
    <div class="page-header">
        <h1>
            <?php echo $this->escape($this->params->get('page_heading')); ?>
        </h1>
    </div>
<?php endif; ?>

<form id="adminForm" action="<?php echo Route::_('index.php'); ?>" method="post" class="com-content-archive__form">
    <fieldset class="com-content-archive__filters filters">
        <legend class="visually-hidden">
            <?php echo Text::_('COM_CONTENT_FORM_FILTER_LEGEND'); ?>
        </legend>
        <div class="filter-search form-inline">
            <?php if ($this->params->get('filter_field') !== 'hide') : ?>
            <div class="mb-2">
                <label class="filter-search-lbl visually-hidden" for="filter-search"><?php echo Text::_('COM_CONTENT_TITLE_FILTER_LABEL') . '&#160;'; ?></label>
                <input type="text" name="filter-search" id="filter-search" value="<?php echo $this->escape($this->filter); ?>" class="inputbox col-md-2" onchange="document.getElementById('adminForm').submit();" placeholder="<?php echo Text::_('COM_CONTENT_TITLE_FILTER_LABEL'); ?>">
            </div>
            <?php endif; ?>

            <span class="me-2">
                <label class="visually-hidden" for="month"><?php echo Text::_('JMONTH'); ?></label>
                <?php echo $this->form->monthField; ?>
            </span>
            <span class="me-2">
                <label class="visually-hidden" for="year"><?php echo Text::_('JYEAR'); ?></label>
                <?php echo $this->form->yearField; ?>
            </span>
            <span class="me-2">
                <label class="visually-hidden" for="limit"><?php echo Text::_('JGLOBAL_DISPLAY_NUM'); ?></label>
                <?php echo $this->form->limitField; ?>
            </span>

            <button type="submit" class="btn btn-primary" style="vertical-align: top;"><?php echo Text::_('JGLOBAL_FILTER_BUTTON'); ?></button>
            <input type="hidden" name="view" value="archive">
            <input type="hidden" name="option" value="com_content">
            <input type="hidden" name="limitstart" value="0">
        </div>
    </fieldset>
</form>
<?php echo $this->loadTemplate('items'); ?>
</div>
