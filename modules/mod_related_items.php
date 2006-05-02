<?php
/**
* @version $Id: mod_related_items.php,v 1.2 2005/08/29 15:52:20 alekandreev Exp $
* @package Mambo
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

class modRelatedItemsData {

	function &getLists( &$params ) {
		global $my, $mainframe, $database;

		$option 			= trim( mosGetParam( $_REQUEST, 'option', null ) );
		$task 				= trim( mosGetParam( $_REQUEST, 'task', null ) );
		$id 				= intval( mosGetParam( $_REQUEST, 'id', null ) );
		$moduleclass_sfx 	= $params->get( 'moduleclass_sfx' );

		if ( $option == 'com_content' && $task == 'view' && $id ) {

			// select the meta keywords from the item
			$query = "SELECT metakey"
			. "\n FROM #__content"
			. "\n WHERE id = '$id' AND active = 1"
			;
			$database->setQuery( $query );
			if ( $metakey = trim( $database->loadResult() ) ) {
				// explode the meta keys on a comma
				$keys 	= explode( ',', $metakey );
				$likes 	= array();

				// assemble any non-blank word(s)
				foreach ( $keys as $key ) {
					$key = trim( $key );
					if ($key) {
						$likes[] = $database->getEscaped( $key );
					}
				}

				if ( count( $likes ) ) {
					// select other items based on the metakey field 'like' the keys found
					$query = "SELECT id, title"
					. "\n FROM #__content"
					. "\n WHERE id <> $id"
					. "\n AND state = 1"
					. "\n AND access <= $my->gid AND active = 1"
					. "\n AND ( metakey LIKE '%";
					$query .= implode( "%' OR metakey LIKE '%", $likes );
					$query .= "%')";
					$database->setQuery( $query );
					if ( $related = $database->loadObjectList() ) {
						$i = 0;
						foreach ($related as $item) {
							if ( $option = 'com_content' && $task = 'view' ) {
								$Itemid = $mainframe->getItemid( $item->id );
							}

							$lists[$i]->link = sefRelToAbs( 'index.php?option=com_content&task=view&id='. $item->id .'&Itemid='. $Itemid );
							$lists[$i]->text = $item->title;
							$i++;
						}
					}
				}
			}
		}

		return $lists;
	}
}


class modRelatedItems {

	function show( &$params ){
		global $my;
		$cache  = mosFactory::getCache( "mod_related_items" );

		$cache->setCaching($params->get('cache', 1));
		$cache->setCacheValidation(false);

		$cache->callId( "modRelatedItems::_display", array( $params ), "mod_related_items".$my->gid );
	}

	function _display( &$params ) {

		$lists = modRelatedItemsData::getLists( $params );

		$tmpl =& moduleScreens::createTemplate( 'mod_related_items.html' );

		$tmpl->addVar( 'mod_related_items', 'class', $params->get( 'moduleclass_sfx' ) );

		$tmpl->addObject( 'mod_related_items',''  );
		$tmpl->addObject( 'mod_related_items-items', $lists, 'row_' );

		$tmpl->displayParsedTemplate( 'mod_related_items' );
	}
}

modRelatedItems::show( $params );
?>
