<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die();

use HelixUltimate\Framework\Platform\Settings;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('behavior.formvalidator');
?>
<div class="remind<?php echo $this->pageclass_sfx; ?>">
	<div class="row justify-content-center">
		<div class="col-lg-4">
			<?php if ($this->params->get('show_page_heading')) : ?>
				<div class="page-header">
					<h1>
						<?php echo $this->escape($this->params->get('page_heading')); ?>
					</h1>
				</div>
			<?php endif; ?>

			<form id="user-registration" action="<?php echo Route::_('index.php?option=com_users&task=remind.remind'); ?>" method="post" class="form-validate">
				<?php foreach ($this->form->getFieldsets() as $fieldset) : ?>
					<fieldset>
						<p><?php echo Text::_($fieldset->label); ?></p>
						<?php foreach ($this->form->getFieldset($fieldset->name) as $name => $field) : ?>
							<?php
								$showon = $field->getAttribute('showon');
								$attribs = '';
								if ($showon) 
								{
									$attribs .= ' data-showon=\'' . json_encode(Settings::parseShowOnConditions($showon, $field->formControl)) . '\'';
								}
								// Enable disable on
								$enableOn = $field->getAttribute('enableon', '');
								if ($enableOn)
								{
									$attribs .= ' data-enableon="' . $enableOn . '"';
								}
							?>
							<div class="mb-3" <?php echo $attribs; ?>>
								<?php echo $field->label; ?>
								<?php echo $field->input; ?>
							</div>
						<?php endforeach; ?>
					</fieldset>
				<?php endforeach; ?>
				<div>
					<button type="submit" class="btn btn-primary validate"><?php echo Text::_('JSUBMIT'); ?></button>
				</div>
				<?php echo HTMLHelper::_('form.token'); ?>
			</form>
		</div>
	</div>
</div>
