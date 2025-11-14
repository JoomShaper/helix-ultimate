<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\String\PunycodeHelper;

?>
<?php if (PluginHelper::isEnabled('user', 'profile')) :
    $fields = $this->item->profile->getFieldset('profile'); ?>
    <div class="com-contact__profile contact-profile" id="users-profile-custom">
        <dl class="dl-horizontal">
            <?php foreach ($fields as $profile) :
                // Skip empty values
                if (!$profile->value) {
                    continue;
                }

                $label = $profile->label; 
                $rawValue = (string) $profile->value;
                $text = htmlspecialchars($rawValue, ENT_QUOTES, 'UTF-8');

                echo '<dt>' . $label . '</dt>';

                switch ($profile->id) {
                    case 'profile_website':
                        $hasScheme = preg_match('#^https?://#i', $rawValue) === 1;
                        $href = $hasScheme ? $rawValue : ('http://' . $rawValue);
                        $hrefEsc = htmlspecialchars($href, ENT_QUOTES, 'UTF-8');
                        $display = PunycodeHelper::urlToUTF8($href);
                        $displayEsc = htmlspecialchars($display, ENT_QUOTES, 'UTF-8');
                        echo '<dd><a href="' . $hrefEsc . '">' . $displayEsc . '</a></dd>';
                        break;

                    case 'profile_dob':
                        echo '<dd>' . HTMLHelper::_('date', $rawValue, Text::_('DATE_FORMAT_LC4'), false) . '</dd>';
                        break;

                    default:
                        echo '<dd>' . $text . '</dd>';
                        break;
                }
            endforeach; ?>
        </dl>
    </div>
<?php endif; ?>
