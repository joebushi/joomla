<?php // @version $Id: default.php 11845 2009-05-27 23:28:59Z robs $
defined('_JEXEC') or die;
?>

<?php if (count($list)) : ?>
<ul class="latestnews<?php echo $params->get('pageclass_sfx'); ?>">
	<?php foreach ($list as $item) : ?>
	<li class="latestnews<?php echo $params->get('pageclass_sfx'); ?>">
		<a href="<?php echo $item->link; ?>" class="latestnews<?php echo $params->get('pageclass_sfx'); ?>">
			<?php echo $item->text; ?></a>
	</li>
	<?php endforeach; ?>
</ul>
<?php endif;
