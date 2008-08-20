<?php defined('_JEXEC') or die('Restricted access'); ?>

<fieldset class="col width-40">
<legend>Groups &amp; Users</legend>
<div id="mytree"></div>
<?php
echo $this->usergrouphelper->getGroupTree($this->usergroups);
?>
</fieldset>
