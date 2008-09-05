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
     * Name of the fallback connection
     *
     * @var string
     */
    const CONN_FALLBACK                  = 'fallback';



    /**
     * Default Prefix Placeholder to be replaced by Relation Prefix (e.g. #__)
     *
     * @var string
     */
    const DEFAULT_PREFIX                            = '#__';

    /**
     * Default Relation/Table Prefix (e.g. jos_)
     *
     * This is JConnection specific. See {@link JConnection}
     *
     * @var string
     */
    const DEFAULT_RELATION_PREFIX                   = 'jos_';

    /**
     * Max Named Connections Number
     *
     * Defines how many named connections could be defined as a configuration property.
     * (currently configuration.php)
     *
     * @var integer
     */
    const MAX_NAMED_CONNECTIONS                   = 10;

    /**
     * Return an array of dummy connections, some kind of defaults
     *
     * This is to normalize the connections configuration and to describe possible properties...
     * //TODO make it more elegant please
     *
     * @return array
     */
    static function dummy_connections($default=false) {
    	$tmp = array();
        for ( $i = 0; $i < self::MAX_NAMED_CONNECTIONS; $i++ ) {
            $tmp[] = array(
                           "name" => "",
                           "default" => 0,
                           "host" => "localhost",
                           "port" => "",
                           "user" => "" ,
                           "password" => "",
                           "database" => "",
                           "driver" => "mysql",
                           "prefix" => "jos_",
                           "debug" => 0
            );
        }
        $tmp[0]["default"] = 1;
        $tmp[0]["name"] = "default";

        if ($default) {
        	return $tmp[0];
        }
        else {
            return $tmp;
        }
    }



} //Joda

?>
