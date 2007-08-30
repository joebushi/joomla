<?php
/**
* @version		$Id$
* @package		Joomla
* @subpackage	Massmail
* @copyright	Copyright (C) 2005 - 2007 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software and parts of it may contain or be derived from works
* licensed under the GNU General Public License or other free or open source
* software licenses. See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Media Manager Views
 *
 * @static
 * @package		Joomla
 * @subpackage	Media
 * @since 1.5
 */
class MediaViews
{

	function imageStyle()
	{
		?>
		<script language="javascript" type="text/javascript">
		function confirmDeleteImage(file)
		{
			if(confirm("<?php echo JText::_( 'Delete file' ); ?> \""+file+"\"?")) {
				var form = document.getElementById('mediamanager-form');
				form.task.value = 'delete';
				if (top.$('username')) {
					form.username.value = top.$('username').value;
					form.password.value = top.$('password').value;
				}
				var files = document.getElementsByName('rm[]');
				for (var i = 0; i < files.length; i++) {
					files[i].checked = (files[i].value == file);
				}
				form.submit();
			}
		}
		function confirmDeleteFolder(folder, numFiles)
		{
			if(numFiles > 0) {
				alert("<?php echo JText::_( 'There are', true ); ?> "+numFiles+" <?php echo JText::_( 'files/folders in' ); ?> \""+folder+"\".\n\n<?php echo JText::_( 'Please delete all files/folder in' ); ?> \""+folder+"\" <?php echo JText::_( 'first.' ); ?>");
				return false;
			}

			if(confirm("<?php echo JText::_( 'Delete folder', true ); ?> \""+folder+"\"?")) {
				var form = document.getElementById('mediamanager-form');
				form.task.value = 'delete';
				if (top.$('username')) {
					form.username.value = top.$('username').value;
					form.password.value = top.$('password').value;
				}
				var folders = document.getElementsByName('rm[]');
				for (var i = 0; i < folders.length; i++) {
					folders[i].checked = (folders[i].value == folder);
				}
				form.submit();
			}
		}
		</script>
		<?php
	}
}
?>
