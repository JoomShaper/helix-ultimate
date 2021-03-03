<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('JPATH_BASE') or die;

use Joomla\CMS\Language\Text;

$msgList = $displayData['msgList'];

$alert = [
	'message' => 'alert-primary',
	'error' => 'alert-danger',
	'warning' => 'alert-warning',
	'notice' => 'alert-info',
	'info' => 'alert-info',
	'debug' => 'alert-warning',
	'success' => 'alert-success'
];


?>
<div id="system-message-container">
	<?php if (is_array($msgList) && !empty($msgList)) : ?>
		<div id="system-message">
			<?php foreach ($msgList as $type => $msgs) : ?>
				<?php $type = \in_array($type, array_keys($alert)) ? $type : 'message'; ?>
				<div class="alert <?php echo isset($alert[$type]) ? $alert[$type] : 'alert-' . $type; ?>">
					<?php // This requires JS so we should add it trough JS. Progressive enhancement and stuff. ?>
					<a class="close" data-dismiss="alert" aria-label="<?php Text::_('JLIB_HTML_BEHAVIOR_CLOSE'); ?>">&times;</a>

					<?php if (!empty($msgs)) : ?>
						<h4 class="alert-heading"><?php echo Text::_($type); ?></h4>
						<div>
							<?php foreach ($msgs as $msg) : ?>
								<div><?php echo $msg; ?></div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>
