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
<tr>
<td colspan="2">
<div style="float:left;">
	<div class="icon">
	<a href="javascript: var request = new Ajax('index.php', {method: 'post',postBody: 'option=com_users&format=raw&view=groupdetail&layout=edit&parent=<? echo $this->group->getId(); ?>',onFailure:function(){}, onSuccess:function(response){$('details').setHTML( response );} }).request();">
			<?php echo JHTML::_('image.site',  'groups_f2.png', '/images/', '', '', '', array("title"=>"Add Childgroup" )); ?><br/>
			
	</a>
	</div>
</div>

<div style="float:left;">
	<div class="icon">
		<a href="<?php echo JRoute::_('index.php?option=com_users&view=groupdetail&layout=edit&id='.$this->group->getId()); ?>">
			<?php echo JHTML::_('image.site',  'groups_f3.png', '/images/', '', '', '', array("title"=>"Edit group" )); ?><br/>	</div>
</div>

<div style="float:left;">
	<div class="icon">
		<a href="<?php echo JRoute::_('index.php?option=com_users&task=deletegroup&id='.$this->group->getId()); ?>">
			<?php echo JHTML::_('image.site',  'groups_f4.png', '/images/', '', '', '', array("title"=>"Delete group" )); ?><br/>	</div>
</div>

<div style="float:left;">
	<div class="icon">
		<a class="modal" href="<?php echo JRoute::_('index.php?option=com_users&format=raw&view=access&layout=edit&id='.$this->group->getId()); ?>" rel="{handler: 'iframe', size: {x: 400, y: 400}}">
			<?php echo JHTML::_('image.site',  'groups_f5.png', '/images/', '', '', '', array("title"=>"Access rights" )); ?><br/>	</div>
</div>
	
	
</td>	
</tr>
</tbody>
</table>

