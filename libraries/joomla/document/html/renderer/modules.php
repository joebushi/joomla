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
 * JDocument Modules renderer
 *
 * @author		Johan Janssens <johan.janssens@joomla.org>
 * @package		Joomla.Framework
 * @subpackage	Document
 * @since		1.5
 */
class JDocumentRendererModules extends JDocumentRenderer
{
	/**
	 * Renders multiple modules script and returns the results as a string
	 *
	 * @access public
	 * @param string 	$name		The position of the modules to render
	 * @param array 	$params		Associative array of values
	 * @return string	The output of the script
	 */
	function render( $position, $params = array(), $content = null )
	{
		$contents = '';
		$user =& JFactory::getUser();
		if(!isset($params['cache']) || $params['cache'] == true) {
			$cachable = true;
		} else {
			$cachable = false;
		} 

		if( $cachable ) {
			//Caching module positions is possible, generate the cache id
			$cache = JFactory::getCache('modules', 'object');
			$cacheid = md5(
				JRequest::getInt('Itemid', 0) 
				. strtoupper( $position ) 
				. serialize($params) 
				. $content 
				. $user->get('aid', 0)
			);
			$data = $cache->get($cacheid, 'modules');
			if($data) {
				return $data;
			}
			//data isn't there, generate it
			$renderer =&  $this->_doc->loadRenderer('module');
			$cachable = true;
			$ttl = JFactory::getConfig()->getValue('config.cachetime');
			foreach (JModuleHelper::getModules($position) as $mod)  {
				$contents .= $renderer->render($mod, $params, $content);
				$mod_params = new JParameter($mod->params);
				if( !$mod_params->get('cache') ) {
					$cachable = false;
				} else {
					$ttl = min($ttl, $mod_params->get('cache_time', $ttl));
				}
			}
			if($cachable) {
				$cache->store($contents, $cacheid, 'modules', $ttl);
			}
		} else {
			$renderer =&  $this->_doc->loadRenderer('module');
			foreach (JModuleHelper::getModules($position) as $mod)  {
				$contents .= $renderer->render($mod, $params, $content);
			}
		}

		return $contents;
	}
}
