<?php defined('_JEXEC') or die('Restricted access'); ?>

<pre>
	<?php
	ob_start();
	readfile( JPATH_SITE.DS.'CHANGELOG.php' );
	$changelog = ob_get_contents();
	ob_clean();
	
	// Strip php tag
	$changelog = preg_replace('/\<\?php[^\?]*\?\>/','',$changelog);
	
	// Convert all other HTML entities
	echo htmlentities($changelog);
	?>
</pre>