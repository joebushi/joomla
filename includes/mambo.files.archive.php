<?php
/**
 * @version $Id: mambo.files.archive.php,v 1.1 2005/08/25 14:21:09 johanjanssens Exp $
 * @package Mambo
 * @copyright (C) 2000 - 2005 Miro International Pty Ltd
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Mambo is Free Software
 */

// ensure this file is being included by a parent file
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * @package Mambo
 */

class mosArchiveFS extends mosFS {
	/**
	* Extracts an archive file to a directory
	* @return boolean True on success, False on error
	*/
	function extract( $sourceFile, $targetDir ) {
		global $_LANG;

		mosFS::check( $sourceFile );
		mosFS::check( $targetDir );

		if (eregi( '.zip$', $sourceFile )) {
			// Extract functions
			define( 'OS_WINDOWS', MOSFS_ISWIN );
			mosFS::load( '/administrator/includes/pcl/pclzip.lib.php' );
			mosFS::load( '/administrator/includes/pcl/pclerror.lib.php' );
			//require_once( $mosConfig_absolute_path . '/administrator/includes/pcl/pcltrace.lib.php' );
			//require_once( $mosConfig_absolute_path . '/administrator/includes/pcl/pcltar.lib.php' );
			$zipfile = new PclZip( $sourceFile );

			$ret = $zipfile->extract( PCLZIP_OPT_PATH, $targetDir );
			if ($ret == 0) {
				$this->setError( 1, $_LANG->_( 'Unrecoverable error' ).' "'.$zipfile->errorName(true).'"' );
				return false;
			}
		} else {
			mosFS::load( '/includes/Archive/Tar.php' );
			$archive =& new Archive_Tar( $sourceFile );
			$archive->setErrorHandling( PEAR_ERROR_PRINT );

			if (!$archive->extractModify( $targetDir, '' )) {
				$this->setError( 1, $_LANG->_( 'Extract Error' ) );
				return false;
			}
		}
		return true;
	}
}
?>