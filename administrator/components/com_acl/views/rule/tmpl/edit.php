<?php /** $Id$ */ defined('_JEXEC') or die('Restricted access');

	JHTML::addIncludePath(JPATH_COMPONENT.DS.'helpers'.DS.'html');
	JHTML::_('behavior.tooltip');
	JHTML::_('behavior.formvalidation');
?>
<style type="text/css">
/* @TODO Mode to stylesheet */
label.block {
	padding-bottom: 4px;
	display:block;
}

.readonly {
	border: 0;
}

/* Special checklists */

ul.checklist {
	list-style: none;
	padding: 0;
}
ul.checklist li {
	padding: 0;
	border-bottom: 1px solid #eee;
}
ul.checklist li:hover {
	background: #eee;
}

.scroll {
	overflow: auto;
}
</style>

<script language="javascript" type="text/javascript">
<!--
	function submitbutton(task)
	{
		var form = document.adminForm;
		if (task == 'acl.cancel' || document.formvalidator.isValid(document.adminForm)) {
			submitform(task);
		}
	}
-->
</script>
<form action="<?php echo JRoute::_('index.php?option=com_acl'); ?>" method="post" name="adminForm" class="form-validate">
	<fieldset>
		<?php if ($this->item->id) : ?>
		<legend><?php echo JText::sprintf('Record #%d', $this->item->id); ?></legend>
		<?php endif; ?>

		<table class="adminform">
			<tbody>
				<tr>
					<td width="33%">
						<label for="note" class="block">
							<?php echo JText::_('ACL Note'); ?>
						</label>
						<input type="text" name="note" id="note" value="<?php echo $this->item->note; ?>" class="inputbox required"/>
					</td>
					<td width="33%">
						<label for="allow" class="block">
							<?php echo JText::_('ACL Allow'); ?>
						</label>
						<?php echo JHTML::_('select.booleanlist',  'allow', '', (int) $this->item->allow); ?>
					</td>
					<td width="33%">
						<label for="note" class="block">
							<?php echo JText::_('ACL Section'); ?>
						</label>
						<input type="text" name="section_value" id="section_value" value="<?php echo $this->item->section_value; ?>" class="readonly" readonly="readonly" />
					</td>
				</tr>
				<tr>
					<td>
						<label for="note" class="block">
							<?php echo JText::_('ACL Return Value'); ?>
						</label>
						<input type="text" name="return_value" id="return_value" value="<?php echo $this->item->return_value; ?>" class="inputbox"/>
					</td>
					<td>
						<label for="allow" class="block">
							<?php echo JText::_('ACL Enabled'); ?>
						</label>
						<?php echo JHTML::_('select.booleanlist',  'enabled', '', (int) $this->item->enabled); ?>
					</td>
					<td>
						<label for="note" class="block">
							<?php echo JText::_('ACL Updated Date'); ?>
						</label>
						<input type="text" value="<?php echo $this->item->updated_date; ?>" class="readonly" readonly="readonly" />
					</td>
				</tr>
			</tbody>
		</table>

		<table width="100%">
			<tbody>
				<tr valign="top">
					<td valign="top" width="25%">
						<fieldset>
							<legend><?php echo JText::_('ACL Apply User Groups');?></legend>
							<?php echo $this->loadTemplate('arogroups'); ?>
						</fieldset>
					</td>
					<td valign="top" width="25%">
						<fieldset>
							<legend class="hasTip" title="Permissions::Select the permissions that this group will be allowed, or not allowed to do.">
							<?php echo JText::_('ACL Apply Permissions') ?>
							</legend>
							<?php echo $this->loadTemplate('acos'); ?>
						</fieldset>
					</td>
					<?php if ($this->allow_axos) : ?>
					<td valign="top">
						<fieldset>
							<legend class="hasTip" title="Items::These are the items that are associated with the permission">
							<?php echo JText::_('ACL Apply to Items') ?>
							</legend>
							<?php echo $this->loadTemplate('axos'); ?>
						</fieldset>
					</td>
					<td valign="top">
						<?php if ($this->allow_axo_groups) : ?>
						<fieldset>
							<legend class="hasTip" title="Item Groups::These are the item groups that are associated with the permission">
							<?php echo JText::_('ACL Apply to Access Groups') ?>
							</legend>
							<?php echo $this->loadTemplate('axogroups'); ?>
						</fieldset>
						<?php endif; ?>
					</td>
					<?php endif; ?>
			</tbody>
		</table>

	</fieldset>

	<input type="hidden" name="id" value="<?php echo $this->item->id;?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="<?php echo JUtility::getToken();?>" value="1" />
</form>
