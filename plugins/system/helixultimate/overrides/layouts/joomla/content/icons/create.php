<?php

/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

$params = $displayData['params'];

?>
<?php if ($params->get('show_icons')) : ?>
    <span class="icon-plus icon-fw" aria-hidden="true"></span>
    <?php echo Text::_('JNEW'); ?>
<?php else : ?>
    <?php echo Text::_('JNEW') . '&#160;'; ?>
<?php endif; ?>
