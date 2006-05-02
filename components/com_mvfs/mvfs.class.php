<?php	

/**		 
* VFS API FOR ACCESS AND STORAGE INTO MIXED PROTOCOL VIRTUAL FILESYSTEM.
*
* @version $Id: mvfs.class.php,v 1.1 2005/08/30 17:37:41 bluecherry Exp $
* @package MultiVirtualFileSystem
* @author Timothy Beutels - www.bluecherry.be
* @copyright (C) 2005 Timothy Beutels
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/**  
*	CHECK IF LOADED BY MAMBO
*	Ensure this file is being included by a parent file
*/
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/** @constant integer MVFS_FILE  File value for vfs_type. */
define('MVFS_FILE', 1);

/** @constant integer MVFS_FOLDER  Folder value for vfs_type. */
define('MVFS_FOLDER', 2);  

/** @dependency: Pear VFS, Mfile, Mfolder, Config */
require_once("config.php");
require_once(PEAR_VFS);
require_once("mfile.class.php");
require_once("mfolder.class.php");

/**
* MVFS class
*
* @ToDo		Change false returns into Pear_Error returns
* @ToDo		Expand use of mvfs.allowed to interface with Mambo User system & user access db table
*
*/	
class mvfs {
 	 
	 /***									  	  ***
	 *	P A R A M E T E R S  &  V A R I A B L E S   *
	 ***					   				  	  ***/
	 
	/**
    * VFS class that will be used to interface with underlying libraries
    *
	* @access PRIVATE
	*
    * @var VFS $classVFS
    */
	private $classVFS = null; 
	
	 /**
    * String containing the subpath, of the 'root' vfs, that this instance may manipulate
    *
	* @access PRIVATE
	*
    * @var string $subPath
    */
	private $subPath = ""; 
	
	
	 /***									  	 ***
	 *	C L A S S  R E L A T E D  M E T H O D S    *
	 ***					   				  	 ***/
	
	 /**
     * Constructor.		  
     *
     * @access public
     */
	 public function __construct($subPath="")
	 {
		if ($subPath != ""){
		 	$subPath = $this->formalisePath($subPath);
		}
	  	
		/* Save subpath for later use */
		$this->subPath = $subPath;	 
		
		/* Generate path information to be passed in vfsroot parameter */
		$constructPath = $this->formalisePath($mosConfig_absolute_path . $subPath);
				
		/* Load newly constructed path as root for the local vfs. */
		$params = array('vfsroot' => $constructPath);
		 
		/* Initialise the VFS class */	
		$newVFS = new VFS();	
				
		/* Request the object to behave as the VFS File library */
		$this->classVFS = $newVFS->factory("file", $params);
	 
	 }		 
	 
	 /**
     * Returns wether class was constructed.	  
	 *
     * @access public
     *
     * @return boolean true when constructed, false in the other case.
     */
	 public function isInit()
	 {
	 		 return ($this->classVFS != null);
	 }
	 
	 /***									  	 		   ***
	 *	P O I N T E R  R E L A T E D  O P E R A T I O N S    *
	 ***					   				  			   ***/
	 
	 /**
     * Write/Create a file/folder pointer		  
     *
     * @access public
     *
     * @param integer $type		Use MVFS_FILE and MVFS_FOLDER to define the type of pointer.
	 * @param string $vfs_type	The vfs type we should point to. One of the following: file, ftp, sql, sql_file.
	 * @param string $vfs_path	Path where file/folder can be found in vfs specified above!
	 * @param string $param		Parameters necessary to connect to the vfs. Format: "vfsroot:/some/dir;username:someuser;..."
	 *							NOTE: Depends on $vfs_type. Check Pear VFS Doc's!
	 * @param string $path		Path where file/folder is to be found in local mvfs.
	 * @param string $name		Name of file/folder in local mvfs AND remote (or other protocol) vfs.
	 *
	 * @param integer $ID		Default = -1, new pointer will be created. If specified a new pointer will be created.
	 *
	 * @return boolean 			True if created, false if: not created OR access not allowed
     */
	 public function writePointer($type, $vfs_type, $vfs_path, $vfs_param, $path, $name, $ID = -1){
	 	
		/* Is path allowed */
		if (!$this->allowed($path)) { return false; }
		 
		/* Formalise path */ 
		$path = $this->formalisePath($path); 
		
		/* Encrypt parameters */
		$vfs_param = $this->encrypt($vfs_param);
		 
		/* Build query  */ 
		if ($ID == -1) {
		 	// Build query to INSERT pointer 
			$values = "'". $type ."','". $vfs_type ."','". $vfs_param ."','". $path ."','". $name ."'";
			$query = "INSERT INTO #__mvfs (type, vfs_type, vfs_path, vfs_param, path, name) VALUES (". $values .")";
		} else {						  
			// Build query to UPDATE pointer
			$query = "UPDATE #__mvfs " . 
					 "SET type = '". $type ."', vfs_type = '". $vfs_type .", vfs_path = '". $vfs_path .", ".
					 "vfs_param = '". $vfs_param .", path = '". $path ."name = '". $name .
					 "WHERE ID = ". $ID;
		}
		
		/* Execute query and get results */	 
	 	$database -> setQuery($query); 
		$result = $database -> loadObjectList(); 
		
		/* Check for positive return */
			// result should not be false
		if ($result === false) { return false; }
		
			// Count should be one (1)
		$count = count($result);
		if ($count === 1){
			// Check name and path
			return (($result[0] -> name == $name) && ($result[0] -> path == $path));
		} else {
			 return false;
		}
	 }
	 
	 /**
     * Read a file/folder pointer		  
     *
     * @access public
     *
     * @param integer &$type	MVFS_FILE or MVFS_FOLDER, defines the type of pointer.
	 * @param string &$vfs_type	The vfs type we should point to. One of the following: file, ftp, sql, sql_file.
	 * @param string &$vfs_path	Path where file/folder can be found in vfs specified above!
	 * @param string &$vfs_param	Parameters necessary to connect to the vfs. Format: "vfsroot:/some/dir;username:someuser;..."
	 *							NOTE: Depends on $vfs_type. Check Pear VFS Doc's!
	 * @param string &$path		Path where file/folder is to be found in local mvfs.
	 * @param string &$name		Name of file/folder in local mvfs AND remote (or other protocol) vfs.
	 *
	 * @param integer $ID		The ID of the pointer to read.
	 *
	 * @return boolean 			True if found, false if: not found OR access not allowed
     */
	 public function readPointer(&$type, &$vfs_type, &$vfs_path, &$vfs_param, &$path, &$name, $ID){
	 	
	
		/* Build query */ 
		$query = "SELECT * FROM #__mvfs WHERE ID = ". $ID;
		
		/* Execute query and get results */	 
	 	$database -> setQuery($query); 
		$result = $database -> loadObjectList(); 
		
		/* Check for positive return */
			// result should not be false
		if ($result === false) { return false; }
		
			// Count should be one (1)
		$count = count($result);
		if ($count === 1){
				// Return values
				$type = $result[0] -> type;
				$vfs_type = $result[0] -> vfs_type;
				$vfs_path = $result[0] -> vfs_path;
				$vfs_param = $result[0] -> vfs_param;
				$path = $result[0] -> path;
				$name = $result[0] -> name;
				
				// Is path allowed
				if (!$this->allowed($path)) { return false; }
				
				// Encrypt parameters
				$vfs_param = $this->decrypt($vfs_param);
				
		} else {
			 return false;
		}
	 } 
	 
	 /**
     * Delete a file/folder pointer by ID		  
     *
     * @access public
     *
     * @param integer $ID		ID of pointer to delete
	 *
	 * @return boolean 			True on deletion, false if: not deleted OR access not allowed
     */	  
	 public function deletePointer($ID){
	 	
		/* Is path allowed */
		$this -> readPointer("","","","",$path,"",$ID);
		if (!$this->allowed($path)) { return false; } 
		
		/* Build query */
		$query = "DELETE FROM #__mvfs WHERE ID = ". $ID;
		
		/* Execute query and get results */	 
	 	$database -> setQuery($query); 
		$result = $database -> loadObjectList(); 
		
		/* Check for positive return */
			// result should not be false
		if ($result === false) { return false; }
		
			// Count should be zero (0)
		$count = count($result);
		return ($count === 0);
	 
	 }
	 
	 /**
     * Find a file/folder pointer by Path, Name and optionally type (MVFS_FILE or MVFS_FOLDER)
     *
     * @access public
     *
     * @param string $path		Path in mvfs where pointer should be
	 * @param string $name		Name of file/folder in pointer
	 * @param integer $type		OPTIONAL, MVFS_FILE or MVFS_FOLDER
	 *
	 * @return mixed			ID of pointer if found / FALSE if not found OR not allowed path
     */	  
	 public function findPointer($path, $name, $type=0){
	 	/* Is path allowed */
		if (!$this->allowed($path)) { return false; } 		 
		
		/* Build query */ 
		if ($type != 0){ $whereType = "AND type = ". $type; } 
		else { $whereType = ""; }
		$query = "SELECT ID FROM #__mvfs WHERE (path = '". $path ."' AND name = '". $name ."' ". $whereType .")";
		
		/* Execute query and get results */	 
	 	$database -> setQuery($query); 
		$result = $database -> loadObjectList(); 
		
		/* Check for positive return */
			// result should not be false
		if ($result === false) { return false; }
		
			// Count should be one (1)
		$count = count($result);
		if ($count === 1){
				// Return ID
				return $result[0] -> ID;
		} else {
			 return false;
		}
	 }
	 
	 /**
     * Find all file/folder pointers by Path optionally type (MVFS_FILE or MVFS_FOLDER)
     *
     * @access private
     *
     * @param string $path		Path in mvfs where pointer should be
	 * @param integer $type		OPTIONAL, MVFS_FILE or MVFS_FOLDER
	 *
	 * @return array/boolean	ID's of pointers if found / FALSE if not found OR not allowed path
     */	  
	 private function findPointers($path, $type=0){
	 	/* Is path allowed */
		if (!$this->allowed($path)) { return false; } 		 
		
		/* Build query */ 
		if ($type != 0){ $whereType = "AND type = '". $type ."'"; } 
		else { $whereType = ""; }
		$query = "SELECT * FROM #__mvfs WHERE (path = '". $path ."' ". $whereType .")";
		
		/* Execute query and get results */	 
	 	$database -> setQuery($query); 
		$result = $database -> loadObjectList(); 
		
		/* Check for positive return */
			// result should not be false
		if ($result === false) { return false; }
		
			// Load into array
		$arr_ids = array();
		$count = count($result);
		for ($i=0; $i < $count; $i++){
				// Read & save ID
				$arr_ids[$i]['ID'] = $result[$i] -> ID;
				$arr_ids[$i]['NAME'] = $result[$i] -> name;  
		} 
		
		// 	Array of ids should not be empty 
		if (count($arr_ids) == 0) { return false; }
		else { return $arr_ids; }
	 }
	 
	 /**
     * Construct vfs from pointer
     *
     * @access private
     *
     * @param integer $ID		ID of pointer
	 * @param string &$path		Path to file/folder in created vfs. Value on return.
	 *
	 * @return vfs				Initialised VFS based on pointer information, NULL if failed
     */	  
	 private function vfsFromPointer($ID, &$path){ 
	   																									 
	   	/* Read pointer information into variables */
	 	$this->readPointer("", $vfs_type, $path, $vfs_param, "", "", $ID);
				
		/* Translate parameter list into array */
			// Create array
		$params = array();							  
			// Split into parameter groups of format 'par_name:par_value'
		$paramsSplit = explode(";",$vfs_param);
		
		$count = count($paramsSplit);
		for ($i = 0; $i < $count; $i++){
			// Split into parameter data
			$paramSplit = explode(":", $paramsSplit[$i]); 
			// Put in array
			$params[$paramSplit[0]] = $paramSplit[1];
		}
		 
		/* Initialise the VFS class */	
		$newVFS = new VFS();	
				
		/* Request the object to behave as the VFS class of type $vfs_type */
		$newVFS = $newVFS->factory($vfs_type, $params);
		
		/* Return newly created VFS */ 
		return $newVFS;
	 
	 } 
	 
	  /**
     * Return mFile object from given parameters
     *
     * @access public
     *
     * @param string $path		Path in mvfs where file is to be found
	 * @param string $name		Filename of file in mvfs.
	 *
	 * @return mFile			Initialised mFile, NULL if failed
     */	  
	 public function getFile($path, $name){
	 	
		/* Is path allowed */
		if (!$this->allowed($path)) { return false; }
		 
		/* Formalise path */ 
		$path = $this->formalisePath($path); 
		
		/* File in local vfs? */
		if ($this -> classVFS -> exists($path, $name)) {
		
		 	$file = new mFile($path, $path, $name, $this -> classVFS);
			
		} else {
		
			/* File not in local vfs, direct file pointer? */ 
			$filePointer = $this -> findPointer($path, $name, MVFS_FILE); 
			
			if (!($filePointer === false)) {	
			
			 	// Direct file pointer found, construct and return!
				$vfs_path = "";
				$newVFS = $this -> vfsFromPointer($filePointer, $vfs_path);
				$file = new mFile($path, $vfs_path, $name, $newVFS);
				
			} else {   
			
			   // Find parent folder 
			   		/// Explode path
			   $arr_folders = explode("/",$path);
			   		/// Retrieve name of 1st parent folder
			   $arr_count = count($arr_folders);	 
			   $parent_folder = $arr_folders[$count-2]; //// -2 because path ends with '/' so -1 would return ''
			   		/// Reconstruct path to 1st parent folder
				$parent_path = "";
				for ($i = 1; $i < $count-2; $i++){ //// $i = 1 because path begins with '/' so 0 would return ''
				 	$parent_path .= "/" & $arr_folders[$i];
				}
				$parent_path .= "/"; //// Path should end with '/'
			   $folder = $this -> getFolder($parent_path, $parent_folder);	
			   
			   if ($folder == null) {
			   
			   		// No folder found
			   		$file = null;
					
			   } else {
			   
			   	   if ($this -> fileExists($name, $folder)){
				   
				   		// File exists in folder
						$newPath = $folder->path . $folder->name . "/";
						$newVfs_path = $folder->vfs_path . $folder->name . "/";
				   		$file = new mFile($newPath, $newVfs_path, $name, $folder->folderVFS);
						
				   } else {
				   
				   		// File does not exist in folder
				   		$file = null;
						
				   }
			   }
			
			}
		}
		
		return file;
	 
	 }
	 
	  /**
     * Explode $path into vars that can be used to construct mFolder.
	 * E.G.: /path/to/some/folder returns "/path/to/some/" and $name = "folder"
     *
     * @access private
     *
     * @param string $path		Path to folder.
	 * @param string &$name		Name of folder.	
	 *
	 * @return mFile			Initialised mFolder, NULL if failed
     */	
	 private function getFolderInfo($path, &$name){ 
	 	/* Formalise path */ 
		$path = $this->formalisePath($path);
		
		/* Is path allowed */
		if (!$this->allowed($path . $name)) { return false; } 
	 
	  	/* Find parent folder  */
	   		/// Explode path
	   $arr_folders = explode("/",$path);
	   		/// Retrieve name of 1st parent folder
	   $arr_count = count($arr_folders);	 
	   $parent_folder = $arr_folders[$count-2]; //// -2 because path ends with '/' so -1 would return ''
	   		/// Reconstruct path to 1st parent folder
		$parent_path = "";
		for ($i = 1; $i < $count-2; $i++){ //// $i = 1 because path begins with '/' so 0 would return ''
		 	$parent_path .= "/" & $arr_folders[$i];
		}
		$parent_path .= "/"; //// Path should end with '/' 
		
		return $parent_path;
	 }
	 
	 /**
     * Return mFolder object from given parameters
     *
     * @access public
     *
     * @param string $path		Path in mvfs where folder is to be found
	 * @param string $name		Name of folder in mvfs.	
	 *
	 * @return mFile			Initialised mFolder, NULL if failed
     */	  
	 public function getFolder($path, $name){
	 	
		/* Formalise path */ 
		$path = $this->formalisePath($path);
		
		/* Is path allowed */
		if (!$this->allowed($path . $name)) { return false; } 
		
		/* Set vfs to use */
		$vfs = &$this -> classVFS;
		 
		/* Folder in local vfs? */
		if ($vfs -> exists($path, $name)) {
		
		 	$folder = new mFolder($path, $path, $name, $this -> classVFS);
			
		} else {
		
			/* Folder not in local vfs, direct folder pointer? */ 
			$folderPointer = $this -> findPointer($path, $name, MVFS_FOLDER); 
			
			if (!($folderPointer === false)) {	
			
			 	// Direct folder pointer found, construct and return!
				$vfs_path = "/";
				$newVFS = $this -> vfsFromPointer($folderPointer, $vfs_path);
				$folder = new mFolder($path, $vfs_path, $name, $newVFS);
				
			} else {   
			
			   $parent_path = $this -> getFolderInfo($path, $parent_folder);
			   $tempFolder = $this -> getFolder($parent_path, $parent_folder);		    
			   
			   IF ($tempFolder == null) {
			   
			   		/// No folder found
			   		$folder = null;
					
			   } else {
			   	   
			   		/// Adapt folder object
				   if ($this -> folderExists($name, "/", $tempFolder)){
			   
			   	   		$folder = $tempFolder;
						
				   } else {
				   
				   		//// File does not exist in folder
				   		$folder = null;
						
				   }
			   }
			
			}
		}
		
		return folder;
	 
	 }
	 
	 /***									  	  ***
	 *	F I L E S Y S T E M   O P E R A T I O N S   *
	 ***					   				  	  ***/
	 
	 /**
     * Retrieves a file from the VFS.		  
     *
     * @access public
     *
     * @param mFile $file  File object to read from.
	 *
	 * @return String/Boolean The file data. FALSE when file is empty OR file does not exist.
     */
	 public function read($file)
	 {
	 		if ( !($file -> isInit()) ) { return false; } 
		 	return $file -> fileVFS -> read($file -> path, $file -> filename);

	 } 
	 
	 /**
     * Stores the file in the VFS from raw data.		  
     *
     * @access public
     *
     * @param mFile $file  			File object to write to.
	 * @param string $data			The file data
	 * @param boolean $autocreate	Automatically create directories?
	 *
	 * @return mixed 				TRUE on success, PEAR_Error on failure.
     */
	 public function write($file, $data, $autocreate = false)
	 {
	 		if ( !($file -> isInit()) ) { return false; } 
		 	return $file -> fileVFS -> writeData($file -> path, $file -> filename, $data, $autocreate);
	 } 
	 
	 /**
     * Deletes file from VFS.		  
     *
     * @access public
     *
     * @param mFile $file  			File object to delete. Returns NULL if succesfull.
	 *
	 * @return mixed 				TRUE on success, Pear_Error on failure.
     */
	 public function deleteFile(&$file)
	 {
	 		if ( !($file -> isInit()) ) { return false; }
			
			// Delete physical file
			$result = $file -> fileVFS -> deleteFile($file -> vfs_path, $file -> filename);
		 				
			if ($result === true) {
							// Delete pointer if exists
							$filePointer = $this -> findPointer($file -> path, $file -> name, MVFS_FILE); 
							if (!($filePointer === false)){
								// File Pointer to this file was found, delete
								$result = $this -> deletePointer($filePointer);							
							} 								
			}
			
			if ($result) {
			 	$file = null;
			}
			return $result;
	 } 
	 
	 /**
     * Folder exists in folder VFS.		  
     *
     * @access public
     *
	 * @param string $path			Path to folder to check RELATIVE to path from $folder
 	 * @param string $name			Name of folder to check
     * @param mFolder $folder  		Folder object where to look in. Returns NULL if succesfull.
	 *
	 * @return boolean 				TRUE on success, false on failure.
     */
	 public function folderExists($relPath, $name, $folder){ 
	 
	 	/* Formalise path */ 
		$relPath = $this->formalisePath($relPath, true);
		
		/* Folder exists in vfs of $folder? */ 
	  	$result = $folder -> folderVFS -> isFolder($folder -> vfs_path . $relPath, $name);
		
		/* Folder has direct folder pointer? */
		if (!$result){
			$fPointer = $this -> findPointer($folder -> path . $path, $name, MVFS_FOLDER);
			if (!( $fPointer === false )) {
				$folder = vfsFromPointer($fPointer, "");
				$result = $this -> folderExists("/", $name, $folder);
			}
		}
		
		return $result;
	 }
	 
	 /**
     * File exists in folder VFS.		  
     *
     * @access public
     *
	 * @param string $path			Path to file to check RELATIVE to path from $folder
 	 * @param string $name			Name of file to check
     * @param mFolder $folder  		Folder object to look in. Returns NULL if succesfull.
	 *
	 * @return boolean 				TRUE on success, false on failure.
     */
	 public function fileExists($relPath, $name, $folder){ 
	 
	 	/* Formalise path */ 
		$relPath = $this->formalisePath($relPath, true);
		
		/* File exists in vfs of $folder? */ 
	  	$result = $folder -> folderVFS -> exists($folder -> vfs_path . $relPath, $name);
		
		/* File has direct file pointer? */
		if (!$result){
			$fPointer = $this -> findPointer($folder -> path . $path, $name, MVFS_FILE);
			if (!( $fPointer === false )) {
				$file = vfsFromPointer($fPointer, "");
				$result = $this -> fileExists("/", $name, $file);
			}
		}
		
		return $result;
	 }
	 
	 /**
     * Deletes contents of folder from VFS.		  
     *
     * @access public
     *
     * @param mFolder $folder  		Folder object to empty.
	 *
	 * @return mixed 				TRUE on success, PEAR_Error on failure.
     */
	 public function emptyFolder($folder)
	 {
	 	if ( !($folder -> isInit()) ) { return false; }	
		
		// Delete physical content of file
		$result_contents = $folder -> folderVFS -> emptyFolder($folder -> vfs_path . $folder -> name);
		
		// Delete file pointers in folder + physical file						
		$arr_pointer = $this -> findPointers($folder -> path . $folder -> name, MVFS_FILE);  
		$result_fpointers = true;
		for ($i=0; $i < count($arr_pointers); $i++) { 
			$vfs = $this -> vfsFromPointer($arr_pointer[$i]['ID'], $vfs_path);
			$tempfile = mFile($folder -> path . $folder -> name, $vfs_path, $arr_pointer[$i]['NAME'], $vfs);
			$result_pointers  = $result_pointers && $this -> deleteFile($tempfile);
		}
		
		// Delete folder pointers in folder + physical folder						
		$arr_pointer = $this -> findPointers($folder -> path . $folder -> name, MVFS_FOLDER); 
		$result_folpointers = true; 
		for ($i=0; $i < count($arr_pointers); $i++) { 
			$vfs = $this -> vfsFromPointer($arr_pointer[$i]['ID'], $vfs_path);
			$tempfolder = mFolder($folder -> path . $folder -> name, $vfs_path, $arr_pointer[$i]['NAME'], $vfs);
			$result_pointers  = $result_pointers && $this -> deleteFolder($tempfolder);
		}
		
		return ($result_contents && $result_fpointers && $result_folpointers);
			
	}
	 
	 /**
     * Deletes folder from VFS.		  
     *
     * @access public
     *
     * @param mFolder $folder  		Folder object to delete. Returns NULL if succesfull.
	 *
	 * @return mixed 				TRUE on success, PEAR_Error on failure.
     */
	 public function deleteFolder(&$folder)
	 {
	 		if ( !($folder -> isInit()) ) { return false; }
			
			// Delete folder contents
			$result_contents = $this -> emptyFolder($folder);
			
			// Delete folder
			$result_folder = $folder -> folderVFS -> deleteFolder($folder -> vfs_path, $folder -> name);
			
			// Result
			$result = $result_contents && $result_folder;
			
		 	if ($result === true){
				$folder = null;
			}
			return $result;
	 } 
	 
	 /**
     * Renames file in VFS.		  
     *
     * @access public
     *
     * @param mFile &$file	  			File object to rename.
	 * @param string $newname			New filename.
	 *
	 * @return mixed 				TRUE on success, FALSE on failure.
     */
	 public function renameFile(&$file, $newname)
	 {	 
	  	  if ( !($file -> isInit()) ) { return false; }
		  
		  $check = $file -> fileVFS -> rename($file -> vfs_path, $file -> name, $file -> vfs_path, $newname);
		  
		  $filePointer = $this -> findPointer($file -> path, $file -> name, MVFS_FILE);
		  
		  if (!($filepointer === false) && $check){	   
		  		$check = $this -> readPointer($type, $vfs_type, $vfs_path, $vfs_param, $path, $name, $filePointer); 
		  		$check = $check && $this -> writePointer(MVFS_FILE, $vfs_type, $vfs_path, $vfs_param, $path, $newname, $filePointer);
		  }
		  
		   if ($check){ $file -> name = $newname; }
		   
		   return $check;
		  
	 }
	 
	  /**
     * Renames folder in VFS.		  
     *
     * @access public
     *
     * @param mFolder &$folder	  		folder object to rename.
	 * @param string $newname			New name.
	 *
	 * @return mixed 				TRUE on success, FALSE on failure.
     */
	 public function renameFolder(&$folder, $newname)
	 {	 
	  	  if ( !($folder -> isInit()) ) { return false; }
		  
		  $check = $folder -> folderVFS -> rename($folder -> vfs_path, $folder -> name, $folder -> vfs_path, $newname);
		  
		  $folderPointer = $this -> findPointer($folder -> path, $folder -> name, MVFS_FOLDER);
		  
		  if (!($folderPointer === false) && $check){	   
		  		$check = $this -> readPointer($type, $vfs_type, $vfs_path, $vfs_param, $path, $name, $folderPointer); 
		  		$check = $check && $this -> writePointer(MVFS_FOLDER, $vfs_type, $vfs_path, $vfs_param, $path, $newname, $folderPointer);
		  }
		  
		   if ($check){ $folder -> name = $newname; }
		   
		   return $check;
		  
	 }
	 
	  /**
     * Create new, empty, folder		  
     *
     * @access public
     *
     * @param string $path	  			Path to new folder.
	 * @param string $name				Name of new folder.
 	 * @param boolean $autocreate		Create path of not existing? DEFAULT = TRUE 
	 *
	 * @return mFolder 					mFolder on success, NULL on failure.
     */						   
	 public function createFolder($path, $name, $autocreate=true)
	 {	 
	  	 	$path = $this -> formalisePath($path);
			if(!$this -> allowed($path)) { return false; }
			
			$check = true;
			
			/* Get folder to create new file in */
				// Extract folder info from $path
			$fol_path = $this -> getFolderInfo($path, $fol_name);
				 
				// Construct mFolder
			$folder = $this -> getFolder($fol_path, $fol_name);
			if ($autocreate && ($folder == null)){ 
				$folder = $this -> createFolder($fol_path, $fol_name);
			}
			
			if ($folder === null){ $check = false; }
			
			/* Create empty folder  */
			if ($check) {
				$check = $folder -> folderVFS -> createFolder($folder -> vfs_path, $name);
			}
			else { return null; }
			
			/* Return newly created folder */
			return $this -> getFolder($path, $name);
			
		}
	 	 
	  /**
     * Create new, empty, file		  
     *
     * @access public
     *
     * @param string $path	  			Path to new file
	 * @param string $name				Name of new file. 
	 * @param boolean $autocreate		Create path of not existing? DEFAULT = TRUE
	 *
	 * @return mFile 					mFile on success, NULL on failure.
     */						   
	 public function createFile($path, $name, $autocreate=true)
	 {	 
	  	 	$path = $this -> formalisePath($path);
			if(!$this -> allowed($path)) { return false; }
			
			$check = true;
			
			/* Get folder to create new file in */
				// Extract folder info from $path
			$fol_path = $this -> getFolderInfo($path, $fol_name);
				 
				// Construct mFolder
			$folder = $this -> getFolder($fol_path, $fol_name);
			if ($autocreate && ($folder == null)){ 
				$folder = $this -> createFolder($fol_path, $fol_name);
			}
			
			if ($folder === null){ $check = false; }
			
			/* Create empty file  */
			if ($check) {
				$check = $folder -> folderVFS -> writeData($folder -> vfs_path, $name, "");
			}
			else { return null; }
			
			/* Return newly created file */
			return $this -> getFile($path, $name);
			
		}
	 
	 /**
     * Copy/Move file handler.		  
     *
     * @access private
     *
     * @param mFile $file	  			File object to move/copy.
	 * @param string $newpath			New path. 
	 * @param boolean $delete			Delete original file? (TRUE: moveFile, FALSE: copyFile)
	 *
	 * @return mixed 					mFile on success, NULL on failure.
     */						   
	 private function copyMoveFile(&$file, $newpath, $delete)
	 {	 
	  	 	if ( !($file -> isInit()) ) { return null; }
	   		$newpath = $this -> formalisePath($newpath);
			if(!$this -> allowed($newpath)) { return null; }
			
			$check = true;
			
			/* Copy physical file to newpath */
				// Read data & create file at new location
				$data = $this -> read($file);						
				$newFile = $this -> createFile($newpath, $file -> name);
				
				if ($newFile === null) { return null; }
				
				// Write data into newly created file
				$check = $this -> write($newFile, $data);
				
				if ($check && (!($this -> read($newFile) == $data))){ $check = null; }
			
			/* Delete original file if necessary  */
					// Delete physical file
			if ($delete && $check) {
				$check = $this -> deleteFile($file); 
			}
			
					// Delete pointer 
			if ($delete && $check){
				$filePointer = $this -> findPointer($file -> path, $file -> name, MVFS_FILE);
				if (!($filePointer === false)) {
					$check = $this -> deletePointer($filePointer);
				} 
			}
			
			if ($check) {	 
				return $newFile;
			} else {
			 	return null;
			}
		}
		
	/**
     * Copy/Move folder handler.
     *
     * @access private
     *
     * @param mFolder $folder	  		folder object to move/copy.
	 * @param string $newpath			New path. 
	 * @param boolean $delete			Delete original folder? (TRUE: moveFolder, FALSE: copyFolder)
	 *
	 * @return mixed 					mFolder on success, NULL on failure.
     */						   
	 private function copyMoveFolder(&$folder, $newpath, $delete)
	 {	 
	  	 	if ( !($file -> isInit()) ) { return null; }
	   		$newpath = $this -> formalisePath($newpath);
			if(!$this -> allowed($newpath)) { return null; }
			
			$check = true;
			
			/* Copy physical folder to newpath */
				// Read data & create folder at new location
				$data = $this -> read($file);						
				$newFolder = $this -> createFolder($newpath, $folder -> name);
				
				if ($newFolder === null) { return null; }
				
				// Copy & delete (OPT) files. 
				$files = $this -> listFolder($folder);
				
				if ($files === false) { return false; }
				
				foreach ($files as $file){
					if ($file['pointer'] != -1){
					 	$fileObj = $this -> getFile($folder -> path, $file['name']);
						if ($fileObj === null){ return false; }
						$file_result = $this -> copyMoveFile($fileObj, $newpath . $folder -> name, $delete);
					}
					else {
						$id = $file['pointer'];								   
						$file_result = $this -> readPointer($type, $vfs_type, vfs_path, $vfs_param, "", $name, $id);
						if (!$delete) { $id = -1; } // Create new pointer
					 	$file_result = $file_result && $this -> writePointer($type, $vfs_type, $vfs_path, $vfs_param, $newpath . $folder -> name, $name, $id);
					}
					if ($file_result === false) { return false;}
				}
				
				// Copy & delete (OPT) sub-folders.
				$folders = $this -> listFolders($folder);
				
				if ($folders === false) { return false; }
				
				foreach ($folders as $folder){
				 	if ($folder['pointer'] != -1){
					 	$folderObj = $this -> getFolder($folder -> path, $folder['name']);
						if ($folderObj === null){ return false; }
						$folder_result = $this -> copyMoveFolder($folderObj, $newpath . $folder -> name, $delete);
					}
					else {
						$id = $folder['pointer'];								   
						$folder_result = $this -> readPointer($type, $vfs_type, vfs_path, $vfs_param, "", $name, $id);
						if (!$delete) { $id = -1; } // Create new pointer
					 	$folder_result = $folder_result && $this -> writePointer($type, $vfs_type, $vfs_path, $vfs_param, $newpath . $folder -> name, $name, $id);
					}
					if ($folder_result === false) { return false;}
				}  
				

			
			/* Delete original folder if necessary  */
					// Delete physical folder + contents
			if ($delete && $check) {
				$check = $this -> deleteFolder($folder); 
			}	
			
					// Delete pointer 
			if ($delete && $check){
				$folderPointer = $this -> findPointer($folder -> path, $folder -> name, MVFS_FOLDER);
				if (!($folderPointer === false)) {
					$check = $this -> deletePointer($folderPointer);
				} 
			}
			
			if ($check) {	
				if ($delete) { $folder = $newFolder; } 
				return $newFolder;
			} else {
			 	return null;
			}
		}
	 
	  /**
     * Copies file in VFS.		  
     *
     * @access public
     *
     * @param mFile $file	  			File object to move.
	 * @param string $newpath			New path.
	 *
	 * @return mixed 					mFile on success, FALSE on failure.
     */
	 public function copyFile($file, $newpath)
	 {	 
	  	  if ( !($file -> isInit()) ) { return false; }
		  
		  $copyfile = $this -> copyMoveFile($file, $newpath, false);
		  return $copyfile;
		  
	 }
	 
	  /**
     * Moves file through VFS.		  
     *
     * @access public
     *
     * @param mFile $file	  			File object to move.
	 * @param string $newpath			New path.
	 *
	 * @return mixed 					TRUE on success, FALSE on failure.
     */
	 public function moveFile(&$file, $newpath)
	 {	 
	  	  if ( !($file -> isInit()) ) { return false; }
		  
		  $file = $this -> copyMoveFile($file, $newpath, true);
		  return (!($file === null));
		  
	 }
	 
	  /**
     * Copies folder in VFS.		  
     *
     * @access public
     *
     * @param mFolder $folder	  			folder object to move.
	 * @param string $newpath			New path.
	 *
	 * @return mixed 					mFolder on success, FALSE on failure.
     */
	 public function copyFolder($folder, $newpath)
	 {	 
	  	  if ( !($folder -> isInit()) ) { return false; }
		  
		  $copyfolder = $this -> copyMoveFolder($folder, $newpath, false);
		  return $copyfolder;
		  
	 }
	 
	  /**
     * Moves folder through VFS.		  
     *
     * @access public
     *
     * @param mFolder $folder	  			folder object to move.
	 * @param string $newpath			New path.
	 *
	 * @return mixed 					TRUE on success, FALSE on failure.
     */
	 public function moveFolder(&$folder, $newpath)
	 {	 
	  	  if ( !($folder -> isInit()) ) { return false; }
		  
		  $folder = $this -> copyMoveFile($folder, $newpath, true);
		  return (!($folder === null));
		  
	 }
	 
	 /**
     * Renames file.		  
     *
     * @access public
     *
     * @param mFile &$file	  			File to rename.	
	 * @param string $name				New name.
	 *
	 * @return mixed 					TRUE on success, PEAR_Error object on failure.
     */	
	 public function rename(&$file, $name){
	 
	 	if ( !($file -> isInit()) ) { return false; }

		$path = $file -> vfs_path;
		$result = $file -> fileVFS -> rename($path, $file -> name, $path, $name);
		
		if ($result === true) { $file -> name = $name; }
		
		return $result;
	 
	 }
	 
	 /**
     * Returns list of File OR Folder pointers in folder.		  
     *
     * @access private
     *
     * @param mFolder $folder	  		folder object look in. 
 	 * @param array $type				MVFS_FILE or MVFS_FOLDER
	 * @param array $list			array of physical files.
	 
	 *
	 * @return mixed 					PEAR_Error on failure, array file list on success.
	 *
	 *									
     */	
	 private function _listFolder($folder, $list, $type){
	 
	 	if ( !($folder -> isInit()) ) { return false; }
		
		// File pointers in folder
		$pointers =  $this -> findPointers($folder -> path, $type);
		
		if ($pointers === false ){ return $false; }
		
		$count = count($pointers);
		for ($i=0; $i<$count; $i++){
			IF ($type == MVFS_FILE) {
				$name = $pointers[$i]['NAME'];
				$list[$name]['name'] = $name;
				$list[$name]['pointer'] = $pointers[$i]['ID'];
				$list[$name]['perms'] = "";
				$list[$name]['owner'] = "";
				$list[$name]['group'] = "";
				$list[$name]['size'] = "";
				$list[$name]['date'] = "";
			}
			IF ($type == MVFS_FOLDER){
			   $list[$name]['pointer'] = $pointers[$i]['ID']; 
			   $list[$name]['val'] = $folder -> path . $pointers[$i]['name'];
			   $list[$name]['label'] = $folder -> path . $pointers[$i]['name'];
			   $list[$name]['abbrev'] = $pointers[$i]['name'];
			   $list[$name]['name'] = $pointers[$i]['name'];
			}
			
		}
		
		return $list;
	 
	 }
	 
	 /**
     * Returns list of files in folder.		  
     *
     * @access public
     *
     * @param mFolder $folder	  		folder object look in.
	 *
	 * @return mixed 					FALSE on failure, on success array array['name']: ['name'], ['perms'], ['owner'], ['group'], ['size'], [date], [pointer]=-1 of no pointer 
	 *
	 *									
     */	
	 public function listFolder($folder){
	 
	 	if ( !($folder -> isInit()) ) { return false; }
		
		// Files physically in folder
		$physical =  $folder -> folderVFS -> listFolder($folder -> vfs_path);
		
		if (is_a($physical, 'PEAR_Error')){ return $false; }
		
		foreach ($physical as $file){
			$file['pointer'] = -1;
		}
		
		// File pointers.  Only 'name' and 'pointer' values.
		$fileArray = $this -> _listItems($folder, $physical, MVFS_FILE);
		
		IF ($fileArray === false) { return false; }
		
		
		return $fileArray;
	 
	 }
	 
	 /**
     * Returns list of subfolders in folder.		  
     *
     * @access public
     *
     * @param mFolder $folder	  		folder object look in.
	 *
	 * @return mixed 					FALSE on failure, on success array array['name'] = array('val','abbrev','label','pointer','name')
	 *
	 *									
     */	
	 public function listFolders($folder){
	 
	 	if ( !($folder -> isInit()) ) { return false; }
		
		// Folders physically in folder
		$physical =  $folder -> folderVFS -> listFolders($folder -> vfs_path);
		
		if (is_a($physical, 'PEAR_Error')){ return $false; }
		
		foreach ($physical as $folder){
			$folder['pointer'] = -1; 
			$folder['name'] = $folder['abbrev'];
		}
		
		// Folder pointers. 
		$folderArray = $this -> _listItems($folder, $physical, MVFS_FOLDER);
		
		IF ($folderArray === false) { return false; }
		
		
		return $folderArray;
	 
	 }
	 
	 /***									   ***
	 *	S E C U R I T Y	 & U N I F O R M I T Y   *
	 ***					   				  ***/
	 
	 /**
	 * Execute path description restrictions on string.
	 *
	 * @acces private
	 *
	 * @param string $path			Contains path to be processed
	 * @param boolean $relative		OPTIONAL, set true if formalising relative path
	 *
	 * @return string formalised path
	 */								 
	 private function formalisePath($path, $relative = false) {
	 	
	 	// Remove all whitespaces
		$path = str_replace(" ", "", $path);
		
		// If path is empty, further manipulation is useluss
		if ($path == ""){ return $path; }
		
		// Convert string to lowercase string
		$path = strtolower($path);	 
		
		// Only '/' is supported, replace '\' with '/'
		$path = str_replace("\\", "/", $path); 
		
		// '../' is not allowed
		$path = str_replace("../", "/", $path);
		
		// './' is not allowed
		$path = str_replace("./", "", $path);
		
		// Remove '//'
		$path = str_replace("//", "/", $path);
		
		// Path should start with '/' if not relative
		if ((!$relative) && ($path{0} != "/")) { $path = "/" . $path; } 
		
		// Path should not start with '/' if relative
		if (($relative) && ($path{0} == "/")) { 
			$nexstring = ""; 
			// Remove first character '/'
		   for ($i = 1; $i < count($path); $i++){
		   		$newstring .= $path{$i};
			}
			
			$path = $newstring; 
		} 
		
		// Path should end with '/'
		if ($path{strlen($path)-1} != "/") { $path = $path . "/"; } 
		
		// Return formalised version of this path
		return $path;
		
	 }	 
	 
	 /**
	 * Check if given path is within allowed range 
	 *
	 * @acces private
	 *
	 * @param string $path	Contains path to be processed
	 *
	 * @return bool true if allowed, false if not
	 */											 
	 private function allowed($path) {
	   
	 	// Find allowed subPath in $path.
		// E.G.: allowed subPath = "/some/path", all allowed paths should start with "/some/path"
		// => "some/path/allowed" will return true, "some/other/not/allowed" will return false.
	 	$result = strpos($path, $this->subPath); 
		
		if ($result === false) { 
			return false;
		} else {
		 	return ($result === 0);
		}
	 
	 }	 
	 
	 /**
	 * Encrypts string data with algorithm & key specified in config.php
	 *
	 * @acces private
	 *
	 * @param string $data	Contains string data to encrypt
	 *
	 * @return string Encrypted data
	 */
	 private function encrypt($data) {
	  	
		if ($encrypt){	 
		    include_once 'Encryption/' . $encrypt_method . '.class.php';
			$class = $encrypt_method;
			
			if (class_exists($class)){
				return $class->encrypt($data, $encrypt_sentence);
			} 
		}
		
		return $data;
	 
	 }
	 
	 /**
	 * Decrypts string data with algorithm & key specified in config.php
	 *
	 * @acces private
	 *
	 * @param string $data	Contains string data to Decrypts
	 *
	 * @return string Decrypted data
	 */
	 private function decrypt($data) {
	  	
		if ($encrypt){	 
		    include_once 'Encryption/' . $encrypt_method . '.class.php';
			$class = $encrypt_method;
			
			if (class_exists($class)){
				return $class->decrypt($data, $encrypt_sentence);
			}
		}
		
		return $data;

	 
	 }
}
?>
