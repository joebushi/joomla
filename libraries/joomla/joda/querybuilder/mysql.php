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
 * JQueryMySQL class, a Query Builder Tool for MySQL
 *
 * @package 	Joomla.Framework
 *
 */
class JQueryBuilderMysql extends JQueryBuilder
{


	/**
    * Data Type Mapping
	* @var array
	*/
	protected $_typeCastMapping = array(
        Joda::TYPE_CHAR         => 'CHAR',
        Joda::TYPE_VARCHAR      => 'VARCHAR',
        Joda::TYPE_TEXT         => 'TEXT',
        Joda::TYPE_BIGINT       => 'BIGINT',
        Joda::TYPE_BOOLEAN      => 'BOOLEAN',
		Joda::TYPE_DOUBLE       => 'DOUBLE'
        );


   	/**
     * DEFAULT string for default values in INSERT and UPDATE
     *
     * @var string
     */
	protected $_sDefault = 'DEFAULT';



    /**
     * Name Quote starting char
     *
     * @var string
     */
    protected $_name_quote_begin = "`";

    /**
     * Name Quote ending char
     *
     * @var string
     */
    protected $_name_quote_end = "`";

    /**
     * Quoting character for text literals: BEGIN
     *
     * @var character
     */
    protected $_quote_text_begin = "'";
    /**
     * Quoting character for text literals: END
     *
     * @var character
     */
    protected $_quote_text_end = "'";




	/******************************************************************/
	/******************** PROTECTED METHODS ***************************/
	/******************************************************************/



	/**
	 * Initialize sections
	 *
	 */
	protected function _initSections()
    {
		// Init Common && Standard sections
		parent::_initSections();

		// Init Database Specific Sections
	}



	/******************************************************************/
	/******************** PUBLIC METHODS ***************************/
	/******************************************************************/




	/**
	 * Generate a String Concatenation SQL code
	 *
	 * @access	public
	 * @param	array  Array of expressions to concatenate
	 * @return 	SQL piece of code
	 */
	function sqlConcat( $expressions )
    {
		if ( is_array($expressions) ) {
			$result = 'CONCAT('.implode( ', ', $expressions ).')';
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
	function sqlJoin( $join = null )
    {
		$result = "CROSS JOIN";
		if ( $join == "INNER" )
			$result = 'INNER JOIN';
		if ( $join == "JOIN" )
			$result = 'CROSS JOIN';
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
	function sqlLimit( $limit, $offset = 0 )
    {
		$result = "";

		if ( ($limit > 0) || ($offset > 0) ) {
			if ($offset > 0) $result = ' LIMIT '.$offset.', '.$limit;
			else $result = ' LIMIT '.$limit;
		}
		return $result;
	}


	/**
	 * @see JQuery.from
	 */
	function from( $input )
    {

		// Call perent's method
		parent::from($input);

		// MySQL does not allow aliasing table in DELETE queries
		// Generic from() methods does accept!
		if ( $this->_type == self::QT_DELETE ) {
            for( $i=0; $i < count($this->_sections[self::QS_FROM]);$i++ ){
				$this->_sections[self::QS_FROM][$i]["alias"] = "";
			}
		}


		return $this;
	}



}



?>
