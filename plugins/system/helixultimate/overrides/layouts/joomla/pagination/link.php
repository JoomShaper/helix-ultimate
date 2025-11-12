<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

$item    = $displayData['data'];
$display = $item->text;
$app     = Factory::getApplication();

$iconClass = null;
$aria      = '';

switch ((string) $item->text)
{
    // Start
    case Text::_('JLIB_HTML_START'):
        $iconClass = $app->getLanguage()->isRtl()
            ? 'fas fa-angle-double-right'
            : 'fas fa-angle-double-left';
        $aria = Text::sprintf('JLIB_HTML_GOTO_POSITION', strtolower($item->text));
        break;

    // Previous
    case Text::_('JPREV'):
        $item->text = Text::_('JPREVIOUS');
        $iconClass = $app->getLanguage()->isRtl()
            ? 'fas fa-angle-right'
            : 'fas fa-angle-left';
        $aria = Text::sprintf('JLIB_HTML_GOTO_POSITION', strtolower($item->text));
        break;

    // Next
    case Text::_('JNEXT'):
        $iconClass = $app->getLanguage()->isRtl()
            ? 'fas fa-angle-left'
            : 'fas fa-angle-right';
        $aria = Text::sprintf('JLIB_HTML_GOTO_POSITION', strtolower($item->text));
        break;

    // End
    case Text::_('JLIB_HTML_END'):
        $iconClass = $app->getLanguage()->isRtl()
            ? 'fas fa-angle-double-left'
            : 'fas fa-angle-double-right';
        $aria = Text::sprintf('JLIB_HTML_GOTO_POSITION', strtolower($item->text));
        break;

    default:
        $aria = Text::sprintf('JLIB_HTML_GOTO_PAGE', strtolower($item->text));
        break;
}

// Build link & class for active items
if ($displayData['active'])
{
    $limit = ($item->base > 0) ? ('limitstart.value=' . (int) $item->base) : 'limitstart.value=0';
    $class = 'active';

    if ($app->isClient('administrator'))
    {
        $escapedPrefix = htmlspecialchars($item->prefix ?? '', ENT_QUOTES, 'UTF-8');
        $link = 'href="#" onclick="document.adminForm.' . $escapedPrefix . $limit . '; Joomla.submitform();return false;"';
    }
    elseif ($app->isClient('site'))
    {
        $escapedLink = htmlspecialchars($item->link ?? '', ENT_QUOTES, 'UTF-8');
        $link = 'href="' . $escapedLink . '"';
    }
}
else
{
    $class = (property_exists($item, 'active') && $item->active) ? 'active' : 'disabled';
}
?>
<?php if ($displayData['active']) : ?>
    <li class="page-item">
        <a aria-label="<?php echo htmlspecialchars($aria, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $link; ?> class="page-link">
            <?php if ($iconClass): ?>
                <span class="<?php echo $iconClass; ?>" aria-hidden="true"></span>
            <?php else: ?>
                <?php echo htmlspecialchars($display, ENT_QUOTES, 'UTF-8'); ?>
            <?php endif; ?>
        </a>
    </li>
<?php elseif (isset($item->active) && $item->active) : ?>
    <?php $aria = Text::sprintf('JLIB_HTML_PAGE_CURRENT', strtolower($item->text)); ?>
    <li class="<?php echo $class; ?> page-item">
        <span aria-current="true" aria-label="<?php echo htmlspecialchars($aria, ENT_QUOTES, 'UTF-8'); ?>" class="page-link">
            <?php if ($iconClass): ?>
                <span class="<?php echo $iconClass; ?>" aria-hidden="true"></span>
            <?php else: ?>
                <?php echo htmlspecialchars($display, ENT_QUOTES, 'UTF-8'); ?>
            <?php endif; ?>
        </span>
    </li>
<?php else : ?>
    <li class="<?php echo $class; ?> page-item">
        <span class="page-link" aria-hidden="true">
            <?php if ($iconClass): ?>
                <span class="<?php echo $iconClass; ?>" aria-hidden="true"></span>
            <?php else: ?>
                <?php echo htmlspecialchars($display, ENT_QUOTES, 'UTF-8'); ?>
            <?php endif; ?>
        </span>
    </li>
<?php endif; ?>
