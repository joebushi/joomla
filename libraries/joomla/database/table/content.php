<?php
/**
* @version		$Id$
* @package		Joomla.Framework
* @subpackage	Table
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();


/**
 * Content table
 *
 * @package 	Joomla.Framework
 * @subpackage		Table
 * @since	1.0
 */
class JTableContent extends JTable
{
	/** @var int Primary key */
	public $id					= null;
	/** @var string */
	public $title				= null;
	/** @var string */
	public $alias				= null;
	/** @var string */
	public $title_alias			= null;
	/** @var string */
	public $introtext			= null;
	/** @var string */
	public $fulltext			= null;
	/** @var int */
	public $state				= null;
	/** @var int The id of the category section*/
	public $sectionid			= null;
	/** @var int DEPRECATED */
	public $mask				= null;
	/** @var int */
	public $catid				= null;
	/** @var datetime */
	public $created				= null;
	/** @var int User id*/
	public $created_by			= null;
	/** @var string An alias for the author*/
	public $created_by_alias		= null;
	/** @var datetime */
	public $modified			= null;
	/** @var int User id*/
	public $modified_by			= null;
	/** @var boolean */
	public $checked_out			= 0;
	/** @var time */
	public $checked_out_time		= 0;
	/** @var datetime */
	public $frontpage_up		= null;
	/** @var datetime */
	public $frontpage_down		= null;
	/** @var datetime */
	public $publish_up			= null;
	/** @var datetime */
	public $publish_down		= null;
	/** @var string */
	public $images				= null;
	/** @var string */
	public $urls				= null;
	/** @var string */
	public $attribs				= null;
	/** @var int */
	public $version				= null;
	/** @var int */
	public $parentid			= null;
	/** @var int */
	public $ordering			= null;
	/** @var string */
	public $metakey				= null;
	/** @var string */
	public $metadesc			= null;
	/** @var string */
	public $metadata			= null;
	/** @var int */
	public $access				= null;
	/** @var int */
	public $hits				= null;

	/**
	* @param database A database connector object
	*/
	protected function __construct( &$db ) {
		parent::__construct( '#__content', 'id', $db );
	}

	/**
	 * Overloaded check function
	 *
	 * @access public
	 * @return boolean
	 * @see JTable::check
	 * @since 1.5
	 */
	public function check()
	{
		/*
		TODO: This filter is too rigorous,need to implement more configurable solution
		// specific filters
		$filter = & JFilterInput::getInstance( null, null, 1, 1 );
		$this->introtext = trim( $filter->clean( $this->introtext ) );
		$this->fulltext =  trim( $filter->clean( $this->fulltext ) );
		*/


		if(empty($this->title)) {
			$this->setError(JText::_('Article must have a title'));
			return false;
		}

		if(empty($this->alias)) {
			$this->alias = $this->title;
		}
		$this->alias = JFilterOutput::stringURLSafe($this->alias);

		if(trim(str_replace('-','',$this->alias)) == '') {
			$datenow =& JFactory::getDate();
			$this->alias = $datenow->toFormat("%Y-%m-%d-%H-%M-%S");
		}

		if (trim( str_replace( '&nbsp;', '', $this->fulltext ) ) == '') {
			$this->fulltext = '';
		}

		if(empty($this->introtext) && empty($this->fulltext)) {
			$this->setError(JText::_('Article must have some text'));
			return false;
		}

		return true;
	}

	/**
	* Converts record to XML
	* @param boolean Map foreign keys to text values
	*/
	public function toXML( $mapKeysToText=false )
	{
		$db =& JFactory::getDBO();

		if ($mapKeysToText) {
			$query = 'SELECT name'
			. ' FROM #__sections'
			. ' WHERE id = '. (int) $this->sectionid
			;
			$db->setQuery( $query );
			$this->sectionid = $db->loadResult();

			$query = 'SELECT name'
			. ' FROM #__categories'
			. ' WHERE id = '. (int) $this->catid
			;
			$db->setQuery( $query );
			$this->catid = $db->loadResult();

			$query = 'SELECT name'
			. ' FROM #__users'
			. ' WHERE id = ' . (int) $this->created_by
			;
			$db->setQuery( $query );
			$this->created_by = $db->loadResult();
		}

		return parent::toXML( $mapKeysToText );
	}
}
