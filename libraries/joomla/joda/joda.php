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
 * Ideas for types (taken from PDO::PARAM_* constants):
 *
 * Null
 * Boolean
 * Integer
 * String
 * VarString
 * Blob
 *
 *
 * Antony:
 * int, decimal, float, char, varchar, text, binary, blob, enum
 *
 * Other:
 * Boolean
 *
 */



/**
 * Joda Class
 *
 * @package     Joomla.Framework
 * @subpackage  Joda
 * @author      Plamen Petkov <plamendp@zetcom.bg>
 *
 * @todo Use PDO::ATTR_STRINGIFY_FETCHES: Convert numeric values to strings when fetching
 * @todo Try/Catch for transaction related issues and queries and all.. :-)
 * @todo Prepared statements? Optional? Parameters?
 * @todo Parameters in doQuery()/Query()
 * @todo Use quoting from PDO in Querybuilder
 * @todo For metadata - select * from table where 1=1  (?!?!)
 * @todo Table prefix!!
 * @todo Make QueryBuilder returning array of strings, not A string
 * @todo Where to put the prefix? Dataset or Connection... is it specific to dataset or connection
 * @todo protected class propoerties - underscore
 * @todo Prevent idle connections in beginTransaction() /inTransaction check/
 */
class Joda extends JObject
{

    /**
     * General NONE constant, N/A, Not defined, etc.
     */
     const NONE = 1;

    /**
     * Sort in Ascending order
     */
    const SORT_ASC  = 201;

    /**
     * Sort in Descending order
     */
    const SORT_DESC = 202;

    /**
     * CHAR data type, fixed length
     */
    const TYPE_CHAR                = 1001;

    /**
     * VARCHAR data type, variable length, up to 256
     */
    const TYPE_VARCHAR             = 1002;

    /**
     * TEXT data type, variable length, long text
     */
    const TYPE_TEXT                = 1003;

    /**
     * BLOB (LOB) Large Objects, binary or text
     */
    const TYPE_BLOB                = 1004;

    /**
     * BYTE type, single byte: 0..255 (-127..+127)
     */
    const TYPE_BYTE                = 1005;

    /**
     * Integer type, small int, (-32786..+32767) or (0..65535)
     */
    const TYPE_SMALLINT            = 1006;

    /**
     * Integer type, big integers
     */
    const TYPE_BIGINT              = 1007;

    /**
     * Real type, float/double
     */
    const TYPE_DOUBLE              = 1008;

    /**
     * Boolean type, true or false
     */
    const TYPE_BOOLEAN             = 1009;

    /**
     * Transaction isolation level: READ COMMITED
     */
    const READ_COMMITED            = 301;

    /**
     * Transaction isolation level: REPEATABLE READ
     */
    const REPEATABLE_READ          = 302;

    /**
     * Transaction isolation level: READ UNCOMMITTED (dirty read)
     */
    const READ_UNCOMMITTED         = 303;

    /**
     * Transaction isolation level: SERIALIZABLE, the higher one
     */
    const SERIALIZABLE             = 304;

    /**
     * Retreive data in tabular format (fields and rows)
     */
    const DATA_ASTABLE                    = 401;

    /**
     * Retreive data as an array of anonymous objects
     *
     * @var integer
     */
    const DATA_ASOBJECTS                  = 402;



} //Joda

?>
