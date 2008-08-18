<fieldset class="adminform">
	<legend><?php echo JText::_( 'Connections' ); ?></legend>
	<table class="admintable" cellspacing="1">
		<tbody>
        <tr style="font-weight: bold;">
            <td><?php echo JText::_( 'Name' ); ?></td>
            <td><?php echo JText::_( 'Driver' ); ?></td>
            <td><?php echo JText::_( 'Host' ); ?></td>
            <td><?php echo JText::_( 'Port' ); ?></td>
            <td><?php echo JText::_( 'Database' ); ?></td>
            <td><?php echo JText::_( 'User' ); ?></td>
            <td><?php echo JText::_( 'Password' ); ?></td>
            <td><?php echo JText::_( 'Table Prefix' ); ?></td>
        </tr>
        <?php
            $idx = 0;
            while (list ($cname, $cprops) = each($row->connections) ) {
        ?>
		<tr>
		    <td><input class="text_area" type="text" name="conname[]" size="20" value="<?php echo $cname; ?>" /></td>
            <td><input class="text_area" type="text" name="condriver[]" size="12" value="<?php echo $cprops["driver"]; ?>" /></td>
            <td><input class="text_area" type="text" name="conhost[]" size="30" value="<?php echo $cprops["host"]; ?>" /></td>
            <td><input class="text_area" type="text" name="conport[]" size="8" value="<?php echo $cprops["port"]; ?>" /></td>
            <td><input class="text_area" type="text" name="condatabase[]" size="30" value="<?php echo $cprops["database"]; ?>" /></td>
            <td><input class="text_area" type="text" name="conuser[]" size="20" value="<?php echo $cprops["user"]; ?>" /></td>
            <td><input class="text_area" type="text" name="conpassword[]" size="20" value="<?php echo $cprops["password"]; ?>" /></td>
            <td><input class="text_area" type="text" name="conprefix[]" size="20" value="<?php echo $cprops["prefix"]; ?>" /></td>
		</tr>
        <?php
            $idx++;
            }
        ?>
		</tbody>
	</table>
</fieldset>
