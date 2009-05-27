<?php defined('_JEXEC') or die; ?>

<?php foreach ($list as $item) :
	modNewsFlashHelper::renderItem($item, $params, $access);
	if (count($list) > 1) : ?>
		<span class="article_separator">&nbsp;</span>
 	<?php endif; ?>
<?php endforeach; ?>