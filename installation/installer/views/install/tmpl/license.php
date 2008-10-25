<?php
/**
 * @version		$Id$
 * @package		Joomla
 * @subpackage	Installation
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Set our step information to render in the template
JRequest::setVar('step', 'license');
JRequest::setVar('next', 'dbconfig');
JRequest::setVar('prev', 'preinstall');

?>

<form action="index.php" method="post" name="adminForm">


<div id="installer">
	<div class="t">
		<div class="t">
			<div class="t"></div>
		</div>
	</div>
	<div class="m">

			<h2><?php echo JText::_('GNU/GPL License') ?>:</h2>
			<iframe src="gpl.html" class="license" frameborder="0" marginwidth="25" scrolling="auto"></iframe>

			<div class="clr"></div>
	</div>
	<div class="b">
		<div class="b">
			<div class="b"></div>
		</div>
	</div>
</div>

<input type="hidden" name="<?php echo JUtility::getToken(); ?>" value="1" />
<input type="hidden" name="task" value="" />
</form>
