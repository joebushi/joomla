<?php
/**
 * Installer Dependency Checking
 * Builds a tree to determine the dependency
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
 
/**
 * Dependency Checker
 * @since 1.6
 */
class JDependency {
	/**
	 * Resolve
	 */
	public function resolve() {
	
	
	function getCurrentPlatform() {
		$version = new JVersion();
		$filter =& JFilterInput::getInstance();
		$name = strtolower($filter->clean($version->PRODUCT, 'cmd'));
		return Array('name'=>$name, 'version'=>$version->getShortVersion());
	}
	
	/**
	 * Returns path information for a given set of variables
	 * Used in tandem with JApplicationHelper::getPath
	 */
	public static function getPathType($type, $element, $folder, $client) {
		switch($type) {
			case 'plugin':
				return Array('variable'=>'plg_xml', 'path_opt'=>$folder.'/'.$element);
				break;
			case 'component':
				return Array('variable'=>'com_xml', 'path_opt'=>$element);
				break;
			case 'module':
				return Array('variable'=>'mod'.intval($client).'_xml', 'path_opt'=>$element);
				break;
}	}
}
