<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$lang  = Factory::getLanguage();

if ($this->maxLevelcat != 0 && count($this->items[$this->parent->id]) > 0) :
?>
	<?php foreach ($this->items[$this->parent->id] as $id => $item) : ?>
		<?php if ($this->params->get('show_empty_categories_cat') || $item->numitems || count($item->getChildren())) : ?>
			<div class="list-group-item">
				<div style="padding-<?php echo $lang->isRtl() ? 'right' : 'left' ?>: <?php echo (int) $this->level * 16; ?>px">
					<div class="d-flex justify-content-between align-items-center">
						<h5 class="m-0">
							<a href="<?php echo Route::_(ContentHelperRoute::getCategoryRoute($item->id, $item->language)); ?>">
								<?php echo $this->escape($item->title); ?>
							</a>
						</h5>

						<?php if ($this->params->get('show_cat_num_articles_cat') == 1) :?>
							<span class="badge bg-primary rounded-pill">
								<?php echo Text::_('COM_CONTENT_NUM_ITEMS'); ?>
								<?php echo $item->numitems; ?>
							</span>
						<?php endif; ?>
					</div>

					<?php if ($this->params->get('show_subcat_desc_cat') == 1) : ?>
						<?php if ($item->description) : ?>
							<div class="mt-2">
								<?php echo HTMLHelper::_('content.prepare', $item->description, '', 'com_content.categories'); ?>
							</div>
						<?php endif; ?>
					<?php endif; ?>
				</div>
			</div>
			<?php if (count($item->getChildren()) > 0 && $this->maxLevelcat > 1) : ?>
				<?php
					$this->items[$item->id] = $item->getChildren();
					$this->parent = $item;
					$this->maxLevelcat--;
					$this->level++;
					echo $this->loadTemplate('items');
					$this->parent = $item->getParent();
					$this->maxLevelcat++;
				?>
			<?php endif; ?>
		<?php endif; ?>
	<?php endforeach; ?>
<?php endif; ?>