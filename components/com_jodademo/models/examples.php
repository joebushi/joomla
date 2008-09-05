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

    function Test1() { // Just building a simple query
    	$result = "";
        $dataset = JFactory::getDBSet();
        $qb = $dataset->getQueryBuilder();
        $qb
            ->select(array("f1"=>"field1", "f2" => "'#__plamen'"))
            ->from(array("c" => "#__content", "d" => "'#__content'"))
            ->join("#__categories", "c.catid=cats.id", "cats")
            ->orderby(array("c" => Joda::SORT_DESC, "title"))
            ;


        $result = $qb->getSQL();


        $test = "DDD; DDdd; dddddddddd; ddddddd;; dddd11'11XXX\nXXX11'111ddDDDDD";
        $result = "";
        $sqls = $qb->splitSQL($test);
        foreach ( $sqls as $sql ) {
        	$result .= "\n" . $sql;
        }


        $text["explain"] = "Build a query";
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


    function noTest3() {
        $result = "";

        $dataset = JFactory::getDBSet();
        $qb = $dataset->getQueryBuilder();

        $result = "";
        //$result = $qb->replaceString('select " plamen " #__plam"en " ""#__""test from me', "#__", "jos_");

        $s = "SELECT '#__fi\'eld0', '#__field0', `field1`, \"field2\", 'field3\"plus\"' FROM #__TABLE";

        $q = "'";
        $qq = '"';

        $pattern = '/(['.$q.$qq.'])(?:\\\\\1|[\S\s])*?\1/';



        $matches = array();
        $b = preg_match_all($pattern, $s, $matches, PREG_OFFSET_CAPTURE );

        $result = $s . "\n" . $pattern."\n\n";
        $sn = $s;
        $sn_result = "";
        if ($b) {
            $result .= "Result:\n\n";
            if ( is_array($matches) ) {
                $full = $matches[0];
                $subs_count = count($matches) - 1;

                $result .= "Full:\n";
                $i=1;
                $shift = 0;
                foreach ($full as $match) {
                    $result .=  $match[1] . ": " . $match[0] . "\n";
                    $replacement = "XXXXXXXXXXXXXX$i";
                    $replacements[$replacement] = $match[0];
                    $sn = substr_replace($sn, $replacement, $match[1]+$shift, strlen($match[0]));
                    $shift = $shift + strlen($replacement) - strlen($match[0]);
                    $i++;
                }
                if ( $subs_count > 0 ) {
                    $result .= "\n\nSub patterns:\n";
                    for ($i = 1; $i <= $subs_count; $i++ ) {
                        $submatches = $matches[$i];
                        $result .= "\n$i:\n";
                        foreach ( $submatches as $match ){
                            $result .= $match[1] . ": " . $match[0] . "\n";
                        }
                    }
                }
            }
        }

        $text["explain"] = "REGEX";
        $text["result"] = $result . "\n\n" . $sn;
        return $text;
    }




    function Test4()
    {
        $result = "";

    	$text["explain"] = "Replace String/Prefix";
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