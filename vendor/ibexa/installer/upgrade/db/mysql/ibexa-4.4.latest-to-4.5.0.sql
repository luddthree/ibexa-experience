ALTER TABLE ibexa_product_specification_attribute_measurement_range
    ADD base_unit_id INT NULL AFTER unit_id,
    ADD base_min_value DOUBLE NULL,
    ADD base_max_value DOUBLE NULL;

UPDATE ibexa_product_specification_attribute_measurement_range
SET base_min_value = min_value,
    base_max_value = max_value,
    base_unit_id = unit_id
    WHERE base_unit_id IS NULL;

CREATE INDEX ibexa_product_specification_attr_range_measurement_baseunit_idx
    ON ibexa_product_specification_attribute_measurement_range (base_unit_id);

ALTER TABLE ibexa_product_specification_attribute_measurement_range
    ADD CONSTRAINT ibexa_product_specification_attr_range_measurement_baseunit_fk
        FOREIGN KEY (base_unit_id) REFERENCES ibexa_measurement_unit (id)
            ON UPDATE CASCADE;

ALTER TABLE ibexa_product_specification_attribute_measurement_value
    ADD base_unit_id INT NULL AFTER unit_id,
    ADD base_value DOUBLE NULL;

UPDATE ibexa_product_specification_attribute_measurement_value
SET base_value = value,
    base_unit_id = unit_id
    WHERE base_unit_id IS NULL;

CREATE INDEX ibexa_product_specification_attr_value_measurement_baseunit_idx
    ON ibexa_product_specification_attribute_measurement_value (base_unit_id);

ALTER TABLE ibexa_product_specification_attribute_measurement_value
    ADD CONSTRAINT ibexa_product_specification_attr_single_measurement_baseunit_fk
        FOREIGN KEY (base_unit_id) REFERENCES ibexa_measurement_unit (id)
            ON UPDATE CASCADE;

-- Drop ibexa_taxonomy_entries unique index on identifier column
ALTER TABLE ibexa_taxonomy_entries DROP KEY UNIQ_74706FD6772E836A;

CREATE TABLE ibexa_token_type
(
    id int(11) NOT NULL AUTO_INCREMENT,
    identifier varchar(64) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY ibexa_token_type_unique (identifier)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

CREATE TABLE ibexa_token
(
    id int(11) NOT NULL AUTO_INCREMENT,
    type_id int(11) NOT NULL,
    token varchar(255) NOT NULL,
    identifier varchar(128) DEFAULT NULL,
    created int(11) NOT NULL DEFAULT 0,
    expires int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE KEY ibexa_token_unique (token,identifier,type_id),
    CONSTRAINT ibexa_token_type_id_fk
        FOREIGN KEY (type_id) REFERENCES ibexa_token_type (id)
            ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
