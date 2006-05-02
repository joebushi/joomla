<?php	

/**		 
* VFS FILE OBJECT FOR ACCESS AND STORAGE INTO MIXED PROTOCOL VIRTUAL FILESYSTEM.
*
* @version $Id: mfile.class.php,v 1.1 2005/08/30 17:37:41 bluecherry Exp $
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
* VFS FILE class 
*/					 
class mFile 
{   
	/**
    * The VFS class defining the VFS the file actually resides in.
    *
    * @var VFS $fileVFS 
	*
	* @access PROTECTED
    */	 
	public $fileVFS = null; 
	
	
	/**
    * String containing file location in vfs $_VFS.
    *
    * @var STRING $vfs_path
	*
	* @access PROTECTED
    */	
	public $vfs_path = "";
	
	/**
    * String containing file location in the vfs, as end users would see it.
    *
    * @var STRING $path
	*
	* @access PROTECTED
    */	
	public $path = "";
	
	/**
    * String containing filename of the this file.
    *
    * @var STRING $filename 
	*
	* @access PROTECTED
    */	
	public $filename = "";
	
	/**
     * Constructor		  
     *
     * @access public
	 *
	 * @return BOOLEAN True if object was constructed correctly, false if not.
     */	
	public function isInit()
	{
	 	if ( (empty($this->realPath)) | (empty($this->path)) | (empty($this->filename)) | (!is_object($this->fileVFS)) | ($this->fileVFS==null) ) 
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
     * @param STRING $path  Path to this file in the main vfs.
     * @param STRING $vfs_path  Path to this file in vfs $vfs.
	 * @param STRING $name	Filename of this file.	
	 * @param VFS $vfs	Vfs object to set for this file.	
	 *
	 * @return BOOLEAN True if succesfull, false if not.
     */
	 public function __construct($path, $vfs_path, $filename, $vfs)
	 {
	 	/* Pre-conditions */
		if ( (empty($path)) | (empty($vfs_path)) | (empty($filename)) | (!is_object($vfs)) | ($vfs==null) ) 
		{
		 	return false;
		}	
		
		/* Store information into object */	 
		$this->path = $path; 
	 	$this->vfs_path = $vfs_path;
		$this->filename = $filename;
		$this->fileVFS = $vfs; 
		
		/* Post-conditions */
		return $this->isInit();
	 
	 }					
	
}
?>
