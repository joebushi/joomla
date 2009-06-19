<form action="index.php" method="post" name="adminForm">
<fieldset style="width:40%;float:left;">
<legend><?php echo JText::_('Usergroups'); ?></legend>
<ul id="access-usergroups">
<?php
foreach($this->usergroups as $usergroup)
{
	echo '<li><a id="'.$usergroup->title.'">'.$usergroup->title.'</a></li>';
}
?>
</ul>
</fieldset>
<div style="width:50%; float:right;" id="access-document">
<?php
foreach($this->usergroups as $usergroup)
{
	echo '<div  id="page-'.$usergroup->title.'">';
	echo '<fieldset class="noshow">';
	echo '<legend>'.$usergroup->title.'</legend>';
	echo '<table>';
	foreach($this->items->children() as $item)
	{
		if($item->name() == 'action')
		{
			echo'<tr><td>'.$item->attributes('name').'</td>';
			echo '<td>'.JHTML::_('select.booleanlist', 'accessrules['.$usergroup->id.']['.$item->attributes('value').']').'</td></tr>';
		}
	}
	echo '</table></fieldset></div>';
}
?>
</div>
<input type="hidden" name="option" value="com_users" />
<input type="hidden" name="task" value="" />

</form>