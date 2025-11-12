<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\String\PunycodeHelper;

if (PluginHelper::isEnabled('user', 'profile')) :
    $fields = $this->item->profile->getFieldset('profile'); ?>
    <div class="com-contact__profile contact-profile" id="users-profile-custom">
        <dl class="dl-horizontal">
            <?php foreach ($fields as $profile) :
                if ($profile->value !== '' && $profile->value !== null) :

                    // Never write back to $profile (avoids dynamic properties on PHP 8.2+)
                    $raw   = (string) $profile->value;
                    $label = $profile->label;
                    echo '<dt>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</dt>';

                    switch ($profile->id) :
                        case 'profile_website':
                            // Keep raw for href, but escape attribute; show punycode-decoded text safely
                            $href = strncasecmp($raw, 'http', 4) === 0 ? $raw : 'http://' . $raw;

                            // Display text: convert to UTF-8 host for readability, then escape
                            $display = PunycodeHelper::urlToUTF8($href);

                            echo '<dd><a href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '">'
                                . htmlspecialchars($display, ENT_QUOTES, 'UTF-8')
                                . '</a></dd>';
                            break;

                        case 'profile_dob':
                            // Format date first, then escape for output
                            $formatted = HTMLHelper::_('date', $raw, Text::_('DATE_FORMAT_LC4'), false);
                            echo '<dd>' . htmlspecialchars($formatted, ENT_QUOTES, 'UTF-8') . '</dd>';
                            break;

                        default:
                            // Generic text fields
                            $text = htmlspecialchars($raw, ENT_QUOTES, 'UTF-8');
                            echo '<dd>' . $text . '</dd>';
                            break;
                    endswitch;
                endif;
            endforeach; ?>
        </dl>
    </div>
<?php endif; ?>
