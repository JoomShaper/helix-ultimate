<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('JPATH_BASE') or die;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

if(JVERSION >= 4) {
	/* @var $displayData array */
	$msgList   = $displayData['msgList'];
	$document  = Factory::getDocument();
	$msgOutput = '';
	$alert     = [
		CMSApplication::MSG_EMERGENCY => 'danger',
		CMSApplication::MSG_ALERT     => 'danger',
		CMSApplication::MSG_CRITICAL  => 'danger',
		CMSApplication::MSG_ERROR     => 'danger',
		CMSApplication::MSG_WARNING   => 'warning',
		CMSApplication::MSG_NOTICE    => 'info',
		CMSApplication::MSG_INFO      => 'info',
		CMSApplication::MSG_DEBUG     => 'info',
		'message'                     => 'success'
	];

	// Load JavaScript message titles
	Text::script('ERROR');
	Text::script('MESSAGE');
	Text::script('NOTICE');
	Text::script('WARNING');

	// Load other Javascript message strings
	Text::script('JCLOSE');
	Text::script('JOK');
	Text::script('JOPEN');

	// Alerts progressive enhancement
	$document->getWebAssetManager()
		->useStyle('webcomponent.joomla-alert')
		->useScript('messages');

	if (is_array($msgList) && !empty($msgList))
	{
		$messages = [];

		foreach ($msgList as $type => $msgs)
		{
			// JS loaded messages
			$messages[] = [$alert[$type] ?? $type => $msgs];
			// Noscript fallback
			if (!empty($msgs)) {
				$msgOutput .= '<div class="alert alert-' . ($alert[$type] ?? $type) . '">';
				foreach ($msgs as $msg) :
					$msgOutput .= $msg;
				endforeach;
				$msgOutput .= '</div>';
			}
		}

		if ($msgOutput !== '')
		{
			$msgOutput = '<noscript>' . $msgOutput . '</noscript>';
		}

		$document->addScriptOptions('joomla.messages', $messages);
	}
} else {
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
}


?>
<div id="system-message-container" aria-live="polite">
	<?php 
	if (JVERSION >= 4){
		echo $msgOutput; 
	} else { ?>
	<?php if (is_array($msgList) && !empty($msgList)) : ?>
		<div id="system-message">
			<?php foreach ($msgList as $type => $msgs) : ?>
				<?php $type = \in_array($type, array_keys($alert)) ? $type : 'message'; ?>
				<div class="alert <?php echo isset($alert[$type]) ? $alert[$type] : 'alert-' . $type; ?>">
					<?php // This requires JS so we should add it trough JS. Progressive enhancement and stuff. ?>
					<a class="btn-close" data-bs-dismiss="alert" aria-label="<?php Text::_('JLIB_HTML_BEHAVIOR_CLOSE'); ?>"></a>

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
	<?php } ?>
</div>
