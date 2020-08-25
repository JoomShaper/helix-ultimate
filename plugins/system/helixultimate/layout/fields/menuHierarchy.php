<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use HelixUltimate\Framework\Platform\Helper;
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

		if (!empty($attr['data']))
		{
			foreach ($attr['data'] as $dataName => $dataValue)
			{
				$dataAttrs .= ' data-' . $dataName . '=' . $dataValue;
			}
		}

		$html = [];
		$html[]  = '<div class="control-group hu-menu-hierarchy-container ' . $key . ' ' . ($depend ? 'hidden' : '') . '" ' . ($depend ? 'data-depend="' . $depend . '"' : '') . '>';
		$html[] = '<label>' . $attr['title'] . '</label>';

		if (!empty($attr['desc']))
		{
			$html[] = '<label class="hu-help-icon hu-ml-2 fas fa-info-circle"></label>';
			$html[] = '<p class="control-help">' . $attr['desc'] . '</p>';
		}

		$menuElements = new \stdClass;
		$menuElements->$itemId = new \stdClass;
		$menuElements->$itemId->id = $itemId;
		$menuElements->$itemId->title = 'root';
		$menuElements->$itemId->children = [];

		Helper::getMenuItems($itemId, $menuElements);

		if (!empty($menuElements) && !empty($menuElements->$itemId->children))
		{
			$html[] = '<ul class="hu-menu-hierarchy-list">';
			$html[] = '<li class="hu-menu-hierarchy-item level-0">';
			$html[] = '	<label class="hu-menu-item-title">';
			$html[] = '		<input type="checkbox" class="hu-input hu-menu-item-selector select-all level-0" data-level="0"/>';
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

				$html[] = '<li class="hu-menu-hierarchy-item level-' . $level . '">';
				$html[] = '	<label class="hu-menu-item-title">';
				$html[] = '		<input type="checkbox" class="hu-input hu-menu-item-selector level-' . $level . '" value="' . $val . '" data-level="' . $level . '" />';
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

		$html[] = '<input type="hidden" name="' . $key . '" value=\'' . $value . '\' />';
		$html[] = '</div>';

		return implode("\n", $html);
	}

}
