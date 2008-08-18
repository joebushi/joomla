<fieldset class="adminform">
	<legend><?php echo JText::_( 'Connections' ); ?></legend>
	<table class="admintable" cellspacing="1">
		<tbody>
        <?php while (list ($cname, $cprops) = each($row->connections) ) { ?>
		<tr>
		    <td>
		          <input class="text_area" type="text" name="connname" size="30" value="<?php echo $cname; ?>" />
		    </td>
		</tr>
        <?php } ?>
		</tbody>
	</table>
</fieldset>
