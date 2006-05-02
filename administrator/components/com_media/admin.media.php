<?php
/**
* @version $Id: admin.media.php,v 1.6 2005/08/31 17:28:51 facedancer Exp $
* @package Mambo
* @subpackage Media
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

// ensure user has access to this function
if (!($acl->acl_check( 'administration', 'edit', 'users', $my->usertype, 'components', 'all' )
 | $acl->acl_check( 'com_media', 'manage', 'users', $my->usertype, 'components', 'com_media' ))) {
	mosRedirect( 'index2.php', $_LANG->_('NOT_AUTH') );
}

mosFS::load( '@admin_html' );

// check for snooping
mosFS::check( mosFS::getNativePath( $mosConfig_absolute_path . DIRECTORY_SEPARATOR . mosGetParam( $_REQUEST, 'listdir', '' ) ) );

/**
 * @package Mambo
 * @subpackage Polls
 */
class mediaManagerTasks extends mosAbstractTasker {
	/**
	 * Constructor
	 */
	function mediaManagerTasks() {
		global $mosConfig_absolute_path;
		global $mosConfig_live_site;
		$_LANG;
		$params = $this->getComponentParams();
		$root_directories = $params->get('root_directories');
		$listdir = mosGetParam( $_REQUEST, 'listdir' , '');
		if($listdir != "") {
			$dirs = trim($params->get('root_directories'));
			if($dirs == '')
				$dirs = 'images';
			$invalidDir = true;
			$dirsArray = explode(" ", $dirs);
			foreach($dirsArray as $dir) {
				if(strpos($listdir, $dir) === 0) {
					$invalidDir = false;
					break;
				}
			}
			if($invalidDir == true){
				$_REQUEST['listdir'] = '';
			} else {
				if (MOSFS_ISWIN)	{
					$_REQUEST['listdir'] = str_replace( '/', DIRECTORY_SEPARATOR, $_REQUEST['listdir'] );
					// Remove double \\
					$_REQUEST['listdir'] = str_replace( '\\\\', DIRECTORY_SEPARATOR, $_REQUEST['listdir'] );
				} else {
					$_REQUEST['listdir'] = str_replace( '\\', DIRECTORY_SEPARATOR, $_REQUEST['listdir'] );
					// Remove double //
					$_REQUEST['listdir'] = str_replace('//',DIRECTORY_SEPARATOR,$_REQUEST['listdir']);
				}
			}			
		}
					
		// auto register public methods as tasks, set the default task
		parent::mosAbstractTasker( 'showMedia' );

		$this->registerTask( 'icons', 'showIcons' );
		$this->registerTask( 'details', 'showDetails' );
		
		$this->registerTask( 'config', 'showConfig' );
		
		$this->registerTask( 'delete', 'delete_file' );	
		$this->registerTask( 'deletefolder', 'delete_folder' );	
		
		$this->registerTask( 'move_to', 'move_selected');
	}

	/**
	* Show media manager
	* @param String messages to show above files and folders list
	*/
	function showMedia($messages = '', $move_files_list=null) {
		global $mosConfig_absolute_path;
		global $mosConfig_live_site;
		$params = $this->getComponentParams();
		$root_directories = trim($params->get('root_directories'));
		// if there are no root dirs set in media manager config root/images is choosen by default
		if($root_directories == '')
			$root_directories = "images";		
		
		$listdir	= mosGetParam( $_REQUEST, 'listdir', '');
		//$dirPath	= $listdir;	
		$vars = array (
			'orderCol' 		=> mosGetParam( $_REQUEST, 'orderCol' , 'name'),
			'orderDirn' 	=> mosGetParam( $_REQUEST, 'orderDirn', 1 ),
			'style'			=> mosGetParam( $_REQUEST, 'style', 'icons' ),
			'messages'		=> $messages,
			'task'			=> mosGetParam($_REQUEST, 'task', '')
		);
		
		// to not modify mosFS::listFilderTree first get root directories and then subtree of each one
		$lists['directories-list'] = array();
		$lists['directories-list'] = mosFS::listFolderTree( $GLOBALS['mosConfig_absolute_path'], strtr(trim($root_directories), " ", "|"), 1, 0);

		for($i = 0; $i < count($lists['directories-list']); $i++) {
			$tmpRootArray[$lists['directories-list'][$i]['name']] = $lists['directories-list'][$i]['id'];
		}
		
		$root_dirs_array = explode(" ", $root_directories);
		foreach($root_dirs_array as $root_dir) {
			if(isset($tmpRootArray[$root_dir]))
				$lists['directories-list'] = array_merge(mosFS::listFolderTree( $GLOBALS['mosConfig_absolute_path'].'/'.$root_dir, '.', 10, 1, $tmpRootArray[$root_dir]), $lists['directories-list']);
		}
		
		foreach($lists['directories-list'] as $key => $dir)
			$lists['directories-list'][$key]['relname'] = str_replace("\\", "/", str_replace($mosConfig_absolute_path, '', $dir['relname']));
		
		$fs_list = listImages($listdir, $vars, $params);
		
		mediaScreens::view( $listdir, $lists, $vars, $params, $fs_list, $move_files_list);
	}
	
	/**
	* Show media manager in details view
	*/
	function showDetails() {
		$listdir	= mosFS::getNativePath( mosGetParam( $_REQUEST, 'listdir', ''), false );	
		$this->setRedirect( 'index2.php?option=com_media&amp;style=details&amp;listdir='.$listdir);
	}
	
	/**
	* Show media manager in icons view
	*/
	function showIcons() {	
		$listdir	= mosFS::getNativePath( mosGetParam( $_REQUEST, 'listdir', ''), false );	
		$this->setRedirect( 'index2.php?option=com_media&amp;style=icons&amp;listdir='.$listdir);
	}	
	
	/**
	* Show config page
	*/
	function showConfig() {
		global $database, $mainframe;
		// disable menu
		$mainframe->set('disableMenu', true);
		
		$query = "SELECT a.id"
		. "\n FROM #__components AS a"
		. "\n WHERE a.option = 'com_media'"
		;
		$database->setQuery( $query );
		$id = $database->loadResult();
	
		// load the row from the db table
		$row = new mosComponent( $database );
		$row->load( $id );
	
		// get params definitions
		$params =& new mosParameters( $row->params, $mainframe->getPath( 'com_xml', $row->option ), 'component' );
	
		mediaScreens::viewConfig($params, $id );
	}
	
	/**
	* Back to media manager from config page
	*/
	function cancel() {
		/*
			TODO: Should back to the directory which was showed when user pressed config button
		*/
		$listdir	= mosGetParam( $_REQUEST, 'listdir', '');	
		$this->setRedirect( 'index2.php?option=com_media&amp;style=icons&amp;listdir='.$listdir);		
	}

	/**
	* Save config and back to media manager from config page
	*/	
	function save() {
		/*
			TODO: Should back to the directory which was showed when user pressed config button
		*/	
		global $database;
		global $_LANG;
	
		$id 	= intval( mosGetParam( $_POST, 'id', '' ) );
		if($id == '')
			die('big problem, there\'s no id set :(');
		
		
		$params = mosGetParam( $_POST, 'params', '' );
		
		if (is_array( $params )) {
			$txt = array();
			foreach ($params as $k=>$v) {
				$txt[] = "$k=$v";
			}
			$_POST['params'] = mosParameters::textareaHandling( $txt );
		}
		
		$row = new mosComponent( $database );
		$row->load( $id );
		
		if (!$row->bind( $_POST )) {
			mosErrorAlert( $row->getError() );
		}		
		if (!$row->check()) {
			mosErrorAlert( $row->getError() );
		}
		if (!$row->store()) {
			mosErrorAlert( $row->getError() );
		}

		$listdir = mosGetParam( $_REQUEST, 'listdir', '');
		$style = mosGetParam($_REQUEST, 'style', 'icons');
		$this->setRedirect( 'index2.php?option=com_media&amp;style='.$style.'&amp;listdir='.$listdir, $_LANG->_( 'Settings successfully saved' ) );
	}
	
	/**
	 * It will do everything what should be done with uploaded file/s
	 */	
	function upload() {
		global $_LANG;
		global $mosConfig_absolute_path;
		
		$listdir = mosGetParam( $_REQUEST, 'listdir', '');			
		$style = mosGetParam( $_REQUEST, 'style', 'icons');	
			
		// error check
		if ( !isset( $_FILES['upload'] ) ) {
			$msg = $_LANG->_( 'Please Select a file to Upload' );
		}
		
		if( $listdir == '' || $listdir == '.' ) {
			$msg = $_LANG->_( 'You can\'t upload files into *Mambo* main direcotory.' );
		}
		
		$filesMessagesArray = Array();
			
		if ( isset( $_FILES['upload'] ) && is_array( $_FILES['upload'] ) && !isset( $msg )) {

			$dest_dir = mosFS::getNativePath( $mosConfig_absolute_path. DIRECTORY_SEPARATOR .$listdir. DIRECTORY_SEPARATOR, false );

			// get params from db
			$params = $this->getComponentParams();	
			
			$allowedfiletypes 	 = ' '.trim($params->get('docs_filetypes'));	
			$allowedfiletypes 	.= ' ' .trim($params->get('images_filetypes')).' ';

			$max_upload_size = $params->get('max_upload_size');
			
			// facedancer: whatisit
			global $clearUploads;
			
			for($i = 1; $i < count($_FILES['upload']['name']); $i++)
			{
				foreach ($_FILES['upload'] as $key => $value) {
				    $file[$key] = $value[$i];
				}
							
				if ( file_exists( $dest_dir . $file['name'] ) ) {
					$filesMessagesArray[] = $_LANG->_( 'File ').'\''.$file['name'].'\' '. $_LANG->_( 'upload FAILED. File already exists. ' );
					continue;
				}
					
				// check if file excedes size limit
				
				if(filesize($file['tmp_name']) > $max_upload_size) {
					$filesMessagesArray[] = $_LANG->_( 'File ').'\''.$file['name'].'\' '. $_LANG->_( 'upload FAILED. File excedes size limit. ' );					
					continue;
				}					
				
				// compare uploaded filetype with allowed filetypes
				
				if(stristr( $allowedfiletypes, ' '.substr(strrchr($file['name'], '.'), 1).' ') == false) {
					$filesMessagesArray[] = $_LANG->_( 'File '). '\''.$file['name'].'\' '. $_LANG->_( ' upload FAILED. Only files of type' ) .$allowedfiletypes. $_LANG->_( 'can be uploaded.' );
					continue;
				}
				
				if ( !move_uploaded_file( $file['tmp_name'], $dest_dir.strtolower($file['name'] ) ) ){
					$filesMessagesArray[] = $_LANG->_( 'File ').'\''.$file['name'].'\' '. $_LANG->_( 'upload FAILED. Please check destination directory permissions. ' );
				} else {
					mosFS::CHMOD( $dest_dir . strtolower( $file['name'] ) );
					
					$filesMessagesArray[] = $_LANG->_( 'File ').'\''.$file['name'].'\''.$_LANG->_( ' uploaded successfully.');
				}
				// facedancer: whatisit
				$clearUploads = true;
			}
		}
		
		if(isset($msg)) {
			$redirect = 'index2.php?option=com_media&amp;style='.$style.'&amp;listdir='.$listdir;	
			$this->setRedirect( $redirect, $msg );
		} else {
			$messages = '';
			foreach ($filesMessagesArray as $message) {
				$messages .= $message.'<br/>';
			}
			$this->showMedia($messages);
		}
	}
	
	/**
	* This do everything what should be done when creating folder
	*/		
	function create_folder() {

		if ( ini_get('safe_mode') == 'On' ) {
			$msg = $_LANG->_( 'WARNDIRCREATIONNOTALLOWEDSAFEMODE' );
			mosErrorAlert( $msg );
		} else {	
		
			global $_LANG;
			$listdir 		= mosGetParam( $_REQUEST, 'listdir', '');
			$style 			= mosGetParam( $_REQUEST, 'style', 'icons');
			$folder_name 	= mosGetParam( $_REQUEST, 'foldername', '');			
			$msg = $_LANG->_( 'Failed' );
		
			// error check
			if ( !$folder_name ) {
				mosErrorAlert( $_LANG->_( 'Please enter the name of the directory you want to create' ) );
			}
		
			if ( strlen( $folder_name ) > 0 ) {
				if (eregi("[^0-9a-zA-Z_]", $folder_name)) {
					mosErrorAlert( $_LANG->_( 'WARNDIRNAMEMUSTCONTAINALPHACHARACTERS' ) );
				}
				
				$basePath 	= mosFS::getNativePath( $mosConfig_absolute_path);
				
				$folder = $basePath. '/' .$listdir . '/'. $folder_name;
				
				if( !is_dir( $folder ) && !is_file( $folder ) ) {
					if(mosMakePath( $folder ))
						$msg = $_LANG->_( 'Success' );
		
					$fp = fopen( $folder .'/index.html', 'w' );
					fwrite( $fp, "<html>\n<body bgcolor=\"#FFFFFF\">\n</body>\n</html>" );
					fclose( $fp );
					mosFS::CHMOD( $folder . '/index.html' );
					$refresh_dirs = true;
				}
			}
		
			$redirect = 'index2.php?option=com_media&amp;style='.$style.'&amp;listdir='.$listdir;
			$this->setRedirect( $redirect, $msg );
		}	
	}
	
	/**
	* Deletes the file
	* @return nothing
	* @param filename to delete
	* @param directory in which file should be
	*/	
	function delete_file() {
		global $mosConfig_absolute_path, $_LANG;

		$listdir 	= mosGetParam( $_REQUEST, 'listdir', '');
		$delfile 	= mosGetParam( $_REQUEST, 'delFile', '');
		$style 		= mosGetParam( $_REQUEST, 'style', 'icons');
		 
		 
		$file = mosFS::getNativePath( $mosConfig_absolute_path . '/' . $listdir .'/'. $delfile, false );
		if (mosFS::deleteFile( $file )) {
			$msg = $_LANG->_( 'Success' );
		} else {
			$msg = $_LANG->_( 'Failed' );
		}
		$this->setRedirect( 'index2.php?option=com_media&listdir='.$listdir.'&amp;style='.$style, $msg );
	}
	
	/**
	* Deletes folder, empty one only (deletes index.html too)
	* @param name of the folder to delete
	* @param directory in which directory should be
	*/
	function delete_folder() {
		global $mosConfig_absolute_path, $_LANG;
	
		$basePath 		= mosFS::getNativePath( $mosConfig_absolute_path);
		$listdir 		= mosGetParam( $_REQUEST, 'listdir', '');
		$delFolder 		= mosGetParam( $_REQUEST, 'delFolder', '');
		$style 			= mosGetParam( $_REQUEST, 'style', 'icons');	
		
		if($listdir != '')
			$del_folder = $basePath . $listdir . DIRECTORY_SEPARATOR . $delFolder;
		else 
			$del_folder = $basePath . $listdir . $delFolder;
		
		if($this->rm_all_dir($del_folder) == false) {
			$msg = $_LANG->_( 'Success' );
		} else {
			$msg = $_LANG->_( 'Failed' );
		}
		
		$this->setRedirect( 'index2.php?option=com_media&listdir='.$listdir.'&amp;style='.$style, $msg );
	}
	
	
	
	
	/**
	* Deletes folder and all files/folders inside it
	* @param name of the folder to delete
	*/
	function rm_all_dir($dir) {
		if(is_dir($dir))
		{
			$d = @dir($dir);
	
			while (false !== ($entry = $d->read()))
			{
				//echo "#".$entry.'<br>';
				if($entry != '.' && $entry != '..')
				{
					$node = $dir.'/'.$entry;
					//echo "NODE:".$node;
					if(is_file($node)) {
						//echo " - is file<br>";
						unlink($node);
					}
					else if(is_dir($node)) {
						//echo " -	is Dir<br>";
						$this->rm_all_dir($node);
					}
				}
			}
			$d->close();
	
			if(rmdir($dir))
				return true;
			else 
				return false;
		}
	}
	
	/**
	* Recursive function to copy all subdirectories and contents.
	* @param string source directory
	* @param string destination directory
	*
	* Recursive Copy Function by noisia on 03/15/04, found on codewalkers
	* Copies an entire directory and sub-directories recursively. Usage is COPY_RECURSIVE_DIRS(source_directory, target_directory). This is based on mugane's Recursive Delete Function
	*/
	function copy_recursive_dirs($dirsource, $dirdest)
	{ 
		// recursive function to copy
		// all subdirectories and contents:
		if(is_dir($dirsource))
			$dir_handle=opendir($dirsource);
			
		if(!mosMakePath($dirdest."/".$dirsource))
			return false;
		while($file = readdir($dir_handle)) {
			if($file!="." && $file!="..") {
				if(!is_dir($dirsource."/".$file)) {
					if(!copy ($dirsource."/".$file, $dirdest."/".$dirsource."/".$file)) {
						return false;
					}
				} else {
					if(!$this->copy_recursive_dirs($dirsource."/".$file, $dirdest)) {
						return false;
					}					
				}
			}
		}
		closedir($dir_handle);
	}
	
	/**
	 * Returns com_media parameters object
	 * @return mosParameters component parameters
	 */		
	function getComponentParams()
	{
		global $database, $mainframe;
		
		$query = "SELECT a.id"
		. "\n FROM #__components AS a"
		. "\n WHERE a.option = 'com_media'"
		;
		$database->setQuery( $query );
		$id = $database->loadResult();
	
		// load the row from the db table
		$row = new mosComponent( $database );
		$row->load( $id );
	
		// get params definitions
		$params =& new  mosParameters( $row->params, $mainframe->getPath( 'com_xml', $row->option ), 'component' );
		return $params;
	}	
	
	/**
	* Deletes all selected files and folders
	*/
	function remove()
	{
		global $mosConfig_absolute_path, $_LANG;
		
		$basePath 		= mosFS::getNativePath( $mosConfig_absolute_path, false);
		$listdir 		= mosGetParam( $_REQUEST, 'listdir', '');
		$style 			= mosGetParam( $_REQUEST, 'style', 'icons');
		
		$msg = "";
		$dirs = 0;
		$files = 0;
		foreach ($_REQUEST['cid'] as $value)
		{
			$item = $basePath. DIRECTORY_SEPARATOR .$listdir. DIRECTORY_SEPARATOR .$value;
			if(is_dir($item) && $value != '.' && $value != '..') {
				if($this->rm_all_dir($item) != false) {
					$dirs++;
				}
			}
			else if(is_file($item)) {
				if (mosFS::deleteFile( $item )) {
					$files++;
				}
			}			
		}
		$msg = $dirs.' folder/s deleted, '.$files.' file/s deleted ';
		$this->setRedirect( 'index2.php?option=com_media&listdir='.$listdir.'&amp;style='.$style, $msg );
	}

	/**
	 * Move selected files and folders to another location
	 * Checks only if user is not trying to copy a directory into itself
	 */	
	function move_selected()
	{
		global $mosConfig_absolute_path, $_LANG;
		
		$basePath 		= mosFS::getNativePath( $mosConfig_absolute_path);
		$listdir 		= mosGetParam( $_REQUEST, 'listdir', '');
		$style 			= mosGetParam( $_REQUEST, 'style', 'icons');
		$task			= mosGetParam( $_REQUEST, 'task', '');
		
		$params = $this->getComponentParams();
		
		// get the list of allowed filetypes (images and docs)	
		$docFiletypes 		= strtr(trim($params->get('docs_filetypes')), " ", "|");	
		$imagesFiletypes 	= strtr(trim($params->get('images_filetypes')), " ", "|");
		$regex 		= '\.('.$docFiletypes.'|'.$imagesFiletypes.')$';
				
		foreach($_REQUEST['cid'] as $item) {
			if( (strpos( "/$regex/", mosFS::getExt($item) ) || strpos($item, ".") === false ) && (strpos($item, "/") === false) && (strpos($item, "\\") === false) )
				$move_files_list[]['name'] = $item;
		}
		
		if($task != 'move_to') {
			$this->showMedia('<span class="message">'.$_LANG->_( 'Please browse the directory tree to the left, and click on the destination folder')."</span>", $move_files_list);
		} else {	
			
			$sourcedir = mosGetParam($_REQUEST, 'lastdir', '');
			$destdir = mosFS::getNativePath($mosConfig_absolute_path.DIRECTORY_SEPARATOR.$listdir, true);
			
			$msg = '';
			foreach($move_files_list as $item) {
				$name = mosFS::getNativePath($mosConfig_absolute_path.DIRECTORY_SEPARATOR.$sourcedir.DIRECTORY_SEPARATOR.$item['name'], false);

				if(is_file($name)) {
					if((!file_exists($destdir.DIRECTORY_SEPARATOR.$item['name'])) && ($listdir != '') && rename($name, $destdir.DIRECTORY_SEPARATOR.$item['name'])) {
						$msg .= $name." file moved<br/>";
					} else {
						$msg .= '<span class="mmError">'.$name." file could not be moved to destination directory</span><br/>";
					}
				} else if(is_dir($name)) {
					
					if((strpos($destdir, $name) === false) && (!file_exists($destdir.DIRECTORY_SEPARATOR.$item['name'])) && (rename($name, $destdir.DIRECTORY_SEPARATOR.$item['name']))) {
						$msg .= $name." directory moved<br/>";
					} else {
						$msg .= '<span class="mmError">'.$name." directory could not be moved</span><br/>";
					}
				}
			}
			$this->showMedia($msg);
		}
	}
	
	/**
	* Extracts selected archive to current directory
	*/
	function extract() {
		global $mosConfig_absolute_path, $_LANG;
			
		$listdir 		= mosGetParam( $_REQUEST, 'listdir', '');
		$archive 		= mosGetParam( $_REQUEST, 'archive', '');
		$style 			= mosGetParam( $_REQUEST, 'style', 'icons');
		$current_folder = mosFS::getNativePath( $mosConfig_absolute_path. '/' .$listdir. '/');	
		
		mosFS::load( '/includes/mambo.files.archive.php' );
		
		$ret = mosArchiveFS::extract( $current_folder.$archive, $current_folder );
					
		if($ret == true)
			$message = 'Files extracted successfully';
		else
			$message = 'Extraction problem. Please check directory rights.';		

		$this->showMedia($message);
	}
}

$tasker =& new mediaManagerTasks();
$tasker->performTask( mosGetParam( $_REQUEST, 'task', '' ) );
$tasker->redirect();



/**
* Returns number of files in given directory
* @param directory
* @return integer files count
*/
function num_files( $dir ) {
	$files = mosFS::listFiles( $dir );
	return count( $files );
}	

/**
* Returns filesize in human-readable format
* @param filesize in bytes
* @return string filesize in human-redable format
*/
function parse_size( $size ){
	if ( $size < 1024 ) {
		return $size.' bytes';
	} else if ( $size >= 1024 && $size < 1024 * 1024 ) {
		return sprintf( '%01.2f', $size / 1024.0 ). ' Kb';
	} else {
		return sprintf( '%01.2f', $size / ( 1024.0 * 1024 ) ) .' Mb';
	}
}

/**
* takes the larger from the width and height and applies the
* formula accordingly...this is so this script will work
* dynamically with any size image
* @param width
* @param height
* @param square side that image should fit into
* @return image width and height in url query-like format
*/
function imageResize( $width, $height, $target ) {
	if ( $width > $target || $height > $target ) {
		if ( $width > $height ) {
			$percentage = ( $target / $width );
		} else {
			$percentage = ( $target / $height );
		}

		//gets the new value and applies the percentage, then rounds the value
		$width 	= round( $width * $percentage );
		$height = round( $height * $percentage );
	}

	return 'width="'. $width .'" height="'. $height .'"';
}

/**
* takes the larger size of the width and height and applies the
* formula accordingly...this is so this script will work
* dynamically with any size image
* Modifies given parameters by reference
* @param width
* @param height
* @param square side that image should fit into
*/
function imageResize2( &$width, &$height, $target ) {
	if ( $width > $target || $height > $target ) {
		if ( $width > $height ) {
			$percentage = ( $target / $width );
		} else {
			$percentage = ( $target / $height );
		}

		//gets the new value and applies the percentage, then rounds the value
		$width 	= round( $width * $percentage );
		$height = round( $height * $percentage );
	}
}



/**
* Build imagelist
* @param string The image directory to display
* @param array with variables
* @param mosParameters component parameters
* @return array files and folders list
*/
function listImages( $listdir, &$vars, &$params ) {

	global $mosConfig_absolute_path;
	global $mosConfig_live_site;
	
	$files = array();

	$path = mosFS :: getNativePath( $mosConfig_absolute_path . DIRECTORY_SEPARATOR . $listdir);
	
	// get the list of allowed filetypes (images and docs)	
	$docFiletypes 		= strtr(trim($params->get('docs_filetypes')), " ", "|");	
	$imagesFiletypes 	= strtr(trim($params->get('images_filetypes')), " ", "|");
		
	// get list of images for directory
	$regex 		= '\.('.$docFiletypes.'|'.$imagesFiletypes.')$';
	$imgRegex 	= '\.('.$imagesFiletypes.')$';

	/*if ( !is_dir( $path ) ) {
		HTML_Media :: draw_no_dir();
		return;
	}*/

	if($listdir != "")
	{
		$f = mosFS :: listFolders( $path, '.' );
		foreach ( $f as $folder ) {
			$temp = array(
				'type' 			=> 'folder',
				'name' 			=> $folder,
				'path' 			=> strtr($listdir. DIRECTORY_SEPARATOR . $folder, '\\', '/'),
				'numfiles' 		=> num_files( $path . $folder ),
				'icon'			=> 'images/icons/folder.gif',
				'item_id' 		=> $folder,
				'size' 			=> -1 // folder filesize set to -1 for parse_size_in_assoc_array function
			);
			$temp['item_checked_out'] = 0;
			$files[] = $temp;
		}
	} else {
		$dirsRegex	= strtr(trim($params->get('root_directories')), " ", "|");
		// if there are no root dirs set in media manager config root/images is choosen by default
		if($dirsRegex == '')
			$dirsRegex = "images";
		$f = mosFS :: listFolders( $path, $dirsRegex );
		foreach ( $f as $folder ) {
			$temp = array(
				'type' 			=> 'folder',
				'name' 			=> $folder,
				'path' 			=> $folder,
				'numfiles' 		=> num_files( $path . $folder ),
				'icon'			=> 'images/icons/folder.gif',
				'item_id' 		=> $folder,
				'size' 			=> -1 // folder filesize set to -1 for parse_size_in_assoc_array function
			);
			$temp['item_checked_out'] = 0;
			$files[] = $temp;
		}			
	}
	
	$d = mosFS :: listFiles( $path, $regex );
		
	foreach ( $d as $file ) {
		$filepath = $path.$file;
		if (eregi($imgRegex, $file)) {
			// get info of image file
			$image_info = @ getimagesize( $filepath );
			$temp = array(
				'type' 		=> 'image',
				'filetype'  => mosFS::getExt($file),
				'name' 		=> $file,
				'path' 		=> $filepath,
				'url' 		=> '/' . str_replace( '\\', '/', $listdir ) . '/' . $file,
				'width' 	=> $image_info[0],
				'height' 	=> $image_info[1],
				'pwidth' 	=> $image_info[0] + 70,
				'pheight' 	=> $image_info[1] + 70,
				'size' 		=>  filesize( $filepath ),
				'item_id' 	=> $file
			);	

			if ( $image_info[0] < 85 && $image_info[1] < 40  ) {
				$temp['padding'] = 'class="image"';
			}				
			
			if ( $image_info[0] > 85 || $image_info[1] > 85 ) {
				imageResize2( $image_info[0], $image_info[1], 85 );
			}				
			$temp['iwidth'] 	= $image_info[0];
			$temp['iheight'] 	= $image_info[1];
			
		} else {
		
			// document files
			$filetype = mosFS::getExt($file);
			
			// maybe this should not be hardcoded too?
			if($filetype == 'zip' || $filetype == 'gz' || $filetype == 'bz2' ) {
				$type = 'archive';
			} else {
				$type = 'doc';
			}
				
			$temp = array(
				'type' 		=> $type,
				'filetype'	=> $filetype,
				'name' 		=> $file,
				'path' 		=> $filepath,
				'url' 		=> '/' . str_replace( '\\', '/', $listdir ) . '/' . $file,
				'size' 		=> filesize( $filepath ),
				'item_id' 	=> $file
			);
		}

		// look in administrator/images/icons/ for file filetype.gif, if not found show unknown.gif
		$filename = 'images/icons/'.mosFS::getExt($temp['name']).'.gif';
		if (file_exists($filename)) {
			$temp['icon'] = $filename;
		} else {
			if($temp['type'] != 'archive')
				$temp['icon'] = 'images/icons/unknown.gif';
			else
				$temp['icon'] = 'images/icons/archive.gif';			
		}
		
		// look in administrator/images/bigicons/ for file filetype.gif, if not found show unknown.gif
		$filename = 'images/bigicons/'.mosFS::getExt($temp['name']).'.png';
		if (file_exists($filename)) {
			$temp['bigicons'] = $filename;
		} else {
			if($temp['type'] != 'archive')
				$temp['bigicons'] = 'images/bigicons/unknown.png';
			else 
				$temp['bigicons'] = 'images/bigicons/archive.png';
		}		
		
		// facedancer: not needed? but I don't know how to make it works without that
		$temp['item_checked_out'] = 0;
		$files[] = $temp;
	}
	
	//ordering
	switch($vars['orderCol'])
	{
		case 'name' :	if($vars['orderDirn'] == 0)
							usort($files, "cmp_name_asc");
						else
							usort($files, "cmp_name_desc");		
						break;

		case 'size' :	if($vars['orderDirn'] == 0)
							usort($files, "cmp_size_asc");
						else
							usort($files, "cmp_size_desc");		
						break;

		case 'type' :	if($vars['orderDirn'] == 0)
							usort($files, "cmp_type_asc");
						else
							usort($files, "cmp_type_desc");		
						break;
	}
	
	array_walk($files, 'parse_size_in_assoc_array');
	

	if($listdir != "") {
		// after sorting files array add 'up' folder
		
		$upDir = dirname($listdir);
		if($upDir == ".")
			$upDir = "";
		$temp = array(
				'type' 			=> 'special_folder',
				'name' 			=> 'up',
				'path' 			=> $upDir, //dirname(strtr($listdir, '\\', '/')),
				'numfiles' 		=> '',
				'icon'			=> 'images/btnFolderUp.gif',
				'item_id' 		=> '..', 
				'item_checked_out' => 0
			);
		$temp['item_checked_out'] = 0;
		array_unshift($files, $temp);
	}
	
	/*
	// and 'current' folder
	$temp = array(
			'type' 			=> 'special_folder',
			'name' 			=> '.',
			'path' 			=> strtr($listdir, '\\', '/'),
			'numfiles' 		=> '',
			'icon'			=> 'images/icons/folder.gif',
			'item_id' 		=> '.', 
			'item_checked_out' => 0
		);
	$temp['item_checked_out'] = 0;
	array_unshift($files, $temp);
	*/
	
	
	// facedancer: dont know whatisit
	//now sort the folders and images by name.

	// handling for document file display
	// TODO Still need to do docs
	/*
	foreach ($docs as $doc_name => $doc) {
		$doc_name = key($docs);
		$iconfile = $mosConfig_absolute_path.'/administrator/images/'.substr($doc_name, -3).'_16.png';

		// represent documents with icon images
		if (file_exists($iconfile)) {
			$icon = 'images/'. (substr($doc_name, -3)).'_16.png';
		} else {
			$icon = "images/con_info.png";
		}

		//HTML_Media :: show_doc($doc['file'], $listdir, $icon, $doc['size']);
	}
	*/
	return $files;
}


/**
 * Sort by name function for file list sorting, descending
 */ 
function cmp_name_desc($a, $b)
{
	if($a['type'] == 'folder' && $b['type'] != 'folder')
		return -1;
	else if($a['type'] != 'folder' && $b['type'] == 'folder')
		return 1;
	else	
    	return strcasecmp($a['name'],$b['name']);
}

/**
 * Sort by name function for file list sorting, ascending
 */ 
function cmp_name_asc($a, $b)
{
	if($a['type'] == 'folder' && $b['type'] != 'folder')
		return 1;
	else if($a['type'] != 'folder' && $b['type'] == 'folder')
		return -1;
	else	
    	return strcasecmp($b['name'],$a['name']);
}

/**
 * Sort by filesize function for file list sorting, descending
 */ 
function cmp_size_desc($a, $b)
{
    if ($a['size'] == $b['size']) {
        return 0;
    }
    return ($a['size'] < $b['size']) ? -1 : 1;		
}

/**
 * Sort by filesize function for file list sorting, ascending
 */ 
function cmp_size_asc($a, $b)
{
    if ($a['size'] == $b['size']) {
        return 0;
    }
    return ($a['size'] > $b['size']) ? -1 : 1;		
}

/**
 * Sort by filetype function for file list sorting, descending
 */ 
function cmp_type_desc($a, $b)
{
	if($a['type'] == 'folder' && $b['type'] != 'folder')
		return -1;
	else if($a['type'] != 'folder' && $b['type'] == 'folder')
		return 1;
	else if($a['type'] == 'folder' && $b['type'] == 'folder')
		 	return cmp_name_desc($a, $b);
	else	
    	return strcasecmp($a['filetype'],$b['filetype']);
}

/**
 * Sort by filetype function for file list sorting, ascending
 */ 
function cmp_type_asc($a, $b)
{
	if($a['type'] == 'folder' && $b['type'] != 'folder')
		return 1;
	else if($a['type'] != 'folder' && $b['type'] == 'folder')
		return -1;
	else if($a['type'] == 'folder' && $b['type'] == 'folder')
		 	return cmp_name_asc($a, $b);
	else		 	
    	return strcasecmp($b['filetype'],$a['filetype']);
}	

/**
 * Change filesize in bytes for something readable, folders will have an empty size becouse
 */
function parse_size_in_assoc_array(&$a)
{
	if($a['size'] != -1)
		$a['size'] = parse_size($a['size']);
	else
		$a['size'] = '';
}


?>