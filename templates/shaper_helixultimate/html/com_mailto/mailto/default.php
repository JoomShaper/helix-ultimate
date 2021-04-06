<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('behavior.core');
HTMLHelper::_('behavior.keepalive');

$data = $this->get('data');

Factory::getDocument()->addScriptDeclaration("
	Joomla.submitbutton = function(pressbutton)
	{
		var form = document.getElementById('mailtoForm');

		// do field validation
		if (form.mailto.value == '' || form.from.value == '')
		{
			alert('" . Text::_('COM_MAILTO_EMAIL_ERR_NOINFO', true) . "');
			return false;
		}
		form.submit();
	}
");
?>

<div id="mailto-window" class="p-2">
	<a href="javascript: void window.close()" title="<?php echo Text::_('COM_MAILTO_CLOSE_WINDOW'); ?>" class="btn-close" aria-label="Close"></a>

	<h4 class="mt-0"><?php echo Text::_('COM_MAILTO_EMAIL_TO_A_FRIEND'); ?></h4>

	<form action="<?php echo Uri::base() ?>index.php" id="mailtoForm" method="post">
		<div class="mb-3">
			<label for="mailto_field">
				<?php echo Text::_('COM_MAILTO_EMAIL_TO'); ?>
			</label>
			<input type="text" id="mailto_field" name="mailto" class="form-control" value="<?php echo $this->escape($data->mailto); ?>">
		</div>
		<div class="mb-3">
			<label for="sender_field">
				<?php echo Text::_('COM_MAILTO_SENDER'); ?>
			</label>
			<input type="text" id="sender_field" name="sender" class="form-control" value="<?php echo $this->escape($data->sender); ?>">
		</div>
		<div class="mb-3">
			<label for="from_field">
				<?php echo Text::_('COM_MAILTO_YOUR_EMAIL'); ?>
			</label>
			<input type="text" id="from_field" name="from" class="form-control" value="<?php echo $this->escape($data->from); ?>">
		</div>
		<div class="mb-3">
			<label for="subject_field">
                <?php echo Text::_('COM_MAILTO_SUBJECT'); ?>
            </label>
			<input type="text" id="subject_field" name="subject" class="form-control" value="<?php echo $this->escape($data->subject); ?>">
		</div>
		<div class="mb-3">
			<button class="btn btn-secondary" onclick="window.close();return false;">
				<?php echo Text::_('COM_MAILTO_CANCEL'); ?>
			</button>
			<button class="btn btn-success" onclick="return Joomla.submitbutton('send');">
				<?php echo Text::_('COM_MAILTO_SEND'); ?>
			</button>
		</div>

		<input type="hidden" name="layout" value="<?php echo htmlspecialchars($this->getLayout(), ENT_COMPAT, 'UTF-8'); ?>">
		<input type="hidden" name="option" value="com_mailto">
		<input type="hidden" name="task" value="send">
		<input type="hidden" name="tmpl" value="component">
		<input type="hidden" name="link" value="<?php echo $data->link; ?>">
		<?php echo HTMLHelper::_('form.token'); ?>
	</form>
</div>
