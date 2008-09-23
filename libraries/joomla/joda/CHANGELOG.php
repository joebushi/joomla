<?php
/**
* @version      $Id: CHANGELOG.php 10865 2008-08-30 07:08:40Z plamendp $
* @package      Joomla
* @subpackage   Joda
* @copyright    Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
Changelog
---------
This is a non-exhaustive (but still near complete) changelog for
Joda, the Joomla Database Abstraction Layer. It's a list of changes
made to Joda sub-tree only. See CHANGELOG.php in the Joomla! root directory.
Our thanks to all those people who've contributed bug reports and
code fixes.


Legend:

* -> Security Fix
# -> Bug Fix
$ -> Language fix or change
+ -> Addition
^ -> Change
- -> Removed
! -> Note

Joda specific:
===========================================================
J15 -> Joomla 1.5 Release Branch at Joomlacode.org
WJJ -> Working Joomla Joda - a working SVN repository of Joomla Joda development branch.
	   See: http://zetcom.bg/svn/joomla/development/branches/joda (ask for username plamendp@zetcom.bg)
JJ  -> Joomla Joda development branch at Joomlacode.org

NOTE: Merging line is:  J15->WWJ->JJ. So, JJ is a result of all work done on Joomla 1.5
	  release branch plus what's done on the working copy. The result is JJ in sync with
	  J15 development process. Conflicts are resolved honoring J15 code!


23-Sep-2008 Plamen Petkov
 ! J15 merged into WJJ up to rev.10951
 ! WJJ merged into JJ up to rev.110

22-Sep-2008 Plamen Petkov
 * Fix reQuote() mess
 ! WJJ Merged into JJ up to rev. 108

17-Sep-2008 Plamen Petkov
 + Add reQuoting scheme in JQueryBuilder
 + Add splitSQL() in Jconnection execQueries()
 + Introduce Joda syntax: text quotes and identifier quotes: SINGLE and DOUBLE quotes

15-Sep-2008 Plamen Petkov
 + JFactory::getActiveConnections()
 + Keep connections list in a class static property: $instances
 ^ Debug plugin uses Active Connections idea
 ^ Working on Jodademo

14-Sep-2008 Plamen Petkov
 ! WJJ merged into JJ  up to 98 
 + Working on Jodademo
 ^ JConnection has its own JQueryBuilder compagnion
 ^ System debug plugin enumerates all JConnection connections' query logs

11-Sep-2008 Plamen Petkov
 + Working on Jodademo
 ^ Change debug plugin to enumerate all named connection's queries log

10-Sep-2008 Plamen Petkov
 + Working on Jodademo
 ! WJJ merged into JJ  up to 89
 ! J15 merged into WJJ up to 10921 (install check removed)
 ^ Remove JDEV_SKIPINSTALLCHECK from defines (install check removed in 1.4-stable)
 ^ Finding the right place for QueryBuilder, quoting characters, etc. among other classes
 ^ Make splitSQL() a static Joda method

09-Sep-2008 Plamen Petkov
 ! WJJ Merged into JJ up to rev 85
 ^ Moving methods from JQueryBuilder to Joda as static methods for common use
 ^ Moving final SQL manipulation - in JConnection (replace prefix, split SQL, qtc.)
 ^ JQueryBuilding won't be aware of any prefixes! JConnections is responsible for that (?!)22:27 ÷. 9.9.2008 ã.

08-Sep-2008 Plamen Petkov
 # Make some JQueryBuilder methods static
 # Fine tune relations between JQueryBuilder-Jdataset-JConnection:
 # Fix stupid UniqueID usage in Query Builder - quoted strings replacing now works fine

07-Sep-2008 Plamen Petkov
 + Add Joda $connections list creation to installation process!
 ! WJJ merged into JJ up to rev80 
 ! J15 merged into WWJ up to rev10912
 + Add index.html to all Joda directories
 + Add posibility to skip installation check for development purpose (see top of both admin's and site's framework.php)
 - No more index.php in Joda main dir. Use Jodademo Component instead!
 + Add Joda Demo component to Joomla! schema creation installation file (joomla.sql)

05-Sep-2008 Plamen Petkov
 ! Merge WJJ Note: Working Joomla-Joda (WJJ) merged up to rev. 72
 ! Merge J15 Note: Joomla! 1.5-STABLE (J15) merged up to 10907
 ! Next merge start: WJJ:76, J15:10908
 ! Note: since 01 Sep 2008 work is done on a phisically separate repository to avoid flooding main Joomlacode.org repo with countless commits!
   http://www.zetcom.bg/svn/joomla/development/branches/joda (requires authentication; please ask for username)
   Once in a while J15 is merged into WJJ which is then merged into JJ (the official Joomlacode.org Joomla-Joda branch repo):
     JJ = commit ( (J15 merge in WJJ) merge in JJ )

04-Sep-2008 Plamen Petkov
 + Add __toString() to QueryBuilder,  returning the final SQL string
 + Add splitSQL() method to QB

03-Sep-2008 Plamen Petkov
 ^ Somewhat finished  replaceNonQuotedString()
 ^ QueryBuilder select() accepts null parameters, meaning '*'

02-Sep-2008 Plamen Petkov
 ! Merge Note: 1.5-stable merged up to rev. 10882
 ^ Log changes into joda/CHANGELOG.php  instead of <Joomla! root>/CHANGELOG.php

30-Aug-2008 Plamen Petkov
 ! Merge Note: 1.5-stable merged up to rev. 10860

29-Aug-2008 Plamen Petkov
 + Added "Joda Queries Logged" to debug plugin
 ^ Working on Joda Demo component ("Examples")

28-Aug-2008 Plamen Petkov
 ! Merge Note: 1.5-stable merged  up to rev. 10836
 + Working on JConnection debug, including Named Connections Debug using connection specific Debug option
 ! Merge Note: 1.5-stable  merged up to rev. 10829
 ^ Make JQueryBuilder a  Single Query Builder, rather than an array of queries
 ^ JDataset accepts SQL as a string or array of strings
 + Add methods to addSQL (additive)
 + Transaction flow and error reporting improved

27-Aug-2008 Plamen Petkov
 ! Merge Note: 1.5-stable merged up to  rev. 10818
 ! Merge Note: 1.5-stable merged up to rev. 10817
 + Start minor query execution error handling
 ^ Handle FETCH styles (Assoc/Objects/etc.)

25-Aug-2008 Plamen Petkov
 + Add debuging to JConnection class
 ^ Rename some  variables/properties
 ! Merge Note: 1.5-stable merged up to rev. 10797

24-Aug-2008 Plamen Petkov
 ! Merge Note: 1.5-stable changes merged (rev. 10778-10792)

24-Aug-2008 Plamen Petkov
 ! Joda branch should be considered now: line-endings clean, prop-sets done (php/css/js/ini files), and ...
 ! ... and Joomla 1.5-stable branch merged into Joda branch up to rev.10777. DONE.

23-Aug-2008 Plamen Petkov
 ! Merge Note: 1.5-stable merged into up to  10777

23-Aug-2008 Plamen Petkov
 # Fix "unnamed connection used" (factory.php)
 + Add Joda::default_connections(): a default set of default named connections
 ^ Make config_joda_connections more template-ish, not a controller-like
 ^ Handle empty config connections, max connections number, etc. (com_config application.php controller)

23-Aug-2008 Plamen Petkov
 ! Branch 1.5-stable [up to rev.10752/23 Aug 2008] merged into Joda branch (line endings issue skipped)

-------------------- Joomla 1.5.6 Stable Release [12-August-2008] --------------------
-------------------- Joomla 1.5.5 Stable Release [27-July-2008] ----------------------
-------------------- Joomla 1.5.4 Stable Release [7-July-2008] -----------------------

-------------------- Joda Branch added to Joomla! project tree [23-May-2008] ----------------------------- Joomla 1.5.3 Stable Release [22-April-2008] ----------------------------------------- Joomla 1.5.2 Stable Release [22-March-2008] ---------------------