<?php defined('_JEXEC') or die('Restricted access'); ?>
<h3>Joda Database & SQL Abstraction Layer Usage Examples</h3>
<p>&nbsp;</p>
<table class="contentpaneopen">
<?php
    foreach ( $this->texts as $text) {
    	?>
        <tr>
            <td>
                <h1><?php echo nl2br($text["title"]); ?></h1>
                <p><?php echo nl2br($text["intro"]); ?></p>
            </td>
        </tr>
        <?php if ( ! empty($text["code"]) ) { ?>
        <tr>
            <td>
                <div style='font-weight: bold;'>Code:</div>
                <div style='background-color: #EEEEEE;  margin-bottom: 20px; border: 1px solid;  padding: 8px 8px 8px 8px'>
            	   <code>
                   <?php echo nl2br($text["code"]); ?>
                   </code>
                </div>
            </td>
        </tr>
        <?php } ?>
        <?php if ( ! empty($text["result"]) ) { ?>
        <tr>
            <td>
                <div style='font-weight: bold;'>Result:</div>
                <div style='background-color: #EEEEEE;  margin-bottom: 20px; border: 1px solid; padding: 8px 8px 8px 8px'>
                    <code>
                    <?php echo nl2br($text["result"]); ?>
                    </code>
                </div>
            </td>
        </tr>
        <?php } ?>
        <tr valign='top'><td><p>&nbsp;<BR><BR></p></td></tr>
        <?php
    }
?>
</table>
