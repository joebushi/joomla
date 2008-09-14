<fieldset class="adminform">
	<legend><?php echo JText::_( 'Connections' ); ?></legend>
	<table class="admintable" cellspacing="5" width='100%'>
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
            <td><span class='editlinktip hasTip' title='<?php echo JText::_("System debug must be enabled first"); ?>'> <?php echo JText::_( 'Debug' ); ?></span></td>
        </tr>
        <?php echo $lists["connections"]; ?>
		</tbody>
	</table>
	<span><?php echo JText::_("Note please, connection debug option overrides the system debug option!") ?></span>
</fieldset>
