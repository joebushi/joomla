<?php
/**
 * @version		$Id: edit_metadata.php 13126 2009-10-10 17:33:51Z severdia $
 * @package		Joomla.Administrator
 * @subpackage	com_menus
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>

<div>
	<?php echo $this->form->getLabel('metadesc'); ?>
	<?php echo $this->form->getInput('metadesc'); ?>
</div>
<div>
	<?php echo $this->form->getLabel('metakey'); ?>
	<?php echo $this->form->getInput('metakey'); ?>
</div>
	<?php foreach($this->form->getFields('metadata') as $field): ?>
		<?php if ($field->hidden): ?>
			<?php echo $field->input; ?>
		<?php else: ?>
			<div>
				<?php echo $field->label; ?>
				<?php echo $field->input; ?>
			</div>
		<?php endif; ?>
	<?php endforeach; ?>
