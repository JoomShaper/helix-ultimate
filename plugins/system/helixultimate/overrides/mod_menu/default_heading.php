<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;

$title      = $item->anchor_title ? ' title="' . $item->anchor_title . '"' : '';
$anchor_css = $item->anchor_css ?: '';
$rel 		= $item->anchor_rel ? ' rel="' . $item->anchor_rel . '" ' : '';

$linktype   = $item->title;

$isOffcanvasMenu = $params->get('hu_offcanvas', 0, 'INT') === 1;
$maxLevel = $params->get('endLevel', 0, 'INT');
$showToggler = $maxLevel === 0 || $item->level < $maxLevel;

if ($item->menu_icon) {
	if ($item->params->get('menu_text', 1))
	{
		$linktype = '<span class="pe-2 ' . $item->menu_icon . '" aria-hidden="true"></span>' . $item->title;
	}
	else
	{
		$linktype = '<span class="pe-2 ' . $item->menu_icon . '" aria-hidden="true"></span><span class="visually-hidden">' . $item->title . '</span>';
	}
}
else if ($item->menu_image)
{
	if ($item->menu_image_css)
	{
		$image_attributes['class'] = $item->menu_image_css;
		$linktype = HTMLHelper::_('image', $item->menu_image, $item->title, $image_attributes);
	}
	else
	{
		$linktype = HTMLHelper::_('image', $item->menu_image, $item->title);
	}

	if ($item->params->get('menu_text', 1))
	{
		$linktype .= '<span class="menu-image-title">' . $item->title . '</span>';
	}
}

if ($item->parent && $showToggler)
{
	$linktype .= '<span class="menu-toggler"></span>';
}

?>
<span class="nav-header <?php echo $anchor_css; ?>"<?php echo $title; ?><?php echo $rel; ?>><?php echo $linktype; ?></span>
