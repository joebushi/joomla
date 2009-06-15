# $Id: diff_15_to_16.sql 304 2009-05-27 06:50:21Z andrew.eddie $

# 1.5 to 1.6

-- Menus

ALTER TABLE `jos_menu`
 DROP COLUMN `sublevel`,
 DROP COLUMN `pollid`,
 DROP COLUMN `utaccess`;

ALTER TABLE `jos_menu`
 MODIFY COLUMN `menutype` VARCHAR(24) NOT NULL COMMENT 'The type of menu this item belongs to. FK to jos_menu_types.menutype';

ALTER TABLE `jos_menu`
 CHANGE COLUMN `name` `title` VARCHAR(255) NOT NULL COMMENT 'The display title of the menu item.';

ALTER TABLE `jos_menu`
 MODIFY COLUMN `alias` VARCHAR(255) NOT NULL COMMENT 'The SEF alias of the menu item.';

ALTER TABLE `jos_menu`
 MODIFY COLUMN `link` VARCHAR(1024) NOT NULL COMMENT 'The actually link the menu item refers to.';

ALTER TABLE `jos_menu`
 MODIFY COLUMN `type` VARCHAR(16) NOT NULL COMMENT 'The type of link: Component, URL, Alias, Separator';

ALTER TABLE `jos_menu`
 MODIFY COLUMN `published` TINYINT NOT NULL DEFAULT 0 COMMENT 'The published state of the menu link.';

ALTER TABLE `jos_menu`
 CHANGE COLUMN `parent` `parent_id` INTEGER UNSIGNED NOT NULL DEFAULT 0 COMMENT 'The parent menu item in the menu tree.';

ALTER TABLE `jos_menu`
 ADD COLUMN `level` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'The relative level in the tree.' AFTER `parent_id`;

ALTER TABLE `jos_menu`
 CHANGE COLUMN `componentid` `component_id` INTEGER UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to jos_components.id';

ALTER TABLE `jos_menu`
 MODIFY COLUMN `ordering` INTEGER NOT NULL DEFAULT 0 COMMENT 'The relative ordering of the menu item in the tree.';

ALTER TABLE `jos_menu`
 MODIFY COLUMN `checked_out` INTEGER UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to jos_users.id';

ALTER TABLE `jos_menu`
 MODIFY COLUMN `checked_out_time` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'The time the menu item was checked out.';

ALTER TABLE `jos_menu`
 MODIFY COLUMN `browserNav` TINYINT NOT NULL DEFAULT 0 COMMENT 'The click behaviour of the link.';

ALTER TABLE `jos_menu`
 MODIFY COLUMN `access` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'The access level required to view the menu item.';

ALTER TABLE `jos_menu`
 MODIFY COLUMN `params` VARCHAR(10240) NOT NULL COMMENT 'JSON encoded data for the menu item.';

ALTER TABLE `jos_menu`
 CHANGE COLUMN `lft` `left_id` INTEGER NOT NULL DEFAULT 0 COMMENT 'Nested set left_id.',
 CHANGE COLUMN `rgt` `right_id` INTEGER NOT NULL DEFAULT 0 COMMENT 'Nested set right_id.';

ALTER TABLE `jos_menu`
 MODIFY COLUMN `home` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Indicates if this menu item is the home or default page.';


ALTER TABLE `jos_menu_types`
 MODIFY COLUMN `menutype` VARCHAR(24) NOT NULL,
 MODIFY COLUMN `title` VARCHAR(48) NOT NULL
 DROP INDEX `menutype`;


ALTER TABLE `jos_content`
 ADD COLUMN `featured` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Set if article is featured.' AFTER `metadata`;

ALTER TABLE `jos_content`
 ADD INDEX idx_featured_catid(`featured`, `catid`);

UPDATE `jos_content` AS a
 SET a.featured = 1
 WHERE a.id IN (
 	SELECT f.content_id
 	FROM `jos_content_frontpage` AS f
 );

-- Reconfigure the back module permissions
UPDATE `#__categories`
 SET access = access + 1;

UPDATE `#__contact_details`
 SET access = access + 1;

UPDATE `#__content`
 SET access = access + 1;

UPDATE `#__menu`
 SET access = access + 1;

UPDATE `#__modules`
 SET access = access + 1;

UPDATE `#__plugins`
 SET access = access + 1;

UPDATE `#__sections`
 SET access = access + 1;

-- Schema changes
ALTER TABLE `jos_components`
 MODIFY COLUMN `enabled` TINYINT(4) UNSIGNED NOT NULL DEFAULT 1;
 
ALTER TABLE `jos_weblinks`
 ADD COLUMN `access` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `approved`;

DROP TABLE `#__groups`;

-- Note, devise the migration
DROP TABLE `#__core_acl_aro`;
DROP TABLE `#__core_acl_aro_map`;
DROP TABLE `#__core_acl_aro_groups`;
DROP TABLE `#__core_acl_groups_aro_map`;
DROP TABLE `#__core_acl_aro_sections`;

INSERT INTO `#__plugins` VALUES (NULL, 'Editor - CodeMirror', 'codemirror', 'editors', 1, 0, 1, 1, 0, 0, '0000-00-00 00:00:00', 'linenumbers=0\n\n');