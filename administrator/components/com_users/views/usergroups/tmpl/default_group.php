<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php
$groups = $this->groups->getChildren();
foreach($groups as $group)
{
	echo 'groupstuff<br/>';
	$this->groups->load($group->id);
	echo $this->loadTemplate('group');
	$this->groups->load($group->parent);

}