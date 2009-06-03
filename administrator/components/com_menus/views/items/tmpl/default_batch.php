<?php
/**
 * @version		$Id: default.php 11980 2009-06-02 15:50:07Z louis $
 * @package		Joomla.Administrator
 * @subpackage	com_menus
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

$options = array(
	JHtml::_('select.option', 'c', JText::_('Menus_Batch_Copy')),
	JHtml::_('select.option', 'm', JText::_('Menus_Batch_Move'))
);
$published = $this->state->get('filter.published');
?>
	<fieldset class="batch">
		<p>
			<legend><?php echo JText::_('Menus_Batch_Options');?></legend>

			<label for="batch_access">
				<?php echo JText::_('Menus_Batch_Access_Label'); ?>
			</label>
			<?php echo JHtml::_('access.assetgroups', 'batch[assetgroup_id]', '', 'class="inputbox"', array('title' => '', 'id' => 'batch_access'));?>
		</p>
		<?php if (is_numeric($published)) : ?>
		<p>
			<label for="batch_access">
				<?php echo JText::_('Menus_Batch_Menu_Label'); ?>
			</label>
			<?php echo JHtml::_('menu.menus', 'batch[menu_id]', '', 'class="inputbox"', array('title' => '', 'id' => 'batch_menu_id', 'published' => $published));?>
			<?php echo JHTML::_( 'select.radiolist', $options, 'batch[move_copy]', '', 'value', 'text', 'm'); ?>
		</p>
		<?php endif; ?>

		<button type="submit" onclick="submitbutton('item.batch');">
			<?php echo JText::_('Menus_Batch_Process'); ?></button>

	</fieldset>
