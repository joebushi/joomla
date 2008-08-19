<?php require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_users'.DS.'helper'.DS.'helper.php');
$access = new AccessParameters('com_content');
echo $access->render(1);