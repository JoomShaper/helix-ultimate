<?php

/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

$icon = $displayData['icon'] ?? 'icon-copy article';
$textPrefix = $displayData['textPrefix'] ?? '';
$textSuffix = $displayData['textSuffix'] ?? '';
$title = $displayData['title'] ?? '';
$componentLangString = $textPrefix . '_EMPTYSTATE_TITLE' . $textSuffix;
$moduleLangString = $textPrefix . '_EMPTYSTATE_MODULE_TITLE' . $textSuffix;

// Did we have a definitive title provided to the view? If not, let's find one!
if (!$title) {
    // Can we find a *_EMPTYSTATE_MODULE_TITLE translation, Else use the components *_EMPTYSTATE_TITLE string
    $title = Factory::getApplication()->getLanguage()->hasKey($moduleLangString) ? $moduleLangString : $componentLangString;
}
?>
<div class="mb-4">
    <p class="fw-bold text-center text-muted">
        <span class="<?php echo $icon; ?>" aria-hidden="true"></span> <?php echo Text::_($title); ?>
    </p>
</div>
