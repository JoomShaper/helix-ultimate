<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use HelixUltimate\Framework\Platform\Settings;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

extract($displayData);

?>
<?php
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

	// Group Class
	$group_class = (($group) ? 'group-style-' . $group : '');
	if ($type === 'checkbox')
	{
		$group_class .= ($checkboxStyle === 'plain') ? ' hu-style-checkbox': ' hu-style-switcher';
	}

	$group_class .= $separator ? ' hu-field-separator': '';

	$listStyle = $field->getAttribute('style');
?>
<div class="<?php echo $group_class; ?>" <?php echo $attribs; ?>>
	<div class="control-group">
		<div class="control-group-inner">
			<?php if ($type === 'checkbox' && $checkboxStyle === 'plain'): ?>
				<div class="control-label">
					<div class="controls <?php echo $hasTrack ? 'trackable' : ''; ?>" data-safepoint='<?php echo $setvalue; ?>' data-currpoint='<?php echo $setvalue; ?>' data-selector="#<?php echo $field->id; ?>">
						<?php echo $field->input; ?>
					</div>

					<?php if (!$field->getAttribute('hideLabel', false)): ?>
						<?php echo $field->label; ?>

						<!-- if description exists then show the help icon -->
						<?php if (!empty($description)): ?>
							<span class="hu-help-icon hu-ml-2 fas fa-info-circle"></span>
						<?php endif ?>
					<?php endif; ?>
				</div>
			<?php else: ?>
				<?php if (!$field->getAttribute('hideLabel', false)): ?>
					<div class="control-label">
						<?php echo $field->label; ?>
		
						<!-- if description exists then show the help icon -->
						<?php if (!empty($description)): ?>
							<span class="hu-help-icon hu-ml-2 fas fa-info-circle"></span>
						<?php endif ?>
		
					</div>
		
					<!-- if description exists and type is not the checkbox then show the help text above of the input field. -->
					<?php if (!empty($description) && $type !== 'checkbox'): ?>
						<div class="control-help"><?php echo $description; ?></div>
					<?php endif; ?>
				<?php endif; ?>
		
				<div class="controls <?php echo $hasTrack ? 'trackable' : ''; ?>" data-safepoint='<?php echo $setvalue; ?>' data-currpoint='<?php echo $setvalue; ?>' data-selector="#<?php echo $field->id; ?>">
					<?php echo $field->input; ?>
				</div>
		
				<!-- if description exists and type is checkbox then show the help text next to the input field. -->
				<?php if (!empty($description) && $type === 'checkbox'): ?>
					<div class="control-help"><?php echo $description; ?></div>
				<?php endif; ?>
			<?php endif ?>
		</div>
	</div>
</div>