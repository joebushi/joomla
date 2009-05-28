# $Id: diff_15_to_16.sql 304 2009-05-27 06:50:21Z andrew.eddie $

# 1.5 to 1.6

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

ALTER TABLE `jos_categories`
 MODIFY COLUMN `alias` VARCHAR(50) NOT NULL DEFAULT '',
 MODIFY COLUMN `image_position` VARCHAR(10) NOT NULL DEFAULT '',
 MODIFY COLUMN `description` VARCHAR(5120) NOT NULL DEFAULT '',
 ADD COLUMN `left_id` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `parent_id`,
 ADD COLUMN `right_id` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `left_id`,
 ADD COLUMN `path` VARCHAR(255) NOT NULL DEFAULT '' AFTER `right_id`,
 ADD COLUMN `created_user_id` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `params`,
 ADD COLUMN `created_time` TIMESTAMP NOT NULL AFTER `created_user_id`,
 ADD COLUMN `modified_user_id` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `created_time`,
 ADD COLUMN `modified_time` TIMESTAMP NOT NULL AFTER `modified_user_id`,
 ADD COLUMN `hits` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `modified_time`;

ALTER TABLE `jos_categories`
 ADD INDEX idx_path(`path`),
 ADD INDEX idx_left_right(`left_id`, `right_id`);

DROP TABLE `#__groups`;

-- Note, devise the migration
DROP TABLE `#__core_acl_aro`;
DROP TABLE `#__core_acl_aro_map`;
DROP TABLE `#__core_acl_aro_groups`;
DROP TABLE `#__core_acl_groups_aro_map`;
DROP TABLE `#__core_acl_aro_sections`;



