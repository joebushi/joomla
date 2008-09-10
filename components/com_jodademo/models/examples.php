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


    function Test1() { // Just building a simple query
    	$result = "";
        $qb = JFactory::getQueryBuilder("mysql");

        $result = "Simple select:\n";
        $qb
            ->select("*")
            ->from(array("t" => "#__table"));

        $result .= $qb->getSQL();
        
        
        
        
        $text["title"] = "Using JQueryBuilder as a standalone query builder";
        $text["intro"] = "JQueryBuilder can be used standalone or be part of JDataset/JConnection/JQueryBuilder triple.\n";
        $text["intro"] .= "When used standalone a SQL dialect name must be passed to factoring method.\n";
        $text["code"] = '$qb = JFactory::getQueryBuilder("mysql");';
        
        
        
        $text["intro"] .= "\n \$qb = JFactory::getQueryBuilder('mysql')";
        $text["result"] = $result;
        return $text;
    }

    function Test2() {  //  Getting data from DB
        $result = "";
    	$dataset = JFactory::getDBSet();
        $qb = $dataset->getQueryBuilder();
        $qb->select(array("id","title"))->from("#__content");
        $dataset->addSQL($qb->getSQL());
        $dataset->open();
        $data = $dataset->fetchAll();
        $result = "";
        foreach ( $data as $row ) {
            $result .= $row["id"] . ":" . $row["title"] . "\n";
        }
        $text["title"] = "Getting data from database";
        $text["intro"] = "";
        $text["code"] = "";
        $text["result"] = $result;
        return $text;
    }









    function Test11()
    {
        $result = "";

        $db = JFactory::getDBSet();
        $qb = $db->getQueryBuilder();
        $sql = "SELECT 5 * 6 as c;";
        $db->setSQL($sql);
        $db->open();
        $data = $db->fetchAll();

        ob_start();
        var_export($data);
        $tmpstring = ob_get_contents();
        ob_end_clean();
        $result = $tmpstring;


        $text["title"] = "Escaping quoted SQL parts: ";
        $test["intro"] = "";
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
            	    $texts[$key]["intro"] = "Description";
            	}
            	if ( ! isset($texts[$key]["code"]) ) {
            	    $texts[$key]["code"] = "PHP testing code";
            	}
            	if ( ! isset($texts[$key]["result"]) ) {
            	    $texts[$key]["result"] = "TestResult";
            	}
            	$texts[$key]["title"] = "Test $i:\n" . $texts[$key]["title"];
            }
    	}
        return $texts;
    }

}