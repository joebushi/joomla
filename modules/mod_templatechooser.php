<?php
/**
* @version $Id: mod_templatechooser.php,v 1.1 2005/08/25 14:23:45 johanjanssens Exp $
* @package Mambo
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

class modTemplatechooserData {

	function &getParams( &$params ) {
		global $mainframe;
		global $_LANG, $mosConfig_absolute_path;

		$titlelength 	= $params->def( 'title_length', 20 );
		$show_preview 	= $params->def( 'show_preview', 0 );

		// Read files from template directory
		$template_path 	= $mosConfig_absolute_path .'/templates';
		$templatefolder = @dir( $template_path );
		$darray 		= array();

		if ( $templatefolder ) {
			while ( $templatefile = $templatefolder->read() ) {
				if ( $templatefile != '.' && $templatefile != '..' && $templatefile != 'CVS' && is_dir( $template_path .'/'. $templatefile )  ) {
					if( strlen( $templatefile ) > $titlelength ) {
						$templatename = substr( $templatefile, 0, $titlelength - 3 );
						$templatename .= '...';
					} else {
						$templatename = $templatefile;
					}
					$darray[] = mosHTML::makeOption( $templatefile, $templatename );
				}
			}
			$templatefolder->close();
		}

		$cur_template = $params->def( 'template', $mainframe->getTemplate() );
		sort( $darray );
		// Show the preview image
		if( $show_preview ) {
			$onchange = 'showimage()';
		} else {
			$onchange = '';
		}
		$list = mosHTML::selectList( $darray, 'mos_change_template', 'class="button" onchange="'. $onchange .'" onkeyup="'. $onchange .'"', 'value', 'text', $cur_template );
		$params->set( 'dropdown', $list );

		return $params;
	}
}


class modTemplatechooser {

	function show ( &$params ) {
		modTemplatechooser::_display($params);
	}

	function _display( &$params ) {

		$params = modTemplatechooserData::getParams( $params );

		$tmpl =& moduleScreens::createTemplate( 'mod_templatechooser.html' );

		$tmpl->addVar( 'mod_templatechooser', 'class', 	$params->get( 'moduleclass_sfx' ) );
		$tmpl->addVar( 'mod_templatechooser', 'url', ampReplace( $_SERVER['REQUEST_URI'] ) );

		$tmpl->addObject( 'mod_templatechooser', $params->toObject() );

		$tmpl->displayParsedTemplate( 'mod_templatechooser' );
	}
}

modTemplatechooser::show( $params );
?>