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

02-Sep-2008 Plamen Petkov  
 ! Merge Note: 1.5-stable merged up to rev. 10882
 ^ Log changes into joda/CHANGELOG.php instead of <Joomla! root>/CHANGELOG.php

30-Aug-2008 Plamen Petkov
 ! Merge Note: 1.5-stable merged up to rev. 10860

29-Aug-2008 Plamen Petkov
 + Added "Joda Queries Logged" to debug plugin
 ^ Working on Joda Demo component ("Examples")

28-Aug-2008 Plamen Petkov
 ! Merge Note: 1.5-stable merged  up to rev. 10836
 + Working on JConnection debug, including Named Connections Debug using connection specific Debug option
 ! Merge Note: 1.5-stable merged up to rev. 10829
 ^ Make JQueryBuilder a  Single Query Builder, rather than an array of queries
 ^ JDataset accepts SQL as a string or array of strings
 + Add methods to addSQL (additive)
 + Transaction flow and error reporting improved

27-Aug-2008 Plamen Petkov
 ! Merge Note: 1.5-stable merged up to rev. 10818
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
 + Add Joda::dummy_connections(): a default set of default named connections
 ^ Make config_joda_connections more template-ish, not a controller-like
 ^ Handle empty config connections, max connections number, etc. (com_config application.php controller)

23-Aug-2008 Plamen Petkov
 ! Branch 1.5-stable [up to rev.10752/23 Aug 2008] merged into Joda branch (line endings issue skipped)

-------------------- Joomla 1.5.6 Stable Release [12-August-2008] --------------------
-------------------- Joomla 1.5.5 Stable Release [27-July-2008] ----------------------
-------------------- Joomla 1.5.4 Stable Release [7-July-2008] -----------------------

-------------------- Joda Branch added to Joomla! project tree [23-May-2008] ---------

-------------------- Joomla 1.5.3 Stable Release [22-April-2008] ---------------------
-------------------- Joomla 1.5.2 Stable Release [22-March-2008] ---------------------
