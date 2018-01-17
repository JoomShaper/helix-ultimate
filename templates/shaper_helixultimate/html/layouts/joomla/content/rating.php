<?php
/**
* @package     Helix3
* @subpackage  Layout
* @author 		JoomShaper
* @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('JPATH_BASE') or die;

$rating = (int) $displayData['item']->rating;
$rating_count = $displayData['item']->rating_count;
if($rating_count == '') {
	$rating_count = 0;
}

?>
<div class="article-ratings" data-id="<?php echo (int) $displayData['item']->id; ?>">
	<span class="ratings-label"><?php echo JText::_('HELIX_ULTIMATE_ARTICLE_RATINGS'); ?></span>
	<div class="rating-symbol">
		<?php
		$j = 0;
		for($i = $rating; $i < 5; $i++){
			echo '<span class="rating-star" data-number="'.(5-$j).'"></span>';
			$j = $j+1;
		}
		for ($i = 0; $i < $rating; $i++)
		{
			echo '<span class="rating-star active" data-number="'.($rating - $i).'"></span>';
		}
		?>
	</div>
	<span class="fa fa-spinner fa-spin" style="display: none;"></span>
	<span class="ratings-count">(<?php echo $rating_count; ?>)</span>
</div>
