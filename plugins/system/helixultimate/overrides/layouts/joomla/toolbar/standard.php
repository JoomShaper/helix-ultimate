<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('JPATH_BASE') or die;

use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('behavior.core');

$id       = isset($displayData['id']) ? $displayData['id'] : '';
$doTask   = isset($displayData['onclick']) ? $displayData['onclick'] : $displayData['doTask'];
$class    = $displayData['class'];
$text     = $displayData['text'];
$btnClass = isset($displayData['btnClass']) ? $displayData['btnClass'] : '';
$group    = isset($displayData['group']) ? $displayData['group'] : '';
?>

<?php if ($group) : ?>
<a id="<?php echo $id; ?>" href="#" onclick="<?php echo $doTask; ?>" class="dropdown-item">
	<span class="<?php echo trim($class); ?>"></span>
	<?php echo $text; ?>
</a>
<?php else : ?>
<button id="<?php echo $id; ?>" onclick="<?php echo $doTask; ?>" class="<?php echo $btnClass; ?>">
	<span class="<?php echo trim($class); ?>" aria-hidden="true"></span>
	<?php echo $text; ?>
</button>
<?php endif; ?>
