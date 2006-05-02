<?php

/** @version $Id: mediahandler.php,v 1.4 2005/08/31 11:39:55 facedancer Exp $
  * @package Mambo
  * @copyright (C) Mateusz Krzeszowiec
  * @author Mateusz Krzeszowiec <mateusz@krzeszowiec.com>
  * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
  */

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );


/**
* Media Handler
* @package Mambo
* @subpackage Media Manager
*/
class mediaHandler {
	var $images;
	var $docs;
	
	/**
	 * Returns Media Manager properities object
	 * @return mosParameters object
	 */
	function _getMediaManagerParams()
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
	
	
	function _getDirectoriesAndFilesList($docsRegex, $imgRegex , $rootDirs='images')
	{
		global $mosConfig_absolute_path;
		global $mosConfig_live_site;		
		if(!is_array($rootDirs))
			$rootDirs = array($rootDirs);
		
		foreach($rootDirs as $rootDir)
		{					
			$path 	= $mosConfig_absolute_path . DIRECTORY_SEPARATOR . $rootDir;
			$this->images[$rootDir] 	= str_replace('\\', '/', mosFS :: listFiles( $path, $imgRegex, true, true ));
			$docs[$rootDir] 	= str_replace('\\', '/', mosFS :: listFiles( $path, $docsRegex, true, true ));
			
			$path = str_replace('\\', '/', $path);
			$this->images[$rootDir] 	= str_replace( $path, '', $this->images[$rootDir]);
			$this->docs[$rootDir] 	= str_replace( $path, '', $this->docs[$rootDir]);
		}
	}
	
	/**
	 * Lists folders and files in format suitable for tree display
	 * @param String path to start from
	 * @param String directory filter
	 * @param String file filter
	 * @param Integer recursion levels
	 * @param Integer starting level
	 * @param Integer parent level 
	 */
	function _listFilesAndFolderTree( $path, $filterDirs, $filterImages, $filterDocs, $maxLevel=3, $level=0, $parent=0 ) {
		$fs = array();
		if ($level == 0) {
			$GLOBALS['_mosFS_folder_tree_index'] = 0;
		}

		if ($level < $maxLevel) {
			mosFS::check( $path );

			$folders = mosFS::listFolders( $path, $filterDirs );

			// first path, index foldernames
			for ($i = 0, $n = count( $folders ); $i < $n; $i++) {
				$id = ++$GLOBALS['_mosFS_folder_tree_index'];
				$name = $folders[$i];
				$fullName = mosFS::getNativePath( $path . '/' . $name, false );
				$fs[] = array(
					'id' 		=> $id,
					'parent' 	=> $parent,
					'name' 		=> $name,
					'fullname' 	=> $fullName,
					'relname' 	=> str_replace( MOSFS_ROOT, '', $fullName ),
				);
			
				$fs2 = $this->_listFilesAndFolderTree( $fullName, '', $filterImages, $filterDocs, $maxLevel, $level+1, $id );
				$fs = array_merge( $fs, $fs2 );
			}
			
			$files = mosFS::listFiles($path, $filterImages."|".$filterDocs);
			// files here			
			for ($i = 0, $n = count( $files ); $i < $n; $i++) {
				$name = $files[$i];
				$fullName = mosFS::getNativePath( $path . '/' . $name, false );
				
				$temp = array(
					'id' 		=> ++$GLOBALS['_mosFS_folder_tree_index'],
					'parent' 	=> $parent,
					'name' 		=> $name,
					'fullname' 	=> $fullName,
					'relname' 	=> str_replace( MOSFS_ROOT, '', $fullName ),
					'filetype' 	=> mosFS::getExt($fullName),
					'size' 		=> $this->_parse_size(filesize($fullName))
				);
				
				if(strstr($filterImages, $temp['filetype'])) {
					$temp['isImg'] = true;
					if(is_callable("getimagesize")) {
							$imginfo = @getimagesize($fullName);
							if($imginfo[0] && $imginfo[1]) {
								$temp['width'] = $imginfo[0];
								$temp['height'] = $imginfo[1];
							}
					}
				} else {
					$temp['isImg'] = false;
				}
				$fs[] = $temp;				
			}
		}
		return $fs;
	}	
	
	
	function showMediaList() {
		
		global $_LANG;
		$mediahandler = '';
		
		if(($js = $this->_loadJS()) == "") {
			return "Editor not supported. Media handler will not show itself becouse it would be useless.";
		} else {	
			
			$mediahandler .= $js;
			
			global $mosConfig_absolute_path;
			global $mosConfig_live_site;
		
			// get the list of allowed filetypes (images and docs)
			$params = $this->_getMediaManagerParams();	
			
			$docsRegex 	= strtr(trim($params->get('docs_filetypes')), " ", "|");	
			$imgRegex 	= strtr(trim($params->get('images_filetypes')), " ", "|");
			$dirsRegex	= strtr(trim($params->get('root_directories')), " ", "|");	
			
			// if there are no root dirs set in media manager config root/images is choosen by default
			if($dirsRegex == '')
				$dirsRegex = "images";
				
			
			$mediahandler .= <<<EOD
<div id="treecellMediaHandler">
	<fieldset>
		<legend>
			Directory Structure
		</legend>
		<script type="text/javascript" src="{$mosConfig_live_site}/includes/js/dtree/dtree.js"></script>
		<link rel="stylesheet" href="{$mosConfig_live_site}/includes/js/dtree/dtree.css" type="text/css" />
		<div class="expander">
			<a href="javascript:d.openAll();" title="Expand All">
				<img src="{$mosConfig_live_site}/administrator/images/expandall.png" border="0" height="13" width="13" alt="Expand All"/></a>
			<a href="javascript:d.closeAll();" title="Collapse All">
				<img src="{$mosConfig_live_site}/administrator/images/collapseall.png" border="0" height="13" width="13" alt="Collapse All"/></a>
		</div>			
			<script language="javascript" type="text/javascript">
			<!--
			d = new dTreeOverlibImgTooltip( 'd', '{$mosConfig_live_site}/includes/js/dtree/img/' );
			d.config.useLines=true;
			d.config.useIcons=true;
			d.config.useCookies=true;
			d.add( 0, -1, 'Directories');
EOD;
			// $fs like filesystem, directory depth hardcoded, should it be the param of Media Manager too?
			$fs = $this->_listFilesAndFolderTree(mosFS::getNativePath($mosConfig_absolute_path), $dirsRegex, $imgRegex, $docsRegex, 10);
			
			
			$path 	= $mosConfig_absolute_path;
			$path 	= str_replace('\\', '/', $path);
			
			$i = 0;
			foreach($fs as $fs_item)  {
				$fs_item["fullname"] 	= str_replace('\\', '/', $fs_item["fullname"]);
				$fs_item["fullname"] 	= str_replace($path, $mosConfig_live_site, $fs_item["fullname"]);
								
				//echo $fs_item['isImg']?"d.add( '".$fs_item["id"]."', '".$fs_item["parent"]."', '".$fs_item["name"]."', 'javascript: insertImage(\'".$fs_item["fullname"]."\');', '', '', '', '', '', '".$mosConfig_live_site."/includes/phpThumb/phpThumb.php?f=png&w=200&h=200&q=20&src=".$fs_item["fullname"]."', '".$fs_item["name"]."', '".$width."', '".$height."', '".$size."', '".$filetype."' );\n":"d.add( '".$fs_item["id"]."', '".$fs_item["parent"]."', '".$fs_item["name"]."', '".$link."', '', '', '', '', '' );\n";
				if(isset($fs_item['isImg'])) { // is img or file
					if($fs_item['isImg']) {
						$size = isset($fs_item["filetype"])?"".$fs_item["size"]."":"";
						$width = isset($fs_item["width"])?"".$fs_item["width"]."":"";
						$height = isset($fs_item["height"])?"".$fs_item["height"]."":"";
						$filetype = isset($fs_item["filetype"])?"".$fs_item["filetype"]."":"";						
						$mediahandler .= "d.add( '".$fs_item["id"]."', '".$fs_item["parent"]."', '".$fs_item["name"]."', 'javascript: insertImage(\'".$fs_item["fullname"]."\');', '', '', '', '', '', '".$mosConfig_live_site."/includes/phpThumb/phpThumb.php?f=png&w=200&h=200&q=20&src=".$fs_item["fullname"]."', '".$fs_item["name"]."', '".$width."', '".$height."', '".$size."', '".$filetype."' );\n";
					} else {
						$mediahandler .= "d.add( '".$fs_item["id"]."', '".$fs_item["parent"]."', '".$fs_item["name"]."', 'javascript: insertFile(\'".$fs_item["fullname"]."\');', '', '', '', '', '' );\n";
					}
				} else { // is dir
					$mediahandler .= "d.add( '".$fs_item["id"]."', '".$fs_item["parent"]."', '".$fs_item["name"]."', '', '', '', '', '', '' );\n";				
				}				
			}
			
			
		$mediahandler .= <<<EOD
							document.write(d);
							//-->
							</script>					
					</fieldset>
				</div>
EOD;
		}
		return $mediahandler;
	}
	
	function _loadJS(  ) {
		global $mosConfig_live_site;
		global $mosConfig_editor;
		
		switch($mosConfig_editor)
		{
			
			case "tmedit" :			
				$name = 'tmedit';
			 	break;
			case "tinymce" : 
				$name = 'tinymce';
				break;
				
			case "mosce" :
				$name = 'mosce';
				break;
			
			case "tinymce_exp" :			
				$name = 'tinymce';
			 	break;
			 	
			 case "wysiwygpro" :
			 	$name = 'wysiwygpro';
			 	break;
			 	
			case "none" :
				$name = 'none';	
			 	break;
			 							
			default : $name = "";
		}
		
		global $mosConfig_editor;
		
		
		$js = "<script language=\"JavaScript\" type=\"text/javascript\" src=\"".$mosConfig_live_site."/libraries/mambo/mediamanager/js/insert-".$name.".js\"></script>\n";
		if($name != "")
			return $js;
		else
			return "";
	}
	
	/**
	* Not used currently
	*/
	function _getPublishedEditor() {
		global $database, $my;
		
		$database->setQuery( "SELECT element"
			    . "\nFROM #__mambots"
			    . "\nWHERE published = 1 AND access <= $my->gid AND folder='editors'"
		);
		$editor = $database->loadResult();
		return $editor;
	}

	/**
	* Returns filesize in human-readable format
	* @param filesize in bytes
	* @return string filesize in human-redable format
	* copied from com_media, maybe should be moved to mosFS?
	*/
	function _parse_size( $size ){
		if ( $size < 1024 ) {
			return $size.' bytes';
		} else if ( $size >= 1024 && $size < 1024 * 1024 ) {
			return sprintf( '%01.2f', $size / 1024.0 ). ' Kb';
		} else {
			return sprintf( '%01.2f', $size / ( 1024.0 * 1024 ) ) .' Mb';
		}
	}	
	
}


?>