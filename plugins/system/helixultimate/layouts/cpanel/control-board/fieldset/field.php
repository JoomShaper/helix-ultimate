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
use HelixUltimate\Framework\System\JoomlaBridge;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

extract($displayData);

?>
<?php

	/**
	 * Apply chosen for the multiple select field for J3!
	 * @TODO: apply chosen for J4 multiple select.
	 */
	if (JoomlaBridge::getVersion('major') < 4)
	{
		HTMLHelper::_('formbehavior.chosen', 'select[multiple]');
	}

	$showon = $field->getAttribute('showon');
	$attribs = '';

	if ($showon)
	{
		$attribs .= ' data-showon=\'' . json_encode(Settings::parseShowOnConditions($showon)) . '\'';
	}

	$setvalue = '';

	if (\is_array($field->value) || \is_object($field->value))
	{
		$setvalue = json_encode($field->value);
	}
	else
	{
		$setvalue = $field->value;
	}

	$track = $field->getAttribute('track');
	$hasTrack = true;

	if (!empty($track))
	{
		$hasTrack = !($track === 'false' || $track === 'off');
	}

	$hideLabel = $field->getAttribute('hideLabel', false);
	$description = Text::_($field->getAttribute('description', ''));
	$type = $field->getAttribute('type', 'text');
	$multiple = $field->getAttribute('multiple');
	$multiple = isset($multiple) && ($multiple === 'true' || $multiple === 'on');
	$separator = $field->getAttribute('separator');

	$separator = isset($separator) && ($separator === 'true' || $separator === 'on') ? true : false;

	// Enable disable on
	$enableOn = $field->getAttribute('enableon', '');

	if ($enableOn)
	{
		$attribs .= ' data-enableon="' . $enableOn . '"';
	}

	$checkboxStyle = $field->getAttribute('style', 'switch');
	$className = $field->getAttribute('className', '');

	// Group Class
	$group_class = (($group) ? 'group-style-' . $group : '');

	if ($type === 'checkbox')
	{
		$group_class .= ($checkboxStyle === 'plain') ? ' hu-style-checkbox': ' hu-style-switcher';
	}

	$group_class .= $separator ? ' hu-field-separator': '';
	$group_class .= !empty($className) ? ' ' . $className : '';

	$listStyle = $field->getAttribute('style');
	$display = $field->getAttribute('display', '');
	$invalidDataFields = ['before_head', 'after_body', 'before_body', 'custom_css', 'custom_js'];
	$isValidDataField = !\in_array($field->name, $invalidDataFields);
?>

<div class="<?php echo $group_class; ?>" <?php echo $attribs; ?>>
	<div class="control-group">
		<div class="control-group-inner<?php echo $display === 'inline' ? ' hu-inline-group' : ''; ?>">
			<?php if ($type === 'checkbox' && $checkboxStyle === 'plain'): ?>
				<label class="control-label">
					<div class="controls <?php echo $hasTrack ? 'trackable' : ''; ?>"
						data-safepoint='<?php echo $isValidDataField ? $setvalue : ''; ?>'
						data-currpoint='<?php echo $isValidDataField ? $setvalue : ''; ?>'
						data-selector="#<?php echo $field->id; ?>">
						<?php echo $field->input; ?>
					</div>

					<?php if (!$field->getAttribute('hideLabel', false)): ?>
						<?php echo Text::_(Helper::CheckNull($field->getAttribute('label'))); ?>

						<!-- if description exists then show the help icon -->
						<?php if (!empty($description)): ?>
							<span class="hu-help-icon hu-ml-2 fas fa-info-circle"></span>
						<?php endif ?>
					<?php endif; ?>
				</label>
			<?php else: ?>
				<?php if (!$field->getAttribute('hideLabel', false)): ?>
					<label class="control-label">
						<?php echo Text::_(Helper::CheckNull($field->getAttribute('label'))); ?>

						<!-- if description exists then show the help icon -->
						<?php if (!empty($description)): ?>
							<span class="hu-help-icon hu-ml-2 fas fa-info-circle"></span>
						<?php endif ?>
					</label>

					<!-- if description exists and type is not the checkbox then show the help text above of the input field. -->
					<?php if (!empty($description) && $type !== 'checkbox' && $display !== 'inline'): ?>
						<div class="hu-control-help"><?php echo $description; ?></div>
					<?php endif; ?>
				<?php endif; ?>

				<div class="controls <?php echo $hasTrack ? 'trackable' : ''; ?>"
					data-safepoint='<?php echo $isValidDataField ? $setvalue : ''; ?>'
					data-currpoint='<?php echo $isValidDataField ? $setvalue : ''; ?>'
					data-selector="#<?php echo $field->id; ?>">
					<?php echo $field->input; ?>
				</div>

				<?php endif; ?>
			</div>
		<!-- if description exists and type is checkbox then show the help text next to the input field. -->
		<?php if (!empty($description) && ($type === 'checkbox' || $display === 'inline')): ?>
			<p class="hu-control-help"><?php echo $description; ?></p>
		<?php endif; ?>
	</div>
</div>