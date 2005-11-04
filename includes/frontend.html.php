<?php
/**
* @version $Id$
* @package Joomla
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

/**
* @package Joomla
*/
class modules_html {

	/**
	* @param object
	* @param object
	* @param int -1=show without wrapper and title, -2=xhtml style
	*/
	function module( &$module, &$params, $style=0 ) {
		global $mosConfig_lang, $mosConfig_absolute_path;

		switch ( $style ) {
			case -3:
			// allows for rounded corners
				modules_html::modoutput_rounded( $module, $params );
				break;

			case -2:
			// xhtml (divs and font headder tags)
				modules_html::modoutput_xhtml( $module, $params );
				break;

			case -1:
			// show a naked module - no wrapper and no title
				modules_html::modoutput_naked( $module, $params );
				break;
			
			case 1:
			// show a naked module - no wrapper and no title
				modules_html::modoutput_horz( $module, $params );
				break;
				
			case 0:
			default:
			// standard tabled output
				modules_html::modoutput_table( $module, $params );
				break;
				
			
		}
		
		if ( $params->get( 'rssurl' ) ) {
			// feed output
			// load RSS module file
			// kept for backward compatability
			mosFS::load( 'modules/mod_rss.php' );
			//modules_html::modoutput_feed( $params );
		}
	}
	
		/*
	* standard tabled output
	*/
	function modoutput_table( $module, $params  ) {
		
		$moduleclass_sfx = $params->get( 'moduleclass_sfx' );
		
		?>
		<table cellpadding="0" cellspacing="0" class="moduletable<?php echo $moduleclass_sfx; ?>">
		<?php
		if ( $module->showtitle != 0 ) {
			?>
			<tr>
				<th valign="top">
					<?php echo $module->title; ?>
				</th>
			</tr>
			<?php
		}
		?>
		<tr>
			<td>
				<?php echo $module->content;?>
			</td>
		</tr>
		</table>
		<?php
	}
	
	/*
	* standard tabled output
	*/
	function modoutput_horz( $module, $params  ) {
		?>
		<table cellspacing="1" cellpadding="0" border="0" width="100%">
		<tr>
			<td valign="top">
				<?php
				modules_html::modoutput_table($module, $params);
				?>
			</td>
		</tr>
		</table>
		<?php
	}

	/*
	* show a naked module - no wrapper and no title
	*/
	function modoutput_naked( $module, $params  ) {
		
		$moduleclass_sfx = $params->get( 'moduleclass_sfx' );
		
		echo $module->content;
	}

	/*
	* xhtml (divs and font headder tags)
	*/
	function modoutput_xhtml( $module, $params ) {
		
		$moduleclass_sfx = $params->get( 'moduleclass_sfx' );
		
		?>
		<div class="moduletable<?php echo $moduleclass_sfx; ?>">
			<?php
			if ($module->showtitle != 0) {
				//echo $number;
				?>
				<h3>
					<?php echo $module->title; ?>
				</h3>
				<?php
			}

			echo $module->content;
			?>
		</div>
		<?php
	}

	/*
	* allows for rounded corners
	*/
	function modoutput_rounded( $module, $params ) {
		
		$moduleclass_sfx = $params->get( 'moduleclass_sfx' );
		
		?>
		<div class="module<?php echo $moduleclass_sfx; ?>">
			<div>
				<div>
					<div>
						<?php
						if ($module->showtitle != 0) {
							echo "<h3>$module->title</h3>";
						}
						
						echo $module->content;
						
						?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
/*
	// feed output
	function modoutput_feed( &$params, $moduleclass_sfx ) {
		global $mosConfig_absolute_path;
		
		$moduleclass_sfx   = $params->get( 'moduleclass_sfx' );
		$rssurl 			= $params->get( 'rssurl' );
		$rssitems 			= $params->get( 'rssitems', 5 );
		$rssdesc 			= $params->get( 'rssdesc', 1 );
		$rssimage 			= $params->get( 'rssimage', 1 );
		$rssitemdesc		= $params->get( 'rssitemdesc', 1 );
		$words 				= $params->def( 'word_count', 0 );
		$rsstitle			= $params->get( 'rsstitle', 1 );

		$cacheDir 		= $mosConfig_absolute_path .'/cache/';
		$LitePath 		= $mosConfig_absolute_path .'/includes/Cache/Lite.php';
		require_once( $mosConfig_absolute_path .'/includes/domit/xml_domit_rss.php' );
		
		$rssDoc = new xml_domit_rss_document();
		$rssDoc->useCacheLite(true, $LitePath, $cacheDir, 3600);
		$rssDoc->loadRSS( $rssurl );
		$totalChannels 	= $rssDoc->getChannelCount();

		for ( $i = 0; $i < $totalChannels; $i++ ) {
			$currChannel =& $rssDoc->getChannel($i);
			$elements 	= $currChannel->getElementList();
			$iUrl		= 0;
			foreach ( $elements as $element ) {
				//image handling
				if ( $element == 'image' ) {
					$image =& $currChannel->getElement( DOMIT_RSS_ELEMENT_IMAGE );
					$iUrl	= $image->getUrl();
					$iTitle	= $image->getTitle();
				}
			}

			// feed title
			?>
			<table cellpadding="0" cellspacing="0" class="moduletable<?php echo $moduleclass_sfx; ?>">
			<?php
			// feed description
			if ( $currChannel->getTitle() && $rsstitle ) {
				?>
				<tr>
					<td>
						<strong>
						<a href="<?php echo ampReplace( $currChannel->getLink() ); ?>" target="_blank">
							<?php echo $currChannel->getTitle(); ?></a>
						</strong>
					</td>
				</tr>
				<?php
			}

			// feed description
			if ( $rssdesc ) {
				?>
				<tr>
					<td>
						<?php echo $currChannel->getDescription(); ?>
					</td>
				</tr>
				<?php
			}

			// feed image
			if ( $rssimage && $iUrl ) {
				?>
				<tr>
					<td align="center">
						<image src="<?php echo $iUrl; ?>" alt="<?php echo @$iTitle; ?>"/>
					</td>
				</tr>
				<?php
			}

			$actualItems = $currChannel->getItemCount();
			$setItems = $rssitems;

			if ($setItems > $actualItems) {
				$totalItems = $actualItems;
			} else {
				$totalItems = $setItems;
			}

			?>
			<tr>
				<td>
					<ul class="newsfeed<?php echo $moduleclass_sfx; ?>">
					<?php
					for ($j = 0; $j < $totalItems; $j++) {
						$currItem =& $currChannel->getItem($j);
						// item title
						?>
						<li class="newsfeed<?php echo $moduleclass_sfx; ?>">
							<strong>
							<a href="<?php echo ampReplace( $currItem->getLink() ); ?>" target="_blank">
                                <?php echo str_replace('&apos;', "'", html_entity_decode( $currItem->getTitle() ) ); ?></a>
							</strong>
							<?php
							// item description
							if ( $rssitemdesc ) {
								// item description
								$text = html_entity_decode( $currItem->getDescription() );
                                $text = str_replace('&apos;', "'", $text);

								// word limit check
								if ( $words ) {
									$texts = explode( ' ', $text );
									$count = count( $texts );
									if ( $count > $words ) {
										$text = '';
										for( $i=0; $i < $words; $i++ ) {
											$text .= ' '. $texts[$i];
										}
										$text .= '...';
									}
								}
								?>
								<div>
									<?php echo $text; ?>
								</div>
								<?php
							}
							?>
						</li>
						<?php
					}
					?>
					</ul>
				</td>
			</tr>
			</table>
			<?php
		}
	}
*/
}
?>