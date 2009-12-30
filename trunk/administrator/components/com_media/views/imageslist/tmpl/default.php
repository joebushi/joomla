<?php defined('_JEXEC') or die; ?>
<?php if (count($this->images) > 0 || count($this->folders) > 0) { ?>
<div class="manager">

		<?php for ($i=0,$n=count($this->folders); $i<$n; $i++) :
			$this->setFolder($i);
			echo $this->loadTemplate('folder');
		endfor; ?>

		<?php for ($i=0,$n=count($this->images); $i<$n; $i++) :
			$this->setImage($i);
			echo $this->loadTemplate('image');
		endfor; ?>

</div>
<?php } else { ?>
	<div id="media-noimages">
		<p><?php echo JText::_('No Images Found'); ?></p>
	</div>
<?php } ?>
