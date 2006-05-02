<?php
/**
* @version $Id: templates.class.php,v 1.2 2005/08/27 18:13:22 pasamio Exp $
* @package Mambo
* @subpackage Templates
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * Template factory class
 * @package Mambo
 * @subpackage Templates
 */
class mosTemplateFactory {
	/**
	 * @return object A template installer object
	 */
	function &createInstaller() {
                mosFS::load( '#mambo.installers' );
		return mosInstallerFactory::createClass('template');
	}
}

/**
 * @package Mambo
 * @subpackage Templates
 */
class mosTemplate {
	/**
	 * @param int The client number
	 * @param boolean Add trailing slash to path
	 */
	function getBasePath( $client, $addTrailingSlash=true ) {
		global $mosConfig_absolute_path;

		switch ($client) {
			case '1':
				$dir =  '/administrator/templates';
				break;
			default:
				$dir = '/templates';
				break;
		}

		return mosFS::getNativePath( $mosConfig_absolute_path . $dir, $addTrailingSlash );
	}
}

/**
 * Class mosTemplates_menu
 * @package Mambo
 * @subpackage Templates
 */
class mosTemplatesMenu extends mosDBTable {
	/** @var string The template name */
	var $template;
	/** @var int The menu id (foreign key) */
	var $menuid;
	/** @var int The client identifier */
	var $client_id;

	/**
	 * Constructor
	 */
	function mosTemplatesMenu( &$db ) {
		$this->mosDBTable( '#__templates_menu', '', $db );
	}

	/**
	 * Gets the current template
	 * @param int The client id
	 * @param int The menu id
	 */
	function getCurrent( $client=null, $menuid=null ) {
		if (is_null( $client )) {
			$client = $this->client_id;
		}
		if (is_null( $menuid )) {
			$menuid = $this->menuid;
		}

		$query = '
			SELECT template
			FROM ' . $this->_tbl . '
			WHERE client_id=' . $this->_db->Quote( $client ) . ' AND menuid=' . $this->_db->Quote( $menuid );
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}

	/**
	 * Gets an array of menu ids associated with the template
	 */
	function getMenus( $client=null, $template=null ) {
		if (is_null( $client )) {
			$client = $this->client_id;
		}
		if (is_null( $template )) {
			$template = $this->template;
		}

		$query = '
			SELECT menuid
			FROM ' . $this->_tbl . '
			WHERE client_id=' . $this->_db->Quote( $client ) . ' AND template=' . $this->_db->Quote( $template );
		$this->_db->setQuery( $query );

		return $this->_db->loadAssocList();
	}

	/**
	 * Set the default template
	 */
	function setDefault() {
		if (trim( $this->template )) {
			$query = 'DELETE FROM #__templates_menu' .
					' WHERE client_id='.$this->_db->Quote( $this->client_id ) .' AND menuid=' . $this->_db->Quote( '0' );
			$this->_db->setQuery( $query );
			$this->_db->query();

			$query = 'INSERT INTO #__templates_menu' .
					' SET client_id='.$this->_db->Quote( $this->client_id ) .',' .
					' template='.$this->_db->Quote( $this->template ) .',' .
					' menuid=' . $this->_db->Quote( '0' );
			$this->_db->setQuery( $query );
			$this->_db->query();
		}
	}

}

/**
 * @package Mambo
 * @subpackage Templates
 */
class mosTemplatePosition extends mosDBTable {
	/** @var int Primary key */
	var $id=null;
	/** @var string The position name */
	var $position=null;
	/** @var string An optional description (used in lists) */
	var $description=null;

	/** Constructor */
	function mosTemplatePosition( &$db ) {
		$this->mosDBTable( '#__template_positions', 'id', $db );
	}

	/**
	 * Select records
	 * @param string The name of the view
	 * @return array A list of selected records
	 */
	function select( $view='' ) {
		switch ($view) {
			default:
				$query = "SELECT * FROM #__template_positions ORDER BY ID";
				break;
		}
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	/**
	 * Clears the table
	 * @return boolean
	 */
	function clear() {
		$query = 'DELETE FROM #__template_positions';
		$this->_db->setQuery( $query );
		return $this->_db->query();
	}

	/**
	 * Inserts a data row
	 */
	function insert() {
		return $this->_db->insertObject( $this->_tbl, $this );
	}
}
?>
