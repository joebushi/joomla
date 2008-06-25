<?php
/**
* @version		$Id$
* @package		Joomla.Framework
* @subpackage	Document
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * DocumentVCARD class, provides an easy interface to parse and display a vcard
 *
 * @author		Johan Janssens <johan@joomla.be>
 * @package		Joomla.Framework
 * @subpackage	Document
 * @since		1.6
 */

class JDocumentVCARD extends JDocument
{
	/**
	 * The VCard properties
	 *
	 * @var		array
	 * @access	public
	 */
	var $_properties;
	
	var $_filename;
	
	
	/**
	 * Class constructore
	 *
	 * @access protected
	 * @param	array	$options Associative array of options
	 */
	function __construct($options = array())
	{
		parent::__construct($options);

		//set mime type
		$this->_mime = 'text/x-vcard';

		//set document type
		$this->_type = 'vcard';
	}

	/**
	 * Render the document.
	 *
	 * @access public
	 * @param boolean 	$cache		If true, cache the output
	 * @param array		$params		Associative array of attributes
	 * @return 	The rendered data
	 */
	function render( $cache = false, $params = array())
	{
		$data 	= 'BEGIN:VCARD';
		$data	.= "\r\n";
		$data 	.= 'VERSION:2.1';
		$data	.= "\r\n";

		foreach( $this->_properties as $key => $value ) {
			$data	.= "$key:$value";
			$data	.= "\r\n";
		}
		$data	.= 'REV:'. date( 'Y-m-d' ) .'T'. date( 'H:i:s' ). 'Z';
		$data	.= "\r\n";
		$data	.= 'MAILER: Joomla! vCard for '. $this->getBase();
		$data	.= "\r\n";
		$data	.= 'END:VCARD';
		$data	.= "\r\n";
		
		// Set document type headers
		parent::render();
		
		//JResponse::setHeader('Content-Length', strlen($data), true);
		JResponse::setHeader('Content-disposition: attachment; filename="'.$this->_filename.'"', true);
		
		return $data;
	}
	
	// type may be PREF | WORK | HOME | VOICE | FAX | MSG | CELL | PAGER | BBS | CAR | MODEM | ISDN | VIDEO or any senseful combination, e.g. "PREF;WORK;VOICE"
	function setPhoneNumber($number, $type='') 
	{
		$key = 'TEL';
		if ($type!='') {
			$key .= ';'. $type;
		}
		$key.= ';ENCODING=QUOTED-PRINTABLE';

		$this->_properties[$key] = $this->quoted_printable_encode($number);
	}

	// $type = "GIF" | "JPEG"
	function setPhoto($type, $photo) 
	{ 
		$this->_properties["PHOTO;TYPE=$type;ENCODING=BASE64"] = base64_encode($photo);
	}

	function setFormattedName($name) 
	{
		$this->_properties['FN'] = $this->quoted_printable_encode($name);
	}
	
	function setName( $family='', $first='', $additional='', $prefix='', $suffix='' ) 
	{
		$this->_properties["N"] 	= "$family;$first;$additional;$prefix;$suffix";
		$this->setFormattedName( trim( "$prefix $first $additional $family $suffix" ) );
	}

	// $date format is YYYY-MM-DD
	function setBirthday($date) 
	{ 
		$this->_properties['BDAY'] = $date;
	}

	// $type may be DOM | INTL | POSTAL | PARCEL | HOME | WORK or any combination of these: e.g. "WORK;PARCEL;POSTAL"
	function setAddress( $postoffice='', $extended='', $street='', $city='', $region='', $zip='', $country='', $type='HOME;POSTAL' ) 
	{
		$separator = ';';

		$key 		= 'ADR';
		if ( $type != '' ) {
			$key	.= $separator . $type;
		}
		$key.= ';ENCODING=QUOTED-PRINTABLE';

		$return = $this->encode( $postoffice );
		$return .= $separator . $this->encode( $extended );
		$return .= $separator . $this->encode( $street );
		$return .= $separator . $this->encode( $city );
		$return .= $separator . $this->encode( $region);
		$return .= $separator . $this->encode( $zip );
		$return .= $separator . $this->encode( $country );

		$this->_properties[$key] = $return;
	}

	function setLabel($postoffice='', $extended='', $street='', $city='', $region='', $zip='', $country='', $type='HOME;POSTAL') 
	{
		$label = '';
		if ($postoffice!='') {
			$label.= $postoffice;
			$label.= "\r\n";
		}

		if ($extended!='') {
			$label.= $extended;
			$label.= "\r\n";
		}

		if ($street!='') {
			$label.= $street;
			$label.= "\r\n";
		}

		if ($zip!='') {
			$label.= $zip .' ';
		}

		if ($city!='') {
			$label.= $city;
			$label.= "\r\n";
		}

		if ($region!='') {
			$label.= $region;
			$label.= "\r\n";
		}

		if ($country!='') {
			$country.= $country;
			$label.= "\r\n";
		}

		$this->_properties["LABEL;$type;ENCODING=QUOTED-PRINTABLE"] = $this->quoted_printable_encode($label);
	}

	function setEmail($address) 
	{
		$this->_properties['EMAIL;INTERNET'] = $address;
	}

	function setNote($note) 
	{
		$this->_properties['NOTE;ENCODING=QUOTED-PRINTABLE'] = $this->quoted_printable_encode($note);
	}

	// $type may be WORK | HOME
	function setURL($url, $type='') 
	{
		$key = 'URL';
		if ($type!='') {
			$key.= ";$type";
		}

		$this->_properties[$key] = $url;
	}
	
	function setFilename( $filename ) 
	{
		$this->_filename = $filename .'.vcf';
	}
	
	function setTitle( $title ) 
	{
		$title 	= trim( $title );
		$this->_properties['TITLE'] 	= $title;
	}
	
	function setOrg( $org ) 
	{
		$org 	= trim( $org );
		$this->_properties['ORG'] = $org;
	}


	function encode($string) {
		return $this->escape($this->quoted_printable_encode($string));
	}

	function escape($string) {
		return str_replace(';',"\;",$string);
	}

	function quoted_printable_encode($input, $line_max = 76) 
	{
		$hex 		= array('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F');
		$lines 		= preg_split("/(?:\r\n|\r|\n)/", $input);
		$eol 		= "\r\n";
		$linebreak 	= '=0D=0A';
		$escape 	= '=';
		$output 	= '';

		for ($j=0;$j<count($lines);$j++) 
		{
			$line 		= $lines[$j];
			$linlen 	= strlen($line);
			$newline 	= '';

			for($i = 0; $i < $linlen; $i++) 
			{
				$c 		= substr($line, $i, 1);
				$dec 	= ord($c);

				if ( ($dec == 32) && ($i == ($linlen - 1)) ) { // convert space at eol only
					$c = '=20';
				} elseif ( ($dec == 61) || ($dec < 32 ) || ($dec > 126) ) { // always encode "\t", which is *not* required
					$h2 = floor($dec/16);
					$h1 = floor($dec%16);
					$c 	= $escape.$hex["$h2"] . $hex["$h1"];
				}
				if ( (strlen($newline) + strlen($c)) >= $line_max ) { // CRLF is not counted
					$output .= $newline.$escape.$eol; // soft line break; " =\r\n" is okay
					$newline = "    ";
				}
				$newline .= $c;
			} // end of for
			$output .= $newline;
			if ($j<count($lines)-1) {
				$output .= $linebreak;
			}
		}

		return trim($output);
	}
}