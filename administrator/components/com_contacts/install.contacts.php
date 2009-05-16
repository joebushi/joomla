<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

function com_install() {
	$db = &JFactory::getDBO();
	$sql = "SELECT id FROM #__components " .
		"WHERE `option` = 'com_contacts' AND parent=0";
	$db->setQuery($sql);
	$r = $db->loadObject();
	if (is_object($r)) {
		$sql = "UPDATE #__menu SET componentid=" . $r->id .
			" WHERE link LIKE '%option=com_contacts%' AND type='component'";
		$db->setQuery($sql);
		$db->query();
	}
}
?>
<h1>Contacts Installed</h1>