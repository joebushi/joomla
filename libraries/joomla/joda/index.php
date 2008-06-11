<?php
/**
* @version        $Id$
* @package        Joomla-Framework
* @subpackage   Joda
* @copyright    Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license        GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*
 * @author      Plamen Petkov <plamendp@zetcom.bg>
*/



// Set flag that this is a parent file
define( '_JEXEC', 1 );
define( 'JPATH_BASE', "../../../" );
define( 'DS', DIRECTORY_SEPARATOR );

require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );




JDEBUG ? $_PROFILER->mark( 'afterLoad' ) : null;

/**
 * CREATE THE APPLICATION
 *
 * NOTE :
 */
$mainframe =& JFactory::getApplication('site');

/**
 * INITIALISE THE APPLICATION
 *
 * NOTE :
 */
// set the language
$mainframe->initialise();

JPluginHelper::importPlugin('system');

// trigger the onAfterInitialise events
JDEBUG ? $_PROFILER->mark('afterInitialise') : null;
$mainframe->triggerEvent('onAfterInitialise');

/**
 * ROUTE THE APPLICATION
 *
 * NOTE :
 */
$mainframe->route();

// authorization
$Itemid = JRequest::getInt( 'Itemid');
$mainframe->authorize($Itemid);

// trigger the onAfterRoute events
JDEBUG ? $_PROFILER->mark('afterRoute') : null;
$mainframe->triggerEvent('onAfterRoute');

/**
 * DISPATCH THE APPLICATION
 *
 * NOTE :
 */
$option = JRequest::getCmd('option');
$mainframe->dispatch($option);

// trigger the onAfterDispatch events
JDEBUG ? $_PROFILER->mark('afterDispatch') : null;
$mainframe->triggerEvent('onAfterDispatch');

/**
 * RENDER  THE APPLICATION
 *
 * NOTE :
 */
$mainframe->render();

// trigger the onAfterRender events
JDEBUG ? $_PROFILER->mark('afterRender') : null;
$mainframe->triggerEvent('onAfterRender');

/**
 * RETURN THE RESPONSE
 */
//echo JResponse::toString($mainframe->getCfg('gzip'));



function test( $test) {

    $dataset = JFactory::getDBSet();
    $users = JFactory::getDBRelation("user");
    $sections = JFactory::getDBRelation("section");

    echo "<P><B>Use dataset</B><HR>";
    $dataset->setSQL(array("select menutype from #__menu"));
    $dataset->open();
    print_r($dataset->data);



    echo "<P><B>Open sections</B><HR>";
    $sections->open();
    print_r($sections->data);


    echo "<P><B>Open Users</B><HR>";
    $users->open();
    print_r($users->data);


    echo "<P><B>Transaction</B><HR>";
    $dataset->setSQL(array("insert into #__groups values(4,'test')"));
    $dataset->open();



    echo "<P><B>Fields</B><HR>";
    $dataset2 = JFactory::getDBSet();
    $dataset2->setSQL(array("select * from #__groups as xx"));
    $dataset2->open();
    echo "RecCount=" . $dataset2->recordCount() . "<BR>";
    foreach ($dataset2->fields as $field)
    {
        echo $field["name"] . ":";
        print_r($field);
        echo "<BR>";
    }

    echo "<P><B>All datasets using the same Connection Instance</B><HR>";
    $dataset2 = JFactory::getDBSet();
    if ($users->connection === $sections->connection)  echo "EQ 1<BR>";
    if ($dataset->connection === $dataset2->connection)  echo "EQ 2<BR>";
    if ($users->connection === $dataset->connection)  echo "EQ 3<BR>";
    if ($users->getQueryBuilder()  === $sections->getQueryBuilder())  echo "EQ 4<BR>";


}


/****************************************************************/
/****************************************************************/
/****************************************************************/
/****************************************************************/
/****************************************************************/



echo "<HR><B>Joda Test Drive</B><HR><BR>";

test( 'mysql');




