<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
namespace HelixUltimate\Framework\Platform\Builders;

use Joomla\CMS\Filesystem\Folder;

/**
 * Base builder class.
 *
 * @since   2.0.0
 */
class Builder
{
	/**
	 * Constructor function for the builder class.
	 *
	 * @since	2.0.0
	 */
	public function __construct()
	{
		$this->includeFields();
	}

	/**
	 * Include all the fields from the layout path.
	 *
	 * @return	void
	 * @since	2.0.0
	 */
	protected function includeFields()
	{
		$fields = Folder::files(HELIX_LAYOUT_PATH . '/fields', '\.php$', false, true);

		if (!empty($fields))
		{
			foreach ($fields as $field)
			{
				require_once $field;
			}
		}
	}

	/**
	 * Render field element
	 *
	 * @param	string	$key
	 * @param	array	$attr
	 *
	 * @return	string	HTML string for the field element rendering
	 * @since	2.0.0
	 */
	public function renderFieldElement($key, $attr)
	{
		return \call_user_func_array(
			['HelixultimateField' . ucfirst($attr['type']), 'getInput'],
			[$key, $attr]
		);
	}
}