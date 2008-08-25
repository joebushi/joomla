<?php /** $Id$ */ defined('_JEXEC') or die('Restricted access'); ?>
<fieldset class="adminform">
	<legend><?php echo JText::_( 'Parameters' ); ?></legend>
	<table class="admintable">
		<tr>
			<td>
				<?php
					$params = new JParameter( $this->item->get( 'params' ), JPATH_COMPONENT.DS.'models'.DS.'user.xml' );
					echo $params->render( 'params' );
				?>
			</td>
		</tr>
	</table>
</fieldset>
