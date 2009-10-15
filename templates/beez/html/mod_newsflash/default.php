<?php // @version $Id: default.php 11845 2009-05-27 23:28:59Z robs $
defined('_JEXEC') or die;
?>

<?php
srand((double) microtime() * 1000000);
$flashnum = rand(0, $items - 1);
$item = $list[$flashnum];
modNewsFlashHelper::renderItem($item, $params, $access);
?>
