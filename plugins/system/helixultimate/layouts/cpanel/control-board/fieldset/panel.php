<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use Joomla\CMS\Layout\LayoutHelper;

extract($displayData);

$fields 	= $form->getFieldset($key);
$groups = array();

if (!empty($fields))
{
	foreach ($fields as $i => $field)
	{
		$group = $field->getAttribute('helixgroup') ? $field->getAttribute('helixgroup') : 'no-group';
		$groups[$group]['fields'][] = $field;
	}
}

$headerTitle = implode(' ', explode('_', $fieldset->name));

?>

<div class="helix-ultimate-edit-panel <?php echo strtolower($fieldset->name); ?>-panel">
	<div class="helix-ultimate-panel-header">
		<span><?php echo ucwords($headerTitle); ?></span>
		<button type="button" role="button" class="helix-ultimate-panel-close" data-sidebarclass="<?php echo 'helix-ultimate-fieldset-' . $fieldset->name; ?>">
			<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M13.5934 11.88C14.0734 12.36 14.0734 13.1066 13.5934 13.5866C13.3534 13.8266 13.0601 13.9333 12.7401 13.9333C12.4201 13.9333 12.1267 13.8266 11.8867 13.5866L7.00673 8.70665L2.12671 13.5866C1.88671 13.8266 1.59339 13.9333 1.27339 13.9333C0.953392 13.9333 0.660072 13.8266 0.420072 13.5866C-0.0599284 13.1066 -0.0599284 12.36 0.420072 11.88L5.30005 6.99998L0.420072 2.11998C-0.0599284 1.63998 -0.0599284 0.893311 0.420072 0.413311C0.900072 -0.066689 1.64671 -0.066689 2.12671 0.413311L7.00673 5.29331L11.8867 0.413311C12.3667 -0.066689 13.1134 -0.066689 13.5934 0.413311C14.0734 0.893311 14.0734 1.63998 13.5934 2.11998L8.71337 6.99998L13.5934 11.88Z" fill="#FFF"/>
			</svg>
		</button>
	</div>
	<div class="helix-ultimate-groups-container">
		<?php echo LayoutHelper::render('cpanel.control-board.fieldset.groups', ['groups' => $groups, 'fieldset_name' => $fieldset->name], HELIX_LAYOUTS_PATH); ?>
	</div>
</div>