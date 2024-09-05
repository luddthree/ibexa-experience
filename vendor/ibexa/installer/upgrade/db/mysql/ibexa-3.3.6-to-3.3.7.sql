CREATE TABLE IF NOT EXISTS `ibexa_workflow_version_lock` (
    `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `content_id` INT(11) NOT NULL,
    `version` INT(11) NOT NULL,
    `user_id` INT(11) NOT NULL,
    `created` INT(11) NOT NULL DEFAULT 0,
    `modified` INT(11) NOT NULL DEFAULT 0,
    `is_locked` BOOLEAN NOT NULL DEFAULT true,
    KEY `ibexa_workflow_version_lock_content_id_index` (`content_id`) USING BTREE,
    KEY `ibexa_workflow_version_lock_user_id_index` (`user_id`) USING BTREE,
    UNIQUE KEY `ibexa_workflow_version_lock_content_id_version_uindex` (`content_id`,`version`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
