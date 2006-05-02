<?php
/**
* @version $Id: version.php,v 1.1 2005/08/25 14:21:09 johanjanssens Exp $
* @package Mambo
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/**
 * Version information
 * @package Mambo
 */
class mamboVersion {
	/** @var string Product */
	var $PRODUCT = 'Mambo';
	/** @var int Main Release Level */
	var $RELEASE = '4.5';
	/** @var string Development Status */
	var $DEV_STATUS = 'Dev';
	/** @var int Sub Release Level */
	var $DEV_LEVEL = '3';
	/** @var string Codename */
	var $CODENAME = 'Iapetus';
	/** @var string Date */
	var $RELDATE = 'TBA';
	/** @var string Time */
	var $RELTIME = '00:00';
	/** @var string Timezone */
	var $RELTZ = 'GMT';
	/** @var string Copyright Text */
	var $COPYRIGHT = 'Copyright 2000 - 2005 Miro International Pty Ltd.  All rights reserved.';
	/** @var string URL */
	var $URL = '<a href="http://www.mamboserver.com">Mambo</a> is Free Software released under the GNU/GPL License.';

	/**
	 * @return string Long format version
	 */
	function getLongVersion() {
		return $this->PRODUCT .' '. $this->RELEASE .'.'. $this->DEV_LEVEL .' '
			. $this->DEV_STATUS
			.' [ '.$this->CODENAME .' ] '. $this->RELDATE .' '
			. $this->RELTIME .' '. $this->RELTZ;
	}

	/**
	 * @return string Short version format
	 */
	function getShortVersion() {
		return $this->RELEASE .'.'. $this->DEV_LEVEL;
	}

	/**
	 * @return string Version suffix for help files
	 */
	function getHelpVersion() {
		return substr( str_replace( '.', '', $this->RELEASE .'.'. $this->DEV_LEVEL ), 0 , 3 );
	}

}
$_VERSION =& new mamboVersion();
?>