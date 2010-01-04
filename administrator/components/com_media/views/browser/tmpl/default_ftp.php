<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	com_media
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;
?>
	<form action="index.php?option=com_media&amp;task=ftpValidate" name="ftpForm" id="ftpForm" method="post">
		<fieldset title="<?php echo JText::_('DESCFTPTITLE'); ?>">
			<legend><?php echo JText::_('DESCFTPTITLE'); ?></legend>
			<?php echo JText::_('DESCFTP'); ?>
			<label for="username"><?php echo JText::_('Username'); ?>:</label>
			<input type="text" id="username" name="username" class="inputbox" size="70" value="" />

			<label for="password"><?php echo JText::_('Password'); ?>:</label>
			<input type="password" id="password" name="password" class="inputbox" size="70" value="" />
		</fieldset>
	</form>
