<?php defined('_JEXEC') or die('Restricted access'); ?>
<legend>Groupdetails - <?php echo $this->group->getName(); ?></legend>
<table class="adminlist">
<thead>
<tr><th colspan="2"><?php echo JText::_('Informations'); ?></th></tr></thead>
<tbody>
<tr><td><?php echo JText::_('Name'); ?>:</td>
<td><?php echo $this->group->getName(); ?></td></tr>
<tr><td><?php echo JText::_('ID'); ?>:</td>
<td><?php echo $this->group->getId(); ?></td></tr>
<tr><td><?php echo JText::_('Members'); ?>:</td> 
<td><?php $users = $this->group->getUsers(); echo count($users); ?></td></tr>
<?php if(count($users) > 0) { ?>
<tr><td colspan="2">
<table>
<thead>
<tr><th>Users</th></tr>
</thead>
<tbody>
<?php foreach($users as $user) { ?>
<tr><td><a href="<?php echo JRoute::_('index.php?option=com_users&view=edit&cid[]='.$user->getId()); ?>"><?php echo $user->getName(); ?></a></td></tr>
<?php } ?>
</tbody></table>
</td></tr>
<?php } ?>
</tbody>
</table>

<div style="float:left;">
	<div class="icon">
		<a href="<?php echo JRoute::_('index.php?option=com_users&view=groupdetail&layout=edit&parent='.$this->group->getId()); ?>">
			<?php /*echo JHTML::_('image.site',  'icon-32-lock.png', '/templates/khepri/images/toolbar/' );*/ ?>
			<span>Add Childgroup</span></a>
	</div>
</div>

<div style="float:left;">
	<div class="icon">
		<a href="<?php echo JRoute::_('index.php?option=com_users&view=groupdetail&layout=edit&id='.$this->group->getId()); ?>">
			<?php /*echo JHTML::_('image.site',  'icon-32-lock.png', '/templates/khepri/images/toolbar/' );*/ ?>
			<span>Edit Group</span></a>
	</div>
</div>

<div style="float:left;">
	<div class="icon">
		<a href="<?php echo JRoute::_('index.php?option=com_users&task=deletegroup&id='.$this->group->getId()); ?>">
			<?php /*echo JHTML::_('image.site',  'icon-32-lock.png', '/templates/khepri/images/toolbar/' );*/ ?>
			<span>Delete Group</span></a>
	</div>
</div>

<div style="float:left;">
	<div class="icon">
		<a class="modal" href="<?php echo JRoute::_('index.php?option=com_users&view=access&layout=edit&id='.$this->group->getId()); ?>" rel="{handler: 'iframe', size: {x: 400, y: 400}}">
			<?php /*echo JHTML::_('image.site',  'icon-32-lock.png', '/templates/khepri/images/toolbar/' );*/ ?>
			<span>Edit Accessrights</span></a>
	</div>
</div>
