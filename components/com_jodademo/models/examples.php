<?php
/**
 * @version     $Id: controller.php 10786 2008-08-24 06:55:34Z plamendp $
 * @package     Joomla
 * @subpackage  Jodademo
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );

/**
 * Examples Model
 *
 * @package     Joomla
 * @subpackage  Jodademo
 */
class JodademoModelExamples extends JModel
{


	/**
	 * Use this function as a starting point for your tests
	 *
	 * @return array
	 */
    function templateTest()
    {
        $result = "Test Result";
        $text["title"] = "Test Title";
        $text["intro"] = "Short Description";
        $text["code"] = "PHP testing Code";
        $text["result"] = $result;
        return $text;
    }


    function varToString($var)
    {
        ob_start();
        var_export($var);
        $string = ob_get_contents();
        ob_end_clean();
        return $string;
    }


    function Test1()
    {
        $result = "";
        $text["title"] = "P R I V A T E  T E S T";
        $text["intro"] = "Short Description";
        $text["code"] = "PHP testing Code";
        $text["result"] = $result;
        return $text;
    }



    function Test2() { // Just building a simple query
    	$result = "";
        $qb = JFactory::getQueryBuilder("mysql");

        $result = "";

        // SELECT
        $qb
		->select( array("f1" => "field1", "f2" => "field2") )
		->distinct()
		->from( array("t1" => "table1" ) )
		->join("table2", "t1.f1=t2.f1", "t2");

		$select = $qb->getSQL();

		// UPDATE
		$qb->resetQuery();
		$qb
            ->update("table")
            ->fieldvalues(array(
                    $qb->quoteID("f1") => $qb->quote('v1'),
                    "f2" => $qb->quote('v2')
                    ))
            ->where("f2=id");
        $update = $qb->getSQL();


        $result .= "$select \n\n\n $update\n";
        $text["title"] = "Using JQueryBuilder as a standalone query builder";
        $text["intro"] = "JQueryBuilder can be used as a standalone SQL query building tool or as part of JDataset/JConnection/JQueryBuilder team.\n";
        $text["intro"] .= "\nWhen used separately, an SQL dialect name must be passed to the factoring method. \n";
        $text["intro"] .= "\nOtherwise, the SQL dialect is implicitly derived from the connection. \n";
        $text["intro"] .= "\nSee example bellow to start understanding the basic usage. \n";

        $text["code"]  =
'// Create a query builder, always a new object
$qb = JFactory::getQueryBuilder("mysql");

// Lets make some query
$qb
->select( array("f1" => "field1", "f2" => "field2") )
->distinct()
->from( array("t1" => "table1" ) )
->join("table2", "t1.f1=t2.f1", "t2");
$select = $qb->getSQL();

$qb->resetQuery();
$qb
->update("table")
->fieldvalues(array("f1" => $qb->quote("v1")))
->where("f2=\'id\'");

echo $select;
echo $update;

';
        $text["intro"] .= "\n";

        $text["result"] = $result . "\n\n";
        return $text;
    }






    function Test3() {  //  Getting data from DB
        $result = "";
        $text["title"] = "SQL Statements that JQueryBuilder can build";
        $text["intro"] = "It must be noted that JQueryBuilder, as of this writing, does not isolate
SQL dialects on 100%! Users are expected to make some effort to follow some kind of 'pseudo' SQL standard or
maybe we should call it a convention of common SQL sense. That is:

* Use single quote for string literals and double quote for identifiers.
* Do not use SQL functions in expressions - a wrapping method should be available.
* Check supported SQL command syntax
* Do not expect JQueryBuilder to magically remove or correct SQL syntax errors you made!
* ... to be continued

Supported SQL commands are: SELECT, INSERT, UPDATE and DELETE, limited to the following syntax:

SELECT [DISTINCT] * | expression [ AS output_name ] [, ...]
[ FROM from_item [, ...] ]
[ WHERE condition ]
[ GROUP BY expression [, ...] ]
[ ORDER BY expression [ASC|DESC] ]
[ LIMIT-CLAUSE ]


UPDATE table
SET column=expression|DEFAULT [, ...]
[WHERE condition]

INSERT

DELET

";

        $text["code"] = "";
        $text["result"] = $result;
        return $text;
    }












    function Tests() {
    	$max_tests = 100;

        $texts = array();
    	for ($i=1; $i<=100; $i++) {
    		$method = "test" . "$i";
            if ( method_exists($this, $method) ) {
            	$key = "t$i";
            	$texts[$key] = $this->$method();
            	if ( ! isset($texts[$key]["title"]) ) {
            	    $texts[$key]["title"] = "Title";
            	}
            	if ( ! isset($texts[$key]["intro"]) ) {
            	    $texts[$key]["intro"] = "";
            	}
            	if ( ! isset($texts[$key]["code"]) ) {
            	    $texts[$key]["code"] = "";
            	}
            	if ( ! isset($texts[$key]["result"]) ) {
            	    $texts[$key]["result"] = "";
            	}
            	//$texts[$key]["title"] = "Test $i:\n" . $texts[$key]["title"];
            }
    	}
        return $texts;
    }

}