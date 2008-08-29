<?php defined('_JEXEC') or die('Restricted access'); ?>
<h1>Examples</h1>
<HR>
<table>
    <tr valign='top'>
        <td>
<pre>
$dataset = JFactory::getDBSet();
$qb = $dataset->getQueryBuilder();
$qb->select("*")->from("#__content");
$sql = $qb->getSQL();
echo $sql;
</pre>
        </td>
        <td><?php echo nl2br($this->test1); ?></td>
    </tr>

    <tr valign='top'><td colspan=2><hr></td></tr>
    <tr valign='top'>
        <td>
<pre>
$dataset = JFactory::getDBSet();
$qb = $dataset->getQueryBuilder();
$qb->select("title")->from("#__content");
$dataset->addSQL($qb->getSQL());
$dataset->open();
$data = $dataset->fetchAll();
</pre>
        </td>
        <td><?php echo nl2br($this->test2); ?></td>
    </tr>


</table>
