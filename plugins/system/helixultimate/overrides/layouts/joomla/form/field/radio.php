<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('JPATH_BASE') or die();

use Joomla\CMS\HTML\HTMLHelper;

extract($displayData);

/**
 * Layout variables
 * -----------------
 * @var   string   $autocomplete    Autocomplete attribute for the field.
 * @var   boolean  $autofocus       Is autofocus enabled?
 * @var   string   $class           Classes for the input.
 * @var   string   $description     Description of the field.
 * @var   boolean  $disabled        Is this field disabled?
 * @var   string   $group           Group the field belongs to. <fields> section in form XML.
 * @var   boolean  $hidden          Is this field hidden in the form?
 * @var   string   $hint            Placeholder for the field.
 * @var   string   $id              DOM id of the field.
 * @var   string   $label           Label of the field.
 * @var   string   $labelclass      Classes to apply to the label.
 * @var   boolean  $multiple        Does this field support multiple values?
 * @var   string   $name            Name of the input field.
 * @var   string   $onchange        Onchange attribute for the field.
 * @var   string   $onclick         Onclick attribute for the field.
 * @var   string   $pattern         Pattern (Reg Ex) of value of the form field.
 * @var   boolean  $readonly        Is this field read only?
 * @var   boolean  $repeat          Allows extensions to duplicate elements.
 * @var   boolean  $required        Is this field required?
 * @var   integer  $size            Size attribute of the input.
 * @var   boolean  $spellcheck      Spellcheck state for the form field.
 * @var   string   $validate        Validation rules to apply.
 * @var   string   $value           Value attribute of the field.
 * @var   array    $options         Options available for this field.
 */

// Including fallback code for HTML5 non supported browsers.
HTMLHelper::_('jquery.framework');
HTMLHelper::_('script', 'system/html5fallback.js', array('version' => 'auto', 'relative' => true));

/**
 * The format of the input tag to be filled in using sprintf.
 *     %1 - id
 *     %2 - name
 *     %3 - value
 *     %4 = any other attributes
 */
$format = '<input type="radio" id="%1$s" name="%2$s" value="%3$s" %4$s />';
$alt    = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $name);
?>
<div id="<?php echo $id; ?>" class="<?php echo trim($class . ' radio'); ?>"
	<?php echo $disabled ? 'disabled' : ''; ?>
	<?php echo $required ? 'required aria-required="true"' : ''; ?>
	<?php echo $autofocus ? 'autofocus' : ''; ?>>

	<?php if (!empty($options)) : ?>
		<?php foreach ($options as $i => $option) : ?>
			<?php
				// Initialize some option attributes.
				$checked  = ((string) $option->value === $value) ? 'checked="checked"' : '';
				$optionClass    = !empty($option->class) ? 'class="form-check-input ' . $option->class . '"' : 'class="form-check-input"';
				$labelClass    = !empty($option->class) ? 'class="form-check-label ' . $option->class . '"' : 'class="form-check-label btn"';
				$disabled = !empty($option->disable) || ($disabled && !$checked) ? 'disabled' : '';

				// Initialize some JavaScript option attributes.
				$onclick    = !empty($option->onclick) ? 'onclick="' . $option->onclick . '"' : '';
				$onchange   = !empty($option->onchange) ? 'onchange="' . $option->onchange . '"' : '';
				$oid        = $id . $i;
				$ovalue     = htmlspecialchars($option->value ?? "", ENT_COMPAT, 'UTF-8');
				$attributes = array_filter(array($checked, $optionClass, $disabled, $onchange, $onclick));
			?>

			<?php if ($required) : ?>
				<?php $attributes[] = 'required aria-required="true"'; ?>
			<?php endif; ?>
			<div class="form-check form-check-inline">
				<label for="<?php echo $oid; ?>" <?php echo $labelClass; ?>>
					<?php echo sprintf($format, $oid, $name, $ovalue, implode(' ', $attributes)); ?> <?php echo $option->text; ?>
				</label>
			</div>
		<?php endforeach; ?>
	<?php endif; ?>
</div>
