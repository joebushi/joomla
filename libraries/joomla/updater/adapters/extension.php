<?php

defined('JPATH_BASE') or die();

jimport('joomla.base.adapterinstance');
jimport('pasamio.stringstream');

class JUpdaterExtension extends JAdapterInstance {
	var $xml_parser;
	var $_stack = Array();
	
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
	}
	
	function _endElement($parser, $name) {
		array_pop($this->_stack);
	}
	
	function _characterData($parser, $data) {
		$tag = $this->_getStackLocation();
		if(!isset($this->$tag->_data)) $this->$tag->_data = ''; 
		$this->$tag->_data .= $data;
	}
	
	function findUpdate($url) {
		if(substr($url, -4) != '.xml') {
			if(substr($url, -1) != '/') {
				$url .= '/';
			}
			$url .= 'extension.xml';
		}
		
		$details = file_get_contents($url);
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
		xml_parser_free($this->xml_parser);
		
	}
}