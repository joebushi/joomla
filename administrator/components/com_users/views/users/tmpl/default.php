<?php defined('_JEXEC') or die('Restricted access'); ?>
<fieldset class="col width-40">
<legend>Groups &amp; Users</legend>
<div id="mytree"></div>
<?php
$this->usergroups->load('2');
echo $this->getTree(true);
?>
</fieldset>
<fieldset id="detailuser" class="col width-60">
<legend>user</legend>
</fieldset>