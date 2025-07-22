<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$this->level = 0; // Initialize the level for nested categories
$lang  = Factory::getLanguage();
$isJ4 = version_compare(JVERSION, '4.0', 'ge');

if ($this->maxLevelcat != 0 && count($this->items[$this->parent->id]) > 0) :
?>
    <?php foreach ($this->items[$this->parent->id] as $id => $item) : ?>
        <?php if ($this->params->get('show_empty_categories_cat') || $item->numitems || count($item->getChildren())) : ?>
            <?php if ($isJ4) : ?>
                <div class="com-contact-categories__items">
                    <h3 class="page-header item-title">
            <?php else : ?>
                <div class="list-group-item">
                    <div style="padding-<?php echo $lang->isRtl() ? 'right' : 'left' ?>: <?php echo (int) $this->level * 16; ?>px">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="m-0">
            <?php endif; ?>
            
            <a href="<?php echo Route::_($isJ4 ? \Joomla\Component\Contact\Site\Helper\RouteHelper::getCategoryRoute($item->id, $item->language) : ContactHelperRoute::getCategoryRoute($item->id, $item->language)); ?>">
                <?php echo $this->escape($item->title); ?>
            </a>

            <?php if ($isJ4) : ?>
                <?php if ($this->params->get('show_cat_items_cat') == 1) :?>
                    <span class="badge bg-info">
                        <?php echo Text::_('COM_CONTACT_NUM_ITEMS'); ?>&nbsp;
                        <?php echo $item->numitems; ?>
                    </span>
                <?php endif; ?>
                
                <?php if ($this->maxLevelcat > 1 && count($item->getChildren()) > 0) : ?>
                    <button
                        type="button"
                        id="category-btn-<?php echo $item->id; ?>"
                        data-bs-target="#category-<?php echo $item->id; ?>"
                        data-bs-toggle="collapse"
                        class="btn btn-secondary btn-sm float-end"
                        aria-label="<?php echo Text::_('JGLOBAL_EXPAND_CATEGORIES'); ?>"
                    >
                        <span class="icon-plus" aria-hidden="true"></span>
                    </button>
                <?php endif; ?>
                </h3>
            <?php else : ?>
                </h5>

                <?php if ($this->params->get('show_cat_num_articles_cat') == 1) :?>
                    <span class="badge bg-primary rounded-pill">
                        <?php echo Text::_('COM_CONTACT_NUM_ITEMS'); ?>
                        <?php echo $item->numitems; ?>
                    </span>
                <?php endif; ?>
                        </div>
            <?php endif; ?>

            <?php if ($this->params->get('show_subcat_desc_cat') == 1) : ?>
                <?php if ($item->description) : ?>
                    <div class="<?php echo $isJ4 ? 'category-desc' : 'mt-2'; ?>">
                        <?php echo HTMLHelper::_('content.prepare', $item->description, '', 'com_contact.categories'); ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (count($item->getChildren()) > 0 && $this->maxLevelcat > 1) : ?>
                <?php if ($isJ4) : ?>
                    <div class="collapse fade" id="category-<?php echo $item->id; ?>">
                <?php endif; ?>
                
                <?php
                    $this->items[$item->id] = $item->getChildren();
                    $this->parent = $item;
                    $this->maxLevelcat--;
                    if (!$isJ4) {
                        $this->level++;
                    }
                    echo $this->loadTemplate('items');
                    $this->parent = $item->getParent();
                    $this->maxLevelcat++;
                ?>
                
                <?php if ($isJ4) : ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (!$isJ4) : ?>
                    </div>
                </div>
            <?php else : ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>