<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Component\Contact\Site\Helper\RouteHelper;

$lang  = Factory::getApplication()->getLanguage();

if ($this->maxLevelcat != 0 && count($this->items[$this->parent->id]) > 0) :
    ?>
    <?php foreach ($this->items[$this->parent->id] as $id => $item) : ?>
        <?php if ($this->params->get('show_empty_categories_cat') || $item->numitems || count($item->getChildren())) : ?>
            <div class="list-group-item">
                <div class="com-contact-categories__item-title-wrapper">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="m-0">
                            <a href="<?php echo Route::_(RouteHelper::getCategoryRoute($item->id, $item->language)); ?>">
                            <?php echo $this->escape($item->title); ?></a>
                            <?php if ($this->params->get('show_cat_items_cat') == 1) :?>
                                <span class="badge bg-primary rounded-pill">
                                    <?php echo Text::_('COM_CONTACT_NUM_ITEMS'); ?>&nbsp;
                                    <?php echo $item->numitems; ?>
                                </span>
                            <?php endif; ?>
                            <?php if ($this->maxLevelcat > 1 && count($item->getChildren()) > 0) : ?>
                                <button
                                    type="button"
                                    id="category-btn-<?php echo $item->id; ?>"
                                    class="btn btn-secondary btn-sm float-end"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#category-<?php echo $item->id; ?>"
                                    aria-expanded="false"
                                    aria-controls="category-<?php echo $item->id; ?>"
                                    aria-label="<?php echo Text::_('JGLOBAL_EXPAND_CATEGORIES'); ?>"
                                >
                                    <span class="icon-plus" aria-hidden="true"></span>
                                </button>
                            <?php endif; ?>
                        </h5>
                    </div>

                    <?php if ($this->params->get('show_subcat_desc_cat') == 1) : ?>
                        <?php if ($item->description) : ?>
                            <div class="mt-2">
                                <?php echo HTMLHelper::_('content.prepare', $item->description, '', 'com_contact.categories'); ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Child Categories (Collapse Section) -->
                    <?php if (count($item->getChildren()) > 0 && $this->maxLevelcat > 1) : ?>
                        <div class="com-contact-categories__children collapse" id="category-<?php echo $item->id; ?>">
                            <?php
                            $this->items[$item->id] = $item->getChildren();
                            $this->parent = $item;
                            $this->maxLevelcat--;
                            echo $this->loadTemplate('items');
                            $this->parent = $item->getParent();
                            $this->maxLevelcat++;
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>
