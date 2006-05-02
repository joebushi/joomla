<?php
/**
* @version $Id: toolbar.initialise.php,v 1.1 2005/08/27 15:31:33 ratlaw101 Exp $
* @package $ambo
* @subpackage Initialise Manager
* @copyright (C) 2005 Richard Allinson www.ratlaw.co.uk
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* $ambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * Toolbar for Contents Manager
 * @package Mambo
 * @subpackage Content
 */
class initialiseToolbar extends mosAbstractTasker
{
	/**
	 * Constructor
	 */
	function initialiseToolbar()
	{
		// auto register public methods as tasks, set the default task
		parent::mosAbstractTasker( 'initialiser_manager' );
		
		$this->registerTask( 'new', 'addNew' );
		$this->registerTask( 'edit', 'addNew' );
		$this->registerTask( 'apply', 'addNew' );
	}
	
	function initialiser_manager()
	{
		mosMenuBar::title( 'Initialiser Manager', 'addedit.png' );
		mosMenuBar::startTable();
		mosMenuBar::publishList();
		mosMenuBar::unpublishList();
		mosMenuBar::deleteList();
		mosMenuBar::editList();
		mosMenuBar::addNew();
		mosMenuBar::help( 'screen.initialise.manager' );
		mosMenuBar::endTable();
	}
	
	function addNew()
	{
		mosMenuBar::title( 'Add Initialiser', 'addedit.png' );
		mosMenuBar::startTable();
		mosMenuBar::save();
		mosMenuBar::cancel();
		mosMenuBar::help( 'screen.initialise.manager' );
		mosMenuBar::endTable();
	}
}

$tasker =& new initialiseToolbar();
$tasker->performTask( mosGetParam( $_REQUEST, 'task', '' ) );
?>