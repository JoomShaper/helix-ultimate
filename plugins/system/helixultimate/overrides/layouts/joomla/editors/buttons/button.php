<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined ('JPATH_BASE') or die();

$button = $displayData;

if ($button->get('name')) :
	$class   = 'btn btn-secondary';
	$class  .= ($button->get('class')) ? ' ' . $button->get('class') : null;
	$class  .= ($button->get('modal')) ? ' modal-button' : null;
	$target  = '#' . $button->get('text', '');
	$onclick = ($button->get('onclick')) ? ' onclick="' . $button->get('onclick') . '"' : '';
	$title   = ($button->get('title')) ? $button->get('title') : $button->get('text');
	$href	 = ' href="' . $target . '"';
	$toggle  = '';

	if ($button->get('modal', 0))
	{
		$toggle = ' data-bs-toggle="modal"';
		$href = ' href="' . $target . 'Modal"';
	}
?>
<a
	type="button"
	role="button"
	class="<?php echo $class; ?>"
	<?php echo $toggle . $href . $onclick; ?>
	title="<?php echo $title; ?>"
>
	<span class="icon-<?php echo $button->get('name'); ?>" aria-hidden="true"></span> <?php echo $button->get('text'); ?>
</a>
<?php endif; ?>
