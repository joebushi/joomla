<?php
/**
 * @version     $Id$
 *
 * @package     Joomla.Framework
 * @subpackage  Joda
 *
 * @copyright    Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license        GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 *
 */

/**
 * Check to ensure this file is within the rest of the framework
 */
defined( 'JPATH_BASE' ) or die();


/**
 * JQueryBuilderPgSQL class, a Query Builder Tool for PostgreSQL
 *
 * @package 	Joomla.Framework
 * @since		None
 *
 */
class JQueryBuilderPgsql extends JQueryBuilder {


	/**
	* @var array Data Type mapping
	*/
	protected $_typeCastMapping = array(
		'integer' => 'BIGINT',
		'string' => 'VARCHAR',
		'float' => 'REAL' );

	/** @var string DEFAULT string for default values in INSERT and UPDATE */
	protected $_sDefault = 'DEFAULT';

    /**    @var string Name Quote starting char */
    protected $_name_quote_begin = ''';

    /**    @var string Name Quote ending char */
    protected $_name_quote_end = ''';

    /** @var Quoting character for text literals */
    protected $_quote_text = '"';

	/** @var Array List of SQL reserved keywords */
	protected $_reservedKeywords = array(
		'select',
		'update',
		'delete'
	);



	/******************************************************************/
	/******************** PROTECTED METHODS ***************************/
	/******************************************************************/

	/**
	 * Build SQL piece of code: 'UPDATE table AS alias'
	 *
	 * @param string Section name
	 */
	protected function _qs_update_toString( $section ) {
		$input = $this->_sections[$section];
		$tmp = '';
		$table = trim( $input["table"] );
		$alias = trim( $input["alias"] );
		if ( ! empty($table) ) {
			$tmp = $table;
			if ( ! empty($alias) ) {
				$tmp .= ' AS '.$alias;
			}
		}
		$this->_sectionsSQL[$section] = $tmp;
		return true;
	}


	/**
	 * Initialize sections
	 *
	 */
	function _initSections() {
		// Init Common && Standard sections
		parent::_initSections();


		// Init Database Specific Sections

	}



	/******************************************************************/
	/******************** PUBLIC METHODS ******************************/
	/******************************************************************/



	/**
	 * Generate a SUB-select item
	 *
	 * @access	public
	 * @param	string  The Sub-Select
	 * @return 	SQL piece of code
	 *
	 */
	function sqlSubselect( $select, $subselectname = null ) {
		$result = parent::sqlSubselect( $select, $subselectname = null );
		return $result;
	}


	/**
	 * Generate a String Concatenation SQL code
	 *
	 * @access	public
	 * @param	array  Array of expressions to concatenate
	 * @return 	SQL piece of code
	 */
	function sqlConcat( $expressions ) {
		if ( is_array($expressions) ) {
			$result = implode( ' || ', $expressions );
		} else {
			$result = $expressions;
		}
		return $result;
	}


	/**
	 * Generate a JOIN type
	 *
	 * @access	public
	 * @param	string
	 * @return 	SQL piece of code
	 */
	function sqlJoin( $join = null ) {
		$result = "INNER JOIN";
		if ( $join == "INNER" )
			$result = 'INNER JOIN';
		if ( $join == "JOIN" )
			$result = 'INNER JOIN';
		if ( $join == "LEFT" )
			$result = 'LEFT JOIN';
		if ( $join == "RIGHT" )
			$result = 'RIGHT JOIN';

		return $result;
	}


	/**
	 * Generate a LIMIT SQL piece of code
	 *
	 * @access	public
	 * @param	integer  Limit
	 * @param	integer  (optional) Offset
	 * @return 	SQL piece of code
	 */
	function sqlLimit( $limit, $offset = 0 ) {
		$result = "";
		if ( ($limit > 0) || ($offset > 0) ) {
			if ( $offset > 0 ) {
                $result = ' LIMIT '.$limit.' OFFSET '.$offset;
            }
			else {
                $result = ' LIMIT '.$limit;
            }
		}
		return $result;
	}



	/**
	 *  Set UPDATE section for UPDATE query type
	 *
	 *  @param string   Table name
	 *  @param string   Table alias
	 */
	function update( $table, $alias = '' ) {
		$this->resetQuery();
		$this->_type = self::QT_UPDATE;
		$this->_sections[self::QS_UPDATE]["table"] = $table;
		$this->_sections[self::QS_UPDATE]["alias"] = $alias;
		return $this;
	}




}



?>