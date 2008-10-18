<?php
/**
 * @version		$Id: default_dbconfig.php 11081 2008-10-13 10:36:16Z eddieajau $
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

jimport('joomla.html.html');

?>

<script type="text/javascript">


	function validateForm( frm, task ) {
		submitForm( frm, task );
	}

	
window.addEvent('domready', function() {

	$('loadDataForm').addEvent('submit', function(e) {

		e.stop();
		var log = $('loadDataDiv').empty().addClass('ajax-loading');


		this.set('send', {onComplete: function(response) { 

			log.removeClass('ajax-loading');

			log.set('html', response);

		}});

		
		var elements = this.elements;
		var method;
		
		for (var iElm = 0; iElm < elements.length; iElm++) {
			var formEl = elements[iElm];
			
			// Skip non inputs
			if ( formEl.nodeName.toLowerCase() != 'input' )
			{
				continue;
			}
			
			// Check for the input that we want
			if ( formEl.name != 'task' || ! formEl.checked )
			{
				
				continue;
			}

			method = formEl.id;
			break;

		}
		
		if ( ! method ) 
		{
			alert('<?php echo JText::_('Please select an install method', true) ?>');
			return false;
		}
		
		if ( !confirm('<?php echo JText::_('You have selected the install method ', true) ?>' + method) )
		{
			return false;
		}

		this.send();
		
		return true;

	});

});
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm" class="form-validate" autocomplete="off">
<div id="right">
	<div id="rightpad">
		<div id="step">
			<div class="t">
				<div class="t">
					<div class="t"></div>
				</div>
			</div>
			<div class="m">
				<div class="far-right">
					<?php if ( $this->direction == 'ltr' ) : ?>
						<div class="button1-right"><div class="prev"><a onclick="submitForm( adminForm, 'ftpconfig' );" alt="<?php echo JText::_('Previous', true ) ?>"><?php echo JText::_('Previous' ) ?></a></div></div>
						<div class="button1-left"><div class="next"><a onclick="validateForm( adminForm, 'mainconfig' );" alt="<?php echo JText::_('Next', true ) ?>"><?php echo JText::_('Next' ) ?></a></div></div>
					<?php else: ?>
						<div class="button1-right"><div class="prev"><a onclick="validateForm( adminForm, 'mainconfig' );" alt="<?php echo JText::_('Next', true ) ?>"><?php echo JText::_('Next' ) ?></a></div></div>
						<div class="button1-left"><div class="next"><a onclick="submitForm( adminForm, 'ftpconfig' );" alt="<?php echo JText::_('Previous', true ) ?>"><?php echo JText::_('Previous' ) ?></a></div></div>
					<?php endif; ?>
				</div>
				<span class="step"><?php echo JText::_('Load Data' ) ?></span>
			</div>
			
		<div class="b">
		<div class="b">
			<div class="b"></div>
		</div>
	</div>
</div>

<input type="hidden" name="task" value="" />
<input type="hidden" name="previous" value="loaddata" />

</form>

<div id="installer">
	<div class="t">
		<div class="t">
			<div class="t"></div>
		</div>
	</div>
	<div class="m">

		<form action="index.php?format=raw" method="post" name="loadDataForm" id="loadDataForm" class="form-validate" >
		<h2><?php echo JText::_('loadSampleOrMigrate') ?></h2>
		<div class="install-text">
				
				<fieldset>
					<div>

					<h3 title="<?php echo JText::_('Fresh Install', true ) ?>"><?php echo JText::_('Fresh Install' ) ?></h3>

						<div>
							<input id="taskFreshInstall" type="radio" name="task" value="installFresh" />
	
							<label for="taskFreshInstall">
								<?php echo JText::_('Fresh Install') ?>
							</label>
						</div>

						<div>
							<em>
							<?php echo JText::_('tipFreshInstall') ?>
							</em>
						</div>
						
					</div>
					
					<div >

						<h3 title="<?php echo JText::_('Upgrade', true ) ?>"><?php echo JText::_('Upgrade' ) ?></h3>
						
						<div>
							<input id="taskUpgrade" type="radio" name="task" value="installUpgrade" />
		
							<label for="taskUpgrade">
								<?php echo JText::_('Upgrade Existing') ?>
							</label>
						</div>

						<div>
							<em>
							<?php echo JText::_('tipUpgradeExisting') ?>
							</em>
						</div>

					</div>
					
					<div>

						<h3 title="<?php echo JText::_('Migration', true ) ?>"><?php echo JText::_('Migration' ) ?></h3>
						
						<div>
							<input id="taskMigrate" type="radio" name="task" value="installMigrate" />
		
							<label for="taskMigrate">
								<?php echo JText::_('Upload Migration Script') ?>
							</label>
						</div>

						<div>
							<em>
							<?php echo JText::_('tipMigrationScript') ?>
							</em>
						</div>
						
					</div>
					
					
					<br/><br/>
					
					<div>
						<input class="button" type="submit" name="startDataLoad" value="<?php echo JText::_('clickToBegin', true) ?>"  />
					</div>
					
				</fieldset>
		</div>
		
		</form>
		
		<div class="install-body">
			<div class="t">
				<div class="t">
					<div class="t"></div>
				</div>
			</div>
			<div class="m">
				
				<div id="loadDataDiv" name="loadDataDiv"></div>
				
				<div class="clr"></div>
			</div>
			<div class="b">
				<div class="b">
					<div class="b"></div>
				</div>
			</div>
					<div class="clr"></div>
				</div>
				<div class="clr"></div>
			</div>
			<div class="b">
				<div class="b">
					<div class="b"></div>
				</div>
			</div>
			
			
		</div>
	</div>
</div>


<div class="clr"></div>
