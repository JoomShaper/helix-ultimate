<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('JPATH_BASE') or die();

use HelixUltimate\Framework\Platform\Helper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Utilities\ArrayHelper;

extract($displayData);

// Get some system objects.
$document = Factory::getDocument();

$inputvalue = '';

// Build the attributes array.
$attributes = array();

empty($size)      ? null : $attributes['size'] = $size;
empty($maxlength) ? null : $attributes['maxlength'] = $maxLength;
empty($class)     ? $attributes['class'] = 'form-control' : $attributes['class'] = 'form-control ' . $class;
!$readonly        ? null : $attributes['readonly'] = 'readonly';
!$disabled        ? null : $attributes['disabled'] = 'disabled';
empty($onchange)  ? null : $attributes['onchange'] = $onchange;

if ($required)
{
	$attributes['required'] = '';
	$attributes['aria-required'] = 'true';
}

// Handle the special case for "now".
if (strtoupper($value) == 'NOW')
{
	$value = Factory::getDate()->format('Y-m-d H:i:s');
}

$readonly = isset($attributes['readonly']) && $attributes['readonly'] == 'readonly';
$disabled = isset($attributes['disabled']) && $attributes['disabled'] == 'disabled';

if (is_array($attributes))
{
	$attributes = ArrayHelper::toString($attributes);
}

$cssFileExt = ($direction === 'rtl') ? '-rtl.css' : '.css';
$localesPath = $localesPath ?? '';
$helperPath = $helperPath ?? '';

if (JVERSION < 4)
{
	// The static assets for the calendar
	HTMLHelper::_('script', Helper::CheckNull($localesPath), false, true, false, false, true);
	HTMLHelper::_('script', Helper::CheckNull($helperPath), false, true, false, false, true);
	HTMLHelper::_('script', 'system/fields/calendar.min.js', false, true, false, false, true);
	HTMLHelper::_('stylesheet', 'system/fields/calendar' . Helper::CheckNull($cssFileExt), array(), true);
}

// Redefine locale/helper assets to use correct path, and load calendar assets
if (JVERSION >= 4)
{
	$document->getWebAssetManager()
		->registerAndUseScript('field.calendar.locale', $localesPath, [], ['defer' => true])
		->registerAndUseScript('field.calendar.helper', $helperPath, [], ['defer' => true])
		->useStyle('field.calendar' . ($direction === 'rtl' ? '-rtl' : ''))
		->useScript('field.calendar');
}

?>
<div class="field-calendar">
	<?php if (!$readonly && !$disabled) : ?>
	<div class="input-group">
		<?php endif; ?>
		<input
			type="text"
            id="<?php echo $id; ?>"
            name="<?php echo $name; ?>"
			value="<?php echo htmlspecialchars(($value !== '0000-00-00 00:00:00') ? $value : '', ENT_COMPAT, 'UTF-8'); ?>"
			<?php echo $attributes; ?>
			<?php echo !empty($hint) ? 'placeholder="' . htmlspecialchars($hint ?? "", ENT_COMPAT, 'UTF-8') . '"' : ''; ?>
			data-alt-value="<?php echo htmlspecialchars($value ?? "", ENT_COMPAT, 'UTF-8'); ?>" autocomplete="off">
		<span class="input-group-text">
				<button type="button" class="<?php echo ($readonly || $disabled) ? 'hidden ' : ''; ?>btn btn-secondary"
					id="<?php echo $id; ?>_btn"
					data-inputfield="<?php echo $id; ?>"
					data-dayformat="<?php echo $format; ?>"
					data-date-format="<?php echo $format; ?>"
					data-button="<?php echo $id; ?>_btn"
					data-firstday="<?php echo Factory::getLanguage()->getFirstDay(); ?>"
					data-weekend="<?php echo Factory::getLanguage()->getWeekEnd(); ?>"
					data-today-btn="<?php echo $todaybutton; ?>"
					data-week-numbers="<?php echo $weeknumbers; ?>"
					data-show-time="<?php echo $showtime; ?>"
					data-show-others="<?php echo $filltable; ?>"
					data-time-24="<?php echo $timeformat; ?>"
					data-only-months-nav="<?php echo $singleheader; ?>"
					<?php echo !empty($minYear) ? 'data-min-year="' . $minYear . '"' : ''; ?>
					<?php echo !empty($maxYear) ? 'data-max-year="' . $maxYear . '"' : ''; ?>
				><span class="fas fa-calendar" aria-hidden="true"></span></button>
		</span>
		<?php if (!$readonly && !$disabled) : ?>
	</div>
<?php endif; ?>
</div>
