<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers');
// HTMLHelper::_('behavior.caption');

?>
<div class="category-list<?php echo $this->pageclass_sfx; ?>">
    <?php
        $this->subtemplatename = 'articles';
        echo LayoutHelper::render('joomla.content.category_default', $this);
    ?>
</div>
