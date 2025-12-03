<?php

/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   (C) 2019 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 * html5 (chosen html5 tag and font header tags)
 */

defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;

$module  = $displayData['module'];
$params  = $displayData['params'];
$attribs = $displayData['attribs'];

if ((string) $module->content === '') {
    return;
}

$allowedTags = ['div', 'article', 'section', 'aside', 'main'];
$moduleTagInput = $params->get('module_tag', 'div');

$moduleTag              = in_array($moduleTagInput, $allowedTags, true) ? $moduleTagInput : 'div';
$moduleAttribs          = [];
$moduleAttribs['class'] = 'moduletable ' . htmlspecialchars($params->get('moduleclass_sfx', ''), ENT_QUOTES, 'UTF-8');
$bootstrapSize          = (int) $params->get('bootstrap_size', 0);
$moduleAttribs['class'] .= $bootstrapSize !== 0 ? ' col-md-' . $bootstrapSize : '';

$allowedHeaderTags = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
$headerTagInput = $params->get('header_tag', 'h3');
$headerTag = in_array($headerTagInput, $allowedHeaderTags, true) ? $headerTagInput : 'h3';

$headerClass            = htmlspecialchars($params->get('header_class', ''), ENT_QUOTES, 'UTF-8');
$headerAttribs          = [];

// Only output a header class if one is set
if ($headerClass !== '') {
    $headerAttribs['class'] = $headerClass;
}

// Add class from attributes if any
if (!empty($attribs['class'])) {
    $moduleAttribs['class'] .= ' ' . htmlspecialchars($attribs['class'], ENT_QUOTES, 'UTF-8');
}

$moduleId = htmlspecialchars($module->id, ENT_QUOTES, 'UTF-8');
$escapedTitle = htmlspecialchars($escapedTitle, ENT_QUOTES, 'UTF-8');

// Only add aria if the moduleTag is not a div
if ($moduleTag !== 'div') {
    if ($module->showtitle) :
        $moduleAttribs['aria-labelledby'] = 'mod-' . $moduleId;
        $headerAttribs['id']              = 'mod-' . $moduleId;
    else :
        $moduleAttribs['aria-label'] = $escapedTitle;
    endif;
}

$header = '<' . $headerTag . ' ' . ArrayHelper::toString($headerAttribs) . '>' . $escapedTitle . '</' . $headerTag . '>';
?>
<<?php echo $moduleTag; ?> <?php echo ArrayHelper::toString($moduleAttribs); ?>>
    <?php if ((bool) $module->showtitle) : ?>
        <?php echo $header; ?>
    <?php endif; ?>
    <?php echo $module->content; ?>
</<?php echo $moduleTag; ?>>
