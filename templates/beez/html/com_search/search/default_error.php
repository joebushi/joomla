<?php // @version $Id: default_error.php 11845 2009-05-27 23:28:59Z robs $
defined('_JEXEC') or die;
?>

<h2 class="error<?php $this->params->get('pageclass_sfx') ?>">
	<?php echo JText::_('Error') ?>
</h2>
<div class="error<?php echo $this->params->get('pageclass_sfx') ?>">
	<p><?php echo $this->escape($this->error); ?></p>
</div>
