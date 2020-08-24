<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use HelixUltimate\Framework\Platform\Helper;

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

		if (!empty($attr['data']))
		{
			foreach ($attr['data'] as $dataName => $dataValue)
			{
				$dataAttrs .= ' data-' . $dataName . '=' . $dataValue;
			}
		}

		$html = [];
		$menuElements = new \stdClass;
		$menuElements->$itemId = new \stdClass;
		$menuElements->$itemId->id = $itemId;
		$menuElements->$itemId->title = 'root';
		$menuElements->$itemId->children = [];

		Helper::getMenuElements($itemId, $menuElements);

		if (!empty($menuElements) && !empty($menuElements->$itemId->children))
		{
			$html[] = '<ul>';
			$children = $menuElements->$itemId->children;

			while (count($children) > 0)
			{
				$child = array_shift($children);
				$html[] = '<li>' . str_repeat('-', $menuElements->$child->level) . $menuElements->$child->title . '</li>';

				if (!empty($menuElements->$child->children))
				{
					array_push($children, ...$menuElements->$child->children);
				}
			}

			$html[] = '</ul>';
		}

		return implode("\n", $html);
	}

}
