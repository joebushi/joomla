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
        $result = "XXX";
        $text["explain"] = "Array to string: ";
        $text["result"] = $result;
        return $text;
    }



    function Test1() { // Just building a simple query
    	$result = "";
        $dataset = JFactory::getDBSet();
        $qb = $dataset->getQueryBuilder();
        /*
        $qb
            ->select(array("f1"=>"field1", "f2" => "'#__plamen'"))
            ->from(array("c" => "#__content", "d" => "'#__content'"))
            ->join("#__categories", "c.catid=cats.id", "cats")
            ->orderby(array("c" => Joda::SORT_DESC, "title"))
            ;
*/
        $result = $qb->getSQL();


        $test = "
INSERT INTO `#__components`
VALUES (1, 'Banners', 'track_impressions=0\ntrack_clicks=0\ntag_prefix=\n\n', 0, 0, '', 'Banner Management', 'com_banners', 0, 'js/ThemeOffice/component.png', 0, 'track_impressions=0\ntrack_clicks=0\ntag_prefix=\n\n', 1);
INSERT INTO `#__components` VALUES (9, 'Categories', '', 0, 7, 'option=com_categories&section=com_contact_details', 'Manage contact categories', '', 2, 'js/ThemeOffice/categories.png', 1, 'contact_icons=0\nicon_address=\nicon_email=\nicon_telephone=\nicon_fax=\nicon_misc=\nshow_headings=1\nshow_position=1\nshow_email=0\nshow_telephone=1\nshow_mobile=1\nshow_fax=1\nbannedEmail=\nbannedSubject=\nbannedText=\nsession=1\ncustomReply=0\n\n', 1);
";

        $result = "";
        $sqls = $qb->splitSQL($test, true);
        $tick = 1;
        foreach ( $sqls as $sql ) {
            //$sql = preg_replace('/\n/','\\\\n',$sql);
        	$result .= "\n$tick : " . $sql;
        	$tick++;
        }


        $text["explain"] = "Split Query String by semicolon: ';'";
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
        $text["explain"] = "Getting data from database";
        $text["result"] = $result;
        return $text;
    }






    function Test4()
    {
        $result = "";

        $a = array("0" => array("name" => "default", "default" => "1", "host" => "localhost", "port" => "", "user" => "root", "password" => "Dimana87", "database" => "joomlajoda", "driver" => "mysql", "prefix" => "jos_", "debug" => "0"), "1" => array("name" => "", "default" => "0", "host" => "localhost", "port" => "", "user" => "", "password" => "", "database" => "", "driver" => "mysql", "prefix" => "jos_", "debug" => "0"), "2" => array("name" => "", "default" => "0", "host" => "localhost", "port" => "", "user" => "", "password" => "", "database" => "", "driver" => "mysql", "prefix" => "jos_", "debug" => "0"), "3" => array("name" => "", "default" => "0", "host" => "localhost", "port" => "", "user" => "", "password" => "", "database" => "", "driver" => "mysql", "prefix" => "jos_", "debug" => "0"), "4" => array("name" => "", "default" => "0", "host" => "localhost", "port" => "", "user" => "", "password" => "", "database" => "", "driver" => "mysql", "prefix" => "jos_", "debug" => "0"), "5" => array("name" => "", "default" => "0", "host" => "localhost", "port" => "", "user" => "", "password" => "", "database" => "", "driver" => "mysql", "prefix" => "jos_", "debug" => "0"), "6" => array("name" => "", "default" => "0", "host" => "localhost", "port" => "", "user" => "", "password" => "", "database" => "", "driver" => "mysql", "prefix" => "jos_", "debug" => "0"), "7" => array("name" => "", "default" => "0", "host" => "localhost", "port" => "", "user" => "", "password" => "", "database" => "", "driver" => "mysql", "prefix" => "jos_", "debug" => "0"), "8" => array("name" => "", "default" => "0", "host" => "localhost", "port" => "", "user" => "", "password" => "", "database" => "", "driver" => "mysql", "prefix" => "jos_", "debug" => "0"), "9" => array("name" => "", "default" => "0", "host" => "localhost", "port" => "", "user" => "", "password" => "", "database" => "", "driver" => "mysql", "prefix" => "jos_", "debug" => "0"));
        ob_start();
        var_export($a);
        $string = ob_get_contents();
        ob_end_clean();

        $string = preg_replace('/\n/','',$string);

        $result = $string;

    	$text["explain"] = "Array to string: ";
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


        $text["explain"] = "Escaping quoted SQL parts: ";
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
            	$texts[$key]["explain"] = "Test $i:\n" . $texts[$key]["explain"];
            }
    	}
        return $texts;
    }

}