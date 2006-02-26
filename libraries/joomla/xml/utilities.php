<?PHP
/**
 * @version $Id: loader.php 1400 2005-12-09 15:34:58Z Jinx $
 * @package Joomla
 * @subpackage Libraries
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

/**
 * error code for invalid chars in XML name
 */
define("JXMLUtilities_ERROR_INVALID_CHARS", 51);

/**
 * error code for invalid chars in XML name
 */
define("JXMLUtilities_ERROR_INVALID_START", 52);

/**
 * error code for non-scalar tag content
 */
define("JXMLUtilities_ERROR_NON_SCALAR_CONTENT", 60);

/**
 * error code for missing tag name
 */
define("JXMLUtilities_ERROR_NO_TAG_NAME", 61);

/**
 * replace XML entities
 */
define("JXMLUtilities_REPLACE_ENTITIES", 1);

/**
 * embedd content in a CData Section
 */
define("JXMLUtilities_CDATA_SECTION", 5);

/**
 * do not replace entitites
 */
define("JXMLUtilities_ENTITIES_NONE", 0);

/**
 * replace all XML entitites
 * This setting will replace <, >, ", ' and &
 */
define("JXMLUtilities_ENTITIES_XML", 1);

/**
 * replace only required XML entitites
 * This setting will replace <, " and &
 */
define("JXMLUtilities_ENTITIES_XML_REQUIRED", 2);

/**
 * replace HTML entitites
 * @link    http://www.php.net/htmlentities
 */
define("JXMLUtilities_ENTITIES_HTML", 3);

/**
 * Collapse all empty tags.
 */
define("JXMLUtilities_COLLAPSE_ALL", 1);

/**
 * Collapse only empty XHTML tags that have no end tag.
 */
define("JXMLUtilities_COLLAPSE_XHTML_ONLY", 2);

/**
 * Utility class for working with XML documents ported from PEAR to Joomla
 * 
 * This class draws heavily on work by Stephan Schmidt <schst@php.net> and is based
 * on the PEAR JXMLUtilities class
 *
 * @category XML
 * @package  JXMLUtilities
 * @version  1.1.0
 * @author   Stephan Schmidt <schst@php.net>
 */
class JXMLUtilities extends JObject {

	/**
	 * replace XML entities
	 *
	 * With the optional second parameter, you may select, which
	 * entities should be replaced.
	 *
	 * <code>
	 * jimport('joomla.jxml.utilities');
	 * 
	 * // replace XML entites:
	 * $string = JXMLUtilities::replaceEntities("This string contains < & >.");
	 * </code>
	 *
	 * @access   public
	 * @static
	 * @param    string  string where XML special chars should be replaced
	 * @param    integer setting for entities in attribute values (one of JXMLUtilities_ENTITIES_XML, JXMLUtilities_ENTITIES_XML_REQUIRED, JXMLUtilities_ENTITIES_HTML)
	 * @return   string  string with replaced chars
	 * @see      reverseEntities()
	 */
	function replaceEntities($string, $replaceEntities = JXMLUtilities_ENTITIES_XML) {
		switch ($replaceEntities) {
			case JXMLUtilities_ENTITIES_XML :
				$string = strtr($string, array ('&' => '&amp;', '>' => '&gt;', '<' => '&lt;', '"' => '&quot;', '\'' => '&apos;'));
				break;
			case JXMLUtilities_ENTITIES_XML_REQUIRED :
				$string = strtr($string, array ('&' => '&amp;', '<' => '&lt;', '"' => '&quot;'));
				break;
			case JXMLUtilities_ENTITIES_HTML :
				$string = htmlentities($string);
				break;
		}
		return $string;
	}

	/**
	 * reverse XML entities
	 *
	 * With the optional second parameter, you may select, which
	 * entities should be reversed.
	 *
	 * <code>
	 * jimport('joomla.jxml.utilities');
	 * 
	 * // reverse XML entites:
	 * $string = JXMLUtilities::reverseEntities("This string contains &lt; &amp; &gt;.");
	 * </code>
	 *
	 * @access   public
	 * @static
	 * @param    string  string where XML special chars should be replaced
	 * @param    integer setting for entities in attribute values (one of JXMLUtilities_ENTITIES_XML, JXMLUtilities_ENTITIES_XML_REQUIRED, JXMLUtilities_ENTITIES_HTML)
	 * @return   string  string with replaced chars
	 * @see      replaceEntities()
	 */
	function reverseEntities($string, $replaceEntities = JXMLUtilities_ENTITIES_XML) {
		switch ($replaceEntities) {
			case JXMLUtilities_ENTITIES_XML :
				$string = strtr($string, array ('&amp;' => '&', '&gt;' => '>', '&lt;' => '<', '&quot;' => '"', '&apos;' => '\''));
				break;
			case JXMLUtilities_ENTITIES_XML_REQUIRED :
				$string = strtr($string, array ('&amp;' => '&', '&lt;' => '<', '&quot;' => '"'));
				break;
			case JXMLUtilities_ENTITIES_HTML :
				$arr = array_flip(get_html_translation_table(HTML_ENTITIES));
				$string = strtr($string, $arr);
				break;
		}
		return $string;
	}

	/**
	 * build an xml declaration
	 *
	 * <code>
	 * jimport('joomla.jxml.utilities');
	 * 
	 * // get an XML declaration:
	 * $xmlDecl = JXMLUtilities::getXMLDeclaration("1.0", "UTF-8", true);
	 * </code>
	 *
	 * @access   public
	 * @static
	 * @param    string  $version     xml version
	 * @param    string  $encoding    character encoding
	 * @param    boolean $standAlone  document is standalone (or not)
	 * @return   string  $decl xml declaration
	 * @uses     JXMLUtilities::attributesToString() to serialize the attributes of the XML declaration
	 */
	function getXMLDeclaration($version = "1.0", $encoding = null, $standalone = null) {
		$attributes = array ("version" => $version,);
		// add encoding
		if ($encoding !== null) {
			$attributes["encoding"] = $encoding;
		}
		// add standalone, if specified
		if ($standalone !== null) {
			$attributes["standalone"] = $standalone ? "yes" : "no";
		}

		return sprintf("<?xml%s?>", JXMLUtilities :: attributesToString($attributes, false));
	}

	/**
	 * build a document type declaration
	 *
	 * <code>
	 * jimport('joomla.jxml.utilities');
	 * 
	 * // get a doctype declaration:
	 * $xmlDecl = JXMLUtilities::getDocTypeDeclaration("rootTag","myDocType.dtd");
	 * </code>
	 *
	 * @access   public
	 * @static
	 * @param    string  $root         name of the root tag
	 * @param    string  $uri          uri of the doctype definition (or array with uri and public id)
	 * @param    string  $internalDtd  internal dtd entries   
	 * @return   string  $decl         doctype declaration
	 * @since    0.2
	 */
	function getDocTypeDeclaration($root, $uri = null, $internalDtd = null) {
		if (is_array($uri)) {
			$ref = sprintf(' PUBLIC "%s" "%s"', $uri["id"], $uri["uri"]);
		}
		elseif (!empty ($uri)) {
			$ref = sprintf(' SYSTEM "%s"', $uri);
		} else {
			$ref = "";
		}

		if (empty ($internalDtd)) {
			return sprintf("<!DOCTYPE %s%s>", $root, $ref);
		} else {
			return sprintf("<!DOCTYPE %s%s [\n%s\n]>", $root, $ref, $internalDtd);
		}
	}

	/**
	 * create string representation of an attribute list
	 *
	 * <code>
	 * jimport('joomla.jxml.utilities');
	 * 
	 * // build an attribute string
	 * $att = array(
	 *              "foo"   =>  "bar",
	 *              "argh"  =>  "tomato"
	 *            );
	 *
	 * $attList = JXMLUtilities::attributesToString($att);    
	 * </code>
	 *
	 * @access   public
	 * @static
	 * @param    array         $attributes        attribute array
	 * @param    boolean|array $sort              sort attribute list alphabetically, may also be an assoc array containing the keys 'sort', 'multiline', 'indent', 'linebreak' and 'entities'
	 * @param    boolean       $multiline         use linebreaks, if more than one attribute is given
	 * @param    string        $indent            string used for indentation of multiline attributes
	 * @param    string        $linebreak         string used for linebreaks of multiline attributes
	 * @param    integer       $entities          setting for entities in attribute values (one of JXMLUtilities_ENTITIES_NONE, JXMLUtilities_ENTITIES_XML, JXMLUtilities_ENTITIES_XML_REQUIRED, JXMLUtilities_ENTITIES_HTML)
	 * @return   string                           string representation of the attributes
	 * @uses     JXMLUtilities::replaceEntities() to replace XML entities in attribute values
	 * @todo     allow sort also to be an options array
	 */
	function attributesToString($attributes, $sort = true, $multiline = false, $indent = '    ', $linebreak = "\n", $entities = JXMLUtilities_ENTITIES_XML) {
		/**
		 * second parameter may be an array
		 */
		if (is_array($sort)) {
			if (isset ($sort['multiline'])) {
				$multiline = $sort['multiline'];
			}
			if (isset ($sort['indent'])) {
				$indent = $sort['indent'];
			}
			if (isset ($sort['linebreak'])) {
				$multiline = $sort['linebreak'];
			}
			if (isset ($sort['entities'])) {
				$entities = $sort['entities'];
			}
			if (isset ($sort['sort'])) {
				$sort = $sort['sort'];
			} else {
				$sort = true;
			}
		}
		$string = '';
		if (is_array($attributes) && !empty ($attributes)) {
			if ($sort) {
				ksort($attributes);
			}
			if (!$multiline || count($attributes) == 1) {
				foreach ($attributes as $key => $value) {
					if ($entities != JXMLUtilities_ENTITIES_NONE) {
						if ($entities === JXMLUtilities_CDATA_SECTION) {
							$entities = JXMLUtilities_ENTITIES_XML;
						}
						$value = JXMLUtilities :: replaceEntities($value, $entities);
					}
					$string .= ' '.$key.'="'.$value.'"';
				}
			} else {
				$first = true;
				foreach ($attributes as $key => $value) {
					if ($entities != JXMLUtilities_ENTITIES_NONE) {
						$value = JXMLUtilities :: replaceEntities($value, $entities);
					}
					if ($first) {
						$string .= " ".$key.'="'.$value.'"';
						$first = false;
					} else {
						$string .= $linebreak.$indent.$key.'="'.$value.'"';
					}
				}
			}
		}
		return $string;
	}

	/**
	 * Collapses empty tags.
	 *
	 * @access   public
	 * @static
	 * @param    string  $xml  XML
	 * @param    integer $mode Whether to collapse all empty tags (JXMLUtilities_COLLAPSE_ALL) or only XHTML (JXMLUtilities_COLLAPSE_XHTML_ONLY) ones.
	 * @return   string  $xml  XML
	 */
	function collapseEmptyTags($xml, $mode = JXMLUtilities_COLLAPSE_ALL) {
		if ($mode == JXMLUtilities_COLLAPSE_XHTML_ONLY) {
			return preg_replace('/<(area|base|br|col|hr|img|input|link|meta|param)([^>]*)><\/\\1>/s', '<\\1\\2 />', $xml);
		} else {
			return preg_replace('/<(\w+)([^>]*)><\/\\1>/s', '<\\1\\2 />', $xml);
		}
	}

	/**
	 * create a tag
	 *
	 * This method will call JXMLUtilities::createTagFromArray(), which
	 * is more flexible.
	 *
	 * <code>
	 * jimport('joomla.jxml.utilities');
	 * 
	 * // create an XML tag:
	 * $tag = JXMLUtilities::createTag("myNs:myTag", array("foo" => "bar"), "This is inside the tag", "http://www.w3c.org/myNs#");
	 * </code>
	 *
	 * @access   public
	 * @static
	 * @param    string  $qname             qualified tagname (including namespace)
	 * @param    array   $attributes        array containg attributes
	 * @param    mixed   $content
	 * @param    string  $namespaceUri      URI of the namespace
	 * @param    integer $replaceEntities   whether to replace XML special chars in content, embedd it in a CData section or none of both
	 * @param    boolean $multiline         whether to create a multiline tag where each attribute gets written to a single line
	 * @param    string  $indent            string used to indent attributes (_auto indents attributes so they start at the same column)
	 * @param    string  $linebreak         string used for linebreaks
	 * @return   string  $string            XML tag
	 * @see      JXMLUtilities::createTagFromArray()
	 * @uses     JXMLUtilities::createTagFromArray() to create the tag
	 */
	function createTag($qname, $attributes = array (), $content = null, $namespaceUri = null, $replaceEntities = JXMLUtilities_REPLACE_ENTITIES, $multiline = false, $indent = "_auto", $linebreak = "\n") {
		$tag = array ("qname" => $qname, "attributes" => $attributes);

		// add tag content
		if ($content !== null) {
			$tag["content"] = $content;
		}

		// add namespace Uri
		if ($namespaceUri !== null) {
			$tag["namespaceUri"] = $namespaceUri;
		}

		return JXMLUtilities :: createTagFromArray($tag, $replaceEntities, $multiline, $indent, $linebreak);
	}

	/**
	 * create a tag from an array
	 * this method awaits an array in the following format
	 * <pre>
	 * array(
	 *  "qname"        => $qname         // qualified name of the tag
	 *  "namespace"    => $namespace     // namespace prefix (optional, if qname is specified or no namespace)
	 *  "localpart"    => $localpart,    // local part of the tagname (optional, if qname is specified)
	 *  "attributes"   => array(),       // array containing all attributes (optional)
	 *  "content"      => $content,      // tag content (optional)
	 *  "namespaceUri" => $namespaceUri  // namespaceUri for the given namespace (optional)
	 *   )
	 * </pre>
	 *
	 * <code>
	 * jimport('joomla.jxml.utilities');
	 * 
	 * $tag = array(
	 *           "qname"        => "foo:bar",
	 *           "namespaceUri" => "http://foo.com",
	 *           "attributes"   => array( "key" => "value", "argh" => "fruit&vegetable" ),
	 *           "content"      => "I'm inside the tag",
	 *            );
	 * // creating a tag with qualified name and namespaceUri
	 * $string = JXMLUtilities::createTagFromArray($tag);
	 * </code>
	 *
	 * @access   public
	 * @static
	 * @param    array   $tag               tag definition
	 * @param    integer $replaceEntities   whether to replace XML special chars in content, embedd it in a CData section or none of both
	 * @param    boolean $multiline         whether to create a multiline tag where each attribute gets written to a single line
	 * @param    string  $indent            string used to indent attributes (_auto indents attributes so they start at the same column)
	 * @param    string  $linebreak         string used for linebreaks
	 * @return   string  $string            XML tag
	 * @see      JXMLUtilities::createTag()
	 * @uses     JXMLUtilities::attributesToString() to serialize the attributes of the tag
	 * @uses     JXMLUtilities::splitQualifiedName() to get local part and namespace of a qualified name
	 */
	function createTagFromArray($tag, $replaceEntities = JXMLUtilities_REPLACE_ENTITIES, $multiline = false, $indent = "_auto", $linebreak = "\n") {
		if (isset ($tag['content']) && !is_scalar($tag['content'])) {
			return JXMLUtilities :: raiseError('Supplied non-scalar value as tag content', JXMLUtilities_ERROR_NON_SCALAR_CONTENT);
		}

		if (!isset ($tag['qname']) && !isset ($tag['localPart'])) {
			return JXMLUtilities :: raiseError('You must either supply a qualified name (qname) or local tag name (localPart).', JXMLUtilities_ERROR_NO_TAG_NAME);
		}

		// if no attributes hav been set, use empty attributes
		if (!isset ($tag["attributes"]) || !is_array($tag["attributes"])) {
			$tag["attributes"] = array ();
		}

		// qualified name is not given
		if (!isset ($tag["qname"])) {
			// check for namespace
			if (isset ($tag["namespace"]) && !empty ($tag["namespace"])) {
				$tag["qname"] = $tag["namespace"].":".$tag["localPart"];
			} else {
				$tag["qname"] = $tag["localPart"];
			}
			// namespace URI is set, but no namespace
		}
		elseif (isset ($tag["namespaceUri"]) && !isset ($tag["namespace"])) {
			$parts = JXMLUtilities :: splitQualifiedName($tag["qname"]);
			$tag["localPart"] = $parts["localPart"];
			if (isset ($parts["namespace"])) {
				$tag["namespace"] = $parts["namespace"];
			}
		}

		if (isset ($tag["namespaceUri"]) && !empty ($tag["namespaceUri"])) {
			// is a namespace given
			if (isset ($tag["namespace"]) && !empty ($tag["namespace"])) {
				$tag["attributes"]["xmlns:".$tag["namespace"]] = $tag["namespaceUri"];
			} else {
				// define this Uri as the default namespace
				$tag["attributes"]["xmlns"] = $tag["namespaceUri"];
			}
		}

		// check for multiline attributes
		if ($multiline === true) {
			if ($indent === "_auto") {
				$indent = str_repeat(" ", (strlen($tag["qname"]) + 2));
			}
		}

		// create attribute list
		$attList = JXMLUtilities :: attributesToString($tag['attributes'], true, $multiline, $indent, $linebreak, $replaceEntities);
		if (!isset ($tag['content']) || (string) $tag['content'] == '') {
			$tag = sprintf('<%s%s />', $tag['qname'], $attList);
		} else {
			switch ($replaceEntities) {
				case JXMLUtilities_ENTITIES_NONE :
					break;
				case JXMLUtilities_CDATA_SECTION :
					$tag['content'] = JXMLUtilities :: createCDataSection($tag['content']);
					break;
				default :
					$tag['content'] = JXMLUtilities :: replaceEntities($tag['content'], $replaceEntities);
					break;
			}
			$tag = sprintf('<%s%s>%s</%s>', $tag['qname'], $attList, $tag['content'], $tag['qname']);
		}
		return $tag;
	}

	/**
	 * create a start element
	 *
	 * <code>
	 * jimport('joomla.jxml.utilities');
	 * 
	 * // create an XML start element:
	 * $tag = JXMLUtilities::createStartElement("myNs:myTag", array("foo" => "bar") ,"http://www.w3c.org/myNs#");
	 * </code>
	 *
	 * @access   public
	 * @static
	 * @param    string  $qname             qualified tagname (including namespace)
	 * @param    array   $attributes        array containg attributes
	 * @param    string  $namespaceUri      URI of the namespace
	 * @param    boolean $multiline         whether to create a multiline tag where each attribute gets written to a single line
	 * @param    string  $indent            string used to indent attributes (_auto indents attributes so they start at the same column)
	 * @param    string  $linebreak         string used for linebreaks
	 * @return   string  $string            XML start element
	 * @see      JXMLUtilities::createEndElement(), JXMLUtilities::createTag()
	 */
	function createStartElement($qname, $attributes = array (), $namespaceUri = null, $multiline = false, $indent = '_auto', $linebreak = "\n") {
			// if no attributes hav been set, use empty attributes
	if (!isset ($attributes) || !is_array($attributes)) {
			$attributes = array ();
		}

		if ($namespaceUri != null) {
			$parts = JXMLUtilities :: splitQualifiedName($qname);
		}

		// check for multiline attributes
		if ($multiline === true) {
			if ($indent === "_auto") {
				$indent = str_repeat(" ", (strlen($qname) + 2));
			}
		}

		if ($namespaceUri != null) {
			// is a namespace given
			if (isset ($parts["namespace"]) && !empty ($parts["namespace"])) {
				$attributes["xmlns:".$parts["namespace"]] = $namespaceUri;
			} else {
				// define this Uri as the default namespace
				$attributes["xmlns"] = $namespaceUri;
			}
		}

		// create attribute list
		$attList = JXMLUtilities :: attributesToString($attributes, true, $multiline, $indent, $linebreak);
		$element = sprintf("<%s%s>", $qname, $attList);
		return $element;
	}

	/**
	 * create an end element
	 *
	 * <code>
	 * jimport('joomla.jxml.utilities');
	 * 
	 * // create an XML start element:
	 * $tag = JXMLUtilities::createEndElement("myNs:myTag");
	 * </code>
	 *
	 * @access   public
	 * @static
	 * @param    string  $qname             qualified tagname (including namespace)
	 * @return   string  $string            XML end element
	 * @see      JXMLUtilities::createStartElement(), JXMLUtilities::createTag()
	 */
	function createEndElement($qname) {
		$element = sprintf("</%s>", $qname);
		return $element;
	}

	/**
	 * create an XML comment
	 *
	 * <code>
	 * jimport('joomla.jxml.utilities');
	 * 
	 * // create an XML start element:
	 * $tag = JXMLUtilities::createComment("I am a comment");
	 * </code>
	 *
	 * @access   public
	 * @static
	 * @param    string  $content           content of the comment
	 * @return   string  $comment           XML comment
	 */
	function createComment($content) {
		$comment = sprintf("<!-- %s -->", $content);
		return $comment;
	}

	/**
	 * create a CData section
	 *
	 * <code>
	 * jimport('joomla.jxml.utilities');
	 * 
	 * // create a CData section
	 * $tag = JXMLUtilities::createCDataSection("I am content.");
	 * </code>
	 *
	 * @access   public
	 * @static
	 * @param    string  $data              data of the CData section
	 * @return   string  $string            CData section with content
	 */
	function createCDataSection($data) {
		return sprintf("<![CDATA[%s]]>", $data);
	}

	/**
	 * split qualified name and return namespace and local part
	 *
	 * <code>
	 * require_once 'XML/Util.php';
	 * 
	 * // split qualified tag
	 * $parts = JXMLUtilities::splitQualifiedName("xslt:stylesheet");
	 * </code>
	 * the returned array will contain two elements:
	 * <pre>
	 * array(
	 *       "namespace" => "xslt",
	 *       "localPart" => "stylesheet"
	 *      );
	 * </pre>
	 *
	 * @access public
	 * @static
	 * @param  string    $qname      qualified tag name
	 * @param  string    $defaultNs  default namespace (optional)
	 * @return array     $parts      array containing namespace and local part
	 */
	function splitQualifiedName($qname, $defaultNs = null) {
		if (strstr($qname, ':')) {
			$tmp = explode(":", $qname);
			return array ("namespace" => $tmp[0], "localPart" => $tmp[1]);
		}
		return array ("namespace" => $defaultNs, "localPart" => $qname);
	}

	/**
	 * check, whether string is valid XML name
	 *
	 * <p>XML names are used for tagname, attribute names and various
	 * other, lesser known entities.</p>
	 * <p>An XML name may only consist of alphanumeric characters,
	 * dashes, undescores and periods, and has to start with a letter
	 * or an underscore.
	 * </p>
	 *
	 * <code>
	 * jimport('joomla.jxml.utilities');
	 * 
	 * // verify tag name
	 * $result = JXMLUtilities::isValidName("invalidTag?");
	 * if (JXMLUtilities::isError($result)) {
	 *    print "Invalid XML name: " . $result->getMessage();
	 * }
	 * </code>
	 *
	 * @access  public
	 * @static
	 * @param   string  $string string that should be checked
	 * @return  mixed   $valid  true, if string is a valid XML name, PEAR error otherwise
	 * @todo    support for other charsets
	 */
	function isValidName($string) {
		// check for invalid chars
		if (!preg_match("/^[[:alnum:]_\-.]$/", $string {
			0 })) {
			return JError :: raiseError("XML names may only start with letter or underscore", JXMLUtilities_ERROR_INVALID_START);
		}

		// check for invalid chars
		if (!preg_match("/^([a-zA-Z_]([a-zA-Z0-9_\-\.]*)?:)?[a-zA-Z_]([a-zA-Z0-9_\-\.]+)?$/", $string)) {
			return JError :: raiseError("XML names may only contain alphanumeric chars, period, hyphen, colon and underscores", JXMLUtilities_ERROR_INVALID_CHARS);
		}
		// XML name is valid
		return true;
	}
}
?>