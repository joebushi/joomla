<?php defined('_JEXEC') or die('Restricted access'); ?>
<h1>Examples</h1>
<HR>
<table width="100%">
<?php

    foreach ( $this->texts as $text) {
    	?>
        <tr valign='top'>
            <td align="LEFT">
                <code>
                <?php echo nl2br($text["explain"]); ?>
                <?php //echo $text["explain"]; ?>
                </code>
            </td>
            <td> 
                <?php echo nl2br($text["result"]); ?>
                <?php //echo $text["result"]; ?>
            </td>
        </tr>
        <tr valign='top'><td colspan=2><hr></td></tr>
        <?php
    }
?>
</table>
