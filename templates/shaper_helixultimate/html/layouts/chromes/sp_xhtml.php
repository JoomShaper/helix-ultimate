<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.cassiopeia
 *
 * @copyright   (C) 2020 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$module  = $displayData['module'];
$params  = $displayData['params'];
$attribs = $displayData['attribs'];

if ($module->content === null || $module->content === '')
{
	return;
}

$moduleTag     = htmlspecialchars($params->get('module_tag', 'div'), ENT_QUOTES, 'UTF-8');
$bootstrapSize = (int) $params->get('bootstrap_size', 0);
$moduleClass   = $bootstrapSize !== 0 ? ' span' . $bootstrapSize : '';
$headerTag     = htmlspecialchars($params->get('header_tag', 'h3'), ENT_QUOTES, 'UTF-8');
$headerClass   = htmlspecialchars($params->get('header_class', 'sp-module-title'), ENT_COMPAT, 'UTF-8');

if ($module->content) {
	echo '<' . $moduleTag . ' class="sp-module ' . htmlspecialchars($params->get('moduleclass_sfx'), ENT_COMPAT, 'UTF-8') . $moduleClass . '">';

	if ($module->showtitle) {
		echo '<' . $headerTag . ' class="' . $headerClass . '">' . $module->title . '</' . $headerTag . '>';
	}

	echo '<div class="sp-module-content">';
	echo $module->content;
	echo '</div>';
	echo '</' . $moduleTag . '>';
}
