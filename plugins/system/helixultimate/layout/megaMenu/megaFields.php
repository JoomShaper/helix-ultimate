<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use HelixUltimate\Framework\Core\Lib\FontawesomeIcons;
use Joomla\CMS\Language\Text;

/**
 * Fields definition for the mega menu fields.
 *
 * @since   2.0.0
 */
class MegaFields
{
    /**
     * Mega menu settings array.
     *
     * @var     object   The mega menu settings.
     * @since   2.0.0
     */
    private $settings = null;

    /**
     * Menu Item Id
     *
     * @var     int     $itemId     The menu item id.
     * @since   2.0.0
     */
    private $itemId = 0;

    /**
     * Constructor function for the class.
     *
     * @param   array   $settings   The mega menu settings array.
     *
     * @since   2.0.0
     */
    public function __construct($settings, $itemId)
    {
        $this->settings = $settings;
        $this->itemId = $itemId;
    }

    /**
     * Make font awesome options for the listing.
     *
     * @return  array   the options array.
     * @since   2.0.0
     */
    private function getFontOptions()
    {
        $fontawesome = new FontawesomeIcons;
        $icons = $fontawesome->getIcons();

        $options = [];

        /** Set an empty option for working the chosen deselect functionality. */
        $options[''] = '';

        foreach ($icons as $icon)
        {
            $iconName = preg_replace("@^fa[sbr]\s+fa-@", '', $icon);
            $iconName = array_map(function($name) {
                return ucfirst($name);
            }, explode('-', $iconName));
            $options[$icon] = implode(' ', $iconName);
        }

        return $options;
    }

    public function getSidebarFields()
    {
        return [
            'megamenu' => [
                'type' => 'checkbox',
                'title' => Text::_('HELIX_ULTIMATE_ENABLE_MEGA_MENU'),
                'menu-builder' => true,
                'data' => ['itemid' => $this->itemId],
                'value' => $this->settings->megamenu ?? '',
                'internal' => true,
            ],
            'width' => [
                'type' => 'unit',
                'title' => Text::_('HELIX_ULTIMATE_MEGA_MENU_WIDTH'),
                'menu-builder' => true,
                'data' => ['itemid' => $this->itemId],
                'value' => $this->settings->width ?? '600px',
                'internal' => true,
            ],
            'showtitle' => [
                'type' => 'checkbox',
                'title' => Text::_('HELIX_ULTIMATE_SHOW_MENU_TITLE'),
                'menu-builder' => true,
                'data' => ['itemid' => $this->itemId],
                'value' => $this->settings->showtitle ?? '0',
                'internal' => true,
            ],
            'menualign' => [
                'type' => 'select',
                'title' => Text::_('HELIX_ULTIMATE_MEGA_MENU_ALIGNMENT'),
                'menu-builder' => true,
                'data' => ['itemid' => $this->itemId],
                'options' => [
                    'left' 		=> Text::_('HELIX_ULTIMATE_GLOBAL_LEFT'),
                    'center' 	=> Text::_('HELIX_ULTIMATE_GLOBAL_CENTER'),
                    'right' 	=> Text::_('HELIX_ULTIMATE_GLOBAL_RIGHT'),
                    'full' 		=> Text::_('HELIX_ULTIMATE_GLOBAL_FULL'),
                ],
                'value' => $this->settings->menualign ?? 'left',
                'internal' => true,
            ],
            'faicon' => [
                'type' => 'select',
                'title' => Text::_('HELIX_ULTIMATE_MENU_ICON'),
                'menu-builder' => true,
                'options' => $this->getFontOptions(),
                'data' => ['itemid' => $this->itemId, 'husearch' => 1],
                'value' => $this->settings->faicon ?? '',
                'internal' => true,
            ],
            'dropdown' => [
                'type' => 'select',
                'title' => Text::_('HELIX_ULTIMATE_MENU_DROPDOWN_POSITION'),
                'menu-builder' => true,
                'data' => ['itemid' => $this->itemId],
                'options' => [
                    'left' => Text::_('HELIX_ULTIMATE_GLOBAL_LEFT'),
                    'right' => Text::_('HELIX_ULTIMATE_GLOBAL_RIGHT'),
                ],
                'value' => $this->settings->dropdown ?? 'right',
                'internal' => true,
            ],
            'customclass' => [
                'type' => 'text',
                'title' => Text::_('HELIX_ULTIMATE_MENU_EXTRA_CLASS'),
                'placeholder' => Text::_('HELIX_ULTIMATE_MENU_EXTRA_CLASS_PLACEHOLDER'),
                'menu-builder' => true,
                'data' => ['itemid' => $this->itemId],
                'value' => $this->settings->customclass ?? '',
                'internal' => true,
            ],
            'badge' => [
                'type' => 'text',
                'title' => Text::_('HELIX_ULTIMATE_MENU_BADGE_TEXT'),
                'placeholder' => Text::_('HELIX_ULTIMATE_MENU_BADGE_TEXT'),
                'menu-builder' => true,
                'data' => ['itemid' => $this->itemId],
                'value' => $this->settings->badge ?? '',
                'internal' => true,
            ],
            'badge_position' => [
                'type' => 'select',
                'title' => Text::_('HELIX_ULTIMATE_MENU_BADGE_POSITION'),
                'menu-builder' => true,
                'data' => ['itemid' => $this->itemId],
                'options' => [
                    'left' => Text::_('HELIX_ULTIMATE_GLOBAL_LEFT'),
                    'right' => Text::_('HELIX_ULTIMATE_GLOBAL_RIGHT'),
                ],
                'value' => $this->settings->badge_position ?? 'right',
                'internal' => true,
            ],
            'badge_bg_color' => [
                'type' => 'color',
                'title' => Text::_('HELIX_ULTIMATE_MENU_BADGE_BACKGROUND'),
                'menu-builder' => true,
                'data' => ['itemid' => $this->itemId],
                'value' => $this->settings->badge_bg_color ?? '',
                'internal' => true,
            ],
            'badge_text_color' => [
                'type' => 'color',
                'title' => Text::_('HELIX_ULTIMATE_MENU_BADGE_COLOR'),
                'menu-builder' => true,
                'data' => ['itemid' => $this->itemId],
                'value' => $this->settings->badge_text_color ?? '',
                'internal' => true,
            ]
        ];
    }
}