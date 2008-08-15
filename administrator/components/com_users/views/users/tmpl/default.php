<?php defined('_JEXEC') or die('Restricted access'); ?>
<div id="mytree"></div>
<?php
$this->usergroups->load('2');
echo $this->getTree(true);
?>
