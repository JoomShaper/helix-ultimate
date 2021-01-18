<?php

use Joomla\CMS\Language\Text;
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

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
     * @var     array   The mega menu settings.
     * @since   2.0.0
     */
    private $mega = [];

    /**
     * Constructor function for the class.
     *
     * @param   array   $mega   The mega menu settings array.
     *
     * @since   2.0.0
     */
    public function __construct($mega)
    {
        $this->mega = $mega;
    }

    public function getSidebarFields()
    {
        return [
            'mega' => [
                'type' => 'checkbox',
                'title' => Text::_('HELIX_ULTIMATE_ENABLE_MEGA_MENU'),
                'desc' => Text::sprintf('HELIX_ULTIMATE_ENABLE_MEGA_MENU_DESC', ''),
                'menu-builder' => true,
                // 'data' => ['itemid' => $itemId],
                'value' => '',
                'internal' => true,
            ],
            'width' => [
                'type' => 'text',
                'title' => Text::_('HELIX_ULTIMATE_MEGA_MENU_WIDTH'),
                'menu-builder' => true,
                // 'data' => ['itemid' => $itemId],
                'value' => '',
                'internal' => true,
            ],
            'show_title' => [
                'type' => 'checkbox',
                'title' => Text::_('HELIX_ULTIMATE_SHOW_MENU_TITLE'),
                'menu-builder' => true,
                // 'data' => ['itemid' => $itemId],
                'value' => '',
                'internal' => true,
            ],
            'alignment' => [
                'type' => 'select',
                'title' => Text::_('HELIX_ULTIMATE_MEGA_MENU_ALIGNMENT'),
                'desc' => Text::_('HELIX_ULTIMATE_MEGA_MENU_ALIGNMENT_DESC'),
                'menu-builder' => true,
                // 'data' => ['rowid' => $rowId, 'itemid' => $item->id, 'columnid' => $id],
                'options' => [
                    'left' 		=> Text::_('HELIX_ULTIMATE_GLOBAL_LEFT'),
                    'center' 	=> Text::_('HELIX_ULTIMATE_GLOBAL_CENTER'),
                    'right' 	=> Text::_('HELIX_ULTIMATE_GLOBAL_RIGHT'),
                    'full' 		=> Text::_('HELIX_ULTIMATE_GLOBAL_FULL'),
                ],
                // 'value' => !empty($settings->col_type) ? $settings->col_type : '',
                'internal' => true,
            ],
            'icon' => [
                'type' => 'text',
                'title' => Text::_('HELIX_ULTIMATE_MENU_ICON'),
                'placeholder' => Text::_('HELIX_ULTIMATE_MENU_ICON_PLACEHOLDER'),
                'menu-builder' => true,
                // 'data' => ['itemid' => $item->id],
                // 'value' => !empty($menuItemSettings->menu_icon) ? $menuItemSettings->menu_icon : '',
                'internal' => true,
            ],
            'custom_class' => [
                'type' => 'text',
                'title' => Text::_('HELIX_ULTIMATE_MENU_EXTRA_CLASS'),
                'placeholder' => Text::_('HELIX_ULTIMATE_MENU_EXTRA_CLASS_PLACEHOLDER'),
                'menu-builder' => true,
                // 'data' => ['itemid' => $item->id],
                // 'value' => !empty($menuItemSettings->menu_custom_classes) ? $menuItemSettings->menu_custom_classes : '',
                'internal' => true,
            ],
            'badge' => [
                'type' => 'text',
                'title' => Text::_('HELIX_ULTIMATE_MENU_BADGE_TEXT'),
                'placeholder' => Text::_('HELIX_ULTIMATE_MENU_BADGE_TEXT'),
                'menu-builder' => true,
                // 'data' => ['itemid' => $item->id],
                // 'value' => !empty($menuItemSettings->menu_badge) ? $menuItemSettings->menu_badge : '',
                'internal' => true,
            ],
            'badge_position' => [
                'type' => 'select',
                'title' => Text::_('HELIX_ULTIMATE_MENU_BADGE_POSITION'),
                'menu-builder' => true,
                // 'data' => ['itemid' => $item->id],
                'options' => [
                    'left' => Text::_('HELIX_ULTIMATE_GLOBAL_LEFT'),
                    'right' => Text::_('HELIX_ULTIMATE_GLOBAL_RIGHT'),
                ],
                // 'value' => !empty($menuItemSettings->menu_badge_position) ? $menuItemSettings->menu_badge_position : '',
                'internal' => true,
            ],
            'badge_background' => [
                'type' => 'color',
                'title' => Text::_('HELIX_ULTIMATE_MENU_BADGE_BACKGROUND'),
                'menu-builder' => true,
                // 'data' => ['itemid' => $item->id],
                'options' => [
                    'left' => Text::_('HELIX_ULTIMATE_GLOBAL_LEFT'),
                    'right' => Text::_('HELIX_ULTIMATE_GLOBAL_RIGHT'),
                ],
                // 'value' => !empty($menuItemSettings->menu_badge_background) ? $menuItemSettings->menu_badge_background : '',
                'internal' => true,
            ],
            'badge_color' => [
                'type' => 'color',
                'title' => Text::_('HELIX_ULTIMATE_MENU_BADGE_COLOR'),
                'menu-builder' => true,
                // 'data' => ['itemid' => $item->id],
                'options' => [
                    'left' => Text::_('HELIX_ULTIMATE_GLOBAL_LEFT'),
                    'right' => Text::_('HELIX_ULTIMATE_GLOBAL_RIGHT'),
                ],
                // 'value' => !empty($menuItemSettings->menu_badge_color) ? $menuItemSettings->menu_badge_color : '',
                'internal' => true,
            ]
        ];
    }
}