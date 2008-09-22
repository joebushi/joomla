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

        $ds = JFactory::getDBSet();
        $ds2 = JFactory::getDBSet("mysql");


        $qb = JFactory::getQueryBuilder("mysql");
        $qb->select("there is no quoted here 'but this \'is quoted'")->from('no name quoting but "here is the name"');
        $result = $qb->getSQL();
        $result = $qb->reQUoteNames($result);
        

        $text["result"] = $result;
        return $text;
    }



    function Test2() { // Just building a simple query
    	$result = "";

    	// Create a brand new query builder
    	$qb = JFactory::getQueryBuilder("mysql");

    	// Create a SELECT query statement, fill sections
    	$qb
            ->select( array("f1" => "field1", "f2" => "field2") )
		    ->distinct()
		    ->from( array("t1" => "table1" ) )
		    ->join("table2", "t1.f1=t2.f1", "t2");

        // Get the query
		$select = $qb->getSQL();

		// Reset query builder
		$qb->resetQuery();

		// Create an UPDATE query statement, fill sections
		$qb
            ->update("table")
            ->fieldvalues(array(
                    $qb->quoteID("f1") => $qb->quote('v1'),
                    "f2" => $qb->quote('v2'),
                    "pi" => 3.14
                    ))
            ->where("f2=id");

        // Get the query
        $update = $qb->getSQL();

        // Reset query builder
        $qb->resetQuery();

        // Create an INSERT query statement, fill sections
        $qb
            ->insertinto("table")
            ->fields(array("f1", "f2", "f3"))
            ->values(array("v1", "v2", $qb->quote("v3")));

        // Get the query
        $insert = $qb->getSQL();


        // Reset query builder
        $qb->resetQuery();

        // Create DELETE query statement, fill sections
        $qb
            ->delete()
            ->from("table")
            ->where("f1=" . $qb->quote("test"));

        // Get the query
        $delete = $qb->getSQL();


        //MORE
        $subselect = $qb->subselect($select, "mysubname")->getSQL();

        $qb->resetQuery();
        $qb->select("*")
        ->from($subselect);

        $another = $qb->getSQL();


        $result .= "$select \n\n\n $update \n\n\n $insert \n\n\n $delete\n\n\n $subselect \n\n\n $another\n";

        $text["title"] = "JQueryBuilder";
        $text["intro"] = "";

        $text["code"]  = '
        // Create a brand new query builder
        $qb = JFactory::getQueryBuilder("mysql");

        // Create a SELECT query statement, fill sections
        $qb
            ->select( array("f1" => "field1", "f2" => "field2") )
            ->distinct()
            ->from( array("t1" => "table1" ) )
            ->join("table2", "t1.f1=t2.f1", "t2");

        // Get the query
        $select = $qb->getSQL();

        // Reset query builder
        $qb->resetQuery();

        // Create an UPDATE query statement, fill sections
        $qb
            ->update("table")
            ->fieldvalues(array(
                    $qb->quoteID("f1") => $qb->quote(\'v1\'),
                    "f2" => $qb->quote(\'v2\'),
                    "pi" => 3.14
                    ))
            ->where("f2=id");

        // Get the query
        $update = $qb->getSQL();

        // Create an INSERT query statement, fill sections
        $qb
            ->insertinto("table")
            ->fields(array("f1", "f2", "f3"))
            ->values(array("v1", "v2", $qb->quote("v3")));

        // Get the query
        $insert = $qb->getSQL();

        // Reset query builder
        $qb->resetQuery();

        // Create DELETE query statement, fill sections
        $qb
            ->delete()
            ->from("table")
            ->where("f1=" . $qb->quote("test"));

        // Get the query
        $delete = $qb->getSQL();


        //MORE
        $subselect = $qb->subselect($select, "mysubname")->getSQL();

        $qb->resetQuery();
        $qb->select("*")
        ->from($subselect);

        $another = $qb->getSQL();

        $result .= "$select \n\n\n $update \n\n\n $insert \n\n\n $delete\n\n\n $subselect \n\n\n $another\n";

        echo $result;
';

        $text["intro"] .= "\n";

        $text["result"] = $result . "\n\n";
        return $text;
    }






    function Test3() {  //  Getting data from DB
        $result = "";


        $text["title"] = "JDataset";
        $text["intro"] = "";


        $ds_def = JFactory::getDBSet();
        $ds_mysql = JFactory::getDBSet("mysql");

        $sql = "SELECT name FROM #__menu";
        $ds_def->setSQL($sql);
        $ds_def->open();
        $data = $ds_def->fetchAll();

        foreach ($data as $row) {
        	$result .= "\n" . $row["name"];
        }

        $text["code"] = '
        $ds_def = JFactory::getDBSet();
        // Use named connection (mysql)
        $ds_mysql = JFactory::getDBSet("mysql");

        $sql = "SELECT name FROM #__menu";
        $ds_def->setSQL($sql);
        $ds_def->open();
        $data = $ds_def->fetchAll();

        foreach ($data as $row) {
            $result .= "\n" . $row["name"];
        }

        ';


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