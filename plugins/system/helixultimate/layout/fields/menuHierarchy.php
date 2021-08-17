<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use HelixUltimate\Framework\Platform\Helper;
use HelixUltimate\Framework\Platform\Settings;
use Joomla\CMS\Language\Text;

/**
 * Text field.
 *
 * @since	 1.0.0
 */
class HelixultimateFieldMenuHierarchy
{
	/**
	 * Get input for the field.
	 *
	 * @param	string	$key
	 * @param	array	$attr
	 *
	 * @return	string
	 * @since	1.0.0
	 */
	public static function getInput($key, $attr)
	{
		$itemId = $attr['itemid'];
		$dataAttrs = '';
		$value = isset($attr['value']) ? $attr['value'] : '';
		$depend = isset($attr['depend']) ? $attr['depend'] : false;
		$className = $attr['class'] ?? '';
		$internal = !empty($attr['internal']) ? ' internal-use-only' : '';

		if ($depend)
		{
			$showon = Settings::parseShowOnConditions($attr['depend']);
			$dataShowon = ' data-revealon=\'' . json_encode($showon) . '\' ';
		}

		if (!empty($value) && \is_string($value))
		{
			$value = json_decode($value, true);
		}
		else
		{
			$value = [];
		}

		if (!empty($attr['data']))
		{
			foreach ($attr['data'] as $dataName => $dataValue)
			{
				$dataAttrs .= ' data-' . $dataName . '=' . $dataValue;
			}
		}

		$html = [];
		$html[]  = '<div class="control-group hu-menu-hierarchy-container ' . $key . ' ' . $className . '" ' . $dataShowon . '>';
		$html[] = '<label>' . $attr['title'] . '</label>';

		if (!empty($attr['desc']))
		{
			$html[] = '<label class="hu-help-icon hu-ml-2 fas fa-info-circle"></label>';
			$html[] = '<p class="hu-control-help">' . $attr['desc'] . '</p>';
		}

		$menuElements = new \stdClass;
		$menuElements->$itemId = new \stdClass;
		$menuElements->$itemId->id = $itemId;
		$menuElements->$itemId->title = 'root';
		$menuElements->$itemId->children = [];

		Helper::getMenuItems($itemId, $menuElements);

		/**
		 * Calculate the total menu Items for detecting the select all checkbox.
		 */
		$totalElements = 0;
		$children = $menuElements->$itemId->children;
		$totalElements += count($children);
		$allElements = [];
		$allElements = array_merge($allElements, $children);

		if (empty($children))
		{
			$html[] = '<div><strong>There is not child items for this menu item.</strong></div>';
			$value = [];
		}
		else
		{
			while (count($children))
			{
				$child = array_shift($children);

				if (!empty($menuElements->$child->children))
				{
					array_unshift($children, ...$menuElements->$child->children);
					$totalElements += count($menuElements->$child->children);
					$allElements = array_merge($allElements, $menuElements->$child->children);
				}
			}

			if (!empty($menuElements) && !empty($menuElements->$itemId->children))
			{
				$checkAll = count($value) === $totalElements ? 'checked="checked"' : '';
				$elements = ' data-elements=\'' . json_encode($allElements) . '\'';

				$html[] = '<ul class="hu-menu-hierarchy-list">';
				$html[] = '<li class="hu-menu-hierarchy-item level-0">';
				$html[] = '	<label class="hu-menu-item-title">';
				$html[] = '		<input type="checkbox" class="hu-input hu-menu-item-selector select-all level-0 ' . $internal . '" data-level="0" ' . $checkAll . $elements . '/>';
				$html[] = '		<span>' . Text::_('HELIX_ULTIMATE_MENU_HIERARCHY_SELECT_ALL') . '</span>';
				$html[] = '	</label>';
				$html[] = '</li>';
				$children = $menuElements->$itemId->children;

				while (count($children) > 0)
				{
					$child = array_shift($children);

					$margin = ($menuElements->$child->level - 2) * 10;
					$level = $menuElements->$child->level - 1;
					$val = $menuElements->$child->id;
					$check = \in_array($val, $value) ? 'checked="checked"' : '';

					$html[] = '<li class="hu-menu-hierarchy-item level-' . $level . '">';
					$html[] = '	<label class="hu-menu-item-title">';
					$html[] = '		<input type="checkbox" class="hu-input hu-menu-item-selector level-' . $level . $internal . '" value="' . $val . '" data-level="' . $level . '" ' . $check . ' />';
					$html[] = '		<span style="margin-left: ' . $margin . 'px;">' . $menuElements->$child->title . '</span>';
					$html[] = '	</label>';
					$html[] = '</li>';

					if (!empty($menuElements->$child->children))
					{
						array_unshift($children, ...$menuElements->$child->children);
					}
				}

				$html[] = '</ul>';
			}
		}

		if (\is_array($value))
		{
			$value = json_encode($value);
		}

		$html[] = '<input type="hidden" class="' . $internal . '" name="' . $key . '" value=\'' . $value . '\' ' . $dataAttrs . ' />';
		$html[] = '</div>';

		return implode("\n", $html);
	}

}
