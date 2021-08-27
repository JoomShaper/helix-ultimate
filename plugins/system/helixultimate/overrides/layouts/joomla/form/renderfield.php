<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

extract($displayData);

if (!empty($options['showonEnabled']))
{
	if (JVERSION < 4)
	{
		HTMLHelper::_('jquery.framework');
		HTMLHelper::_('script', 'system/cms.min.js', array('version' => 'auto', 'relative' => true));
	}
	else
	{
		/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
		$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
		$wa->useScript('showon');
	}
}

$name 			 = $name ?? '';
$class           = empty($options['class']) ? '' : ' ' . $options['class'];
$rel             = empty($options['rel']) ? '' : ' ' . $options['rel'];
$id              = $name . '-desc';
$hideLabel       = !empty($options['hiddenLabel']);
$hideDescription = empty($options['hiddenDescription']) ? false : $options['hiddenDescription'];

if (!empty($parentclass))
{
	$class .= ' ' . $parentclass;
}

?>
<div class="control-group<?php echo $class; ?>"<?php echo $rel; ?>>
	<?php if ($hideLabel) : ?>
		<div class="visually-hidden"><?php echo $label; ?></div>
	<?php else : ?>
		<?php echo $label; ?>
	<?php endif; ?>
	<?php echo $input; ?>
	<?php if (!$hideDescription && !empty($description)) : ?>
		<div id="<?php echo $id; ?>">
			<small class="form-text">
				<?php echo $description; ?>
			</small>
		</div>
	<?php endif; ?>
</div>
