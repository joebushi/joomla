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
        <?php echo $lists["connections"]; ?>
		</tbody>
	</table>
</fieldset>
