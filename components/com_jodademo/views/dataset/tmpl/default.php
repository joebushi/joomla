<?php defined('_JEXEC') or die('Restricted access'); ?>
<h1>JDataset</h1>
JDataset is the class that should be used most of the time!
<HR>
<table>
    <tr valign='top'>
        <td width='50%'>
<code><pre>
$dataset = JFactory::getDBSet();
$qb = $dataset->getQueryBuilder();
$qb->select("*")->from("#__content");
$sql = $qb->getSQL();
echo $sql;
</pre></code>
        </td>
        <td><?php echo $this->test1; ?></td>
    </tr>

    <tr valign='top'><td colspan=2><hr></td></tr>
    <tr valign='top'>
        <td>
<code><pre>
$dataset = JFactory::getDBSet();
$qb = $dataset->getQueryBuilder();
$qb->select("title")->from("#__content");
$dataset->setSQL($qb->getSQL());
$dataset->open();
$data = $dataset->fetchAll();
print_r($data);
</pre></code>
        </td>
        <td><?php print_r ($this->test2); ?></td>
    </tr>


</table>
