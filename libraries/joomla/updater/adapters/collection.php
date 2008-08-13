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
	
	var $update_sites;
	
	/**
     * Gets the reference to the current direct parent
     *
     * @return object
     */
    function _getStackLocation()
    {
            $return = '';
            foreach($this->_stack as $stack) {
                    $return .= $stack.'->';
            }

            return rtrim($return, '->');
    }
	
	function _startElement($parser, $name, $attrs = Array()) {
		array_push($this->_stack, $name);
		$tag = $this->_getStackLocation();
		// reset the data
		eval('$this->'. $tag .'->_data = "";');
		echo 'Opened: '; print_R($this->_stack); echo '<br />';
		print_r($attrs); echo '<br />';
		switch($name) {
			case 'CATEGORY':
				if(isset($attrs['REF'])) {
					$this->update_sites[] = Array('type'=>'collection','location'=>$attrs['REF']);
					echo 'Found new update collection: '. $attrs['NAME'] .'<br />';
				}
				break;
		}
	}
	
	function _endElement($parser, $name) {
		echo 'Closed: ' . array_pop($this->_stack) .'; Stack: '. print_r($this->_stack,1) .'<br /><br />';
		//echo 'Closed: '; print_r($this->_stack); echo '<br /><br />';
	}
	
	function _characterData($parser, $data) {
		$tag = $this->_getStackLocation();
		eval('$obj =& $this->'. $tag .'->_data;');
		$obj .= $data;
	}
	
	function findUpdate($url) {
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
		xml_set_character_data_handler($this->xml_parser, '_characterData');
	
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