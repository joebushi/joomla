<?php
/**
 * @version		$Id$
 * @package	Joomla.Framework
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

/**
 * Version information
 *
 * @package	Joomla.Framework
 * @since	1.0
 */
class JVersion
{
	/** @public string Product */
	public $PRODUCT		= 'Joomla!';
	/** @public int Main Release Level */
	public $RELEASE		= '1.5';
	/** @public string Development Status */
	public $DEV_STATUS	= 'Production/Stable';
	/** @public int Sub Release Level */
	public $DEV_LEVEL	= '2';
	/** @public int build Number */
	public $BUILD		= '';
	/** @public string Codename */
	public $CODENAME	= 'Woi';
	/** @public string Date */
	public $RELDATE		= '22-March-2008';
	/** @public string Time */
	public $RELTIME		= '22:00';
	/** @public string Timezone */
	public $RELTZ		= 'GMT';
	/** @public string Copyright Text */
	public $COPYRIGHT	= 'Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.';
	/** @public string URL */
	public $URL		= '<a href="http://www.joomla.org">Joomla!</a> is Free Software released under the GNU General Public License.';

	/**
	 *
	 *
	 * @return string Long format version
	 */
	public function getLongVersion()
	{
		return $this->PRODUCT .' '. $this->RELEASE .'.'. $this->DEV_LEVEL .' '
			. $this->DEV_STATUS
			.' [ '.$this->CODENAME .' ] '. $this->RELDATE .' '
			. $this->RELTIME .' '. $this->RELTZ;
	}

	/**
	 *
	 *
	 * @return string Short version format
	 */
	public function getShortVersion() {
		return $this->RELEASE .'.'. $this->DEV_LEVEL;
	}

	/**
	 *
	 *
	 * @return string Version suffix for help files
	 */
	public function getHelpVersion()
	{
		if ($this->RELEASE > '1.0') {
			return '.' . str_replace( '.', '', $this->RELEASE );
		} else {
			return '';
		}
	}

	/**
	 * Compares two "A PHP standardized" version number against the current Joomla! version
	 *
	 * @return boolean
	 * @see http://www.php.net/version_compare
	 */
	public function isCompatible ( $minimum ) {
		return (version_compare( JVERSION, $minimum, 'eq' ) == 1);
	}
}
