<?php
/**
 * @version $Id: $
 * @package Joomla
 * @subpackage JFramework
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Ensure this file is included from a valid Joomla entry point
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

jimport('.joomla.jxml.transform.adapter');
jimport('.joomla.jxml.utilities');

/**
 * DocBook Adapter Handler.
 *
 * This Adapter handler provides transformations to render a subset of
 * the popular DocBook/XML markup (http://www.docbook.org/) into HTML.
 *
 * Transformations for the following DocBook tags are implemented:
 *
 *   - <artheader>
 *   - <article>
 *   - <author>
 *   - <book>
 *   - <chapter>
 *   - <emphasis>
 *   - <example>
 *   - <figure>
 *   - <filename>
 *   - <firstname>
 *   - <function>
 *   - <graphic>
 *   - <itemizedlist>
 *   - <listitem>
 *   - <orderedlist>
 *   - <para>
 *   - <programlisting>
 *   - <section>
 *   - <surname>
 *   - <title>
 *   - <ulink>
 *   - <xref>
 *
 *
 * This class draws heavily on work by Sebastian Bergmann <sb@sebastian-bergmann.de> 
 * and Kristian Köhntopp <kris@koehntopp.de> and is based on the PEAR XML_Transformer 
 * package
 *
 * @category XML Transformation
 * @package  Joomla
 * @subpackage JFramework
 * @since  1.1
 */
class JXMLTransformerAdapter_DocBook extends JXMLTransformerAdapter {

	/**
	 * @var    string
	 * @access public
	 */
	var $defaultNamespacePrefix = '&MAIN';

	/**
	 * @var    boolean
	 * @access public
	 */
	var $secondPassRequired = TRUE;

	/**
	 * @var    string
	 * @access private
	 */
	var $_author = '';

	/**
	 * @var    array
	 * @access private
	 */
	var $_context = array ();

	/**
	 * @var    string
	 * @access private
	 */
	var $_currentExampleNumber = '';

	/**
	 * @var    string
	 * @access private
	 */
	var $_currentFigureNumber = '';

	/**
	 * @var    string
	 * @access private
	 */
	var $_currentSectionNumber = '';

	/**
	 * @var    array
	 * @access private
	 */
	var $_examples = array ();

	/**
	 * @var    array
	 * @access private
	 */
	var $_figures = array ();

	/**
	 * @var    array
	 * @access private
	 */
	var $_highlightColors = array ('bg' => '#ffffff', 'comment' => '#ba8370', 'default' => '#113d73', 'html' => '#000000', 'keyword' => '#005500', 'string' => '#550000');

	/**
	 * @var    array
	 * @access private
	 */
	var $_ids = array ();

	/**
	 * @var    boolean
	 * @access private
	 */
	var $_roles = array ();

	/**
	 * @var    array
	 * @access private
	 */
	var $_secondPass = FALSE;

	/**
	 * @var    array
	 * @access private
	 */
	var $_sections = array ();

	/**
	 * @var    string
	 * @access private
	 */
	var $_title = '';

	/**
	 * @var    array
	 * @access private
	 */
	var $_xref = '';

	/**
	 * @param  array
	 * @access public
	 */
	function __construct($parameters = array ()) {
		if (isset ($parameters['highlightColors'])) {
			$this->_highlightColors = $parameters['highlightColors'];
		}

		foreach ($this->_highlightColors as $highlight => $color) {
			ini_set('highlight.'.$highlight, $color);
		}
	}

	/**
	 * @param  array
	 * @return string
	 * @access public
	 */
	function start_artheader($attributes) {
		if (!$this->_secondPass) {
			return sprintf('<artheader%s>', JXMLUtilities :: attributesToString($attributes));
		}
	}

	/**
	 * @param  string
	 * @return string
	 * @access public
	 */
	function end_artheader($cdata) {
		if (!$this->_secondPass) {
			$cdata = $cdata.'</artheader>';

			return array ($cdata, FALSE);
		}
	}

	/**
	 * @param  array
	 * @return string
	 * @access public
	 */
	function start_article($attributes) {
		return $this->_startDocument('article', $attributes);
	}

	/**
	 * @param  string
	 * @return string
	 * @access public
	 */
	function end_article($cdata) {
		return $this->_endDocument('article', $cdata);
	}

	/**
	 * @param  array
	 * @return string
	 * @access public
	 */
	function start_author($attributes) {
	}

	/**
	 * @param  string
	 * @return string
	 * @access public
	 */
	function end_author($cdata) {
		$this->_author = trim(str_replace("\n", '', $cdata));
	}

	/**
	 * @param  array
	 * @return string
	 * @access public
	 */
	function start_book($attributes) {
		return $this->_startDocument('book', $attributes);
	}

	/**
	 * @param  string
	 * @return string
	 * @access public
	 */
	function end_book($cdata) {
		return $this->_endDocument('book', $cdata);
	}

	/**
	 * @param  array
	 * @return string
	 * @access public
	 */
	function start_chapter($attributes) {
		$id = $this->_startSection('chapter', isset ($attributes['id']) ? $attributes['id'] : '');

		return '<div class="chapter">'.$id;
	}

	/**
	 * @param  string
	 * @return string
	 * @access public
	 */
	function end_chapter($cdata) {
		$this->_endSection('chapter');

		return $cdata.'</div>';
	}

	/**
	 * @param  array
	 * @return string
	 * @access public
	 */
	function start_emphasis($attributes) {
		$emphasisRole = isset ($attributes['role']) ? $attributes['role'] : '';

		switch ($emphasisRole) {
			case 'bold' :
			case 'strong' :
				{
					$this->_roles['emphasis'] = 'b';
				}
				break;

			default :
				{
					$this->_roles['emphasis'] = 'i';
				}
		}

		return '<'.$this->_roles['emphasis'].'>';
	}

	/**
	 * @param  string
	 * @return string
	 * @access public
	 */
	function end_emphasis($cdata) {
		$cdata = sprintf('%s</%s>', $cdata, $this->_roles['emphasis']);

		$this->_roles['emphasis'] = '';

		return $cdata;
	}

	/**
	 * @param  array
	 * @return string
	 * @access public
	 */
	function start_example($attributes) {
		$id = $this->_startSection('example', isset ($attributes['id']) ? $attributes['id'] : '');

		return '<div class="example">'.$id;
	}

	/**
	 * @param  string
	 * @return string
	 * @access public
	 */
	function end_example($cdata) {
		$this->_endSection('example');

		return $cdata.'</div>';
	}

	/**
	 * @param  array
	 * @return string
	 * @access public
	 */
	function start_figure($attributes) {
		$id = $this->_startSection('figure', isset ($attributes['id']) ? $attributes['id'] : '');

		return '<div class="figure">'.$id;
	}

	/**
	 * @param  string
	 * @return string
	 * @access public
	 */
	function end_figure($cdata) {
		$this->_endSection('figure');

		return $cdata.'</div>';
	}

	/**
	 * @param  array
	 * @return string
	 * @access public
	 */
	function start_filename($attributes) {
		return '<tt>';
	}

	/**
	 * @param  string
	 * @return string
	 * @access public
	 */
	function end_filename($cdata) {
		return trim($cdata).'</tt>';
	}

	/**
	 * @param  array
	 * @return string
	 * @access public
	 */
	function start_firstname($attributes) {
	}

	/**
	 * @param  string
	 * @return string
	 * @access public
	 */
	function end_firstname($cdata) {
		return trim($cdata);
	}

	/**
	 * @param  array
	 * @return string
	 * @access public
	 */
	function start_function($attributes) {
		return '<code><b>';
	}

	/**
	 * @param  string
	 * @return string
	 * @access public
	 */
	function end_function($cdata) {
		return array (trim($cdata).'</b></code>', FALSE);
	}

	/**
	 * @param  array
	 * @return string
	 * @access public
	 */
	function start_graphic($attributes) {
		return sprintf('<img alt="%s" border="0" src="%s"%s%s/>', isset ($attributes['srccredit']) ? $attributes['srccredit'] : '', isset ($attributes['fileref']) ? $attributes['fileref'] : '', isset ($attributes['width']) ? ' width="'.$attributes['width'].'"' : '', isset ($attributes['height']) ? ' height="'.$attributes['height'].'"' : '');
	}

	/**
	 * @param  string
	 * @return string
	 * @access public
	 */
	function end_graphic($cdata) {
		return $cdata;
	}

	/**
	 * @param  array
	 * @return string
	 * @access public
	 */
	function start_itemizedlist($attributes) {
		return '<ul>';
	}

	/**
	 * @param  string
	 * @return string
	 * @access public
	 */
	function end_itemizedlist($cdata) {
		return $cdata.'</ul>';
	}

	/**
	 * @param  array
	 * @return string
	 * @access public
	 */
	function start_listitem($attributes) {
		return '<li>';
	}

	/**
	 * @param  string
	 * @return string
	 * @access public
	 */
	function end_listitem($cdata) {
		return $cdata.'</li>';
	}

	/**
	 * @param  array
	 * @return string
	 * @access public
	 */
	function start_orderedlist($attributes) {
		return '<ol>';
	}

	/**
	 * @param  string
	 * @return string
	 * @access public
	 */
	function end_orderedlist($cdata) {
		return $cdata.'</ol>';
	}

	/**
	 * @param  array
	 * @return string
	 * @access public
	 */
	function start_para($attributes) {
		return '<p>';
	}

	/**
	 * @param  string
	 * @return string
	 * @access public
	 */
	function end_para($cdata) {
		return $cdata.'</p>';
	}

	/**
	 * @param  array
	 * @return string
	 * @access public
	 */
	function start_programlisting($attributes) {
		$this->_roles['programlisting'] = isset ($attributes['role']) ? $attributes['role'] : '';

		switch ($this->_roles['programlisting']) {
			case 'php' :
				{
					return '';
				}
				break;

			default :
				{
					return '<code>';
				}
		}
	}

	/**
	 * @param  string
	 * @return mixed
	 * @access public
	 */
	function end_programlisting($cdata) {
		switch ($this->_roles['programlisting']) {
			case 'php' :
				{
					$cdata = array (str_replace('&nbsp;', ' ', highlight_string($cdata, 1)), FALSE);
				}
				break;

			default :
				{
					$cdata = array ($cdata.'</code>', FALSE);
				}
		}

		$this->_roles['programlisting'] = '';

		return $cdata;
	}

	/**
	 * @param  array
	 * @return string
	 * @access public
	 */
	function start_section($attributes) {
		$id = $this->_startSection('section', isset ($attributes['id']) ? $attributes['id'] : '');

		return '<div class="section">'.$id;
	}

	/**
	 * @param  string
	 * @return string
	 * @access public
	 */
	function end_section($cdata) {
		$this->_endSection('section');

		return $cdata.'</div>';
	}

	/**
	 * @param  array
	 * @return string
	 * @access public
	 */
	function start_surname($attributes) {
	}

	/**
	 * @param  string
	 * @return string
	 * @access public
	 */
	function end_surname($cdata) {
		return trim($cdata);
	}

	/**
	 * @param  array
	 * @return string
	 * @access public
	 */
	function start_title($attributes) {
		switch ($this->_context[sizeof($this->_context) - 1]) {
			case 'chapter' :
			case 'section' :
				{
					return '<h2 class="title">'.$this->_currentSectionNumber.'. ';
				}
				break;

			case 'example' :
				{
					return '<h3 class="title">Example '.$this->_currentExampleNumber;
				}
				break;

			case 'figure' :
				{
					return '<h3 class="title">Figure '.$this->_currentFigureNumber;
				}
				break;
		}
	}

	/**
	 * @param  string
	 * @return string
	 * @access public
	 */
	function end_title($cdata) {
		$cdata = trim($cdata);

		if (!empty ($this->_ids[sizeof($this->_ids) - 1])) {
			$this->_xref[$this->_ids[sizeof($this->_ids) - 1]] = strip_tags($cdata);
		}

		switch ($this->_context[sizeof($this->_context) - 1]) {
			case 'article' :
			case 'book' :
				{
					$this->_title = $cdata;
				}
				break;

			case 'chapter' :
			case 'section' :
				{
					return $cdata.'</h2>';
				}
				break;

			case 'example' :
			case 'figure' :
				{
					return $cdata.'</h3>';
				}
				break;

			default :
				{
					return $cdata;
				}
		}
	}

	/**
	 * @param  array
	 * @return string
	 * @access public
	 */
	function start_ulink($attributes) {
		return '<a href="'.$attributes['url'].'">';
	}

	/**
	 * @param  string
	 * @return string
	 * @access public
	 */
	function end_ulink($cdata) {
		return $cdata.'</a>';
	}

	/**
	 * @param  array
	 * @return string
	 * @access public
	 */
	function start_xref($attributes) {
		if ($this->_secondPass) {
			return sprintf('<a href="#%s">%s</a>', isset ($attributes['linkend']) ? $attributes['linkend'] : '', isset ($this->_xref[$attributes['linkend']]) ? $this->_xref[$attributes['linkend']] : '');
		} else {
			return sprintf('<xref%s>', JXMLUtilities :: attributesToString($attributes));
		}
	}

	/**
	 * @param  string
	 * @return string
	 * @access public
	 */
	function end_xref($cdata) {
		if (!$this->_secondPass) {
			$cdata = $cdata.'</xref>';
		}

		return array ($cdata, FALSE);
	}

	/**
	 * @param  string
	 * @param  array
	 * @return string
	 * @access private
	 */
	function _startDocument($type, $attributes) {
		if (!$this->_secondPass) {
			$id = $this->_startSection($type, isset ($attributes['id']) ? $attributes['id'] : '');

			return sprintf('<%s>%s', $type, $id);
		} else {
			return sprintf('<html><head><title>%s: %s</title><body><h1 class="title">%s: %s</h1>', $this->_author, $this->_title, $this->_author, $this->_title);
		}
	}

	/**
	 * @param  string
	 * @param  string
	 * @return string
	 * @access private
	 */
	function _endDocument($type, $cdata) {
		if (!$this->_secondPass) {
			$this->_endSection($type);

			$this->_secondPass = TRUE;

			$cdata = sprintf('%s</%s>', $cdata, $type);
		} else {
			$cdata = $cdata.'</body></html>';
		}

		return array ($cdata, FALSE);
	}

	/**
	 * @param  string
	 * @return string
	 * @access private
	 */
	function _startSection($type, $id) {
		array_push($this->_context, $type);
		array_push($this->_ids, $id);

		switch ($type) {
			case 'article' :
			case 'book' :
			case 'chapter' :
			case 'section' :
				{
					$this->_currentSectionNumber = '';

					if (!isset ($this->_sections[$type]['open'])) {
						$this->_sections[$type]['open'] = 1;
					} else {
						$this->_sections[$type]['open']++;
					}

					if (!isset ($this->_sections[$type]['id'][$this->_sections[$type]['open']])) {
						$this->_sections[$type]['id'][$this->_sections[$type]['open']] = 1;
					} else {
						$this->_sections[$type]['id'][$this->_sections[$type]['open']]++;
					}

					for ($i = 1; $i <= $this->_sections[$type]['open']; $i ++) {
						if (!empty ($this->_currentSectionNumber)) {
							$this->_currentSectionNumber .= '.';
						}

						$this->_currentSectionNumber .= $this->_sections[$type]['id'][$i];
					}
				}
				break;

			case 'example' :
				{
					if (!isset ($this->_examples[$this->_currentSectionNumber])) {
						$this->_examples[$this->_currentSectionNumber] = 1;
					} else {
						$this->_examples[$this->_currentSectionNumber]++;
					}

					$this->_currentExampleNumber = $this->_currentSectionNumber.'.'.$this->_examples[$this->_currentSectionNumber];
				}
				break;

			case 'figure' :
				{
					if (!isset ($this->_figures[$this->_currentFigureNumber])) {
						$this->_figures[$this->_currentSectionNumber] = 1;
					} else {
						$this->_figures[$this->_currentSectionNumber]++;
					}

					$this->_currentFigureNumber = $this->_currentSectionNumber.'.'.$this->_figures[$this->_currentSectionNumber];
				}
				break;
		}

		if (!empty ($id)) {
			$id = '<a id="'.$id.'" />';
		}

		return $id;
	}

	/**
	 * @param  string
	 * @access private
	 */
	function _endSection($type) {
		array_pop($this->_context);

		switch ($type) {
			case 'article' :
			case 'book' :
			case 'chapter' :
			case 'section' :
				{
					$this->_sections[$type]['open']--;
				}
				break;
		}
	}
}
?>