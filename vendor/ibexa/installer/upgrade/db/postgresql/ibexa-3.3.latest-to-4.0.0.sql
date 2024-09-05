ALTER TABLE "ezcontentclassgroup" ADD "is_system" boolean DEFAULT false NOT NULL;

CREATE TABLE IF NOT EXISTS ibexa_attribute_group
(
    id SERIAL PRIMARY KEY,
    identifier VARCHAR(64) NOT NULL,
    position INTEGER DEFAULT 0 NOT NULL
    );

CREATE TABLE IF NOT EXISTS ibexa_attribute_definition
(
    id SERIAL PRIMARY KEY,
    attribute_group_id INTEGER NOT NULL
    CONSTRAINT ibexa_attribute_definition_attribute_group_fk
    REFERENCES ibexa_attribute_group
    ON UPDATE CASCADE ON DELETE CASCADE,
    identifier VARCHAR(64) NOT NULL,
    type VARCHAR(32) NOT NULL,
    position INTEGER DEFAULT 0 NOT NULL,
    options JSON
    );

CREATE INDEX IF NOT EXISTS ibexa_attribute_definition_attribute_group_idx
    ON ibexa_attribute_definition (attribute_group_id);

CREATE UNIQUE INDEX IF NOT EXISTS attribute_definition_identifier_idx
    ON ibexa_attribute_definition (identifier);

CREATE TABLE IF NOT EXISTS ibexa_attribute_definition_ml
(
    id SERIAL PRIMARY KEY,
    attribute_definition_id INTEGER NOT NULL
    CONSTRAINT ibexa_attribute_definition_ml_attribute_definition_fk
    REFERENCES ibexa_attribute_definition
    ON UPDATE CASCADE ON DELETE CASCADE,
    language_id BIGINT NOT NULL
    CONSTRAINT ibexa_attribute_definition_ml_language_fk
    REFERENCES ezcontent_language
    ON UPDATE CASCADE ON DELETE CASCADE,
    name VARCHAR(190) NOT NULL,
    name_normalized VARCHAR(190) NOT NULL,
    description VARCHAR(10000) NOT NULL
    );

CREATE INDEX IF NOT EXISTS ibexa_attribute_definition_ml_language_idx
    ON ibexa_attribute_definition_ml (language_id);

CREATE INDEX IF NOT EXISTS ibexa_attribute_definition_ml_attribute_definition_idx
    ON ibexa_attribute_definition_ml (attribute_definition_id);

CREATE INDEX IF NOT EXISTS ibexa_attribute_definition_ml_name_idx
    ON ibexa_attribute_definition_ml (name_normalized);

CREATE UNIQUE INDEX IF NOT EXISTS ibexa_attribute_definition_ml_uidx
    ON ibexa_attribute_definition_ml (attribute_definition_id, language_id);

CREATE UNIQUE INDEX IF NOT EXISTS ibexa_attribute_group_identifier_uidx
    ON ibexa_attribute_group (identifier);

CREATE TABLE IF NOT EXISTS ibexa_attribute_group_ml
(
    id SERIAL PRIMARY KEY,
    attribute_group_id INTEGER NOT NULL
    CONSTRAINT ibexa_attribute_group_ml_attribute_group_fk
    REFERENCES ibexa_attribute_group
    ON UPDATE CASCADE ON DELETE CASCADE,
    language_id BIGINT NOT NULL
    CONSTRAINT ibexa_attribute_group_ml_language_fk
    REFERENCES ezcontent_language
    ON UPDATE CASCADE ON DELETE CASCADE,
    name VARCHAR(190) NOT NULL,
    name_normalized VARCHAR(190) NOT NULL
    );

CREATE INDEX IF NOT EXISTS ibexa_attribute_group_ml_language_idx
    ON ibexa_attribute_group_ml (language_id);

CREATE INDEX IF NOT EXISTS ibexa_attribute_group_ml_attribute_group_idx
    ON ibexa_attribute_group_ml (attribute_group_id);

CREATE INDEX IF NOT EXISTS ibexa_attribute_group_ml_name_idx
    ON ibexa_attribute_group_ml (name_normalized);

CREATE UNIQUE INDEX IF NOT EXISTS attribute_group_ml_idx
    ON ibexa_attribute_group_ml (attribute_group_id, language_id);

CREATE TABLE IF NOT EXISTS ibexa_attribute_definition_assignment
(
    id SERIAL PRIMARY KEY,
    attribute_definition_id INTEGER NOT NULL
    CONSTRAINT ibexa_attribute_definition_assignment_attribute_definition_fk
    REFERENCES ibexa_attribute_definition
    ON UPDATE CASCADE ON DELETE CASCADE,
    field_definition_id INTEGER NOT NULL,
    status INTEGER NOT NULL,
    required BOOLEAN DEFAULT false NOT NULL,
    discriminator BOOLEAN DEFAULT false NOT NULL
);

CREATE INDEX IF NOT EXISTS ibexa_attribute_definition_assignment_field_definition_idx
    ON ibexa_attribute_definition_assignment (field_definition_id);

CREATE INDEX IF NOT EXISTS ibexa_attribute_definition_assignment_attribute_definition_idx
    ON ibexa_attribute_definition_assignment (attribute_definition_id);

CREATE UNIQUE INDEX IF NOT EXISTS ibexa_attribute_definition_assignment_main_uidx
    ON ibexa_attribute_definition_assignment (field_definition_id, status, attribute_definition_id);

CREATE TABLE IF NOT EXISTS ibexa_customer_group
(
    id SERIAL PRIMARY KEY,
    identifier VARCHAR(64) NOT NULL,
    global_price_rate NUMERIC(5, 2) DEFAULT '0'::NUMERIC NOT NULL
    );

CREATE UNIQUE INDEX IF NOT EXISTS ibexa_customer_group_identifier_idx
    ON ibexa_customer_group (identifier);

CREATE TABLE IF NOT EXISTS ibexa_customer_group_ml
(
    id SERIAL PRIMARY KEY,
    customer_group_id INTEGER NOT NULL
    CONSTRAINT ibexa_customer_group__ml_fk
    REFERENCES ibexa_customer_group
    ON UPDATE CASCADE ON DELETE CASCADE,
    language_id BIGINT NOT NULL,
    name VARCHAR(190) NOT NULL,
    name_normalized VARCHAR(190) NOT NULL,
    description VARCHAR(10000) NOT NULL
    );

CREATE INDEX IF NOT EXISTS ibexa_customer_group_idx
    ON ibexa_customer_group_ml (customer_group_id);

CREATE INDEX IF NOT EXISTS ibexa_language_idx
    ON ibexa_customer_group_ml (language_id);

CREATE UNIQUE INDEX IF NOT EXISTS ibexa_customer_group_ml_customer_group_language_uidx
    ON ibexa_customer_group_ml (customer_group_id, language_id);

CREATE TABLE IF NOT EXISTS ibexa_content_customer_group
(
    id SERIAL PRIMARY KEY,
    field_id INTEGER NOT NULL,
    field_version_no INTEGER NOT NULL,
    customer_group_id INTEGER NOT NULL
    CONSTRAINT ibexa_content_customer_group_customer_group_fk
    REFERENCES ibexa_customer_group
    ON UPDATE CASCADE ON DELETE CASCADE,
    content_id INTEGER NOT NULL,
    CONSTRAINT ibexa_content_customer_group_attribute_fk
    FOREIGN KEY (field_id, field_version_no) REFERENCES ezcontentobject_attribute
    ON UPDATE CASCADE ON DELETE CASCADE
    );

CREATE INDEX IF NOT EXISTS ibexa_content_customer_group_customer_group_idx
    ON ibexa_content_customer_group (customer_group_id);

CREATE UNIQUE INDEX IF NOT EXISTS ibexa_content_customer_group_attribute_uidx
    ON ibexa_content_customer_group (field_id, field_version_no);

CREATE TABLE IF NOT EXISTS ibexa_product_specification
(
    id SERIAL PRIMARY KEY,
    content_id INTEGER NOT NULL,
    version_no INTEGER NOT NULL,
    field_id INTEGER NOT NULL,
    code VARCHAR(64) NOT NULL
    );

CREATE INDEX IF NOT EXISTS ibexa_product_specification_cv
    ON ibexa_product_specification (content_id, version_no);

CREATE INDEX IF NOT EXISTS ibexa_product_specification_fid
    ON ibexa_product_specification (field_id);

CREATE INDEX IF NOT EXISTS ibexa_product_specification_pc
    ON ibexa_product_specification (code);

CREATE TABLE IF NOT EXISTS ibexa_product_specification_attribute
(
    id SERIAL PRIMARY KEY,
    product_specification_id INTEGER NOT NULL
    CONSTRAINT ibexa_product_specification_attribute_sid_fk
    REFERENCES ibexa_product_specification
    ON UPDATE CASCADE ON DELETE CASCADE,
    attribute_definition_id  INTEGER NOT NULL
    CONSTRAINT ibexa_product_specification_attribute_aid
    REFERENCES ibexa_attribute_definition
    ON UPDATE CASCADE ON DELETE CASCADE,
    value JSON
);

CREATE INDEX IF NOT EXISTS ibexa_product_specification_attribute_sid_idx
    ON ibexa_product_specification_attribute (product_specification_id);

CREATE INDEX IF NOT EXISTS ibexa_product_specification_attribute_aid_idx
    ON ibexa_product_specification_attribute (attribute_definition_id);

CREATE TABLE IF NOT EXISTS ibexa_currency
(
    id SERIAL PRIMARY KEY,
    code VARCHAR(3) NOT NULL,
    subunits SMALLINT NOT NULL,
    enabled BOOLEAN DEFAULT true NOT NULL
    );

CREATE UNIQUE INDEX IF NOT EXISTS ibexa_currency_code_idx
    ON ibexa_currency (code);

CREATE TABLE IF NOT EXISTS ibexa_product_specification_price
(
    id SERIAL PRIMARY KEY,
    currency_id INTEGER NOT NULL
    CONSTRAINT ibexa_product_specification_price_currency_fk
    REFERENCES ibexa_currency
    ON UPDATE CASCADE ON DELETE CASCADE,
    product_code VARCHAR(64) NOT NULL,
    discriminator VARCHAR(20) DEFAULT 'main'::character varying NOT NULL,
    amount NUMERIC(19, 4) NOT NULL,
    custom_price_amount NUMERIC(19, 4) DEFAULT NULL::NUMERIC
    );

CREATE INDEX IF NOT EXISTS ibexa_product_specification_price_product_code_idx
    ON ibexa_product_specification_price (product_code);

CREATE INDEX IF NOT EXISTS ibexa_product_specification_price_currency_idx
    ON ibexa_product_specification_price (currency_id);

CREATE TABLE IF NOT EXISTS ibexa_product_specification_price_customer_group
(
    id INTEGER NOT NULL PRIMARY KEY,
    customer_group_id INTEGER NOT NULL
    CONSTRAINT ibexa_product_specification_price_customer_group_fk
    REFERENCES ibexa_customer_group
    ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS ibexa_product_specification_price_customer_group_idx
    ON ibexa_product_specification_price_customer_group (customer_group_id);

CREATE TABLE IF NOT EXISTS ibexa_product_type_specification_region_vat_category
(
    id SERIAL PRIMARY KEY,
    field_definition_id INTEGER NOT NULL,
    status INTEGER NOT NULL,
    region VARCHAR(190) NOT NULL,
    vat_category VARCHAR(190) NOT NULL
    );

CREATE UNIQUE INDEX IF NOT EXISTS ibexa_product_type_region_vat_category_product_region_fk
    ON ibexa_product_type_specification_region_vat_category (field_definition_id, status, region);

CREATE TABLE IF NOT EXISTS ibexa_product_specification_availability
(
    id SERIAL PRIMARY KEY,
    product_code VARCHAR(64) NOT NULL,
    availability BOOLEAN DEFAULT false NOT NULL,
    stock INTEGER
    );

CREATE UNIQUE INDEX IF NOT EXISTS ibexa_product_specification_availability_product_code_uidx
    ON ibexa_product_specification_availability (product_code);

-- IBX-3333: Segment user map is not cleaned up after removing segments
DELETE FROM "ibexa_segment_user_map" su
    WHERE NOT EXISTS (
        SELECT FROM "ibexa_segments" s WHERE s.id = su.segment_id
    );
