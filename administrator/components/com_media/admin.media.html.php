<?php
/**
* @version $Id: admin.media.html.php,v 1.6 2005/08/31 17:28:51 facedancer Exp $
* @package Mambo
* @subpackage Media
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

/**
 * @package Mambo
 * @subpackage Polls
 */
class mediaScreens {

	/**
	 * @param string The main template file to include for output
	 * @param array An array of other standard files to include
	 * @return patTemplate A template object
	 */
	function &createTemplate( $bodyHtml, $files=null) {
		$tmpl =& mosFactory::getPatTemplate( $files );
		$tmpl->setRoot( dirname( __FILE__ ) . '/tmpl' );
		$tmpl->setAttribute( 'body', 'src', $bodyHtml );

		return $tmpl;
	}

	/**
	 * Shows configuration page
	 * @param mosParameters component parameters
	 * @param integer component id
	 */
	function viewConfig(&$params, $id) {
		global $mosConfig_lang;

		$tmpl =& mediaScreens::createTemplate('view_config.html');
		$tmpl->addVar( 'body', 'title', 'Parameters'); // this should be here but don't work :( $_LANG->_( 'Parameters' )
		
		global  $mosConfig_live_site;
		$overlib_files = '		
		<script language="javascript" type="text/javascript" src="'.$mosConfig_live_site.'/includes/js/overlib_mini.js"></script>
		<script language="javascript" type="text/javascript" src="'.$mosConfig_live_site.'/includes/js/overlib_hideform_mini.js"></script>
		<div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>';

		$tmpl->addVar( 'body', 'javascript_files_include', $overlib_files);
		$tmpl->addVar( 'body', 'content', $params->render( 'params', 0 ));
		
		$tmpl->addVar( 'body', 'id', $id);
		
		$listdir = mosGetParam($_REQUEST, 'listdir', '');
		$style = mosGetParam($_REQUEST, 'style', 'icons');
		
		$tmpl->addVar( 'body', 'listdir', $listdir);
		$tmpl->addVar( 'body', 'style', $style);		
		
		$tmpl->displayParsedTemplate();
	}

	/**
	 * Shows media manager
	 * @param string directory in which user is
	 * @param array folders list for tree view
	 * @param array additional variables for sorting and viewing purposes
	 * @param mosParameters component parameters
	 */
	function view( $listdir, &$lists, &$vars, &$params, &$fs_list, $move_list=null) {
		global $_LANG;
		global $mosConfig_live_site;
		$tmpl =& mediaScreens::createTemplate( 'view.html', array( 'adminlists.html', 'adminfilters.html' ) );
		$tmpl->setAttribute('body', 'src', 'view.html');
		$tmpl->readTemplatesFromInput( 'list_' . $vars['style'] . '.html' );
		$tmpl->addVar('form', 'formEnctype', 'multipart/form-data');
		$tmpl->addRows( 'list-items', $fs_list );
		$tmpl->addRows( 'directories-list', $lists['directories-list']);
		$tmpl->addVar( 'body', 'post_max_size', ini_get('post_max_size'));
		$tmpl->addGlobalVar( 'listdir', str_replace("\\", "/", $listdir) );				
		$tmpl->addGlobalVar('thumbnailer', $mosConfig_live_site.'/includes/phpThumb/phpThumb.php');
		$tmpl->addGlobalVar('quality', $params->get('jpg_quality_admin'));
		
		if(is_array($move_list)) {
			$tmpl->addVar( 'move-list', 'save_move_list', 'yes');
			$tmpl->addRows( 'move-list', $move_list);
		}		
		
		if($params->get('enable_thumbnailer_adm') == '1')
			$tmpl->addVar('body', 'use_thumbnailer', 'yes');
		else
			$tmpl->addVar('body', 'use_thumbnailer', 'no');
		
		$tmpl->addVars( 'body', $vars);
		
		$tmpl->addGlobalVar( 'style', $vars['style'] );
		
		$tmpl->displayParsedTemplate('form');
	}
}

/**
* @package Mambo
* @subpackage Massmail
*/
class HTML_Media {

	// Built in function of dirname is faulty
	// It assumes that the directory nane can not contain a . (period)
	function dir_name( $dir ) {
		$lastSlash = intval( strrpos( $dir, '/' ) );
		if ($lastSlash == strlen($dir) - 1) {
			return substr($dir, 0, $lastSlash);
		} else {
			return dirname($dir);
		}
	}

	function draw_no_results() {
		global $_LANG;
		?>
		<div>
			<div align="center" class="noimages">
				<?php echo $_LANG->_( 'No Files Found' ); ?>
			</div>
		</div>
	  	<?php
	}

	function draw_no_dir() {
		global $_LANG;
		?>
		<div class="nodirectory">
			<?php echo $_LANG->_( 'Configuration Problem' ); ?>:
			&quot;/images/stories&quot;
			<?php echo $_LANG->_( 'does not exist.' ); ?>
		</div>
		<?php

	}

	function show_doc($doc, $listdir, $icon, $size) {
		global $mosConfig_absolute_path, $mosConfig_live_site;
		global $_LANG;

		$del_link = 'index2.php?option=com_media&amp;task=delete&amp;delFile='.$doc.'&amp;listdir='.$listdir;

		$filesize = parse_size($size);

		$path = $mosConfig_live_site.str_replace('\\', '/', $listdir).$doc;
		$text = $_LANG->_('Insert your text here');
		$onclick = "javascript:window.top.document.adminForm.imagecode.value = '<a href=&quot;$path&quot;>$text</a>';";

		$text = $doc;
		$text .= '<br/>'.$filesize;
		$text = htmlspecialchars( $text );
		$caption = $_LANG->_('File Information');
		$onmouseover = "this.T_BGIMG='$mosConfig_live_site/images/M_images/tt_bg.jpg';this.T_WIDTH=200;return escape('$text')";

		$alt_title = 'alt="'.$_LANG->_('Delete File').'" title="'.$_LANG->_('Delete File').'"';
		?>
		<div style="float:left;">
			<div align="center" class="imgBorder">
				<a href="#" onclick="<?php echo $onclick; ?>"  onmouseover="<?php echo $onmouseover; ?>" onMouseOut="return nd();">
				<div class="image">
					<img border="0" src="<?php echo $icon ?>" alt="<?php echo $doc; ?>" width="30" height="30" />
				</div>
				</a>
			</div>
			<div class="imginfoBorder">
				<?php echo $doc; ?>
				<div class="buttonOut">
					<a href="#" onclick="<?php echo $onclick; ?>">
					<img src="images/edit_pencil.gif" width="15" height="15" border="0" alt="<?php echo $_LANG->_( 'Generate Code' ); ?>" title="<?php echo $_LANG->_( 'Generate Code' ); ?>" />
					</a>
					<a href="<?php echo $del_link; ?>" target="_top" onclick="return deleteImage('<?php echo $doc; ?>');">
					<img src="images/edit_trash.gif" width="15" height="15" border="0" <?php echo $alt_title; ?>/>
					</a>
			</div>
			</div>
		</div>
		<?php
	}
	
	function popupUpload( $basePath ) {
		global $mosConfig_absolute_path;
		global $_LANG;
		
		$imgFiles 	= mosFS::listFolders( $basePath, '.', true, true );
		$folders 	= array();
		$folders[] 	= mosHTML::makeOption( '/' );
		
		$len = strlen( $basePath );
		foreach ( $imgFiles as $file ) {
			$folders[] = mosHTML::makeOption( str_replace( '\\', '/', substr( $file, $len ) ) );
		}
		
		if ( is_array( $folders ) ) {
			sort( $folders );
		}
		// create folder selectlist
		$dirPath = mosHTML::selectList( $folders, 'dirPath', 'class="inputbox" size="1" ', 'value', 'text', '.' );
		?>
		<form method="post" action="index2.php" enctype="multipart/form-data" name="adminForm">
		
		<table id="toolbar">
		<tr>
			<td>
			<?php echo mosAdminHTML::imageCheck( 'mediamanager.png', '/administrator/images/', NULL, NULL, $_LANG->_( 'Upload a File' ), 'upload' ); ?>
			</td>
			<td class="title">
			<?php echo $_LANG->_( 'Upload a File' ); ?> 
			</td>
		</tr>
		</table>
				
		<table class="adminform">
		<tr>
			<td colspan="2">
			<?php echo $_LANG->_( 'Select File' ); ?>&nbsp;&nbsp;&nbsp;
			[ <?php echo $_LANG->_( 'Max size' ); ?> = <?php echo ini_get( 'post_max_size' );?> ]
			<br/>
			<input class="inputbox" name="upload" type="file" size="70" />			
			</td>
		</tr>
		<tr>
			<td colspan="2">
			<?php echo $_LANG->_( 'Destination Sub-folder' ); ?>: <?php echo $dirPath; ?>
			</td>
		</tr>
		<tr>
			<td>
			<input class="button" type="button" value="<?php echo $_LANG->_( 'Upload' ); ?>" name="fileupload" onclick="javascript:submitbutton('upload')" />			
			</td>
			<td>
			<div align="right">
			<input class="button" type="button" value="<?php echo $_LANG->_( 'Close' ); ?>" onclick="javascript:window.close();" align="right" />
			</div>
			</td>
		</tr>
		</table>
		
		<input type="hidden" name="option" value="com_media" />
		<input type="hidden" name="task" value="" />
		</form>
		<?php		
	}
	
	function popupDirectory( $basePath ) {
		global $_LANG;
		
		$imgFiles 	= mosFS::listFolders( $basePath, '.', true, true );
		$folders 	= array();
		$folders[] 	= mosHTML::makeOption( '/' );
		
		$len = strlen( $basePath );
		foreach ( $imgFiles as $file ) {
			$folders[] = mosHTML::makeOption( str_replace( '\\', '/', substr( $file, $len ) ) );
		}
		
		if ( is_array( $folders ) ) {
			sort( $folders );
		}
		// create folder selectlist
		$dirPath = mosHTML::selectList( $folders, 'dirPath', 'class="inputbox" size="1"', 'value', 'text', '.' );
		?>
		<form action="index2.php" name="adminForm" method="post">
		
		<table id="toolbar">
		<tr>
			<td>
			<?php echo mosAdminHTML::imageCheck( 'module.png', '/administrator/images/', NULL, NULL, $_LANG->_( 'Upload a File' ), 'upload' ); ?>
			</td>
			<td class="title">
			<?php echo $_LANG->_( 'Create a Directory' ); ?> 
			</td>
		</tr>
		</table>
				
		<table class="adminform">
		<tr>
			<td colspan="2">
			<?php echo $_LANG->_( 'Directory Name' ); ?>
			<br/>
			<input class="inputbox" name="foldername" type="text" size="60" />			
			</td>
		</tr>
		<tr>
			<td colspan="2">
			<?php echo $_LANG->_( 'Parent Directory' ); ?>: <?php echo $dirPath; ?>
			</td>
		</tr>
		<tr>
			<td>
			<input class="button" type="button" value="<?php echo $_LANG->_( 'Create' ); ?>" onclick="javascript:submitbutton('newdir')" />			
			</td>
			<td>
			<div align="right">
			<input class="button" type="button" value="<?php echo $_LANG->_( 'Close' ); ?>" onclick="javascript:window.close();" align="right" />
			</div>
			</td>
		</tr>
		</table>
		
		<input type="hidden" name="option" value="com_media" />
		<input type="hidden" name="task" value="" />
		</form>
		<?php		
	}
}
?>