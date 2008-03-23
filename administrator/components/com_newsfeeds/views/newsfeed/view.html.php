<?php
/**
* @version		$Id: $
* @package		Joomla
* @subpackage	Newsfeeds
* @copyright	Copyright (C) 2005 - 2007 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Newsfeeds component
 *
 * @static
 * @package		Joomla
 * @subpackage	Newsfeeds
 * @since 1.0
 */
class NewsfeedsViewNewsfeed extends JView
{
	function display($tpl = null)
	{
		$db 		=& JFactory::getDBO();
		$user 		=& JFactory::getUser();
	
		$catid 		= JRequest::getVar( 'catid', 0, '', 'int' );
		$cid 		= JRequest::getVar( 'cid', array(0), '', 'array' );
		$option 	= JRequest::getCmd( 'option' );
		$model		=& $this->getModel();
		JArrayHelper::toInteger($cid, array(0));
	
		$newsfeed	=& $this->get('data');
		$isNew		= ($newsfeed->id < 1);

		// fail if checked out not by 'me'
		if ($model->isCheckedOut( $user->get('id') )) {
			$msg = JText::sprintf( 'DESCBEINGEDITTED', JText::_( 'The newsfeed' ), $newsfeed->name );
			$mainframe->redirect( 'index.php?option='. $option, $msg );
		}

		if (!$isNew) {
			// do stuff for existing records
			$model->checkout( $user->get('id') );
		} else {
			// do stuff for new records
			$newsfeed->ordering 		= 0;
			$newsfeed->numarticles 	= 5;
			$newsfeed->cache_time 	= 3600;
			$newsfeed->published 	= 1;
			$newsfeed->catid 	= JRequest::getVar( 'catid', 0, 'post', 'int' );
		}
	
		// build the html select list for ordering
		$query = 'SELECT a.ordering AS value, a.name AS text'
			. ' FROM #__newsfeeds AS a'
			. ' WHERE catid = ' . (int) $newsfeed->catid
			. ' ORDER BY a.ordering'
		;
	
		if(!$isNew)
			$lists['ordering'] 			= JHTML::_('list.specificordering',  $newsfeed, $cid[0], $query, 1 );
		else
			$lists['ordering'] 			= JHTML::_('list.specificordering',  $newsfeed, '', $query, 1 );
	
		// build list of categories
		$lists['category'] 			= JHTML::_('list.category',  'catid', $option, intval( $newsfeed->catid ) );
		// build the html select list
		$lists['published'] 		= JHTML::_('select.booleanlist',  'published', 'class="inputbox"', $newsfeed->published );
	
		$this->assignRef('user',		$user);
		$this->assignRef('lists',		$lists);
		$this->assignRef('newsfeed',	$newsfeed);

		parent::display($tpl);
	}
}