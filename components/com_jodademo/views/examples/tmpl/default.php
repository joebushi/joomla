<?php defined('_JEXEC') or die('Restricted access'); ?>
<h1>Joda Database & SQL ABstraction Layer Usage Examples</h1>
<HR>
<table width="100%">
<?php

    foreach ( $this->texts as $text) {
    	?>
        <tr valign='top'>
            <td align="LEFT">
                <h4>
                <?php echo nl2br($text["title"]); ?>
                <?php //echo $text["explain"]; ?>
                </h4>
                <h5>
                <?php echo nl2br($text["intro"]); ?>
                </h5>
            </td>
        </tr>
        <tr>
            <td>
            	CODE:<hr>
            	<code>
                <?php echo nl2br($text["code"]); ?>
                </code>
                <br>
            </td>
        </tr>
        <tr>
            <td>
            	<BR><BR><BR><BR>
            	RESULT:<hr>
            	<code>
                <?php echo nl2br($text["result"]); ?>
                <?php //echo $text["result"]; ?>
                </code>
                <br>
            </td>
        </tr>
        <tr valign='top'><td><p>&nbsp;<BR><BR></p></td></tr>
        <?php
    }
?>
</table>
