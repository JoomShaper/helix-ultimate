<?php

/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

if (isset($displayData['ariaDescribed'])) {
    $aria_described = $displayData['ariaDescribed'];
} elseif (isset($displayData['article'])) {
    $article        = $displayData['article'];
    $aria_described = 'editarticle-' . (int) $article->id;
} elseif (isset($displayData['contact'])) {
    $contact        = $displayData['contact'];
    $aria_described = 'editcontact-' . (int) $contact->id;
}

$tooltip = $displayData['tooltip'];

?>
<span class="hasTooltip icon-lock" aria-hidden="true"></span>
    <?php echo Text::_('JLIB_HTML_CHECKED_OUT'); ?>
<div role="tooltip" id="<?php echo $aria_described; ?>">
    <?php echo $tooltip; ?>
</div>
