<?php defined('_JEXEC') or die('Restricted access'); ?>
<legend>Groupdetails - <?php echo $this->group->getName(); ?></legend>
Name: <?php echo $this->group->getName(); ?><br />
ID: <?php echo $this->group->getId(); ?><br />
Members: <?php echo count($this->group->getUsers()); ?>
