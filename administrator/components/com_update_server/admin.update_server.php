<?php
/**
* @version $Id: admin.update_server.php,v 1.1 2005/08/25 14:14:54 johanjanssens Exp $
* @package Mambo Update Server
* @copyright (C) Samuel Moffatt
* @author Samuel Moffatt <pasamio@gmail.com>
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

// update_server logic code
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
require_once($mainframe->getPath('admin_html'));
require_once($mainframe->getPath('class'));

// Task Switcher
switch(strtolower($task)) { 
	case "newproduct":
		editProduct(0,$option);
		break;
	case "editproduct":
		editProduct($cid, $option);
		break;
	case "listremotesites":
		listRemoteSites($option);
		break;
	case "newremotesite":
		editRemoteSite(0, $option);
		break;
	case "editremotesite":
		editRemoteSite($cid, $option);
		break;
	case "listreleases":
		listReleases($option);
		break;
	case "newrelease":
		newRelease(0, $option);
		break;
	case "editrelease":
		editRelease($cid, $option);
		break;
	case "editdependency":
		editDependency($cid, $option);
		break;
	case "newdependency":
		newDependency($cid, $option);
		break;
	case "listdependencies":
		listDependencies($option);
		break;		
	case "cancel":
		cancel($option);
		break;
	case "new":
		createNew($option);
		break;
	case "save":
		save($option);
		break;
	case "remove":
		remove($option);
		break;
	case "listproducts":
		listProducts($option);
		break;
	case "publish":
		publish($option);
		break;
	case "unpublish":
		unpublish($option);
		break;		
	default:
		listProducts($option);
		break;

}

// Function Implementation
/**
 * Description: Publishes a release or product
 */
function publish($option) {
	global $database;
	$type = mosGetParam($_REQUEST,'type','');
	$cid = mosGetParam($_REQUEST,'cid','');
	$query = '';
	foreach($cid as $id) {
		switch($type) {
			case 'product':
				// Do something!\
				$query = 'UPDATE #__update_product SET published = 1 WHERE productid = ' . $id;
				break;
			case 'release':
				// Do something else!
				$query = 'UPDATE #__update_releases SET published = 1 WHERE releaseid = ' . $id;
				break;
		}
		if($query) { 
			$database->setQuery($query); 
			$database->Query();
			$database->getQuery();
		}
	}
	if($type == 'release') {
		listReleases($option);
	} else {
		listProducts($option);
	}
}

/**
 * Description: Unpublishes a release or product
 */
function unpublish($option) {
	global $database;
	$type = mosGetParam($_REQUEST,'type','');
	$cid = mosGetParam($_REQUEST,'cid','');
	$query = '';
	foreach($cid as $id) {
		switch($type) {
			case 'product':
				// Do something!\
				$query = 'UPDATE #__update_product SET published = 0 WHERE productid = ' . $id;
				break;
			case 'release':
				// Do something else!
				$query = 'UPDATE #__update_releases SET published = 0 WHERE releaseid = ' . $id;
				break;
		}
		if($query) { 
			$database->setQuery($query); 
			$database->Query();
			$database->getQuery();
		}
	}
	if($type == 'release') {
		listReleases($option);
	} else {
		listProducts($option);
	}
}

/**
 * Description: Pulls out a list of projects into a Select List
 */
function listProductSelectList($selected=NULL) {
	global $database, $mainframe;
	$database->setQuery("SELECT productid, productname FROM #__update_product");
	$database->Query() or die("Unable to execute product listing SQL: " . $database->getErrorMsg() . "; SQL: " . $database->getQuery());
	$arr = Array(); 
	$data = $database->loadObjectList();
	foreach($data as $datum) {
		$arr[] = mosHTML::makeOption( $datum->productid, $datum->productname );
	}
	return mosHTML::selectList( $arr, "productid", "", 'value', 'text',$selected);	
}

/**
 * Description: Pulls out a list of projects into a Select List
 */
function listReleasesSelectList($selected=NULL) {
	global $database, $mainframe;
	$database->setQuery("SELECT a.releaseid, a.releasetitle, b.productname FROM #__update_releases AS a, #__update_product AS b WHERE a.productid = b.productid ORDER BY b.productid, a.releaseid");
	$database->Query() or die("Unable to execute release listing SQL: " . $database->getErrorMsg() . "; SQL: " . $database->getQuery());
	$arr = Array(); 	
	$data = $database->loadObjectList();
	foreach($data as $datum) {
		$arr[] = mosHTML::makeOption( $datum->releaseid, $datum->productname . " " . $datum->releasetitle );
	}
	return mosHTML::selectList( $arr, "releaseid", "", 'value', 'text', $selected);
}

/**
 * Description: Pulls out a list of projects into a Select List
 */
function listRemoteSitesSelectList($selected=NULL) {
	global $database, $mainframe;
	$database->setQuery("SELECT remotesiteid, remotesitename FROM #__update_remotesite");
	$database->Query() or die("Unable to execute remotesite listing SQL: " . $database->getErrorMsg() . "; SQL: " . $database->getQuery());
	$arr = Array(); 	
	$data = $database->loadObjectList();
	foreach($data as $datum) {
		$arr[] = mosHTML::makeOption( $datum->remotesiteid, $datum->remotesitename );
	}
	return mosHTML::selectList( $arr, "remotesiteid", "", 'value', 'text',$selected);
}

/**
 * Description: Builds a product list that copies itself onto a textbox
 */
function buildProductSelectList() {
	global $database, $mainframe;
	$database->setQuery("SELECT productname FROM #__update_product");
	$database->Query() or die("Unable to execute product listing SQL: " . $database->getErrorMsg() . "; SQL: " . $database->getQuery());
	$arr = Array(); 
	$arr[] = mosHTML::makeOption( "", "Non Local" );
	$data = $database->loadObjectList();
	foreach($data as $datum) {
		$arr[] = mosHTML::makeOption( $datum->productname, $datum->productname );
	}
	return mosHTML::selectList( $arr, "dbdepproductname", "id=\"dbdepproductname\" onChange=\"setProduct()\"", 'value', 'text');	
}

/**
 * Description: Builds a version string list that copies itself onto a textbox
 */
function buildVersionStringSelectList() {
	global $database, $mainframe;
	$database->setQuery("SELECT versionstring FROM #__update_releases");
	$database->Query() or die("Unable to execute product listing SQL: " . $database->getErrorMsg() . "; SQL: " . $database->getQuery());
	$arr = Array(); 
	$arr[] = mosHTML::makeOption( "", "Not Applicable" );
	$data = $database->loadObjectList();
	$versionstrings = Array();
	foreach($data as $datum) {
		if(!in_array(trim($datum->versionstring),$versionstrings)) {
			$versionstrings[] = trim($datum->versionstring);
			$arr[] = mosHTML::makeOption( $datum->versionstring, $datum->versionstring );
		}
	}
	return mosHTML::selectList( $arr, "dbdepversionstring", "id=\"dbdepversionstring\" onChange=\"setVersion()\"", 'value', 'text');
}

/**
 * Description: Grabs the product name
 */
function getProductName($prodid) {
	global $database;
	$database->setQuery("SELECT productname FROM #__update_product WHERE productid = '$prodid'");
	$database->Query() or die("Unable to execute product name search: " . $database->getErrorMsg() . "; SQL: " . $database->getQuery());
	return $database->loadResult();
}

function removeProduct($option) {
	global $database, $cid;
	$cprod = new updateproduct($database);
	foreach($cid as $id) {
		$cprod->productid = $id;		
		$cprod->delete() or die("Failed to delete: " . $cprod->getError());
	}
	listProducts($option);
}



/**
 * Description: Creates a listing of products
 */
function listProducts($option) {
	global $database, $mainframe, $mosConfig_list_limit;
	
	if(!isset($sectionid)) { $sectionid = ''; }
	
	$limit 				= $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit );
	$limitstart 		= $mainframe->getUserStateFromRequest( "view{$option}{$sectionid}limitstart", 'limitstart', 0 );


	// get the total number of records
	$database->setQuery( "SELECT count(*) FROM #__update_product");
	$total = $database->loadResult();
	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit );

	$query = "SELECT * FROM #__update_product LIMIT $pageNav->limitstart,$pageNav->limit";
	$database->setQuery( $query );
	$rows = $database->loadObjectList();

	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	HTML_update_server::listProducts( $rows, $pageNav);
}

/**
 * Description: Creates a new product
 */
function newProduct($option) {
	global $database, $my, $mainframe;
	global $mosConfig_absolute_path, $mosConfig_live_site, $mosConfig_offset;	   
	// load the row from the db table
	$row = new updateproduct( $database );
	$row->load( $cid );
	HTML_update_server::editProduct($row);
}

function saveProduct($option) {
	global $database;
	$cprod = new updateproduct($database);
	$cprod->bind($_REQUEST);
	//echo "<pre>" . print_r($_REQUEST) . "</pre>";
	$cprod->store() or die("Failed to save: " . $cprod->getError());
	echo "Saved!<br>";	
	listProducts($option);
}

/**
 * Description: Edits a product
 */
function editProduct($cid, $option) {
	global $database, $my, $mainframe;
	global $mosConfig_absolute_path, $mosConfig_live_site, $mosConfig_offset;
    	
	$cid = intval(mosGetParam($_REQUEST,"cid",0));
	// load the row from the db table
	$row = new updateproduct( $database );
	$row->load( $cid );
	HTML_update_server::editProduct( $row);
}


/**
 * Description: Lists remote sites or servers
 */
function listRemoteSites($option) {
	global $database, $mainframe, $mosConfig_list_limit;
	
	if(!isset($sectionid)) { $sectionid = ''; }
	
	$limit 			= $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit );
	$limitstart 		= $mainframe->getUserStateFromRequest( "view{$option}{$sectionid}limitstart", 'limitstart', 0 );


	// get the total number of records
	$database->setQuery( "SELECT count(*) FROM #__update_remotesite");
	$total = $database->loadResult();
	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit );

	$query = "SELECT * FROM #__update_remotesite LIMIT $pageNav->limitstart,$pageNav->limit";
	$database->setQuery( $query );
	$rows = $database->loadObjectList();

	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	HTML_update_server::listRemoteSites( $rows, $pageNav);

}


/**
 * Description: Edits a remote site
 */
function editRemoteSite($cid, $option) {
	global $database, $my, $mainframe;
	global $mosConfig_absolute_path, $mosConfig_live_site, $mosConfig_offset;
    	
	$cid = intval(mosGetParam($_REQUEST,"cid",0));
	// load the row from the db table
	$row = new updateremotesite( $database );
	$row->load( $cid );
	HTML_update_server::editRemoteSite( $row);
}

/**
 * Description: Creates a new remote site
 */
function newRemoteSite($option) {
	global $database, $my, $mainframe;
	global $mosConfig_absolute_path, $mosConfig_live_site, $mosConfig_offset;
    
	// load the row from the db table
	$row = new updateremotesite( $database );
	$row->load( $cid );
	HTML_update_server::editRemoteSite( $row);


}
function removeRemoteSite($option) {
	global $database,$cid;
	$cprod = new updateremotesite($database);
	foreach($cid as $id) {		
		$cprod->remotesiteid = $id;		
		$cprod->delete() or die("Failed to delete: " . $cprod->getError());
	}
	listRemoteSites($option);
}

function saveRemoteSite($option) {
	global $database;
	$cprod = new updateremotesite($database);
	$cprod->bind($_REQUEST);
	$cprod->store() or die("Failed to save: " . $cprod->getError());
	echo "Saved!<br>";		
	listRemoteSites($option);
}

/**f
 * Description: Creates a listing of releases
 */
function listReleases($option) {
	global $database, $mainframe, $mosConfig_list_limit;
	
	if(!isset($sectionid)) { $sectionid = ''; }
	
	$limit 			= $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit );
	$limitstart 		= $mainframe->getUserStateFromRequest( "view{$option}{$sectionid}limitstart", 'limitstart', 0 );


	// get the total number of records
	$database->setQuery( "SELECT count(*) FROM #__update_releases");
	$total = $database->loadResult();
	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit );

	$query = "SELECT a.*,b.productname FROM #__update_releases AS a, #__update_product as b WHERE a.productid = b.productid ORDER BY b.productid, a.releaseid LIMIT $pageNav->limitstart,$pageNav->limit ";
	$database->setQuery( $query );
	$rows = $database->loadObjectList();

	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}
	
	HTML_update_server::listReleases( $rows, $pageNav);
}

/**
 * Description: Creates a new release
 */
function newRelease($option) {
	global $database, $my, $mainframe;
	global $mosConfig_absolute_path, $mosConfig_live_site, $mosConfig_offset;
    
	// load the row from the db table
	$row = new updaterelease( $database );	
	$row->releasedate = date("Y-m-d",time());
	HTML_update_server::editRelease( $row, listProductSelectList());

}

/**
 * Description: Edits a release
 */
function editRelease($cid, $option) {
	global $database, $my, $mainframe;
	global $mosConfig_absolute_path, $mosConfig_live_site, $mosConfig_offset;
    
	$cid = intval(mosGetParam($_REQUEST,"cid",0));
	// load the row from the db table
	$row = new updaterelease( $database );
	$row->load( $cid );
	HTML_update_server::editRelease($row, listProductSelectList($row->productid));
}

function removeRelease($option) {
	global $database, $cid;
	$cprod = new updaterelease($database);
	foreach($cid as $id) {
		$cprod->releaseid = $id;		
		$cprod->delete() or die("Failed to delete: " . $cprod->getError());
	}
	listReleases($option);
}

function saveRelease($option) {
	global $database;
	$cprod = new updaterelease($database);
	$cprod->bind($_REQUEST);	
	$cprod->store() or die("Failed to save: " . $cprod->getError());
	echo "Saved!<br>";		
	listReleases($option);
}

function newDependency($option) {
	global $database, $my, $mainframe;
	global $mosConfig_absolute_path, $mosConfig_live_site, $mosConfig_offset;
    
	// load the row from the db table
	$row = new updatedependency( $database );
	$row->load( $cid );
	HTML_update_server::editDependency($row, listReleasesSelectList(), listRemoteSitesSelectList(), buildProductSelectList(), buildVersionStringSelectList());
}

function editDependency($cid, $option) {
	global $database, $my, $mainframe;
	global $mosConfig_absolute_path, $mosConfig_live_site, $mosConfig_offset;
    
	$cid = intval(mosGetParam($_REQUEST,"cid",0));
	// load the row from the db table
	$row = new updatedependency( $database );
	$row->load( $cid );
	HTML_update_server::editDependency($row, listReleasesSelectList($row->currentrelease), listRemoteSitesSelectList($row->depremotesite), buildProductSelectList(), buildVersionStringSelectList());

}
	
function listDependencies($option) {
	global $database, $mainframe, $mosConfig_list_limit;
	
	if(!isset($sectionid)) { $sectionid = ''; }
	
	$limit 			= $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit );
	$limitstart 		= $mainframe->getUserStateFromRequest( "view{$option}{$sectionid}limitstart", 'limitstart', 0 );


	// get the total number of records
	$database->setQuery( "SELECT count(*) FROM #__update_dependencies");
	$total = $database->loadResult();
	require_once( $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit );

	$query = "SELECT c.*,b.productname,a.releasetitle,d.remotesitename FROM #__update_releases AS a, #__update_product as b, #__update_dependencies as c, #__update_remotesite AS d WHERE d.remotesiteid = c.depremotesite AND c.currentrelease = a.releaseid AND a.productid = b.productid LIMIT $pageNav->limitstart,$pageNav->limit ";
	$database->setQuery( $query );	
	$rows = $database->loadObjectList();	
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}
	
	HTML_update_server::listDependencies( $rows, $pageNav);
}

function removeDependency($option) {
	global $database, $cid;
	$cprod = new updatedependency($database);
	if(is_array($cid)) {		
		foreach($cid as $id) {		
			$cprod->dependencyid = $id;		
			$cprod->delete() or die("Failed to delete: " . $cprod->getError());
		}
	} else {
		$cprod->dependencyid = $cid;		
		$cprod->delete() or die("Failed to delete: " . $cprod->getError());	
	}
	listDependencies($option);
}

function saveDependency($option) {
	global $database;
	$cprod = new updatedependency($database);
	$cprod->bind($_REQUEST);	
	$cprod->currentrelease = mosGetParam($_REQUEST, "releaseid","Not set either");
	$cprod->depremotesite = mosGetParam($_REQUEST, "remotesiteid","Not set");
	//echo mosGetParam($_REQUEST, "remotesiteid","Not set");
	$cprod->store() or die("Failed to save: " . $cprod->getError());
	echo "Saved!<br>";		
	listDependencies($option);
}	

/**
 * Description: Generic multitask new option
 */
function createNew($option) {
	$type = mosGetParam($_REQUEST,"type",0);
	if($type) {	
		switch($type) {
			case "product":
				newProduct($option);
				break;
			
			case "release":
				newRelease($option);
				break;
			
			case "remotesite":
				newRemoteSite($option);
				break;	
				
			case "dependency":
				newDependency($option);
				break;
		}
	} else {
		echo "No Type Specified!";
	}
}


/**
 * Description: Generic multitask save option
 */
function save($option) {
	$type = mosGetParam($_REQUEST,"type",0);
	if($type) {	
		switch($type) {
			case "product":
				saveProduct($option);
				break;
			
			case "release":
				saveRelease($option);
				break;
			
			case "remotesite":
				saveRemoteSite($option);
				break;
				
			case "dependency":
				saveDependency($option);
				break;	
		}
	} else {
		echo "No Type Specified!";
	}
}

/**
 * Description: Generic multitask remove option
 */
function remove($option) {
	$type = mosGetParam($_REQUEST,"type",0);
	if($type) {	
		switch($type) {
			case "product":
				removeProduct($option);
				break;
			
			case "release":
				removeRelease($option);
				break;
			
			case "remotesite":
				removeRemoteSite($option);
				break;
			case "dependency":
				removeDependency($option);
				break;
		}
	} else {
		echo "No Type Specified!";
	}
}

/**
 * Description: Generic multitask cancel option
 */
function cancel($option) {
	$type = mosGetParam($_REQUEST,"type",0);
	if($type) {	
		switch($type) {
			case "product":
				listProducts($option);
				break;			
			case "release":
				listReleases($option);
				break;			
			case "remotesite":
				listRemoteSites($option);
				break;	
			case "dependency":
				listDependencies($option);
				break;
			default:
				listProducts($option);
				break;
		}
	} else {
		listProducts($option);
	}
}


?>
