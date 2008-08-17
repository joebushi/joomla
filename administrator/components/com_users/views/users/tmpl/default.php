<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
	JToolBarHelper::title( JText::_( 'User Manager' ), 'user.png' );
	JToolBarHelper::addNew('adduser', 'New User');
	JToolBarHelper::addNew('addgroup', 'New Group');
	JToolBarHelper::help( 'screen.users' );
?>
<fieldset class="col width-40">
<legend>Groups &amp; Users</legend>
<div id="mytree"></div>
<?php
echo $this->getTree(true);
?>
</fieldset>
<fieldset id="detailuser" class="col width-60">
<legend>Details</legend>
</fieldset>