<?php

defined('JPATH_BASE') or die;

extract($displayData);
?>

<?php if(isset($attribs->helix_audio) && $attribs->helix_audio) : ?>
	<div class="article-intro-audio">
		<div class="embed-responsive embed-responsive-16by9">
			<?php echo $attribs->helix_audio; ?>
		</div>
	</div>
<?php endif; ?>