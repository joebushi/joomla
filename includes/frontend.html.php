<?php
/**
* @version $Id: frontend.html.php,v 1.1 2005/08/25 14:21:09 johanjanssens Exp $
* @package Mambo
* @copyright (C) 2000 - 2005 Miro International Pty Ltd
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Mambo is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
* @package Mambo
*/
class modules_html {
	/*
	* For Custom created internal Modules
	*/
	function module( &$module, &$params, $style=0 ) {
		global $mosConfig_live_site, $mosConfig_sitename, $mosConfig_lang, $mosConfig_absolute_path;
		global $_LANG;

		$_LANG->load( $module->module );

		// params
		$rssurl 			= $params->get( 'rssurl' );
		$moduleclass_sfx 	= $params->get( 'moduleclass_sfx' );

		switch ( $style ) {
			case -1:
			// show a naked module - no wrapper and no title
				echo $module->content;
				break;

			case -2:
			// x-mambo (divs and font header tags)
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
				break;

			case -3:
			// allows for rounded corners
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
				break;

			default:
			// table based output
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

				if ( $module->content ) {
					?>
					<tr>
						<td>
							<?php echo $module->content; ?>
						</td>
					</tr>
					<?php
				}
				?>
				</table>
				<?php
				break;
		}

		if ( $rssurl ) {
			// load RSS module file
			// kept for backward compatability
			mosFS::load( 'modules/mod_rss.php' );
		}
	}

	/**
	* For loading of 'normal' modules
	* @param object
	* @param object
	* @param int -1=show without wrapper and title, -2=x-mambo style
	*/
	function module2( &$module, &$params, $style=0 ) {
		global $mosConfig_live_site, $mosConfig_sitename, $mosConfig_lang, $mosConfig_absolute_path;
		global $mainframe, $database, $my, $_LANG;

		// params
		$moduleclass_sfx = $params->get( 'moduleclass_sfx' );

		$_LANG->load( $module->module );

		switch ( $style ) {
			case -1:
			// show a naked module - no wrapper and no title
				include( $mosConfig_absolute_path .'/modules/'. $module->module .'.php' );
				break;

			case -2:
			// x-mambo (divs and font headder tags)
				?>
				<div class="moduletable<?php echo $moduleclass_sfx; ?>">
					<?php
					if ($module->showtitle != 0) {
						?>
						<h3>
							<?php echo $module->title; ?>
						</h3>
						<?php
					}
					include( $mosConfig_absolute_path .'/modules/'. $module->module .'.php' );
					?>
				</div>
				<?php
				break;

			case -3:
			// allows for rounded corners
				?>
				<div class="module<?php echo $moduleclass_sfx; ?>">
					<div>
						<div>
							<div>
								<?php
								if ($module->showtitle != 0) {
									?>
									<h3>
										<?php echo $module->title; ?>
									</h3>
									<?php
								}
								include( $mosConfig_absolute_path .'/modules/'. $module->module .'.php' );
								?>
							</div>
						</div>
					</div>
				</div>
				<?php
				break;

			default:
			// table based output
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
						<?php
						include( $mosConfig_absolute_path . '/modules/' . $module->module . '.php' );
						?>
					</td>
				</tr>
				</table>
				<?php
				break;
		}
	}
}
?>