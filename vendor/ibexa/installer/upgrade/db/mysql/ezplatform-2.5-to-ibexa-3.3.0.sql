-- Product name migration
START TRANSACTION;
DELETE FROM `ezsite_data`
WHERE `name` IN ('ezpublish-version', 'ezplatform-release');

INSERT INTO ezsite_data (`name`, `value`)
VALUES ('ezplatform-release', '3.0.0');
COMMIT;

--
ALTER TABLE `ezcontentclass_attribute` MODIFY `data_text1` VARCHAR(255);
--

--
ALTER TABLE `ezcontentclass_attribute`
    ADD COLUMN `is_thumbnail` BOOLEAN NOT NULL DEFAULT false;
--

-- EZP-31471: Keywords versioning
ALTER TABLE `ezkeyword_attribute_link`
    ADD COLUMN `version` INT(11) DEFAULT '0',
    ADD KEY `ezkeyword_attr_link_oaid_ver` (`objectattribute_id`, `version`);

UPDATE `ezkeyword_attribute_link`
SET `version` = COALESCE(
        (
            SELECT `current_version`
            FROM `ezcontentobject_attribute` AS `cattr`
                     JOIN `ezcontentobject` AS `contentobj`
                          ON `cattr`.`contentobject_id` = `contentobj`.`id`
                              AND `cattr`.`version` = `contentobj`.`current_version`
            WHERE `cattr`.`id` = `ezkeyword_attribute_link`.`objectattribute_id`
        ), 0
    );

ALTER TABLE `ezkeyword_attribute_link`
    MODIFY `version` INT(11) NOT NULL DEFAULT '0';
--

-- EZP-31079: Provided default value for ezuser login pattern --
UPDATE `ezcontentclass_attribute`
SET `data_text2` = '^[^@]+$'
WHERE `data_type_string` = 'ezuser'
  AND `data_text2` IS NULL;
--

-- EZEE-2880: Added support for stage and transition actions --
ALTER TABLE `ezeditorialworkflow_markings`
    ADD COLUMN `message`     TEXT NOT NULL DEFAULT '',
    ADD COLUMN `reviewer_id` INT(11),
    ADD COLUMN `result`      TEXT;
--

ALTER TABLE `ezeditorialworkflow_markings` ALTER `message`  DROP DEFAULT;

-- EZEE-2988: Added availability for schedule hide --
START TRANSACTION;
ALTER TABLE `ezdatebasedpublisher_scheduled_version`
    CHANGE COLUMN `publication_date` `action_timestamp` INT(11) NOT NULL;

ALTER TABLE `ezdatebasedpublisher_scheduled_version`
    ADD COLUMN `action` VARCHAR(32);

UPDATE `ezdatebasedpublisher_scheduled_version` SET `action` = 'publish';

ALTER TABLE `ezdatebasedpublisher_scheduled_version` CHANGE COLUMN `action` `action` VARCHAR(32) NOT NULL;
COMMIT;

--
ALTER TABLE `ezdatebasedpublisher_scheduled_version`
    CHANGE COLUMN `version_id` `version_id` INT NULL,
    CHANGE COLUMN `version_number` `version_number` INT NULL,
    RENAME TO `ezdatebasedpublisher_scheduled_entries`;
--

UPDATE `ezuser`
SET `email` =
        CASE
            WHEN `contentobject_id` = 10 THEN 'anonymous@link.invalid'
            WHEN `contentobject_id` = 14 THEN 'admin@link.invalid'
            END
WHERE `contentobject_id` IN (10, 14) AND `email` = 'nospam@ibexa.co';

ALTER TABLE `ezpage_attributes` MODIFY `value` LONGTEXT;
--
START TRANSACTION;
DROP TABLE IF EXISTS `ezsite`;
CREATE TABLE `ezsite`
(
    `id`      int(11)                                     NOT NULL AUTO_INCREMENT,
    `name`    varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
    `created` int(11)                                     NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;
COMMIT;
--

START TRANSACTION;
DROP TABLE IF EXISTS `ezsite_public_access`;
CREATE TABLE `ezsite_public_access`
(
    `public_access_identifier` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
    `site_id`                  int(11)                                     NOT NULL,
    `site_access_group`        varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
    `status`                   int(11)                                     NOT NULL,
    `config`                   text COLLATE utf8mb4_unicode_520_ci         NOT NULL,
    `site_matcher_host`        varchar(255) COLLATE utf8mb4_unicode_520_ci          DEFAULT NULL,
    PRIMARY KEY (`public_access_identifier`),
    KEY `ezsite_public_access_site_id` (`site_id`),
    CONSTRAINT `fk_ezsite_public_access_site_id` FOREIGN KEY (`site_id`) REFERENCES `ezsite` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

-- EZEE-3244: Added path to site configuration --
ALTER TABLE `ezsite_public_access`
    ADD COLUMN `site_matcher_path` VARCHAR(255) DEFAULT NULL;
--
COMMIT;
DROP TABLE IF EXISTS `ibexa_segment_group_map`;
CREATE TABLE `ibexa_segment_group_map`
(
    `segment_id` int(11) NOT NULL,
    `group_id`   int(11) NOT NULL,
    PRIMARY KEY (`segment_id`, `group_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_520_ci;

DROP TABLE IF EXISTS `ibexa_segment_groups`;
CREATE TABLE `ibexa_segment_groups`
(
    `id`         int(11)                                     NOT NULL AUTO_INCREMENT,
    `identifier` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
    `name`       varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
    PRIMARY KEY (`id`, `identifier`),
    UNIQUE KEY `ibexa_segment_groups_identifier` (`identifier`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_520_ci;

DROP TABLE IF EXISTS `ibexa_segment_user_map`;
CREATE TABLE `ibexa_segment_user_map`
(
    `segment_id` int(11) NOT NULL,
    `user_id`    int(11) NOT NULL,
    PRIMARY KEY (`segment_id`, `user_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_520_ci;

DROP TABLE IF EXISTS `ibexa_segments`;
CREATE TABLE `ibexa_segments`
(
    `id`         int(11)                                     NOT NULL AUTO_INCREMENT,
    `identifier` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
    `name`       varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
    PRIMARY KEY (`id`, `identifier`),
    UNIQUE KEY `ibexa_segments_identifier` (`identifier`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_520_ci;



DROP TABLE IF EXISTS `ibexa_setting`;
CREATE TABLE `ibexa_setting`
(
    `id`         int(11)                                     NOT NULL AUTO_INCREMENT,
    `group`      varchar(128) COLLATE utf8mb4_unicode_520_ci NOT NULL,
    `identifier` varchar(128) COLLATE utf8mb4_unicode_520_ci NOT NULL,
    `value`      text COLLATE utf8mb4_unicode_520_ci         NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `ibexa_setting_group_identifier` (`group`, `identifier`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_520_ci;

INSERT INTO `ibexa_setting` (`group`, `identifier`, `value`)
VALUES ('personalization', 'installation_key', '""');
