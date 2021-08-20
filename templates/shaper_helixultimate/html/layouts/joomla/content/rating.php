<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('JPATH_BASE') or die();

use Joomla\CMS\Language\Text;

$rating = (int) $displayData['item']->rating;
$rating_count = $displayData['item']->rating_count;
if($rating_count == '') {
	$rating_count = 0;
}

?>
<div class="article-ratings" data-id="<?php echo (int) $displayData['item']->id; ?>">
	<span class="ratings-label"><?php echo Text::_('HELIX_ULTIMATE_ARTICLE_RATINGS'); ?></span>
	<div class="rating-symbol">
		<?php
		$j = 0;
		for($i = $rating; $i < 5; $i++)
		{
			echo '<span class="rating-star" data-number="' . (5 - $j) . '"></span>';
			$j++;
		}
		for ($i = 0; $i < $rating; $i++)
		{
			echo '<span class="rating-star active" data-number="'.($rating - $i).'"></span>';
		}
		?>
	</div>
	<span class="fas fa-circle-notch fa-spin" aria-hidden="true" style="display: none;"></span>
	<span class="ratings-count">(<?php echo $rating_count; ?>)</span>
</div>
