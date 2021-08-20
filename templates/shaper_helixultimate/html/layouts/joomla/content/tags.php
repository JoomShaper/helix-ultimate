<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('JPATH_BASE') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\Registry\Registry;

if (JVERSION < 4)
{
	JLoader::register('TagsHelperRoute', JPATH_BASE . '/components/com_tags/helpers/route.php');
}


$authorised = Factory::getUser()->getAuthorisedViewLevels();

?>
<?php if (!empty($displayData)) : ?>
	<ul class="tags list-inline mb-4">
		<?php foreach ($displayData as $i => $tag) : ?>
			<?php if (in_array($tag->access, $authorised)) : ?>
				<?php $tagParams = new Registry($tag->params); ?>
				<?php $link_class = $tagParams->get('tag_link_class', 'label label-info'); ?>
				<li class="list-inline-item tag-<?php echo $tag->tag_id; ?> tag-list<?php echo $i; ?>" itemprop="keywords">
					<?php if (JVERSION < 4): ?>
						<a href="<?php echo Route::_(TagsHelperRoute::getTagRoute($tag->tag_id . ':' . $tag->alias)); ?>" class="<?php echo $link_class; ?>">
							<?php echo $this->escape($tag->title); ?>
						</a>
					<?php else: ?>
						<a href="<?php echo Route::_(Joomla\Component\Tags\Site\Helper\RouteHelper::getTagRoute($tag->tag_id . ':' . $tag->alias)); ?>" class="<?php echo $link_class; ?>">
							<?php echo $this->escape($tag->title); ?>
						</a>
					<?php endif ?>
				</li>
			<?php endif; ?>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>
