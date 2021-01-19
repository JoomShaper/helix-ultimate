<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Uri\Uri;

extract($displayData);

$cellLayout = new FileLayout('megaMenu.cell', HELIX_LAYOUT_PATH);
$cells = [];

if (!empty($column->items))
{
	$cells = $column->items;
}

?>

<div class="col-<?php echo $column->colGrid; ?>">
	<div class="hu-column-contents">
		<?php foreach ($cells as $cell): ?>
			<?php echo $cellLayout->render(['itemId' => $itemId, 'builder' => $builder, 'cell' => $cell]); ?>
		<?php endforeach ?>
	</div>
</div>
