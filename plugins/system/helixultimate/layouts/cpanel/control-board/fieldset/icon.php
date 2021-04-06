<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Uri\Uri;

extract($displayData);


$iconsrc = Uri::root() . "plugins/system/helixultimate/assets/images/icons/{$fieldset->name}.svg";

?>
<div class="hu-fieldset hu-fieldset-<?php echo $fieldset->name; ?>">
	<div class="hu-fieldset-header" data-fieldset="<?php echo strtolower($fieldset->name); ?>">
		<div class="hu-fieldset-header-inner">
			<img class="hu-option-icon" src="<?php echo $iconsrc; ?>" alt="<?php echo $fieldset->name; ?>" alt="<?php echo Text::_($fieldset->label); ?>" />
			<span class="hu-option-title"><?php echo Text::_($fieldset->label); ?></span>
		</div>
	</div>
</div>