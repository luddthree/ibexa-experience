ALTER TABLE `ezcontentclassgroup`
    ADD COLUMN `is_system` TINYINT(1) NOT NULL DEFAULT '0';

CREATE TABLE IF NOT EXISTS ibexa_attribute_group
(
    id INT AUTO_INCREMENT PRIMARY KEY,
    identifier VARCHAR(64) NOT NULL,
    position INT DEFAULT 0 NOT NULL,
    CONSTRAINT ibexa_attribute_group_identifier_uidx
        UNIQUE (identifier)
)
ENGINE = InnoDB
DEFAULT CHARSET = utf8mb4
COLLATE = utf8mb4_unicode_520_ci;

CREATE TABLE IF NOT EXISTS ibexa_attribute_definition
(
    id INT AUTO_INCREMENT PRIMARY KEY,
    attribute_group_id INT NOT NULL,
    identifier VARCHAR(64) NOT NULL,
    type VARCHAR(32) NOT NULL,
    position INT DEFAULT 0 NOT NULL,
    options JSON NULL,
    CONSTRAINT attribute_definition_identifier_idx
        UNIQUE (identifier),
    CONSTRAINT ibexa_attribute_definition_attribute_group_fk
        FOREIGN KEY (attribute_group_id) REFERENCES ibexa_attribute_group (id)
            ON UPDATE CASCADE ON DELETE CASCADE
)
ENGINE = InnoDB
DEFAULT CHARSET = utf8mb4
COLLATE = utf8mb4_unicode_520_ci;

CREATE INDEX ibexa_attribute_definition_attribute_group_idx
    ON ibexa_attribute_definition (attribute_group_id);

CREATE TABLE IF NOT EXISTS ibexa_attribute_definition_assignment
(
    id INT AUTO_INCREMENT PRIMARY KEY,
    attribute_definition_id INT NOT NULL,
    field_definition_id INT NOT NULL,
    status INT NOT NULL,
    required TINYINT(1) DEFAULT 0 NOT NULL,
    discriminator TINYINT(1) DEFAULT 0 NOT NULL,
    CONSTRAINT ibexa_attribute_definition_assignment_main_uidx
        UNIQUE (field_definition_id, status, attribute_definition_id),
    CONSTRAINT ibexa_attribute_definition_assignment_attribute_definition_fk
        FOREIGN KEY (attribute_definition_id) REFERENCES ibexa_attribute_definition (id)
            ON UPDATE CASCADE ON DELETE CASCADE
)
ENGINE = InnoDB
DEFAULT CHARSET = utf8mb4
COLLATE = utf8mb4_unicode_520_ci;

CREATE INDEX ibexa_attribute_definition_assignment_attribute_definition_idx
    ON ibexa_attribute_definition_assignment (attribute_definition_id);

CREATE INDEX ibexa_attribute_definition_assignment_field_definition_idx
    ON ibexa_attribute_definition_assignment (field_definition_id);

CREATE TABLE IF NOT EXISTS ibexa_attribute_definition_ml
(
    id INT AUTO_INCREMENT PRIMARY KEY,
    attribute_definition_id INT NOT NULL,
    language_id BIGINT NOT NULL,
    name VARCHAR(190) NOT NULL,
    name_normalized VARCHAR(190) NOT NULL,
    description VARCHAR(10000) NOT NULL,
    CONSTRAINT ibexa_attribute_definition_ml_uidx
        UNIQUE (attribute_definition_id, language_id),
    CONSTRAINT ibexa_attribute_definition_ml_attribute_definition_fk
        FOREIGN KEY (attribute_definition_id) REFERENCES ibexa_attribute_definition (id)
            ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT ibexa_attribute_definition_ml_language_fk
        FOREIGN KEY (language_id) REFERENCES ezcontent_language (id)
            ON UPDATE CASCADE ON DELETE CASCADE
)
ENGINE = InnoDB
DEFAULT CHARSET = utf8mb4
COLLATE = utf8mb4_unicode_520_ci;

CREATE INDEX ibexa_attribute_definition_ml_attribute_definition_idx
    ON ibexa_attribute_definition_ml (attribute_definition_id);

CREATE INDEX ibexa_attribute_definition_ml_language_idx
    ON ibexa_attribute_definition_ml (language_id);

CREATE INDEX ibexa_attribute_definition_ml_name_idx
    ON ibexa_attribute_definition_ml (name_normalized);

CREATE TABLE IF NOT EXISTS ibexa_attribute_group_ml
(
    id INT AUTO_INCREMENT PRIMARY KEY,
    attribute_group_id INT NOT NULL,
    language_id BIGINT NOT NULL,
    name VARCHAR(190) NOT NULL,
    name_normalized VARCHAR(190) NOT NULL,
    CONSTRAINT attribute_group_ml_idx
        UNIQUE (attribute_group_id, language_id),
    CONSTRAINT ibexa_attribute_group_ml_attribute_group_fk
        FOREIGN KEY (attribute_group_id) REFERENCES ibexa_attribute_group (id)
            ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT ibexa_attribute_group_ml_language_fk
        FOREIGN KEY (language_id) REFERENCES ezcontent_language (id)
            ON UPDATE CASCADE ON DELETE CASCADE
)
ENGINE = InnoDB
DEFAULT CHARSET = utf8mb4
COLLATE = utf8mb4_unicode_520_ci;

CREATE INDEX ibexa_attribute_group_ml_attribute_group_idx
    ON ibexa_attribute_group_ml (attribute_group_id);

CREATE INDEX ibexa_attribute_group_ml_language_idx
    ON ibexa_attribute_group_ml (language_id);

CREATE INDEX ibexa_attribute_group_ml_name_idx
    ON ibexa_attribute_group_ml (name_normalized);

CREATE TABLE IF NOT EXISTS ibexa_currency
(
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(3) NOT NULL,
    subunits SMALLINT NOT NULL,
    enabled TINYINT(1) DEFAULT 1 NOT NULL,
    CONSTRAINT ibexa_currency_code_idx
        UNIQUE (code)
)
ENGINE = InnoDB
DEFAULT CHARSET = utf8mb4
COLLATE = utf8mb4_unicode_520_ci;

CREATE TABLE IF NOT EXISTS ibexa_customer_group
(
    id INT AUTO_INCREMENT PRIMARY KEY,
    identifier VARCHAR(64) NOT NULL,
    global_price_rate DECIMAL(5, 2) DEFAULT 0.00 NOT NULL,
    CONSTRAINT ibexa_customer_group_identifier_idx
        UNIQUE (identifier)
)
ENGINE = InnoDB
DEFAULT CHARSET = utf8mb4
COLLATE = utf8mb4_unicode_520_ci;

CREATE TABLE IF NOT EXISTS ibexa_content_customer_group
(
    id INT AUTO_INCREMENT PRIMARY KEY,
    field_id INT NOT NULL,
    field_version_no INT NOT NULL,
    customer_group_id INT NOT NULL,
    content_id INT NOT NULL,
    CONSTRAINT ibexa_content_customer_group_attribute_uidx
        UNIQUE (field_id, field_version_no),
    CONSTRAINT ibexa_content_customer_group_attribute_fk
        FOREIGN KEY (field_id, field_version_no) REFERENCES ezcontentobject_attribute (id, version)
            ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT ibexa_content_customer_group_customer_group_fk
        FOREIGN KEY (customer_group_id) REFERENCES ibexa_customer_group (id)
            ON UPDATE CASCADE ON DELETE CASCADE
)
ENGINE = InnoDB
DEFAULT CHARSET = utf8mb4
COLLATE = utf8mb4_unicode_520_ci;

CREATE INDEX ibexa_content_customer_group_customer_group_idx
    ON ibexa_content_customer_group (customer_group_id);

CREATE TABLE IF NOT EXISTS ibexa_customer_group_ml
(
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_group_id INT NOT NULL,
    language_id BIGINT NOT NULL,
    name VARCHAR(190) NOT NULL,
    name_normalized VARCHAR(190) NOT NULL,
    description VARCHAR(10000) NOT NULL,
    CONSTRAINT ibexa_customer_group_ml_customer_group_language_uidx
        UNIQUE (customer_group_id, language_id),
    CONSTRAINT ibexa_customer_group__ml_fk
        FOREIGN KEY (customer_group_id) REFERENCES ibexa_customer_group (id)
            ON UPDATE CASCADE ON DELETE CASCADE
)
ENGINE = InnoDB
DEFAULT CHARSET = utf8mb4
COLLATE = utf8mb4_unicode_520_ci;

CREATE INDEX ibexa_customer_group_idx
    ON ibexa_customer_group_ml (customer_group_id);

CREATE INDEX ibexa_language_idx
    ON ibexa_customer_group_ml (language_id);

CREATE TABLE IF NOT EXISTS ibexa_product_specification
(
    id INT AUTO_INCREMENT PRIMARY KEY,
    content_id INT NOT NULL,
    version_no INT NOT NULL,
    field_id INT NOT NULL,
    code VARCHAR(64) NOT NULL
)
ENGINE = InnoDB
DEFAULT CHARSET = utf8mb4
COLLATE = utf8mb4_unicode_520_ci;

CREATE INDEX ibexa_product_specification_cv
    ON ibexa_product_specification (content_id, version_no);

CREATE INDEX ibexa_product_specification_fid
    ON ibexa_product_specification (field_id);

CREATE INDEX ibexa_product_specification_pc
    ON ibexa_product_specification (code);

CREATE TABLE IF NOT EXISTS ibexa_product_specification_attribute
(
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_specification_id INT NOT NULL,
    attribute_definition_id INT NOT NULL,
    value JSON NULL,
    CONSTRAINT ibexa_product_specification_attribute_aid
        FOREIGN KEY (attribute_definition_id) REFERENCES ibexa_attribute_definition (id)
            ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT ibexa_product_specification_attribute_sid_fk
        FOREIGN KEY (product_specification_id) REFERENCES ibexa_product_specification (id)
            ON UPDATE CASCADE ON DELETE CASCADE
)
ENGINE = InnoDB
DEFAULT CHARSET = utf8mb4
COLLATE = utf8mb4_unicode_520_ci;

CREATE INDEX ibexa_product_specification_attribute_aid_idx
    ON ibexa_product_specification_attribute (attribute_definition_id);

CREATE INDEX ibexa_product_specification_attribute_sid_idx
    ON ibexa_product_specification_attribute (product_specification_id);

CREATE TABLE IF NOT EXISTS ibexa_product_specification_availability
(
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_code VARCHAR(64) NOT NULL,
    availability TINYINT(1) DEFAULT 0 NOT NULL,
    stock INT UNSIGNED NULL,
    CONSTRAINT ibexa_product_specification_availability_product_code_uidx
        UNIQUE (product_code)
)
ENGINE = InnoDB
DEFAULT CHARSET = utf8mb4
COLLATE = utf8mb4_unicode_520_ci;

CREATE TABLE IF NOT EXISTS ibexa_product_specification_price
(
    id INT AUTO_INCREMENT PRIMARY KEY,
    currency_id INT NOT NULL,
    product_code VARCHAR(64) NOT NULL,
    discriminator VARCHAR(20) DEFAULT 'main' NOT NULL,
    amount DECIMAL(19, 4) NOT NULL,
    custom_price_amount DECIMAL(19, 4) NULL,
    CONSTRAINT ibexa_product_specification_price_currency_fk
        FOREIGN KEY (currency_id) REFERENCES ibexa_currency (id)
            ON UPDATE CASCADE ON DELETE CASCADE
)
ENGINE = InnoDB
DEFAULT CHARSET = utf8mb4
COLLATE = utf8mb4_unicode_520_ci;

CREATE INDEX ibexa_product_specification_price_currency_idx
    ON ibexa_product_specification_price (currency_id);

CREATE INDEX ibexa_product_specification_price_product_code_idx
    ON ibexa_product_specification_price (product_code);

CREATE TABLE IF NOT EXISTS ibexa_product_specification_price_customer_group
(
    id INT NOT NULL PRIMARY KEY,
    customer_group_id INT NOT NULL,
    CONSTRAINT ibexa_product_specification_price_customer_group_fk
        FOREIGN KEY (customer_group_id) REFERENCES ibexa_customer_group (id)
            ON UPDATE CASCADE ON DELETE CASCADE
)
ENGINE = InnoDB
DEFAULT CHARSET = utf8mb4
COLLATE = utf8mb4_unicode_520_ci;

CREATE INDEX ibexa_product_specification_price_customer_group_idx
    ON ibexa_product_specification_price_customer_group (customer_group_id);

CREATE TABLE IF NOT EXISTS ibexa_product_type_specification_region_vat_category
(
    id INT AUTO_INCREMENT PRIMARY KEY,
    field_definition_id INT NOT NULL,
    status INT NOT NULL,
    region VARCHAR(190) NOT NULL,
    vat_category VARCHAR(190) NOT NULL,
    CONSTRAINT ibexa_product_type_region_vat_category_product_region_fk
        UNIQUE (field_definition_id, status, region)
)
ENGINE = InnoDB
DEFAULT CHARSET = utf8mb4
COLLATE = utf8mb4_unicode_520_ci;

-- IBX-3333: Segment user map is not cleaned up after removing segments
DELETE `su` FROM `ibexa_segment_user_map` AS `su`
    LEFT JOIN `ibexa_segments` AS `s` ON `su`.`segment_id` = `s`.`id`
    WHERE `s`.`id` IS NULL;
