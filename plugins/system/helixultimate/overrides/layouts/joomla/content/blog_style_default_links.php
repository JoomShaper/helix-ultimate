<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('JPATH_BASE') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Version;

$version = new Version();
$JoomlaVersion = $version->getShortVersion();
?>
<ol class="nav nav-tabs nav-stacked">
<?php foreach ($displayData->get('link_items') as $item) : ?>
	<li>
		<?php echo HTMLHelper::_('link', Route::_(version_compare($JoomlaVersion, '4.0.0', '>=') ? Joomla\Component\Content\Site\Helper\RouteHelper::getArticleRoute($item->slug, $item->catid, $item->language) : ContentHelperRoute::getArticleRoute($item->slug, $item->catid, $item->language)), $item->title); ?>
	</li>
<?php endforeach; ?>
</ol>
