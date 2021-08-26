<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('JPATH_BASE') or die;

use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Language\Text;
?>
<h2 class="nav-header"><?php echo OutputFilter::ampReplace(Text::_($displayData)); ?></h2>
<ul class="j-links-group nav nav-list">
