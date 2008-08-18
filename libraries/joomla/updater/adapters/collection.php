<?php
/**
 * Collection Update Adapter
 * Handles retrieving updates from collections
 */
 
defined('JPATH_BASE') or die();
 
jimport('joomla.base.adapterinstance');
jimport('pasamio.stringstream');
 
/**
 * Collection Update Adapater Class
 * @since 1.6
 */
class JUpdaterCollection extends JAdapterInstance {
	var $xml_parser;
	var $_stack = Array('base');
	var $base;
	var $parent = Array(0); 
	var $pop_parent = 0;
	var $update_sites;
	var $_updatesiteid = 0;
	
	var $_updatecols = Array('NAME', 'ELEMENT', 'TYPE', 'FOLDER', 'CLIENT');
	
	/**
     * Gets the reference to the current direct parent
     *
     * @return object
     */
    function _getStackLocation()
    {
            /*$return = '';
            
            foreach($this->_stack as $stack) {
                    $return .= $stack.'->';
            }

            return rtrim($return, '->');*/
            return implode('->', $this->_stack);
    }
    
    function _getParent() {
    	return end($this->parent);
    }
	
	function _startElement($parser, $name, $attrs = Array()) {
		array_push($this->_stack, $name);
		$tag = $this->_getStackLocation();
		// reset the data
		eval('$this->'. $tag .'->_data = "";');
		echo 'Opened: '; print_r($this->_stack); echo '<br />';
		print_r($attrs); echo '<br />';
		switch($name) {
			case 'CATEGORY':
				if(isset($attrs['REF'])) {
					$this->update_sites[] = Array('type'=>'collection','location'=>$attrs['REF'],'updatesiteid'=>$this->_updatesiteid);
					echo 'Found new update collection: '. $attrs['NAME'] .'<br />';
				} else {
					// This item will have children, so prepare to attach them
					$this->pop_parent = 1;
				}
				break;
			case 'EXTENSION':
				$update =& JTable::getInstance('update');
				$update->updatesiteid = $this->_updatesiteid;
				$extension =& JTable::getInstance('extension');
				foreach($this->_updatecols AS $col) {
					// reset the values if it doesn't exist
					if(!array_key_exists($col, $attrs)) {
						$attrs[$col] = '';
						if($col == 'CLIENT') {
							$attrs[$col] = 'site';
						}
					}
				}
				echo '<br /><br />';
				$client = JApplicationHelper::getClientInfo($attrs['CLIENT'],1);
				$attrs['CLIENT_ID'] = $client->id;
				$uid = $update->find(Array('element'=>strtolower($attrs['ELEMENT']), 
						'type'=>strtolower($attrs['TYPE']), 
						'client_id'=>strtolower($attrs['CLIENT_ID']), 
						'folder'=>strtolower($attrs['FOLDER'])));
				$eid = $extension->find(Array('element'=>strtolower($attrs['ELEMENT']), 
						'type'=>strtolower($attrs['TYPE']), 
						'client_id'=>strtolower($attrs['CLIENT_ID']), 
						'folder'=>strtolower($attrs['FOLDER'])));
				// lower case all of the fields
				foreach($attrs as $key=>$attr) {
					$values[strtolower($key)] = $attr;
				}
				if(!$uid) {
					// set the extension id
					if($eid) {
						// we have an installed extension, check the update is actually newer
						$extension->load($eid);
						$data = unserialize($extension->manifestcache);
						if(version_compare($attrs['VERSION'], $data['version'], '>') == 1) {
							//echo '<p>Storing extension since '. $attrs['VERSION'] .' > ' . $data['version']. '</p>';
							$update->extensionid = $eid;
							$update->bind($values);
							$update->store();
						}	
					} else {
						// a potentially new extension to be installed
						//echo '<p>Storing since no equivalent extension is installed</p>';
						$update->bind($values);
						$update->store();
					}				
				} else {
					$update->load($uid);
					// if there is an update, check that the version is newer then replaces
					if(version_compare($attrs['VERSION'], $update->version, '>') == 1) {
						//echo '<p>Storing extension since '. $attrs['VERSION'] .' > ' . $data['version']. '</p>';
						$update->bind($values);
						$update->store();
					}
				}//else { echo '<p>Found a matching update for '. $attrs['NAME'] .'</p>';}
				break;
		}
	}
	
	function _endElement($parser, $name) {
		$lastcell = array_pop($this->_stack);
		echo 'Closed: ' . $lastcell .'; Stack: '. print_r($this->_stack,1) .'<br /><br />';
		//echo 'Closed: '; print_r($this->_stack); echo '<br /><br />';
		switch($name) {
			case 'CATEGORY':
				if($this->pop_parent) {
					$this->pop_parent = 0;
					array_pop($this->parent);
				}
				break;
		}
	}
	
	/*// we don't care about char data in collection because there should be none
	function _characterData($parser, $data) {
		$tag = $this->_getStackLocation();
		eval('$obj =& $this->'. $tag .'->_data;');
		$obj .= $data;
	}*/
	
	function findUpdate($options) {
		$url = $options['location'];
		$this->_updatesiteid = $options['updatesiteid'];
		echo '<p>Find update for collection run on <a href="'. $url .'">'. $url .'</a></p>';
		if(substr($url, -4) != '.xml') {
			if(substr($url, -1) != '/') {
				$url .= '/';
			}
			$url .= 'update.xml';
		}
		
		$this->base = new stdClass();
		$this->update_sites = Array();
		
		$details = @file_get_contents($url);
		if(!$details) return false;
		// TODO: Add a 'mark bad' setting here.
		$file = md5($url);
		StringStreamController::createRef($file, $details );
		$dbo =& $this->parent->getDBO();
		
		if (!($fp = fopen('string://'. $file, "r"))) {
		    die("could not open XML input");
		}
		
		$this->xml_parser = xml_parser_create('');
		xml_set_object($this->xml_parser, $this);
		xml_set_element_handler($this->xml_parser, '_startElement', '_endElement');
		//xml_set_character_data_handler($this->xml_parser, '_characterData');
	
		while ($data = fread($fp, 8192)) {
		    if (!xml_parse($this->xml_parser, $data, feof($fp))) {
		        die(sprintf("XML error: %s at line %d",
		                    xml_error_string(xml_get_error_code($this->xml_parser)),
		                    xml_get_current_line_number($this->xml_parser)));
		    }
		}
		// TODO: Decrement the bad counter if non-zero
		if(count($this->update_sites)) {
			return $this->update_sites;
		} else return true;
	}
}