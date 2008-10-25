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
JRequest::setVar('step', 'mainconfig');
JRequest::setVar('next', 'saveconfig');
JRequest::setVar('prev', 'loaddata');

?>

<script language="JavaScript" type="text/javascript">
<!--
	function validateForm( frm, task ) {

		var valid_site = document.formvalidator.isValid(frm, 'vars[siteName]');
		var valid_email = document.formvalidator.isValid(frm, 'vars[adminEmail]');
		var valid_password = document.formvalidator.isValid(frm, 'vars[adminPassword]');
		var confirm_password = document.formvalidator.isValid(frm, 'vars[confirmAdminPassword]');

		var siteName 				= getElementByName( frm, 'vars[siteName]' );
		var adminEmail 				= getElementByName( frm, 'vars[adminEmail]' );
		var adminPassword 			= getElementByName( frm, 'vars[adminPassword]' );
		var confirmAdminPassword 	= getElementByName( frm, 'vars[confirmAdminPassword]' );

		if (siteName.value == '' || !valid_site) {
			alert( '<?php echo JText::_('warnSiteName', true) ?>' );
		} else if (adminEmail.value == '' || !valid_email) {
			alert( '<?php echo JText::_('warnEmailAddress', true) ?>' );
		} else if (adminPassword.value == '' || !valid_password) {
			alert( '<?php echo JText::_('warnAdminPassword', true) ?>' );
		} else if (adminPassword.value != confirmAdminPassword.value || !confirm_password) {
			alert( '<?php echo JText::_('warnAdminPasswordDoesntMatch', true) ?>' );
		} else {
			submitForm( frm, task );
		}
	}

	function clearPasswordFields(frm) {
		var adminPassword 			= getElementByName( frm, 'vars[adminPassword]' );
		var confirmAdminPassword 	= getElementByName( frm, 'vars[confirmAdminPassword]' );

		if( adminPassword.defaultValue == adminPassword.value || confirmAdminPassword.defaultValue == confirmAdminPassword.value ) {
			adminPassword.value 		= '';
			confirmAdminPassword.value 	= '';
		}
		return;
	}
//-->
</script>


<div id="installer">
	<div class="t">
		<div class="t">
			<div class="t"></div>
		</div>
	</div>
	<div class="m">

			<form action="index.php" method="post" name="adminForm" id="adminForm" class="form-validate">
			<div id="installerpad">


		<div class="newsection"></div>
				<h2><?php echo JText::_('Site Name') ?>:</h2>
				<div class="install-text">
					<?php echo JText::_('enterSiteName') ?>
				</div>
				<div class="install-body">
				<div class="t">
			<div class="t">
				<div class="t"></div>
			</div>
			</div>
			<div class="m">
						<fieldset>
							<table class="content2">
								<tr>
									<td class="item">
									<label for="siteName">
										<span id="sitenamemsg"><?php echo JText::_('Site name') ?></span>
									</label>
									</td>
									<td align="center">
									<input class="inputbox validate required sitename sitenamemsg" type="text" id="siteName" name="vars[siteName]" size="30" value="<?php echo $this->getSessionVar('siteName') ?>" />
									</td>
								</tr>
							</table>
						</fieldset>
			</div>
			<div class="b">
			<div class="b">
				<div class="b"></div>
			</div>
			</div>
					<div class="clr"></div>
				</div>

		<div class="newsection"></div>
				<h2><?php echo JText::_('confTitle') ?></h2>
				<div class="install-text">
					<?php echo JText::_('tipConfSteps') ?>
				</div>
				<div class="install-body">
				<div class="t">
			<div class="t">
				<div class="t"></div>
			</div>
			</div>
			<div class="m">
					<fieldset>
						<table class="content2">
						<tr>
							<td class="item">
							<label for="adminEmail">
								<span id="emailmsg"><?php echo JText::_('Your E-mail') ?></span>
							</label>
							</td>
							<td align="center">
							<input class="inputbox validate required email emailmsg" type="text" id="adminEmail" name="vars[adminEmail]" value="" size="30" />
							</td>
						</tr>
						<tr>
							<td class="item">
							<label for="adminPassword">
								<span id="passwordmsg"><?php echo JText::_('Admin password') ?></span>
							</label>
							</td>
							<td align="center">
							<input onfocus="clearPasswordFields( adminForm );" class="inputbox validate required password passwordmsg" type="password" id="adminPassword" name="vars[adminPassword]" value="" size="30"/>
							</td>
						</tr>
						<tr>
							<td class="item">
							<label for="confirmAdminPassword">
								<span id="confirmpasswordmsg"><?php echo JText::_('Confirm admin password') ?></span>
							</label>
							</td>
							<td align="center">
							<input class="inputbox validate required confirmpassword confirmpasswordmsg" type="password" id="confirmAdminPassword" name="vars[confirmAdminPassword]" value="" size="30"/>
							</td>
						</tr>
						</table>
					</fieldset>
			</div>
			<div class="b">
			<div class="b">
				<div class="b"></div>
			</div>
			</div>
					<div class="clr"></div>
				</div>

			</div>
			<input type="hidden" name="<?php echo JUtility::getToken(); ?>" value="1" />
			<input type="hidden" name="task" value="" />
			</form>

			<div class="clr"></div>



		<div class="clr"></div>
		</div>
		<div class="b">
			<div class="b">
				<div class="b"></div>
			</div>
		</div>
		</div>

