<?php
/**
* @version $Id: version.php 209 2005-09-14 05:17:41Z eddieajau $
* @package Joomla
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software and parts of it may contain or be derived from the
* GNU General Public License or other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

/**
 * Version information
 * @package Joomla
 */
class joomlaVersion {
	/** @var string Product */
	var $PRODUCT 	= 'Joomla!';
	/** @var int Main Release Level */
	var $RELEASE 	= '1.0';
	/** @var string Development Status */
	var $DEV_STATUS = 'RC5';
	/** @var int Sub Release Level */
	var $DEV_LEVEL 	= '0';
	/** @var int build Number */
	var $BUILD	 	= '$Revision: 209 $';
	/** @var string Codename */
	var $CODENAME 	= 'Sunrise';
	/** @var string Date */
	var $RELDATE 	= '14-Sep-2005';
	/** @var string Time */
	var $RELTIME 	= '00:00';
	/** @var string Timezone */
	var $RELTZ 		= 'GMT';
	/** @var string Copyright Text */
	var $COPYRIGHT 	= 'Copyright (C) 2005 Open Source Matters. All rights reserved.';
	/** @var string URL */
	var $URL 		= '<a href="http://www.joomla.org">Joomla!</a> is Free Software released under the GNU/GPL License.';

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
		return str_replace( '.', '', $this->RELEASE );
	}
}
$_VERSION = new joomlaVersion();

$version = $_VERSION->PRODUCT .' '. $_VERSION->RELEASE .'.'. $_VERSION->DEV_LEVEL .' '
. $_VERSION->DEV_STATUS
.' [ '.$_VERSION->CODENAME .' ] '. $_VERSION->RELDATE .' '
. $_VERSION->RELTIME .' '. $_VERSION->RELTZ;
?>