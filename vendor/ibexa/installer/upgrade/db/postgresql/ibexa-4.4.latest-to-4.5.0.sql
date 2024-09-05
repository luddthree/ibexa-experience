ALTER TABLE ibexa_product_specification_attribute_measurement_range
    ADD base_min_value DOUBLE PRECISION,
    ADD base_max_value DOUBLE PRECISION,
    ADD base_unit_id INTEGER;

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
            ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE ibexa_product_specification_attribute_measurement_value
    ADD base_unit_id INTEGER,
    ADD base_value DOUBLE PRECISION;

UPDATE ibexa_product_specification_attribute_measurement_value
SET base_value = value,
    base_unit_id = unit_id
    WHERE base_unit_id IS NULL;

CREATE INDEX ibexa_product_specification_attr_value_measurement_baseunit_idx
    ON ibexa_product_specification_attribute_measurement_value (base_unit_id);

ALTER TABLE ibexa_product_specification_attribute_measurement_value
    ADD CONSTRAINT ibexa_product_specification_attr_single_measurement_baseunit_fk
        FOREIGN KEY (base_unit_id) REFERENCES ibexa_measurement_unit (id)
            ON UPDATE CASCADE ON DELETE RESTRICT;

-- Drop ibexa_taxonomy_entries unique index on identifier column
DROP INDEX uniq_74706fd6772e836a;

CREATE TABLE ibexa_token_type
(
    id serial PRIMARY KEY,
    identifier varchar(64) NOT NULL
);

CREATE TABLE ibexa_token
(
    id serial PRIMARY KEY,
    type_id int NOT NULL
        CONSTRAINT ibexa_token_type_id_fk
            REFERENCES ibexa_token_type (id)
            ON DELETE CASCADE,
    token varchar(255) NOT NULL,
    identifier varchar(128) DEFAULT NULL,
    created int NOT NULL DEFAULT 0,
    expires int NOT NULL DEFAULT 0
);
