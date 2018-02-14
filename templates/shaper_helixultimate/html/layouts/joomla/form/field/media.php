<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('JPATH_BASE') or die();

use Joomla\CMS\Component\ComponentHelper;

extract($displayData);

$doc = \JFactory::getDocument();
$doc->addScript( \JURI::root(true) . '/plugins/system/helixultimate/assets/js/admin/modal.js' );
$doc->addScript( \JURI::root(true) . '/plugins/system/helixultimate/assets/js/admin/media.js' );
$doc->addStylesheet( \JURI::root(true) . '/plugins/system/helixultimate/assets/css/admin/modal.css' );

?>

<div class="helix-ultimate-image-holder">
	<?php if($value != '') : ?>
		<img src="<?php echo \JURI::root() . $value; ?>" alt="">
	<?php endif; ?>
</div>

<input type="hidden" name="<?php echo $name; ?>" id="<?php echo $id; ?>" value="<?php echo $value; ?>">
<a href="#" class="helix-ultimate-media-picker btn btn-primary btn-sm" data-id="<?php echo $id; ?>"><span class="fa fa-picture-o"></span> Select Media</a>
<a href="#" class="helix-ultimate-media-clear btn btn-secondary btn-sm"><span class="fa fa-times"></span> Clear</a>