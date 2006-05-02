<?php
/**
 * Support functions for Mambot Manager
 * @version $Id: mambots.class.php,v 1.2 2005/08/27 15:12:41 pasamio Exp $
 * @package Mambo
 * @subpackage Mambots
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
class mosMambotFactory {
	/**
	 * @return object A template installer object
	 */
	function &createInstaller() {
		mosFS::load( '#mambo.installers' );
                return mosInstallerFactory::createClass('mambot');
	}
}

/**
 * @package Mambo
 * @subpackage Mambots 
 */
class mosMambotViews extends mosMambot {
	/**
	 * Retrieves a data view.
	 * @param string The view name
	 * @param array
	 * @param boolean
	 * @return mixed array of results or count
	 */
	function getView( $view, $options=array(), $countOnly=false ) {
		global $my;

		$wheres = array();

		if ( $folder = mosGetParam( $options, 'folder' ) ) {
			$wheres[] = "m.folder = '$folder'";
		}
		
		if ( $search = mosGetParam( $options, 'search' ) ) {
			$wheres[] = "LOWER( m.name ) LIKE '%$search%'";
		}

		if ( $filter_state = mosGetParam( $options, 'state' ) ) {
			$wheres[] = "m.published = '$filter_state'";
		}

		if ( $filter_access = mosGetParam( $options, 'access' ) ) {
			$wheres[] = "m.access = '$filter_access'";
		}

		switch ( $view ) {
			case 'items':
				$where = (count( $wheres ) > 0 ? "\n WHERE " . implode( ' AND ', $wheres ) : '');				

				if ( $countOnly ) {
					$query = "SELECT COUNT( id )"
					. "\n FROM #__mambots AS m "
					. $where
					;
					$this->_db->setQuery( $query );
					
					return $this->_db->loadResult();
				}

				$orderby = mosGetParam( $options, 'orderby' );

				if ( empty( $orderby ) ) {
					$orderby = 'm.ordering';
				} else {
					$orderby .= ', m.ordering';
				}

				$query = "SELECT m.*, u.name AS editor, g.name AS groupname"
				. "\n FROM #__mambots AS m"
				. "\n LEFT JOIN #__users AS u ON u.id = m.checked_out"
				. "\n LEFT JOIN #__groups AS g ON g.id = m.access"
				. $where
				. "\n GROUP BY m.id"
				. ( $orderby ? "\n ORDER BY " . $orderby : '' )
				;
				break;

			case 'folders':
				$query = "SELECT folder"
				. "\n FROM #__mambots"
				//. "\n WHERE client_id = '$client_id'"
				. "\n GROUP BY folder"
				. "\n ORDER BY folder"
				;
				break;

			default:
				break;
		}

		$limitstart = mosGetParam( $options, 'limitstart', 0 );
		$limit 		= mosGetParam( $options, 'limit', 0 );
		
		$this->_db->setQuery( $query, $limitstart, $limit );

		return $this->_db->loadObjectList();
	}
}
?>
