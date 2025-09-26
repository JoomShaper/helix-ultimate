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

$article = $displayData['article'];
$tooltip = $displayData['tooltip'];
$nowDate = strtotime(Factory::getDate());

$icon = $article->state ? 'edit' : 'eye-slash';
$currentDate   = Factory::getDate()->format('Y-m-d H:i:s');
$isUnpublished = ($article->publish_up > $currentDate)
    || !is_null($article->publish_down) && ($article->publish_down < $currentDate);

if ($isUnpublished) {
    $icon = 'eye-slash';
}
$aria_described = 'editarticle-' . (int) $article->id;

?>
<span class="icon-<?php echo $icon; ?>" aria-hidden="true"></span>
    <?php echo Text::_('JGLOBAL_EDIT'); ?>
<div role="tooltip" id="<?php echo $aria_described; ?>">
    <?php echo $tooltip; ?>
</div>
