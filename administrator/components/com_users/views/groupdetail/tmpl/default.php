<?php defined('_JEXEC') or die('Restricted access'); ?>
<legend>Groupdetails - <?php echo $this->group->getname(); ?></legend>
Name: <?php echo $this->group->getName(); ?><br />
ID: <?php echo $this->group->getId(); ?><br />
Members: <?php echo $this->group->_groups[$this->group->getId()]->userscount; ?>
