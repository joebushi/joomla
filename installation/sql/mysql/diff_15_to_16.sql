# $Id: $

# 1.5 to 1.6

-- 2008-08-25

ALTER TABLE `jos_core_acl_groups_aro_map`
 ADD INDEX aro_id_group_id_group_aro_map USING BTREE(`aro_id`, `group_id`);

--

ALTER TABLE `jos_weblinks`
 CHANGE `published` `state` TINYINT( 1 ) NOT NULL DEFAULT '0'
