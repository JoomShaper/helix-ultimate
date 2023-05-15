<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('JPATH_BASE') or die();

use HelixUltimate\Framework\Platform\Helper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

extract($displayData);

if ($meter)
{
	HTMLHelper::_('behavior.formvalidator');
	HTMLHelper::_('script', 'system/fields/passwordstrength.min.js', array('version' => 'auto', 'relative' => true));

	$class = 'js-password-strength ' . $class;

	if ($forcePassword)
	{
		$class = $class . ' meteredPassword';
	}
}

HTMLHelper::_('script', 'system/fields/passwordview.min.js', array('version' => 'auto', 'relative' => true));

Text::script('JFIELD_PASSWORD_INDICATE_INCOMPLETE');
Text::script('JFIELD_PASSWORD_INDICATE_COMPLETE');
Text::script('JSHOW');
Text::script('JHIDE');

$attributes = array(
	strlen(Helper::CheckNull($hint)) ? 'placeholder="' . htmlspecialchars(Helper::CheckNull($hint), ENT_COMPAT, 'UTF-8') . '"' : '',
	!empty($autocomplete) ? 'autocomplete="off"' : '',
	!empty($class) ? 'class="form-control ' . $class . '"' : 'class="form-control"',
	$readonly ? 'readonly' : '',
	$disabled ? 'disabled' : '',
	!empty($size) ? 'size="' . $size . '"' : '',
	!empty($maxLength) ? 'maxlength="' . $maxLength . '"' : '',
	$required ? 'required aria-required="true"' : '',
	$autofocus ? 'autofocus' : '',
	!empty($minLength) ? 'data-min-length="' . $minLength . '"' : '',
	!empty($minIntegers) ? 'data-min-integers="' . $minIntegers . '"' : '',
	!empty($minSymbols) ? 'data-min-symbols="' . $minSymbols . '"' : '',
	!empty($minUppercase) ? 'data-min-uppercase="' . $minUppercase . '"' : '',
	!empty($minLowercase) ? 'data-min-lowercase="' . $minLowercase . '"' : '',
	!empty($forcePassword) ? 'data-min-force="' . $forcePassword . '"' : '',
);

?>
<div class="password-group">
	<div class="input-group">
		<span class="input-group-text">
			<span class="fas fa-key" aria-hidden="true"></span>
			<span class="visually-hidden"><?php echo Text::_('JSHOW'); ?></span>
		</span>
		<input
			type="password"
			name="<?php echo $name; ?>"
			id="<?php echo $id; ?>"
			value="<?php echo htmlspecialchars(Helper::CheckNull($value), ENT_COMPAT, 'UTF-8'); ?>"
			<?php echo implode(' ', $attributes); ?>>
		<?php if (JVERSION >= 4) :?>
			<button type="button" class="btn btn-secondary input-password-toggle">
				<span class="icon-eye icon-fw" aria-hidden="true"></span>
				<span class="visually-hidden"><?php echo Text::_('JSHOWPASSWORD'); ?></span>
			</button>
		<?php endif; ?>
	</div>
</div>
