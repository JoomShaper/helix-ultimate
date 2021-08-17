<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use HelixUltimate\Framework\Platform\Settings;
use Joomla\CMS\Language\Text;

/**
 * Text field.
 *
 * @since	 1.0.0
 */
class HelixultimateFieldMenuType
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
		$attributes = (isset($attr['placeholder']) && $attr['placeholder']) ? 'placeholder="' . $attr['placeholder'] . '"' : '';

		$value = !empty($attr['value']) ? $attr['value'] : '';
		$depend = isset($attr['depend']) ? $attr['depend'] : false;
		$className = $attr['class'] ?? '';
		$dataAttrs = '';
		$dataShowon = '';
		$internal = !empty($attr['internal']) ? ' internal-use-only' : '';

		if ($depend)
		{
			$showon = Settings::parseShowOnConditions($attr['depend']);
			$dataShowon = ' data-revealon=\'' . json_encode($showon) . '\' ';
		}

		if (!empty($attr['data']))
		{
			foreach ($attr['data'] as $dataName => $dataValue)
			{
				$dataAttrs .= ' data-' . $dataName . '=' . $dataValue;
			}
		}

		$output  = '<div class="control-group ' . $className . '" ' . $dataShowon . ' >';
		$output .= '<label>' . $attr['title'] . '</label>';

		if (!empty($attr['desc']))
		{
			$output .= '<span class="hu-help-icon hu-ml-2 fas fa-info-circle"></span>';
			$output .= '<p class="hu-control-help">' . $attr['desc'] . '</p>';
		}

		$output .= self::menuTypesField();

		$output .= '</div>';

		return $output;
	}

	private static function menuTypesField()
	{
		$html = [];
		$recordId = 0;

		$types = self::getMenuTypes();

		$tmpl = '1';
		$tmpl = "'" . \json_encode($tmpl, \JSON_NUMERIC_CHECK) . "'";
		$dropdownText = 'Select Menu Type';

		$html[] = '<a href="javascript:" class="hu-btn hu-btn-outline text-start hu-btn-block hu-has-dropdown hu-dropdown-toggle" data-target="#menuTypeDropdown">' . $dropdownText . '</a>';

		if (!empty($types))
		{
			$html[] = '<ul class="hu-dropdown" id="menuTypeDropdown">';

			foreach ($types as $name => $children)
			{
				if (!empty($children))
				{
					$html[] = '<li class="hu-has-submenu">';
					$html[] = '<a class="hu-dropdown-item">' . $name . '</a>';
					$html[] = '<ul class="hu-submenu">';

					foreach ($children as $child)
					{
						$menuType = [
							'id' => $recordId,
							'title' => isset($child->type) ? $child->type : Text::_($child->title),
							'request' => $child->request
						];
						$menuType = base64_encode(json_encode($menuType));

						$html[] = '<li><a class="hu-dropdown-item hu-menu-type-item" href="javascript:" data-menutype="' . $menuType . '" >';
						$html[] = '<h4 class="hu-menu-item-title">' . Text::_($child->title) . '</h4>';
						$html[] = '<span class="text-mute">' . Text::_($child->description) . '</span>';
						$html[] = '</a></li>';
					}

					$html[] = '</ul>';
					$html[] = '</li>';
				}
				else
				{
					$html[] = '<li><a class="hu-dropdown-item" href="javascript:">' . $name . '</a></li>';
				}
			}

			$html[] = '</ul>';
		}
		
		return implode("\n", $html);
	}

	public static function getMenuTypes()
	{
		$classUrl = JPATH_ADMINISTRATOR . '/components/com_menus/models/menutypes.php';
		$helperUrl = JPATH_ADMINISTRATOR . '/components/com_menus/helpers/menus.php';

		if (!\class_exists('MenusModelMenutypes'))
		{
			require_once $classUrl;
		}

		if (!\class_exists('MenusHelper'))
		{
			require_once $helperUrl;
		}

		$model = new \MenusModelMenutypes;

		$types = $model->getTypeOptions();

		self::addCustomTypes($types);

		$sortedTypes = [];

		foreach ($types as $name => $list)
		{
			$tmp = [];

			foreach ($list as $item)
			{
				$tmp[Text::_($item->title)] = $item;
			}

			\uksort($tmp, function($a, $b) {
				return \strcasecmp($a, $b);
			});

			$sortedTypes[Text::_($name)] = $tmp;
		}

		\uksort($sortedTypes, function($a, $b) {
			return \strcasecmp($a, $b);
		});

		return $sortedTypes;
	}

	private static function addCustomTypes(&$types)
	{
		if (empty($types))
		{
			$types = array();
		}

		// Adding System Links
		$list           = array();
		$o              = new \stdClass;
		$o->title       = 'HELIX_ULTIMATE_TYPE_EXTERNAL_URL';
		$o->type        = 'url';
		$o->description = 'HELIX_ULTIMATE_TYPE_EXTERNAL_URL_DESC';
		$o->request     = null;
		$list[]         = $o;

		$o              = new \stdClass;
		$o->title       = 'HELIX_ULTIMATE_TYPE_ALIAS';
		$o->type        = 'alias';
		$o->description = 'HELIX_ULTIMATE_TYPE_ALIAS_DESC';
		$o->request     = null;
		$list[]         = $o;

		$o              = new \stdClass;
		$o->title       = 'HELIX_ULTIMATE_TYPE_SEPARATOR';
		$o->type        = 'separator';
		$o->description = 'HELIX_ULTIMATE_TYPE_SEPARATOR_DESC';
		$o->request     = null;
		$list[]         = $o;

		$o              = new \stdClass;
		$o->title       = 'HELIX_ULTIMATE_TYPE_HEADING';
		$o->type        = 'heading';
		$o->description = 'HELIX_ULTIMATE_TYPE_HEADING_DESC';
		$o->request     = null;
		$list[]         = $o;

		$types['HELIX_ULTIMATE_TYPE_SYSTEM'] = $list;
	}

}
