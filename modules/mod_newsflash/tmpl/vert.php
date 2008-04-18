<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php for ($i = 0; $i < count($list); $i ++) :
	modNewsFlashHelper::renderItem($list[$i], $params, $access);
	if (count($list) > 1 && (($i < count($list)-1) || $params->get('showLastSeparator'))) : ?>
		<span class="article_separator">&nbsp;</span>
 	<?php endif; ?>
<?php endforeach; ?>
