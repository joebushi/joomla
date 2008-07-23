<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php //var_dump($this->groups); ?>

      <table cellpadding="2" cellspacing="2" border="2" width="100%">
        <tbody>
          <tr>
            <th width="2%">ID</th>
            <th width="40%">Name</th>
            <th width="20%">Value</th>
            <th width="6%">Objects</th>
            <th width="30%">Functions</th>
            <th width="2%"><input type="checkbox" class="checkbox" name="select_all" onClick="checkAll(this)"/></th>
          </tr>
<?php 
$groups = $this->groups->getChildren();
foreach($groups as $group)
{
	echo $this->loadTemplate('group');
}
?>
        </tbody>
      </table> 