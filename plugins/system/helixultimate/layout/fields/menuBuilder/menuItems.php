<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

extract($displayData);

?>
<div class="hu-menu-items-container">
	<ul class="hu-menu-items">
		<?php if (!empty($items)): ?>
			<?php foreach ($items as $key => $item): ?>
				<li class="hu-menu-item <?php echo $key === 0 ? 'active' : ''; ?>"><?php echo $item->title; ?></li>
			<?php endforeach; ?>
		<?php endif; ?>
	</ul>

	<div class="hu-menu-item-modifiers">
		<div class="row">
			<div class="col-4">
				<label for="">Class</label>
				<input type="text">
			</div>
			<div class="col-4">
				<label for="">Icon</label>
				<input type="text">
			</div>
			<div class="col-4">
				<label for="">Caption</label>
				<input type="text">
			</div>
		</div>
	</div>
</div>
