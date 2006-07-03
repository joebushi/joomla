<?php
/**
 * @version $Id$
 * @package Joomla
 * @subpackage Content
 * @copyright Copyright (C) 2005 - 2006 Open Source Matters. All rights reserved.
 * @license GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


jimport( 'joomla.template.template');

// Process the content plugins
JPluginHelper::importPlugin('content');
$results = $app->triggerEvent('onPrepareContent', array (& $article, & $params, $page));


// var init
$linkOn = '';
$linkText = '';

// Build the link and text of the readmore button
if ($params->get('readmore') || $params->get('link_titles')) {
	if ($params->get('intro_only')) {
		// Check to see if the user has access to view the full article
		if ($article->access <= $user->get('gid')) {
			$Itemid = JContentHelper::getItemid($article->id);
			$linkOn = sefRelToAbs("index.php?option=com_content&amp;task=view&amp;id=".$article->id."&amp;Itemid=".$Itemid);

			if (@$article->readmore) {
			// text for the readmore link
				$linkText = JText::_('Read more...');
			}
		} else {
			$linkOn = sefRelToAbs("index.php?option=com_registration&amp;task=register");

			if (@$article->readmore) {
			// text for the readmore link if accessible only if registered
				$linkText = JText::_('Register to read more...');
			}
		}
	}
}

// Popup pages get special treatment for page titles
if ($params->get('popup') && $type =! 'html') {
	$doc->setTitle($app->getCfg('sitename').' - '.$article->title);
}


$tmpl =& new JTemplate;
$tmpl->setRoot( JPATH_COM_CONTENT .DS. 'view' .DS. 'article' .DS. 'tmpl' );
$tmpl->parse( 'article.html' );
	
// I don't know if we have a function to move parameter to an object
// but this worked 
$p = array('item_title','pdf','print','email','link_titles','popup','icons','readmore','section','category','author','url','createdate','modifydate');
foreach ($p as $elm) {
	$tmpl->addVar('article','params_'.$elm,$params->get($elm));
}	
$ppe = $params->get('pdf') || $params->get('print') || $params->get('email');
$tmpl->addVar('article','ppe',$ppe);

// fire the plugins
$results = $app->triggerEvent('onAfterDisplayTitle', array (& $article, & $params, $page));
$results_onAfterDisplayTitle =  trim(implode("\n", $results));
// Display the output from the onBeforeDisplayContent event
$onBeforeDisplayContent = $app->triggerEvent('onBeforeDisplayContent', array (& $article, & $params, $page));
$results_onBeforeDisplayContent =  trim(implode("\n", $onBeforeDisplayContent));
// Fire the after display content event
$onAfterDisplayContent = $app->triggerEvent('onAfterDisplayContent', array (& $article, & $params, $page));
$results_onAfterDisplayContent = trim(implode("\n", $onAfterDisplayContent));

	// TODO: add output to the template

$tmpl->addVar('article','results_onAfterDisplayTitle',$results_onAfterDisplayTitle);
$tmpl->addVar('article','results_onBeforeDisplayContent',$results_onBeforeDisplayContent);
$tmpl->addVar('article','results_onAfterDisplayContent',$results_onAfterDisplayContent);


//copy article information to template 
$article->created = mosFormatDate($article->created);
$article->modified = mosFormatDate($article->modified);
$tmpl->addObject('article',$article,'A_');

// links an other stuff			
// To Do - make edit bottom availabe.
$tmpl->addVar('article','canedit',$access->canEdit);
$tmpl->addVar('article','linkon',$linkOn);
$tmpl->addVar('article','linktext',$linkText);

// PDF Icon
if ($params->get('icons')) {
	$image = mosAdminMenus::ImageCheck('pdf_button.png', '/images/M_images/', NULL, NULL, JText::_('PDF'), JText::_('PDF'));
} else {
	$image = JText::_('PDF').'&nbsp;';
}
$tmpl->addVar('article','pdf_image',$image);
$tmpl->addVar('article','nojs',$noJS);
$pdf_status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';
$pdf_link = 'index2.php?option=com_content&amp;id='.$article->id.'&amp;format=pdf';
$tmpl->addVar('article','pdf_status',$pdf_status);
$tmpl->addVar('article','pdf_link',$pdf_link);

// Print
$print_status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';
$print_link = $app->getBaseURL().'index2.php?option=com_content&amp;task=view&amp;id='.$article->id.'&amp;Itemid='.$Itemid.'&amp;pop=1&amp;page='.@ $page;
$tmpl->addVar('article','print_Link',$print_link);
if ( $params->get( 'icons' ) ) {
	$image = mosAdminMenus::ImageCheck( 'printButton.png', '/images/M_images/', NULL, NULL, JText::_( 'Print' ), JText::_( 'Print' ) );
} else {
	$image = JText::_( 'ICON_SEP' ) .'&nbsp;'. JText::_( 'Print' ) .'&nbsp;'. JText::_( 'ICON_SEP' );
}
$tmpl->addVar('article','print_image',$image);
$tmpl->addVar('article','print_status',$print_status);

// EMAIL
	// TODO: javascript must in head


//display the template			
$tmpl->display('article');

/**

		// If the user can edit the article, display the edit icon

		// Time to build the title bar... this may also include the pdf/print/email buttons if enabled
		if ($params->get('item_title') || $params->get('pdf') || $params->get('print') || $params->get('email')) {
			// Build the link for the print button
			$printLink = $app->getBaseURL().'index2.php?option=com_content&amp;task=view&amp;id='.$article->id.'&amp;Itemid='.$Itemid.'&amp;pop=1&amp;page='.@ $page;
			?>
			<table class="contentpaneopen<?php echo $params->get( 'pageclass_sfx' ); ?>">
			<tr>
			<?php

			// displays Item Title
			JContentHTMLHelper::title($article, $params, $linkOn, $access);

			// displays PDF Icon
			JContentHTMLHelper::pdfIcon($article, $params, $linkOn, $noJS);

			// displays Print Icon
			mosHTML::PrintIcon($article, $params, $noJS, $printLink);

			// displays Email Icon
			JContentHTMLHelper::emailIcon($article, $params, $noJS);
			?>
			</tr>
			</table>
			<?php
		}

		// If only displaying intro, display the output from the onAfterDisplayTitle event
		if (!$params->get('intro_only')) {
			$results = $app->triggerEvent('onAfterDisplayTitle', array (& $article, & $params, $page));
			echo trim(implode("\n", $results));
		}

		// Display the output from the onBeforeDisplayContent event
		$onBeforeDisplayContent = $app->triggerEvent('onBeforeDisplayContent', array (& $article, & $params, $page));
		echo trim(implode("\n", $onBeforeDisplayContent));
		?>
		<table class="contentpaneopen<?php echo $params->get( 'pageclass_sfx' ); ?>">
		<?php

		// displays Section & Category
		JContentHTMLHelper::sectionCategory($article, $params);

		// displays Author Name
		JContentHTMLHelper::author($article, $params);

		// displays Created Date
		JContentHTMLHelper::createDate($article, $params);

		// displays Urls
		JContentHTMLHelper::url($article, $params);
		?>
		<tr>
			<td valign="top" colspan="2">
		<?php

		// displays Table of Contents
		JContentHTMLHelper::toc($article);

		// displays Item Text
		echo ampReplace($article->text);
		?>
			</td>
		</tr>
		<?php

		// displays Modified Date
		JContentHTMLHelper::modifiedDate($article, $params);

		// displays Readmore button
		JContentHTMLHelper::readMore($params, $linkOn, $linkText);
		?>
		</table>
		<span class="article_seperator">&nbsp;</span>
		<?php

		// Fire the after display content event
		$onAfterDisplayContent = $app->triggerEvent('onAfterDisplayContent', array (& $article, & $params, $page));
		echo trim(implode("\n", $onAfterDisplayContent));

		// displays close button in pop-up window
		mosHTML::CloseButton($params, $noJS);
*/
?>