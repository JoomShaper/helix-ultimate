<?php

defined('JPATH_BASE') or die;

extract($displayData);

$classes = array_filter((array) $classes);

$id = $for . '-lbl';
$title = '';

if (!empty($description))
{
	if ($text && $text !== $description)
	{
		JHtml::_('bootstrap.popover');
		$classes[] = 'hasPopover';
		$title     = ' title="' . htmlspecialchars(trim($text, ':')) . '"'
			. ' data-content="'. htmlspecialchars($description) . '"';

		if (!$position && JFactory::getLanguage()->isRtl())
		{
			$position = ' data-placement="left" ';
		}
	}
	else
	{
		$classes[] = 'hasTooltip';
		$title     = ' title="' . JHtml::_('tooltipText', trim($text, ':'), $description, 0) . '"';
	}
}

if ($required)
{
	$classes[] = 'required';
}

?>
<label id="<?php echo $id; ?>" for="<?php echo $for; ?>"<?php if (!empty($classes)) echo ' class="' . implode(' ', $classes) . '"'; ?><?php echo $title; ?><?php echo $position; ?>>
	<?php echo $text; ?><?php if ($required) : ?><span class="star">&#160;*</span><?php endif; ?>
</label>