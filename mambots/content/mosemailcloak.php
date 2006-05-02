<?php
/**
* @version $Id: mosemailcloak.php,v 1.1 2005/08/25 14:21:34 johanjanssens Exp $
* @package Mambo
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

$_MAMBOTS->registerFunction( 'onPrepareContent', 'botMosEmailCloak' );

/**
* Mambot that Cloaks all emails in content from spambots via javascript
*/
function botMosEmailCloak( $published, &$row, &$params, $page=0 ) {
	global $database;

	// check to see if disabling tag is in text, if tag exists, mambot will not parse text
	$find = '{mosemailcloak=off}';
	if ( strstr( $row->text, $find ) ) {
		$row->text = str_replace( $find, '', $row->text );
	} else {
		// load mambot params info
		$query = "SELECT id FROM #__mambots WHERE element = 'mosemailcloak' AND folder = 'content'";
		$database->setQuery( $query );
	 	$id 		= $database->loadResult();
	 	$mambot 	= new mosMambot( $database );
	  	$mambot->load( $id );
	 	$params 	=& new mosParameters( $mambot->params );
	 	$mode		= $params->def( 'mode', 1 );
	 	$noscript	= $params->def( 'noscript', 0 );

	 	$search 		= "([[:alnum:]_\.\-]+)(\@[[:alnum:]\.\-]+\.+)([[:alnum:]\.\-]+)";
	 	$search_text 	= "([[:alnum:][:space:][:punct:]][^<>]+)";

		// search for derivativs of link code <a href="mailto:email@amail.com">email@amail.com</a>
		// extra handling for inclusion of title and target attributes either side of href attribute
		$searchlink	= "(<a [[:alnum:] _\"\'=\@\.\-]*href=[\"\']mailto:". $search ."[\"\'][[:alnum:] _\"\'=\@\.\-]*>)". $search ."</a>";
		while( eregi( $searchlink, $row->text, $regs ) ) {
			$mail 		= $regs[2] . $regs[3] . $regs[4];
			$mail_text 	= $regs[5] . $regs[6] . $regs[7];

			// check to see if mail text is different from mail addy
			if ( $mail_text ) {
				$replacement 	= mosHTML::emailCloaking( $mail, $mode, $mail_text, 1, $noscript );
			} else {
				$replacement 	= mosHTML::emailCloaking( $mail, $mode, '', 1, $noscript );
			}

			// replace the found address with the js cloacked email
			$row->text 	= str_replace( $regs[0], $replacement, $row->text );
		}

		// search for derivativs of link code <a href="mailto:email@amail.com">anytext</a>
		// extra handling for inclusion of title and target attributes either side of href attribute
		$searchlink	= "(<a [[:alnum:] _\"\'=\@\.\-]*href=[\"\']mailto:". $search ."[\"\'][[:alnum:] _\"\'=\@\.\-]*)>". $search_text ."</a>";
		while( eregi( $searchlink, $row->text, $regs ) ) {
			$mail 		= $regs[2] . $regs[3] . $regs[4];
			$mail_text 	= $regs[5];

			$replacement 	= mosHTML::emailCloaking( $mail, $mode, $mail_text, 0, $noscript );

			// replace the found address with the js cloacked email
			$row->text 	= str_replace( $regs[0], $replacement, $row->text );
		}


		// search for plain text email@amail.com
		while( eregi( $search, $row->text, $regs ) ) {
			$mail = $regs[0];

			$replacement = mosHTML::emailCloaking( $mail, $mode, '', 1, $noscript );

			// replace the found address with the js cloacked email
			$row->text = str_replace( $regs[0], $replacement, $row->text );
		}
	}
}
?>