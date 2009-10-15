<?php // @version $Id: default.php 11845 2009-05-27 23:28:59Z robs $
defined('_JEXEC') or die;
?>

<?php if ($this->params->get('show_page_title',1)) : ?>
<h2 class="componentheading<?php echo $this->params->get('pageclass_sfx') ?>">
	<?php echo $this->escape($this->params->get('page_title')) ?>
</h2>
<?php endif; ?>

<div id="page">

<?php if (!$this->error) :
	echo $this->loadTemplate('results');
else :
	echo $this->loadTemplate('error');
endif; ?>

<?php echo $this->loadTemplate('form'); ?>
</div>
