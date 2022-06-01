<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

use HelixUltimate\Framework\Platform\Settings;

defined ('JPATH_BASE') or die();
?>

<fieldset class="<?php echo !empty($displayData->formclass) ? $displayData->formclass : 'form-horizontal'; ?>">
	<legend><?php echo $displayData->name; ?></legend>
	<?php if (!empty($displayData->description)) : ?>
		<p><?php echo $displayData->description; ?></p>
	<?php endif; ?>
	<?php $fieldsnames = explode(',', $displayData->fieldsname); ?>
	<?php foreach ($fieldsnames as $fieldname) : ?>
		<?php foreach ($displayData->form->getFieldset($fieldname) as $field) : ?>
			<?php
				$showon = $field->getAttribute('showon');
				$attribs = '';
				if ($showon) 
				{
					$attribs .= ' data-showon=\'' . json_encode(Settings::parseShowOnConditions($showon, $field->formControl)) . '\'';
				}
				// Enable disable on
				$enableOn = $field->getAttribute('enableon', '');
				if ($enableOn)
				{
					$attribs .= ' data-enableon="' . $enableOn . '"';
				}
			?>
			<div <?php echo $attribs; ?>><?php echo $field->input; ?></div>
		<?php endforeach; ?>
	<?php endforeach; ?>
</fieldset>
