<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Uri\Uri;

extract($displayData);

$menuItemSettings = new \stdClass;

if (!empty($menuSettings))
{
	$menuItemSettings = json_decode($menuSettings);

	if (isset($menuItemSettings->menu->$menuType->menuItems))
	{
		$menuItemSettings = $menuItemSettings->menu->$menuType->menuItems;
		$itemId = $item->id;

		if (isset($menuItemSettings->$itemId))
		{
			$menuItemSettings = $menuItemSettings->$itemId;
		}
	}
}

$fields = [
	'menu_custom_classes' => [
		'type' => 'text',
		'title' => Text::_('HELIX_ULTIMATE_MENU_EXTRA_CLASS'),
		'placeholder' => Text::_('HELIX_ULTIMATE_MENU_EXTRA_CLASS_PLACEHOLDER'),
		'menu-builder' => true,
		'data' => ['itemid' => $item->id],
		'value' => !empty($menuItemSettings->menu_custom_classes) ? $menuItemSettings->menu_custom_classes : '',
		'internal' => true,
	],
	'menu_icon' => [
		'type' => 'text',
		'title' => Text::_('HELIX_ULTIMATE_MENU_ICON'),
		'placeholder' => Text::_('HELIX_ULTIMATE_MENU_ICON_PLACEHOLDER'),
		'menu-builder' => true,
		'data' => ['itemid' => $item->id],
		'value' => !empty($menuItemSettings->menu_icon) ? $menuItemSettings->menu_icon : '',
		'internal' => true,
	],
	'menu_caption' => [
		'type' => 'text',
		'title' => Text::_('HELIX_ULTIMATE_MENU_CAPTION'),
		'placeholder' => Text::_('HELIX_ULTIMATE_MENU_CAPTION_PLACEHOLDER'),
		'menu-builder' => true,
		'data' => ['itemid' => $item->id],
		'value' => !empty($menuItemSettings->menu_caption) ? $menuItemSettings->menu_caption : '',
		'internal' => true,
	],
	'mega_menu' => [
		'type' => 'checkbox',
		'title' => Text::_('HELIX_ULTIMATE_ENABLE_MEGA_MENU'),
		'desc' => Text::sprintf('HELIX_ULTIMATE_ENABLE_MEGA_MENU_DESC', $item->title),
		'menu-builder' => true,
		'data' => ['itemid' => $item->id],
		'value' => !empty($menuItemSettings->mega_menu) ? $menuItemSettings->mega_menu : '',
		'internal' => true,
	]
];


?>
<div class="hu-menu-item-settings hu-menu-item-<?php echo $item->alias . ($active ? ' active' : ''); ?>" data-itemId="<?php echo $item->id; ?>">
	<div class="hu-menu-item-modifiers">
		<div class="row">
			<?php foreach (['menu_custom_classes', 'menu_icon', 'menu_caption'] as $fieldName): ?>
				<div class="col-4">
					<?php echo $builder->renderFieldElement($fieldName, $fields[$fieldName]); ?>
				</div>
			<?php endforeach ?>
		</div>
	</div>

	<div class="hu-mega-menu-settings">
		<div class="row">
			<div class="col-12">
				<?php echo $builder->renderFieldElement('mega_menu', $fields['mega_menu']); ?>
			</div>
		</div>
		<?php
			$layout = new FileLayout('fields.menuBuilder.megaSettings', HELIX_LAYOUT_PATH);
			echo $layout->render(
				[
					'item' => $item,
					'menuType' => $menuType,
					'menuItemSettings' => $menuItemSettings,
					'active' => $active,
					'params' => $params,
					'builder' => $builder
				]
			);
		?>
	</div>
</div>
