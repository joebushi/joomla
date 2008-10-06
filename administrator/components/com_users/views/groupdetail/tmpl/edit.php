<?php defined('_JEXEC') or die('Restricted access'); ?>
<form action="index.php?option=com_users" method="post" name="adminForm">

<?php

$uri = JURI::getInstance(); 

if(($id = JRequest::getVar('parent', null, 'int')) != null)
{?>
	<table class="adminlist" summary="Add child group">
	<thead>
		<tr>
			<th colspan="2"><? echo JText::_('Add Subgroup') ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><? echo JText::_('Group name'); ?></td>
			<td> <input class="inputbox" type="text" name="groupname" id="groupname" size="40" maxlength="255" /></td>
		</tr>
	</tbody>
	</table>
	<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>	
	<input type="hidden" name="task" value="savegroup" />
	<input type="hidden" name="parent" value="<? echo $id ?>" />
	<input type="hidden" name="redirect" value="<? echo JURI::base() . "index.php?option=com_users&amp;view=users&amp;layout=groups"; ?> />

<?} ?>
</form>
