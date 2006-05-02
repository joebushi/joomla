<?php	

/**		 
* VFS FOLDER OBJECT FOR ACCESS AND STORAGE INTO MIXED PROTOCOL VIRTUAL FILESYSTEM.
*
* @version $Id: mfolder.class.php,v 1.1 2005/08/30 17:37:41 bluecherry Exp $
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

require_once(PEAR_VFS);
 

/**
* VFS FOLDER class 
*/					 
class mFolder 
{   
	/**
    * The VFS class defining the VFS the folder actually resides in.
    *
    * @var VFS $folderVFS 
	*
	* @access PROTECTED
    */	 
	public $folderVFS = null; 
	
	
	/**
    * String containing folder location in vfs $_VFS.
    *
    * @var STRING $vfs_path
	*
	* @access PROTECTED
    */	
	public $vfs_path = "";
	
	/**
    * String containing folder location in the vfs, as end users would see it.
    *
    * @var STRING $path
	*
	* @access PROTECTED
    */	
	public $path = "";
	
	/**
    * String containing name of folder
    *
    * @var STRING $name 
	*
	* @access PROTECTED
    */	
	public $name = "";
	
	/**
     * Constructor		  
     *
     * @access public
	 *
	 * @return BOOLEAN True if object was constructed correctly, false if not.
     */	
	public function isInit()
	{
	 	if ( (empty($this->path)) | (empty($this->vfs_path)) | (empty($this->name)) | (!is_object($this->folderVFS)) | ($this->folderVFS==null) ) 
		{
		 	return false;
		}	
		else
		{
			return true;
		}
	}
	
	/**
     * Constructor		  
     *
     * @access public
     *	
     * @param STRING $path  Path to this folder in the main vfs.
     * @param STRING $vfs_path  Path to this folder in vfs $vfs.
	 * @param STRING $name	foldername of this folder.	
	 * @param VFS $vfs	Vfs object to set for this folder.	
	 *
	 * @return BOOLEAN True if succesfull, false if not.
     */
	 public function __construct($path, $vfs_path, $name, $vfs)
	 {
	 	/* Pre-conditions */
		if ( (empty($path)) | (empty($vfs_path)) | (empty($name)) | (!is_object($vfs)) | ($vfs==null) ) 
		{
		 	return false;
		}	
		
		/* Store information into object */	 
		$this->path = $path; 
	 	$this->vfs_path = $vfs_path;
		$this->name = $name;
		$this->folderVFS = $vfs; 
		
		/* Post-conditions */
		return $this->isInit();
	 
	 }					
	
}
?>
