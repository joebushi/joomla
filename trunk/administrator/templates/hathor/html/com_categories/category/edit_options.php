<?php
/**
 * @version		$Id: edit_options.php 13126 2009-10-10 17:33:51Z severdia $
 * @package		Joomla.Administrator
 * @subpackage	com_menus
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>

<?php foreach($this->form->getFields('params') as $field): ?>
	<?php if ($field->hidden): ?>
		<?php echo $field->input; ?>
	<?php else: ?>
		<div>
			<?php echo $field->label; ?>
			<?php echo $field->input; ?>
		</div>
	<?php endif; ?>
<?php endforeach; ?>
