<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

use HelixUltimate\Framework\Platform\Helper;

defined ('JPATH_BASE') or die();

$fullText = $displayData->fulltext;
$readTime = Helper::getReadTime($fullText);
?>
<span class="read-time" title="read time"><?php echo $readTime; ?></span>
