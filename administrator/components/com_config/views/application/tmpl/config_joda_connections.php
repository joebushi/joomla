<fieldset class="adminform">
	<legend><?php echo JText::_( 'Connections' ); ?></legend>
	<table class="admintable" cellspacing="5">
		<tbody>
        <tr style="font-weight: bold;">
            <td><?php echo JText::_( 'Default' ); ?></td>
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
            reset($row->connections);
            while (list ($id , $cprops) = each($row->connections) ) {
            	$checked = ( $cprops["default"] == 1 ) ? " CHECKED" : "";
        ?>
		<tr>
            <td><input class="text_area" type="radio" name="condefault" value="<?php echo $id; ?>" <?php echo $checked ?> /></td>
		    <td><input class="text_area" type="text"  name="conname[]" size="20" value="<?php echo $cprops["name"]; ?>" /></td>
            <td><?php echo $lists["dbdriver"][$id] ?></td>
            <td><input class="text_area" type="text"  name="conhost[]" size="30" value="<?php echo $cprops["host"]; ?>" /></td>
            <td><input class="text_area" type="text"  name="conport[]" size="8" value="<?php echo $cprops["port"]; ?>" /></td>
            <td><input class="text_area" type="text"  name="condatabase[]" size="30" value="<?php echo $cprops["database"]; ?>" /></td>
            <td><input class="text_area" type="text"  name="conuser[]" size="20" value="<?php echo $cprops["user"]; ?>" /></td>
            <td><input class="text_area" type="text"  name="conpassword[]" size="20" value="<?php echo $cprops["password"]; ?>" /></td>
            <td><input class="text_area" type="text"  name="conprefix[]" size="20" value="<?php echo $cprops["prefix"]; ?>" /></td>
		</tr>
        <?php } ?>
		</tbody>
	</table>
</fieldset>
