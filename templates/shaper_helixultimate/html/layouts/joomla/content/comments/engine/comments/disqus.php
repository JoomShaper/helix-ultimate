<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('JPATH_BASE') or die();

if( $displayData['params']->get('disqus_subdomain') != '' ) {

	?>

	<div id="disqus_thread"></div>

	<script type="text/javascript">

	<?php
	$devmode = $displayData['params']->get('disqus_devmode');
	if ($devmode) {
		echo 'var disqus_developer = "1";';
	}
	?>

	var disqus_url= "<?php echo $displayData['url']; ?>";
	var disqus_identifier = "<?php echo md5( $displayData['url'] ); ?>";

	var disqus_shortname = '<?php echo $displayData["params"]->get("disqus_subdomain"); ?>';
	(function() {
		var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
		dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
		(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
	})();
	</script>
	<noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>

	<?php

}
