<?php
/**
 * Joomla! Stream Interface
 * 
 * The Joomla! stream interface is designed to handle files as streams
 * where as the legacy JFile static class treated files in a rather 
 * atomic manner.
 * 
 * This class adheres to the stream wrapper operations:
 * http://www.php.net/manual/en/function.stream-get-wrappers.php
 * 
 * PHP5
 *  
 * Created on Sep 17, 2008
 * 
 * @package Joomla!
 * @license GNU/GPL http://www.gnu.org/licenses/gpl.html
 * @copyright 2008 OpenSourceMatters.org 
 * @version SVN: $Id:$    
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

jimport('joomla.filesystem.helper');

class JStream extends JObject {
	// Publicly settable vars (protected to let our parent read them)
	/** @var File Mode */
	protected $filemode = 0644;
	/** @var Directory Mode */
	protected $dirmode = 0755;
	/** @var Default Chunk Size */
	protected $chunksize = 8192;
	/** @var Filename */
	protected $filename;
	/** @var Prefix of the connection for writing */
	protected $writeprefix;
	/** @var Prefix of the connection for reading */
	protected $readprefix;
	/** @var Read Processing method: gz, bz, f
	 *			If a scheme is detected, fopen will be defaulted
	 * 			To use compression with a network stream use a filter
	 */
	protected $processingmethod = 'f';
	/** @var array Filters applied to the current stream */
	protected $filters = Array();
	
	// Private vars
	/** @var File Handle */
	private $_fh;
	/** @var File size */
	private $_filesize;
	/** @var Context to use when opening the connection */
	private $_context;
	/** @var Context options; used to rebuild the context */
	private $_contextOptions;
	/** @var The mode under which the file was opened */
	private $_openmode;
	
	/**
	 * Constructor
	 * @param string Prefix of the stream; Note: unlike the JPATH_*, this has a final path seperator!
	 */
	function __construct($writeprefix='', $readprefix='', $context=Array()) {
		$this->writeprefix = $writeprefix;
		$this->readprefix = $readprefix;
		$this->_contextOptions = $context;
		$this->_buildContext();
	}

	/**
	 * Destructor
	 */
	function __destruct() {
		// attempt to close on destruction if there is a file handle
		if($this->_fh) @$this->close();
	}
	
	// ----------------------------
	// Generic File Operations
	// ----------------------------
	
	/**
	 * Open a stream with some lazy loading smarts
	 */
	function open($filename, $mode='r', $use_include_path=false, $context=null, $use_prefix=true, $strip_root=true) {
		if($use_prefix) {
			// get rid of binary or t, should be at the end of the string
			$tmode = trim($mode,'btf123456789');
			// check if its a write mode then add the appropriate prefix
			// get rid of JPATH_ROOT (legacy compat) along the way
			if(in_array($tmode, JFilesystemHelper::getWriteModes())) {
				if($strip_root && $this->writeprefix) $filename = str_replace(JPATH_ROOT, '', $filename);
				$filename = $this->writeprefix . $filename;
			} else {
				if($strip_root && $this->readprefix) $filename = str_replace(JPATH_ROOT, '', $filename);
				$filename = $this->readprefix . $filename;
			}
		}
		$this->_openmode = $mode;
		
		$url = parse_url($filename);
		$retval = false;
		if(isset($url['scheme'])) {
			// if we're dealing with a Joomla! stream, load it
			if(JFilesystemHelper::isJoomlaStream($url['scheme'])) {
				require_once(dirname(__FILE__).DS.'streams'.DS.$url['scheme'].'.php');
			}
			// we have a scheme! force the method to be f
			$this->processingmethod = 'f';
			
		}
		// Capture PHP errors
		$php_errormsg = 'Error Unknown';
		$track_errors = ini_get('track_errors');
		ini_set('track_errors', true);
		// Decide which context to use:
		switch($this->processingmethod) {
			case 'gz': // gzip doesn't support contexts or streams
				$this->_fh = gzopen($filename, $mode, $use_include_path);
				break;
			case 'bz': // bzip2 is much like gzip except it doesn't use the include path
				$this->_fh = bzopen($filename, $mode);
				break;
			case 'f': // fopen can handle streams
			default:
				if($context) {
					//  one supplied at open; overrides everything
					$this->_fh = fopen($filename, $mode, $use_include_path, $context);
				} else if ($this->_context) {
					// one provided at initialisation
					$this->_fh = fopen($filename, $mode, $use_include_path, $this->_context);
				} else {
					// no context; all defaults
					$this->_fh = fopen($filename, $mode, $use_include_path);
				}
				break;
		}
		if(!$this->_fh) {
			$this->setError($php_errormsg);
		} else {
			$this->filename = $filename;
			$retval = true;
		}
		// restore error tracking to what it was before
		ini_set('track_errors',$track_errors);
		// return the result
		return $retval;
	}

	/**
	 * Attempt to close a file handle
	 * Will return false if it failed and true on success
	 * Note: if the file is not open the system will return true
	 * Note: this function destroys the file handle
	 */
	function close() {
		if(!$this->_fh) {
			$this->setError(JText::_('File not open'));
			return true;
		}
		$retval = false;
		// Capture PHP errors
		$php_errormsg = 'Error Unknown';
		$track_errors = ini_get('track_errors');
		ini_set('track_errors', true);
		switch($this->processingmethod) {
			case 'gz':
				$res = gzclose($this->_fh);
				break;
			case 'bz':
				$res = bzclose($this->_fh);
				break;				
			case 'f':
			default:
				$res = fclose($this->_fh);
				break;
		}
		if(!$res) {
			$this->setError($php_errormsg);
		} else {
			$this->_fh = null; // reset this
			$retval = true;
		}
		// chmod the file after its closed if we wrote
		if($this->_openmode[0] == 'w') $this->chmod();
		// restore error tracking to what it was before
		ini_set('track_errors',$track_errors);
		// return the result
		return $retval;
	}

	/**
	 * Work out if we're at the end of the file for a stream
	 */
	function eof() { 
		if(!$this->_fh) {
			$this->setError(JText::_('File not open'));
			return false;
		}
		$retval = false;
		// Capture PHP errors
		$php_errormsg = '';
		$track_errors = ini_get('track_errors');
		ini_set('track_errors', true);
		switch($this->processingmethod) {
			case 'gz':
				$res = gzeof($this->_fh);
				break;
			case 'bz':
			case 'f':
			default:
				$res = feof($this->_fh);
				break;
		}
		if($php_errormsg) {
			$this->setError($php_errormsg);
		}
		// restore error tracking to what it was before
		ini_set('track_errors',$track_errors);
		// return the result
		return $res;
	}

	/**
	 * Retrieve the file size of the path
	 */
	function filesize() {
		if(!$this->filename) {
			$this->setError(JText::_('File not open'));
			return false;
		}
		$retval = false;
		// Capture PHP errors
		$php_errormsg = '';
		$track_errors = ini_get('track_errors');
		ini_set('track_errors', true);
		$res = @filesize($this->filename);
		if(!$res) {
			$tmp_error = '';
			if($php_errormsg) { // some bad went wrong
				$tmp_error = $php_errormsg; // store the error in case we need it
			}
			$res = JFilesystemHelper::remotefsize($this->filename);
			if(!$res) {
				if($tmp_error) { // use the php_errormsg from before
					$this->setError($tmp_error);
				} else  { // error but nothing from php? how strange! create our own
					$this->setError(JText::_('Failed to get file size. This may not work for all streams!'));
				}
			} else {
				$this->_filesize = $res;
				$retval = $res;
			}
		} else {
			$this->_filesize = $res;
			$retval = $res;
		}
		// restore error tracking to what it was before
		ini_set('track_errors',$track_errors);
		// return the result
		return $retval;
	}

	/**
	 * Read a file
	 * Handles user space streams appropriately otherwise any read will return 8192
	 * @see http://www.php.net/manual/en/function.fread.php
	 */
	function read($length=0) {
		if(!$this->_filesize && !$length) {
			$this->filesize(); // get the filesize
			if(!$this->_filesize) {
				//$this->setError(JText::_('No filesize detected; Try specifying a filesize'));
				//return false;
				$length = -1; // set it to the biggest and then wait until eof
			} else {
				$length = $this->_filesize;
			}
		}
		if(!$this->_fh) {
			$this->setError(JText::_('File not open'));
			return false;
		}
		$retval = false;
		// Capture PHP errors
		$php_errormsg = 'Error Unknown';
		$track_errors = ini_get('track_errors');
		ini_set('track_errors', true);
		$remaining = $length;
		do {
			// do chunked reads where relevant
			switch($this->processingmethod) {
				case 'bz':
					$res = ($remaining > 0) ? bzread($this->_fh, $remaining) : bzread($this->_fh, $this->chunksize);
					break;
				case 'gz':
					$res = ($remaining > 0) ? gzread($this->_fh, $remaining) : gzread($this->_fh, $this->chunksize);
					break;
				case 'f':
				default:
					$res = ($remaining > 0) ? fread($this->_fh, $remaining) : fread($this->_fh, $this->chunksize);
					break;
			}
			if(!$res) {
				$this->setError($php_errormsg);
				$remaining = 0; // jump from the loop
			} else {
				if(!$retval) $retval = '';
				$retval .= $res;
				if(!$this->eof()) {
					$len = strlen($res);
					$remaining -= $len;
				} else {
					// if its the end of the file then we've nothing left to read; reset remaining and len
					$remaining = 0;
					$length = strlen($retval);
				}
			}
		} while($remaining || !$length);
		// restore error tracking to what it was before
		ini_set('track_errors',$track_errors);
		// return the result
		return $retval;
	}

	/**
	 * Seek the file
	 * Note: the return value is different to that of fseek
	 * @return boolean True on success, false on failure
	 * @see http://www.php.net/manual/en/function.fseek.php
	 */
	function seek($offset, $whence) { 
		if(!$this->_fh) {
			$this->setError(JText::_('File not open'));
			return false;
		}
		$retval = false;
		// Capture PHP errors
		$php_errormsg = '';
		$track_errors = ini_get('track_errors');
		ini_set('track_errors', true);
		switch($this->processingmethod) {
			case 'gz':
				$res = gzseek($this->_fh, $offset, $whence);
				break;
			case 'bz':
			case 'f':
			default:
				$res = fseek($this->_fh, $offset, $whence);
				break;
		}
		// seek, interestingly returns 0 on success or -1 on failure
		if($res == -1) {
			$this->setError($php_errormsg);
		} else {
			$retval = true;
		}
			
		// restore error tracking to what it was before
		ini_set('track_errors',$track_errors);
		// return the result
		return $retval;
	}

	/**
	 * File write
	 * Note: Whilst this function accepts a reference, the underlying fwrite
	 * will do a copy! This will roughly double the memory allocation for
	 * any write you do. Specifying chunked will get around this by only
	 * writing in specific chunk sizes. This defaults to 8192 which is a
	 * sane number to use most of the time (change the default with 
	 * JStream::set('chunksize', newsize);)
	 * Note: This doesn't support gzip/bzip2 writing like reading does
	 * @see http://www.php.net/manual/en/function.fwrite.php
	 */
	function write(&$string, $length=0, $chunk=0) {
		if(!$this->_fh) {
			$this->setError(JText::_('File not open'));
			return false;
		}
		// if the length isn't set, set it to the length of the string
		if(!$length) $length = strlen($string);
		// if the chunk isn't set, set it to the default
		if(!$chunk) $chunk = $this->chunksize; 
		$retval = true;
		// Capture PHP errors
		$php_errormsg = '';
		$track_errors = ini_get('track_errors');
		ini_set('track_errors', true);
		$remaining = $length;
		do {
			// if the amount remaining is greater than the chunk size, then use the chunk
			$amount = ($remaining > $chunk) ? $chunk : $remaining;
			$res = fwrite($this->_fh, $string, $amount);
			// seek, interestingly returns 0 on success or -1 on failure
			if(!$res) {
				$this->setError($php_errormsg);
				$retval = false;
				$remaining = 0;
			} else {
				$remaining -= $res;
			}
		} while($remaining);
		
		// restore error tracking to what it was before
		ini_set('track_errors',$track_errors);
		// return the result
		return $retval;
	}
	
	/**
	 * chmod wrapper
	 */
	function chmod($mode=0) {
		if(!isset($this->filename) || !$this->filename) {
			$this->setError(JText::_('Filename not set'));
			return false;
		}
		// if no mode is set use the default
		if(!$mode) $mode = $this->filemode;
		$retval = false;
		// Capture PHP errors
		$php_errormsg = '';
		$track_errors = ini_get('track_errors');
		ini_set('track_errors', true);
		$sch = parse_url($this->filename, PHP_URL_SCHEME);
		// scheme specific options; ftp's chmod support is fun
		switch($sch) {
			case 'ftp': 
			case 'ftps':
				$res = JFilesystemHelper::ftpChmod($this->filename, $mode);
				break;
			default:
				$res = chmod($this->filename, $mode);
				break;
		}
		// seek, interestingly returns 0 on success or -1 on failure
		if(!$res) {
			$this->setError($php_errormsg);
		} else {
			$retval = true;
		}
		// restore error tracking to what it was before
		ini_set('track_errors',$track_errors);
		// return the result
		return $retval;
	}
	
	// TODO: Everything from here down :)
	// ----------------------------
	// Stream contexts
	// ----------------------------
	
    /**
     * Rebuilds the context
     */
	function _buildContext() {
		$this->_context = stream_context_create($this->_contextOptions);
	}
	
	/**
	 * Updates the context to the array
	 * Format is the same as stream_context_create
	 * @see http://www.php.net/stream_context_create
	 */
	function setContext($context) {

	}
	
	function addContextEntry($wrapper, $name, $value) {

	}
	
	function deleteContextEntry($wrapper, $name) {

	}    
	
	// ----------------------------
	// Stream filters
	// ----------------------------

	/**
	 * Append a filter to the chain
	 * @see http://www.php.net/manual/en/function.stream-filter-append.php
	 */	
	function appendFilter($stream, $filtername, $read_write=STREAM_FILTER_READ, $params=Array() ) {
		
	}
	
	function prependFilter($stream, $filtername, $read_write=STREAM_FILTER_READ, $params=Array() ) {

	}
	
	/**
	 * Remove a filter, either by resource (handed out from the
	 * append or prepend function alternatively via getting the
	 * filter list) 
	 */
	function removeFilter($resource, $byindex=false) {

	}
    
}