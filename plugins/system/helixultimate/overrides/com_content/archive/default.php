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
use Joomla\CMS\Router\Route;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers');

$params = $this->params;
?>
<div class="archive<?php echo $this->pageclass_sfx; ?>">
    <?php if ($params->get('show_page_heading')) : ?>
        <div class="page-header">
            <h1>
                <?php echo $this->escape($params->get('page_heading')); ?>
            </h1>
        </div>
    <?php endif; ?>

    <form id="adminForm" action="<?php echo Route::_('index.php'); ?>" method="post">
        <fieldset class="filters">
            <legend class="visually-hidden">
                <?php echo Text::_('COM_CONTENT_FORM_FILTER_LEGEND'); ?>
            </legend>

            <div class="filter-search row g-3 align-items-center mb-4">
                <?php if ($params->get('filter_field') !== 'hide') : ?>
                    <div class="col-auto">
                        <label class="filter-search-lbl visually-hidden" for="filter-search">
                            <?php echo Text::_('COM_CONTENT_TITLE_FILTER_LABEL'); ?>
                        </label>
                        <input
                            type="text"
                            name="filter-search"
                            id="filter-search"
                            value="<?php echo $this->escape($this->filter); ?>"
                            class="form-control inputbox col-lg-2"
                            onchange="document.getElementById('adminForm').submit();"
                            placeholder="<?php echo Text::_('COM_CONTENT_TITLE_FILTER_LABEL'); ?>">
                    </div>
                <?php endif; ?>

                <div class="col-auto">
                    <label class="visually-hidden" for="month"><?php echo Text::_('JMONTH'); ?></label>
                    <?php echo $this->form->monthField; ?>
                </div>

                <div class="col-auto">
                    <label class="visually-hidden" for="year"><?php echo Text::_('JYEAR'); ?></label>
                    <?php echo $this->form->yearField; ?>
                </div>

                <div class="col-auto">
                    <label class="visually-hidden" for="limit"><?php echo Text::_('JGLOBAL_DISPLAY_NUM'); ?></label>
                    <?php echo $this->form->limitField; ?>
                </div>

                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">
                        <?php echo Text::_('JGLOBAL_FILTER_BUTTON'); ?>
                    </button>
                </div>

                <input type="hidden" name="view" value="archive">
                <input type="hidden" name="option" value="com_content">
                <input type="hidden" name="limitstart" value="0">
            </div>
        </fieldset>

        <?php echo $this->loadTemplate('items'); ?>
    </form>
</div>
