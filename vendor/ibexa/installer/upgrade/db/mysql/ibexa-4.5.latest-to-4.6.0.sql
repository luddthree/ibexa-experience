ALTER TABLE `ibexa_token`
    ADD COLUMN `revoked` BOOLEAN NOT NULL DEFAULT false;

CREATE TABLE ibexa_product_type_settings
(
    id int(11) NOT NULL AUTO_INCREMENT,
    field_definition_id int(11) NOT NULL,
    status int(11) NOT NULL,
    is_virtual tinyint(1) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY ibexa_product_type_setting_field_definition_uidx (field_definition_id, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

CREATE INDEX ibexa_product_type_setting_field_definition_idx
    ON ibexa_product_type_settings (field_definition_id);

INSERT INTO ibexa_product_type_settings (field_definition_id, status, is_virtual)
SELECT id, version, false
FROM ezcontentclass_attribute
WHERE data_type_string = 'ibexa_product_specification';

-- Ibexa Product Migration
-- Version Ibexa\Bundle\ProductCatalog\Migrations\Version20230719103225
CREATE TABLE ibexa_product (
    id INT AUTO_INCREMENT NOT NULL,
    base_product_id INT DEFAULT NULL,
    code VARCHAR(64) NOT NULL,
    is_published TINYINT(1) DEFAULT '0' NOT NULL,
    UNIQUE INDEX ibexa_product_code_uidx (code),
    INDEX ibexa_product_base_product_idx (base_product_id),
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_520_ci` ENGINE = InnoDB;

ALTER TABLE ibexa_product
    ADD CONSTRAINT ibexa_product_base_product_fk FOREIGN KEY (base_product_id)
        REFERENCES ibexa_product (id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT;

ALTER TABLE ibexa_product_specification ADD product_id INT DEFAULT NULL;

ALTER TABLE ibexa_product_specification
    ADD CONSTRAINT ibexa_product_fkey FOREIGN KEY (product_id)
        REFERENCES ibexa_product (id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT;
CREATE INDEX ibexa_product_idx ON ibexa_product_specification (product_id);

-- Version Ibexa\Bundle\ProductCatalog\Migrations\Version20230719144525
INSERT INTO ibexa_product (code, is_published)
SELECT ibexa_product_specification.code, ezcontentobject.status
FROM ibexa_product_specification
    JOIN ezcontentobject_attribute
        ON ibexa_product_specification.content_id = ezcontentobject_attribute.contentobject_id
            AND ibexa_product_specification.field_id = ezcontentobject_attribute.id
            AND ibexa_product_specification.version_no = ezcontentobject_attribute.version
    JOIN ezcontentobject
        ON ezcontentobject_attribute.contentobject_id = ezcontentobject.id
            AND ezcontentobject_attribute.version = ezcontentobject.current_version
    LEFT JOIN ibexa_product ON ibexa_product.code = ibexa_product_specification.code
WHERE ibexa_product_specification.base_product_id IS NULL
  -- Ensure that we do not try to add product record that was already added by the application
  -- which may happen if a product is edited after 4.6 update
  AND ibexa_product.id IS NULL;

UPDATE ibexa_product_specification ips
    JOIN ezcontentobject_attribute
ON ips.content_id = ezcontentobject_attribute.contentobject_id
    AND ips.field_id = ezcontentobject_attribute.id
    AND ips.version_no = ezcontentobject_attribute.version
    JOIN ibexa_product ON ibexa_product.code = ips.code
SET product_id = ibexa_product.id
WHERE ips.base_product_id IS NULL
  AND ips.product_id IS NULL;

INSERT INTO ibexa_product (code, is_published, base_product_id)
SELECT ibexa_product_specification.code, ip_base_product.is_published, ip_base_product.id
FROM ibexa_product_specification
    JOIN ibexa_product_specification ips_base_product ON ibexa_product_specification.base_product_id = ips_base_product.field_id
    JOIN ibexa_product ip_base_product ON ips_base_product.product_id = ip_base_product.id

    -- This ensures that already inserted records are not re-inserted, combined with "ibexa_product.id IS NULL" condition
    LEFT JOIN ibexa_product ON ibexa_product.code = ibexa_product_specification.code AND ibexa_product.base_product_id = ip_base_product.id

WHERE ibexa_product_specification.base_product_id IS NOT NULL
  AND ibexa_product.id IS NULL;

UPDATE ibexa_product_specification ips
    JOIN ibexa_product_specification ips_base_product ON ips.base_product_id = ips_base_product.field_id
    JOIN ibexa_product ip_base_product ON ips_base_product.product_id = ip_base_product.id

    JOIN ibexa_product ON ibexa_product.code = ips.code AND ibexa_product.base_product_id = ip_base_product.id
    SET ips.product_id = ibexa_product.id
WHERE ips.base_product_id IS NOT NULL
  AND ips.product_id IS NULL;

ALTER TABLE ibexa_product_specification CHANGE product_id product_id INT NOT NULL;

-- Ibexa OAuth2 Server
CREATE TABLE ibexa_oauth2_client
(
    id INT(11) NOT NULL AUTO_INCREMENT,
    client_name VARCHAR(128) NOT NULL,
    client_identifier VARCHAR(32) NOT NULL,
    client_secret VARCHAR(128),
    client_active TINYINT(1) NOT NULL DEFAULT 0,
    client_plain_pkce TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE INDEX ibexa_oauth2_client_identifier_idx (client_identifier)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

CREATE TABLE ibexa_oauth2_client_redirect_uri
(
    id INT(11) NOT NULL AUTO_INCREMENT,
    client_id INT(11) NOT NULL,
    client_redirect_uri VARCHAR(255) NOT NULL,
    PRIMARY KEY (id),
    INDEX ibexa_oauth2_client_redirect_uri_client_id_idx (client_id),
    INDEX ibexa_oauth2_client_redirect_uri_client_redirect_uri_idx (client_redirect_uri),
    CONSTRAINT ibexa_oauth2_client_redirect_uri_unique_idx
        UNIQUE (client_id, client_redirect_uri),
    CONSTRAINT ibexa_oauth2_client_redirect_uri_fk
        FOREIGN KEY (client_id) REFERENCES ibexa_oauth2_client (id)
            ON UPDATE CASCADE
            ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

CREATE TABLE ibexa_oauth2_client_grant
(
    id INT(11) NOT NULL AUTO_INCREMENT,
    client_id INT(11) NOT NULL,
    client_grant VARCHAR(255) NOT NULL,
    PRIMARY KEY (id),
    INDEX ibexa_oauth2_client_grant_client_id_idx (client_id),
    INDEX ibexa_oauth2_client_grant_client_grant_idx (client_grant),
    CONSTRAINT ibexa_oauth2_client_grant_unique_idx
        UNIQUE (client_id, client_grant),
    CONSTRAINT ibexa_oauth2_client_grant_fk
        FOREIGN KEY (client_id) REFERENCES ibexa_oauth2_client (id)
            ON UPDATE CASCADE
            ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

CREATE TABLE ibexa_oauth2_client_token
(
    id INT(11) NOT NULL AUTO_INCREMENT,
    client_id INT(11) NOT NULL,
    token_id INT(11) NOT NULL,
    PRIMARY KEY (id),
    INDEX ibexa_oauth2_client_token_client_id_idx (client_id),
    INDEX ibexa_oauth2_client_token_token_id_idx (token_id),
    CONSTRAINT ibexa_oauth2_client_token_unique_idx
        UNIQUE (client_id, token_id),
    CONSTRAINT ibexa_oauth2_client_token_client_fk
        FOREIGN KEY (client_id) REFERENCES ibexa_oauth2_client (id)
            ON UPDATE CASCADE
            ON DELETE CASCADE,
    CONSTRAINT ibexa_oauth2_client_token_token_fk
        FOREIGN KEY (token_id) REFERENCES ibexa_token (id)
            ON UPDATE CASCADE
            ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

CREATE TABLE ibexa_oauth2_client_scope
(
    id INT(11) NOT NULL AUTO_INCREMENT,
    client_id INT(11) NOT NULL,
    client_scope VARCHAR(255) NOT NULL,
    PRIMARY KEY (id),
    INDEX ibexa_oauth2_client_scope_client_id_idx (client_id),
    INDEX ibexa_oauth2_client_scope_client_scope_idx (client_scope),
    CONSTRAINT ibexa_oauth2_client_scope_unique_idx
        UNIQUE (client_id, client_scope),
    CONSTRAINT ibexa_oauth2_client_scope_fk
        FOREIGN KEY (client_id) REFERENCES ibexa_oauth2_client (id)
            ON UPDATE CASCADE
            ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

CREATE TABLE ibexa_oauth2_token_scope
(
    id INT(11) NOT NULL AUTO_INCREMENT,
    token_id INT(11) NOT NULL,
    scope VARCHAR(255) NOT NULL,
    PRIMARY KEY (id),
    INDEX ibexa_oauth2_token_scope_token_id_idx (token_id),
    INDEX ibexa_oauth2_token_scope_scope_idx (scope),
    CONSTRAINT ibexa_oauth2_token_scope_unique_idx
        UNIQUE (token_id, scope),
    CONSTRAINT ibexa_oauth2_token_scope_fk
        FOREIGN KEY (token_id) REFERENCES ibexa_token (id)
            ON UPDATE CASCADE
            ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

CREATE TABLE ibexa_oauth2_refresh_access_token
(
    id INT(11) NOT NULL AUTO_INCREMENT,
    access_token_id INT(11) NOT NULL,
    refresh_token_id INT(11) NOT NULL,
    PRIMARY KEY (id),
    INDEX ibexa_oauth2_refresh_access_token_access_token_id_idx (access_token_id),
    INDEX ibexa_oauth2_refresh_access_token_refresh_token_id_idx (refresh_token_id),
    CONSTRAINT ibexa_oauth2_refresh_access_token_unique_idx
        UNIQUE (access_token_id, refresh_token_id),
    CONSTRAINT ibexa_oauth2_refresh_access_token_access_token_fk
        FOREIGN KEY (access_token_id) REFERENCES ibexa_token (id)
            ON UPDATE CASCADE
            ON DELETE CASCADE,
    CONSTRAINT ibexa_oauth2_refresh_access_token_refresh_token_fk
        FOREIGN KEY (refresh_token_id) REFERENCES ibexa_token (id)
            ON UPDATE CASCADE
            ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

CREATE TABLE ibexa_oauth2_consent
(
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_identifier VARCHAR(150) NOT NULL,
    client_identifier VARCHAR(32) NOT NULL,
    created INT(11) NOT NULL DEFAULT 0,
    updated INT(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    CONSTRAINT ibexa_oauth2_consent_unique_idx
        UNIQUE (user_identifier, client_identifier),
    CONSTRAINT ibexa_oauth2_consent_user_fk
        FOREIGN KEY (user_identifier) REFERENCES ezuser (login)
            ON UPDATE CASCADE
            ON DELETE CASCADE,
    CONSTRAINT ibexa_oauth2_consent_client_fk
        FOREIGN KEY (client_identifier) REFERENCES ibexa_oauth2_client (client_identifier)
            ON UPDATE CASCADE
            ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

CREATE TABLE ibexa_oauth2_consent_scope
(
    id INT(11) NOT NULL AUTO_INCREMENT,
    consent_id INT(11) NOT NULL,
    consent_scope VARCHAR(255) NOT NULL,
    PRIMARY KEY (id),
    INDEX ibexa_oauth2_consent_scope_consent_id_idx (consent_id),
    INDEX ibexa_oauth2_consent_scope_consent_scope_idx (consent_scope),
    CONSTRAINT ibexa_oauth2_consent_scope_unique_idx
        UNIQUE (consent_id, consent_scope),
    CONSTRAINT ibexa_oauth2_consent_scope_fk
        FOREIGN KEY (consent_id) REFERENCES ibexa_oauth2_consent (id)
            ON UPDATE CASCADE
            ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- Rewrites max file size values from data_int1 to data_float1 column and stores size unit
UPDATE ezcontentclass_attribute
SET data_float1 = CAST(data_int1 AS DOUBLE), data_int1 = NULL, data_text1 = 'MB'
WHERE data_type_string = 'ezimage';

UPDATE ezcontentclass_attribute SET is_searchable = 1 WHERE data_type_string = 'ezimage' AND contentclass_id = (SELECT id FROM ezcontentclass WHERE identifier = 'image');

CREATE TABLE ibexa_activity_log (
    id BIGINT AUTO_INCREMENT NOT NULL,
    object_class_id INT NOT NULL,
    action_id INT NOT NULL,
    group_id BIGINT NOT NULL,
    object_id VARCHAR(64) NOT NULL,
    object_name VARCHAR(190) DEFAULT NULL,
    data LONGTEXT NOT NULL COMMENT '(DC2Type:json)',
    INDEX ibexa_activity_log_object_class_idx (object_class_id),
    INDEX ibexa_activity_log_action_idx (action_id),
    INDEX ibexa_activity_log_object_idx (object_id),
    INDEX ibexa_activity_log_object_name_idx (object_name),
    INDEX ibexa_activity_log_group_idx (group_id),
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB;

CREATE TABLE ibexa_activity_log_group_source (
    id INT AUTO_INCREMENT NOT NULL,
    name VARCHAR(190) NOT NULL,
    UNIQUE INDEX ibexa_activity_log_group_source_name_uidx (name),
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB;

CREATE TABLE ibexa_activity_log_ip (
    id INT AUTO_INCREMENT NOT NULL,
    ip VARCHAR(190) NOT NULL,
    UNIQUE INDEX ibexa_activity_log_group_source_ip_uidx (ip),
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB;

CREATE TABLE ibexa_activity_log_group (
    id BIGINT AUTO_INCREMENT NOT NULL,
    source_id INT DEFAULT NULL,
    ip_id INT DEFAULT NULL,
    description VARCHAR(255) DEFAULT NULL,
    logged_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    user_id INT NOT NULL,
    INDEX ibexa_activity_log_group_source_idx (source_id),
    INDEX ibexa_activity_log_ip_idx (ip_id),
    INDEX ibexa_activity_log_logged_at_idx (logged_at),
    INDEX ibexa_activity_log_user_id_idx (user_id),
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB;

CREATE TABLE ibexa_activity_log_action (
    id INT AUTO_INCREMENT NOT NULL,
    action VARCHAR(30) NOT NULL,
    UNIQUE INDEX ibexa_activity_log_action_uidx (action),
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB;

CREATE TABLE ibexa_activity_log_object_class (
    id INT AUTO_INCREMENT NOT NULL,
    object_class VARCHAR(190) NOT NULL,
    UNIQUE INDEX ibexa_activity_log_object_class_uidx (object_class),
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB;

ALTER TABLE ibexa_activity_log
    ADD CONSTRAINT ibexa_activity_log_object_class_fk
        FOREIGN KEY (object_class_id)
        REFERENCES ibexa_activity_log_object_class (id)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT;

ALTER TABLE ibexa_activity_log
    ADD CONSTRAINT ibexa_activity_log_action_fk
        FOREIGN KEY (action_id)
        REFERENCES ibexa_activity_log_action (id)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT;

ALTER TABLE ibexa_activity_log
    ADD CONSTRAINT ibexa_activity_log_group_fk
        FOREIGN KEY (group_id)
        REFERENCES ibexa_activity_log_group (id)
        ON UPDATE CASCADE
        ON DELETE CASCADE;

ALTER TABLE ibexa_activity_log_group
    ADD CONSTRAINT ibexa_activity_log_group_source_fk
        FOREIGN KEY (source_id)
        REFERENCES ibexa_activity_log_group_source (id)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT;

ALTER TABLE ibexa_activity_log_group
    ADD CONSTRAINT ibexa_activity_log_ip_fk
        FOREIGN KEY (ip_id)
        REFERENCES ibexa_activity_log_ip (id)
        ON UPDATE CASCADE
        ON DELETE SET NULL;
