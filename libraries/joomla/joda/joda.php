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
     * @return array
     */
    static function default_connections($default=false) {
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


    /**
     * Replace quoted substrings with unique identifiers (including quotes!). 
     *
     * Analyzes the input string, discovers quoted substring(s) and 
     * returns structured data for further use (quoting issues)
     *
     * @param string The String to handle
     * @param string The literal quote 
     * @return array Array(	"string" => <string with quoted substrings replaced by unique IDs>, 
     * 						"uids"   => array("<UNIQUEID>" => <quoted literals incl. quotes>),
     * 						"literals" => array("<UNIQUEID>" => "<literals only>"))
     *
     */
    static function quotedToUID($input, $quote)
    {
        // No quoting characters? Ok, no transformation
        if ( trim($quote) == "" ) {
            return array("string" => $input, "uids" => array());
        }

        // Pattern to match quoted strings 
        //$pattern = '/(['.$quote.'])(?:\\\\\1|[\S\s])*?\1/m';    // saving quoted [0]
        $pattern = '/(['.$quote.'])((?:\\\\\1|[\S\s])*?)\1/m';    // saving quoted (all matches) [0] and literals [2]

        $matches = array();

        // Apply REGEX, keep matching offsets as well
        $found = preg_match_all($pattern, $input, $matches, PREG_OFFSET_CAPTURE );

        $newstring = $input;
        $uids = array();
        $literals = array();

        // Did we find any quoted string?
        if ($found) {
            // Array of all matches: array[0..N] of array[0..1] : N: count; 0: match substring, 1: offset of the match
            $allmatches = $matches[0]; // INDEX 0 is very important!!! See REGEX infos

            $shift = 0;
            $i = 0;
            foreach ($allmatches as $match) {
                // String to replace the match with - match_holder
                $uid = "[" . self::getUniqueString() . "]";

                // Keep the (uid => <quote>literal<quote>) pairs
                $uids[$uid] = $match[0];
                
                // Keep the (uid => literal) pairs  (see the pattern grouping)
                $literals[$uid] = $matches[2][$i][0];

                // Replace the match with the replacement string (uid);
                // $match[1] is the offset in the string
                $newstring = substr_replace($newstring, $uid, $match[1]+$shift, strlen($match[0]));

                // Take into an acoount the string lenght difference, if any, calculate the shift (+/-)
                $shift = $shift + strlen($uid) - strlen($match[0]);
                $i++;
            }
        }

        // Huh...
        return array("string" => $newstring, "uids" => $uids, "literals" => $literals);

    }



    /**
     * Generate Unique string
     * @return string
     */
    static function getUniqueString()
    {
        $better_token = md5(uniqid(rand(), true));
        $unique_code = substr($better_token, 16);
        $uniqueid = $unique_code;
        return $uniqueid;
    }


    /**
     * Replace substring that is NOT quoted by text literals quotes (',", etc)
     * e.g. "'this will not be replaced' this will be replaced"
     *
     * @param string The String
     * @param string String to search
     * @param string String replacement
     * @return string
     */
    static function replaceNonQuotedString($input, $search, $replace, $quote)
    {
        $result = Joda::quotedToUID($input, $quote);

        // get the array of ( <UNIQUEID> => <QUOTED-ORIGINAL-SUBSTRING>)
        $uids = $result["uids"];

        // get the string with NO quoted parts init (slated)
        $string = $result["string"];

        // Replace what we want to replace
        $newstring = str_replace($search, $replace, $string);

        // Ok, copy over back quoted strings (currently replaced by Unique IDs)
        reset($uids);
        foreach ($uids as $uid => $original) {
            $newstring = str_replace($uid, $original, $newstring);
        }

        return $newstring;
    }


    /**
     * Splits a string of queries into an array of individual queries
     * if separated by semicolon (;)
     *
     * @param	string Queries to split (; separated)
     * @param 	array  Array of possible text quoting characters
     * @param  	boolean Remove NEW LINES outside quotes?
     * @return  array
     *
     */
    function splitSql( $input, $text_quote, $remove_newlines=false )
    {
    	// Replace New Lines with ';'
        $input = preg_replace('/$/', ';', $input);

        // Hide quoted parts
        $result = Joda::quotedToUID($input, $text_quote);

        // get the array of ( <UNIQUEID> => <QUOTED-ORIGINAL-SUBSTRING>)
        $uids = $result["uids"];

        // get the string with NO quoted parts init (slated)
        $slatedstring = $result["string"];

        // Split into an array of strings, if any
        $sqls = preg_split('/;/', $slatedstring);

        $result = array();

        // Ok, copy-over back quoted strings
        // Enmerate SQL strings
        foreach ( $sqls as $sql ) {
            // Igfnore empty strings
        	$sql = trim($sql);
            if ( $remove_newlines == true ) {
                $sql = preg_replace('/\n/','',$sql);
            }
        	if ($sql !== "") {
                reset($uids);
                // Enumerate uids
                foreach ($uids as $uid => $original) {
                    $sql = str_replace($uid, $original, $sql);
                }
                $result[] = $sql;
            }
        }

        return $result;
    }

} //Joda



?>
