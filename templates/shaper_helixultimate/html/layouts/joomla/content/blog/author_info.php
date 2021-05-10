<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('JPATH_BASE') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\User\UserHelper;

$template = HelixUltimate\Framework\Platform\Helper::loadTemplateData();
$params = $template->params;
?>

<?php if($params->get('author_info', 0)) : ?>
	<div class="article-author-information">
		<?php 
			$author = Factory::getUser( (int) $displayData->created_by );
			$profile = UserHelper::getProfile( (int) $displayData->created_by );
		?>
		<div class="d-flex">
			<div class="flex-shrink-0">
				<img class="me-3" src="https://www.gravatar.com/avatar/<?php echo md5($author->get('email')); ?>?s=64&d=identicon&r=PG" alt="<?php echo $author->name; ?>">
			</div>
			<div class="flex-grow-1 ms-3">
				<h5 class="mt-0"><?php echo $author->name; ?></h5>
				<?php if(isset($profile->profile['aboutme']) && $profile->profile['aboutme']) : ?>
					<div class="author-bio">
						<?php echo $profile->profile['aboutme']; ?>
						<?php if(isset($profile->profile['website']) && $profile->profile['website']) : ?>
							<div class="author-website mt-2">
								<strong><?php echo Text::_('HELIX_ULTIMATE_BLOG_AUTHOR_WEBSITE'); ?>:</strong> <a target="_blank" rel="noopener noreferrer" href="<?php echo strip_tags($profile->profile['website'], ''); ?>"><?php echo strip_tags($profile->profile['website'], ''); ?></a>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
<?php endif; ?>
