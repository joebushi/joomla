# $Id: $

# 1.5 to 1.6

ALTER TABLE `jos_weblinks`
	CHANGE `published` `state` TINYINT( 1 ) NOT NULL DEFAULT '0'