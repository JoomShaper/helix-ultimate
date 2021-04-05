<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\FormField;


/**
 * Form field for Helix mega menu
 *
 * @since	1.0.0
 */
class JFormFieldHelixmegamenu extends FormField
{
	/**
	 * Field type
	 *
	 * @var		string	$type
	 * @since	1.0.0
	 */
	protected $type = "Helixmegamenu";

	/**
	 * Row layouts.
	 *
	 * @var		array	Layouts.
	 * @since	1.0.0
	 */
	private $row_layouts = array('12', '6+6', '4+4+4', '3+3+3+3', '2+2+2+2+2+2', '5+7', '4+8','3+9','2+10');

	/**
	 * Override getInput function form FormField
	 *
	 * @return	string	Field HTML string
	 * @since	1.0.0
	 */
	public function getInput()
	{
		$html  = '<div>';
		$html .= $this->getMegaSettings();
		$html .= '<input type="hidden" name="' . $this->name . '" id="' . $this->id . '" value="' . $this->value . '">';
		$html .= '</div>';

		return $html;
	}

	/**
	 * Get mega menu settings.
	 *
	 * @return	string	Megamenu settings HTML string.
	 * @since	1.0.0
	 */
	public function getMegaSettings()
	{
		$mega_menu_path = JPATH_SITE . '/plugins/system/helixultimate/fields/';
		$menu_data = json_decode($this->value);
		$menu_item = $this->form->getData()->toObject();

		ob_start();
		include_once dirname(__DIR__) . '/Core/Lib/helixmenuhelper.php';
		$html = ob_get_clean();

		return $html;
	}

	/**
	 * Get module name ID.
	 *
	 * @param	mixed	$id		Module ID.
	 *
	 * @return 	mixed	Module list or module object
	 * @since 	1.0.0
	 */
	private function getModuleNameById($id = 'all')
	{
		$db = Factory::getDBO();
		$query = $db->getQuery(true);

		$query->select($db->quoteName(array('id','title')));
		$query->from($db->quoteName('#__modules'));
		$query->where($db->quoteName('published') . ' = 1');
		$query->where($db->quoteName('client_id') . ' = 0');

		if ($id !== 'all')
		{
			$query->where($db->quoteName('id') . ' = ' . (int) $id);
		}

		$db->setQuery($query);

		if ($id !== 'all')
		{
			return $db->loadObject();
		}

		return $db->loadObjectList();
	}

	/**
	 * Get unique menu items.
	 *
	 * @param	integer		$current_menu_id	The running menu item id.
	 * @param	array		$layout				Layouts.
	 *
	 * @return 	array
	 * @since	1.0.0
	 */
	private function uniqueMenuItems($current_menu_id, $layout = array())
	{
		$saved_menu_items = array();

		$items = $this->menuItems();
		$children = isset($items[$current_menu_id]) ? $items[$current_menu_id] : array();

		if (!$layout)
		{
			return $children;
		}

		foreach ($layout as $key => $row)
		{
			foreach ($row->attr as $col_key => $col)
			{
				if ($col->items)
				{
					foreach ($col->items as $item)
					{
						if ($item->type === 'menu_item')
						{
							unset($children[$item->item_id]);
						}
					}
				}
			}
		}

		return $children;
	}

	/**
	 * Menu items.
	 *
	 * @return	array
	 * @since	1.0.0
	 */
	private function menuItems()
	{
		$menus = new \JMenuSite;
		$menus = $menus->getMenu();
		$new = array();

		foreach ($menus as $item)
		{
			$new[$item->parent_id][$item->id] = $item->id;
		}

		return $new;
	}

	/**
	 * Select option field HTML.
	 *
	 * @param	string	$name			Field name.
	 * @param	string	$label			Field label.
	 * @param	array	$lsit			Option list.
	 * @param	string	$default		Default value.
	 * @param	string	$display_class	Select class.
	 *
	 * @return	string	Select option HTML string.
	 * @since	1.0.0
	 */
	private function selectFieldHTML($name, $label, $list, $default, $display_class = '')
	{
		$view_class = '';

		if ($name === 'alignment')
		{
			$view_class = 'hu-megamenu-field-control ' . $display_class;
		}
		elseif ($name === 'dropdown')
		{
			$view_class = 'hu-dropdown-field-control ' . $display_class;
		}

		$html  = '';
		$html .= '<div class="' . $view_class . '">';
		$html .= '<span class="hu-megamenu-label">' . $label . '</span>';
		$html .= '<select id="hu-megamenu-' . $name . '">';

		if ($name === 'fa-icon')
		{
			$html .= '<option value="">' . Text::_('HELIX_ULTIMATE_GLOBAL_SELECT') . '</option>';

			foreach ($list as $each)
			{
				$html .= '<option value="' . $each . '"' . (($default === $each) ? 'selected' : '') . '>' . str_replace('fa-', '', $each) . '</option>';
			}
		}
		else
		{
			foreach ($list as $key => $each)
			{
				$html .= '<option value="' . $key . '"' . (($default === $key) ? 'selected' : '') . '>' . $each . '</option>';
			}
		}

		$html .= '</select>';
		$html .= '</div>';

		return $html;
	}

	/**
	 * Color field HTML.
	 *
	 * @param	string	$name			Field name.
	 * @param	string	$label			Field label.
	 * @param	string	$placeholder	Field placeholder.
	 * @param	string	$value			Default value.
	 *
	 * @return	string	Color field HTML string.
	 * @since	1.0.0
	 */
	private function colorFieldHTML($name, $label, $placeholder, $value)
	{
		$html  = '';
		$html .= '<div>';
		$html .= '<span class="hu-megamenu-label">' . $label . '</span>';
		$html .= '<input type="text" class="minicolors" id="hu-menu-badge-' . $name . '" placeholder="' . $placeholder . '" value="' . $value . '" />';
		$html .= '</div>';

		return $html;
	}

	/**
	 * Text field HTML.
	 *
	 * @param	string	$name			Field name.
	 * @param	string	$label			Field label.
	 * @param	string	$placeholder	Field placeholder.
	 * @param	string	$value			Default value.
	 * @param	string	$type			Field type.
	 * @param	string	$display_class	Field class name.
	 *
	 * @return	string	Text field HTML
	 * @since	1.0.0
	 */
	private function textFieldHTML($name, $label, $placeholder, $value, $type = 'text', $display_class = '')
	{
		if ($type === 'number')
		{
			$display_class = 'hu-megamenu-field-control' . $display_class;
		}

		$html  = '';
		$html .= '<div class="' . $display_class . '">';
		$html .= '<span class="hu-megamenu-label">' . $label . '</span>';
		$html .= '<input type="' . $type . '" id="hu-megamenu-' . $name . '" placeholder="' . $placeholder . '" value="' . $value . '" />';
		$html .= '</div>';

		return $html;
	}

	/**
	 * Switch Field HTML.
	 *
	 * @param	string	$name	Field name.
	 * @param	string	$label	Field label.
	 * @param	string	$value	Defaulf value.
	 *
	 * @return 	string	Switch field HTML string.
	 * @since	1.0.0
	 */
	private function switchFieldHTML($name, $label, $value)
	{
		$html  = '';
		$html .= '<div>';
		$html .= '<span class="hu-megamenu-label">' . $label . '</span>';
		$html .= '<input type="checkbox" class="hu-checkbox" id="hu-megamenu-' . $name . '" ' . (!empty($value) ? 'checked' : '') . '/>';
		$html .= '</div>';

		return $html;
	}
}
