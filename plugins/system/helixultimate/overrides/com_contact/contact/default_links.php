<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

?>
<div class="com-contact__links contact-links">
    <ul class="list-unstyled">
        <?php
        // Letters 'a' to 'e'
        foreach (range('a', 'e') as $char) :
            $link = $this->item->params->get('link' . $char);
            $label = $this->item->params->get('link' . $char . '_name');

            if (!$link) :
                continue;
            endif;

            // Add 'http://' if not present
            $link = (0 === strpos($link, 'http')) ? $link : 'http://' . $link;

            // If no label is present, take the link
            $label = $label ?: $link;
            ?>
            <li>
                <a href="<?php echo $link; ?>" rel="noopener noreferrer">
                    <?php echo $label; ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>