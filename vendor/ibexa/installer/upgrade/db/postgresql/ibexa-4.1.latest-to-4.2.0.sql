-- IBX-2560: Attribute Storage Schema
ALTER TABLE ibexa_product_specification_attribute
    ADD discriminator VARCHAR(190) DEFAULT '';

CREATE TABLE ibexa_product_specification_attribute_boolean (
    id INTEGER NOT NULL PRIMARY KEY
    CONSTRAINT ibexa_product_specification_attribute_boolean_fk
    REFERENCES ibexa_product_specification_attribute
    ON UPDATE CASCADE ON DELETE CASCADE,
    value BOOLEAN
);

CREATE INDEX ON ibexa_product_specification_attribute_boolean (value);

CREATE TABLE ibexa_product_specification_attribute_integer (
    id INTEGER NOT NULL PRIMARY KEY
    CONSTRAINT ibexa_product_specification_attribute_integer_fk
    REFERENCES ibexa_product_specification_attribute
    ON UPDATE CASCADE ON DELETE CASCADE,
    value INTEGER
);

CREATE INDEX ON ibexa_product_specification_attribute_integer (value);

CREATE TABLE ibexa_product_specification_attribute_float (
    id INTEGER NOT NULL PRIMARY KEY
    CONSTRAINT ibexa_product_specification_attribute_float_fk
    REFERENCES ibexa_product_specification_attribute
    ON UPDATE CASCADE ON DELETE CASCADE,
    value DOUBLE PRECISION
);

CREATE INDEX ON ibexa_product_specification_attribute_float (value);

CREATE TABLE ibexa_product_specification_attribute_selection (
    id INTEGER NOT NULL PRIMARY KEY
    CONSTRAINT ibexa_product_specification_attribute_selection_fk
    REFERENCES ibexa_product_specification_attribute
    ON UPDATE CASCADE ON DELETE CASCADE,
    value VARCHAR(190) DEFAULT NULL
);

CREATE INDEX ON ibexa_product_specification_attribute_selection (value);

CREATE TABLE ibexa_product_specification_attribute_simple_custom (
    id INTEGER NOT NULL PRIMARY KEY
    CONSTRAINT ibexa_product_specification_attribute_simple_custom_fk
        REFERENCES ibexa_product_specification_attribute
        ON UPDATE CASCADE ON DELETE CASCADE,
    value JSON
);

UPDATE ibexa_product_specification_attribute AS ipsa_target
SET discriminator = iad.type
FROM ibexa_product_specification_attribute AS ipsa_source
JOIN ibexa_attribute_definition iad on ipsa_source.attribute_definition_id = iad.id;

INSERT INTO ibexa_product_specification_attribute_boolean (id, value)
SELECT
    ipsa.id,
    CASE
        WHEN CAST(ipsa.value AS TEXT) = 'true'
            THEN true
        WHEN CAST(ipsa.value AS TEXT) = 'false'
            THEN false
        END
FROM ibexa_product_specification_attribute ipsa
WHERE ipsa.discriminator IN ('checkbox');

INSERT INTO ibexa_product_specification_attribute_selection (id, value)
SELECT
    ipsa.id,
    CASE
        WHEN json_typeof(ipsa.value) = 'string'
            -- This removes quotes around string after casting
            -- Postgres substr index is 1-based
            THEN substr(CAST(ipsa.value AS VARCHAR(190)), 2, length(CAST(ipsa.value AS VARCHAR(190)))-2)
        END
FROM ibexa_product_specification_attribute ipsa
WHERE ipsa.discriminator IN ('selection', 'color');

INSERT INTO ibexa_product_specification_attribute_integer (id, value)
SELECT
    ipsa.id,
    CASE
        WHEN json_typeof(ipsa.value) = 'number'
            -- This removes quotes around string after casting
            -- Postgres substr index is 1-based
            THEN CAST(
                CAST(ipsa.value AS TEXT
            ) AS INTEGER)
        END
FROM ibexa_product_specification_attribute ipsa
WHERE ipsa.discriminator IN ('integer');

INSERT INTO ibexa_product_specification_attribute_float (id, value)
SELECT
    ipsa.id,
    CASE
        WHEN json_typeof(ipsa.value) = 'number'
            -- This removes quotes around string after casting
            -- Postgres substr index is 1-based
            THEN CAST(
                CAST(ipsa.value AS TEXT
            ) AS DOUBLE PRECISION)
    END
FROM ibexa_product_specification_attribute ipsa
WHERE ipsa.discriminator IN ('float');

INSERT INTO ibexa_product_specification_attribute_simple_custom (id, value)
SELECT
    ipsa.id,
    ipsa.value
FROM ibexa_product_specification_attribute ipsa
WHERE ipsa.discriminator NOT IN ('boolean', 'color', 'float', 'integer', 'selection');

ALTER TABLE ibexa_product_specification_attribute DROP COLUMN value;

ALTER TABLE ibexa_product_specification_attribute
    ALTER discriminator SET NOT NULL;

ALTER TABLE ibexa_product_specification
    ALTER content_id DROP NOT NULL,
    ALTER field_id DROP NOT NULL,
    ALTER version_no DROP NOT NULL,
    ADD base_product_id INT DEFAULT NULL NULL;

CREATE INDEX ON ibexa_product_specification (base_product_id);

-- IBX-2666: Taxonomy Field type
DROP INDEX ibexa_taxonomy_assignments_unique_entry_content_idx;
DROP INDEX ibexa_taxonomy_assignments_content_id_idx;

-- Add new `version_no` column
ALTER TABLE ibexa_taxonomy_assignments ADD version_no INT NOT NULL DEFAULT 0;

-- Fill `version_no` value on all existing records
UPDATE "ibexa_taxonomy_assignments" AS ta
SET "version_no" = COALESCE(
        (
            SELECT "version"
            FROM "ezcontentobject_version" AS v
            WHERE
                v."contentobject_id" = ta."content_id"
                AND v."status" = 1
        ),
        (
            SELECT "version"
            FROM "ezcontentobject_version" AS v
            WHERE v."contentobject_id" = ta."content_id"
            ORDER BY v."version" DESC
            LIMIT 1
        ),
        0
    )
WHERE ta."version_no" = 0;

ALTER TABLE ibexa_taxonomy_assignments ALTER COLUMN version_no DROP DEFAULT;

-- Update indexes
CREATE UNIQUE INDEX ibexa_taxonomy_assignments_unique_entry_content_idx ON ibexa_taxonomy_assignments (entry_id, content_id, version_no);

CREATE INDEX ibexa_taxonomy_assignments_content_id_version_no_idx ON ibexa_taxonomy_assignments (content_id, version_no);


CREATE TABLE ibexa_catalog
(
    id           serial                                       primary key,
    identifier   varchar(64)                                     not null,
    creator_id   integer                                         not null,
    created      integer                                         not null,
    modified     integer                                         not null,
    status       varchar(255) default 'draft'::character varying not null,
    query_string text         default ''::text                   not null
);

CREATE INDEX ibexa_catalog_creator_idx ON ibexa_catalog (creator_id);

CREATE UNIQUE INDEX ibexa_catalog_identifier_idx ON ibexa_catalog (identifier);

CREATE TABLE ibexa_catalog_ml
(
    id              serial         primary key,
    catalog_id      integer        not null
        constraint ibexa_catalog_ml_fk
            references ibexa_catalog
            on update cascade on delete cascade,
    language_id     bigint         not null,
    name            varchar(190)   not null,
    name_normalized varchar(190)   not null,
    description     varchar(10000) not null
);

CREATE INDEX ibexa_catalog_catalog_idx ON ibexa_catalog_ml (catalog_id);

CREATE INDEX ibexa_catalog_language_idx ON ibexa_catalog_ml (language_id);

CREATE UNIQUE INDEX ibexa_catalog_ml_catalog_language_uidx ON ibexa_catalog_ml (catalog_id, language_id);

CREATE TABLE IF NOT EXISTS ibexa_user_invitations
(
    id               SERIAL PRIMARY KEY,
    email            VARCHAR(255) NOT NULL,
    site_access_name VARCHAR(255) NOT NULL,
    hash             VARCHAR(255) NOT NULL,
    creation_date    INTEGER      NOT NULL,
    used             BOOLEAN      DEFAULT false NOT NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS ibexa_user_invitations_email_uindex
    ON ibexa_user_invitations (email);

CREATE UNIQUE INDEX IF NOT EXISTS ibexa_user_invitations_hash_uindex
    ON ibexa_user_invitations (hash);

CREATE TABLE IF NOT EXISTS ibexa_user_invitations_assignments
(
    id               SERIAL PRIMARY KEY,
    invitation_id    INTEGER      NOT NULL,
    user_group_id    INTEGER      NULL,
    role_id          INTEGER      NULL,
    limitation_type  VARCHAR(255) NULL,
    limitation_value VARCHAR(255) NULL,
    CONSTRAINT ibexa_content_customer_group_attribute_fk
    FOREIGN KEY (invitation_id) REFERENCES ibexa_user_invitations (id)
    ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE ibexa_product_specification_asset
(
    id                       serial primary key,
    product_specification_id integer      not null,
    uri                      varchar(255) not null,
    tags                     json
);

CREATE INDEX ibexa_product_specification_asset_pid
    ON ibexa_product_specification_asset (product_specification_id);

-- IBX-3333: Segment user map is not cleaned up after removing segments
DELETE FROM "ibexa_segment_user_map" su
    WHERE NOT EXISTS (
        SELECT FROM "ibexa_segments" s WHERE s.id = su.segment_id
    );

CREATE TABLE ibexa_corporate_member_assignment
(
    id                        serial primary key,
    member_id                 integer          not null,
    member_role               varchar(128) not null,
    company_id                integer          not null,
    company_location_id       integer          not null,
    member_role_assignment_id integer          not null
);

CREATE UNIQUE INDEX ibexa_corporate_member_assignment_unique_idx ON ibexa_corporate_member_assignment (member_id, company_id);

CREATE INDEX ibexa_corporate_member_assignment_company_idx
    ON ibexa_corporate_member_assignment (company_id);

CREATE INDEX ibexa_corporate_member_assignment_location_idx
    ON ibexa_corporate_member_assignment (company_location_id);

CREATE INDEX ibexa_corporate_member_assignment_member_idx
    ON ibexa_corporate_member_assignment (member_id);

CREATE index ibexa_corporate_member_assignment_member_role_assignment_idx
    ON ibexa_corporate_member_assignment (member_role_assignment_id);

-- IBX-3203
CREATE TABLE ibexa_product_specification_attribute_measurement_value
(
    id INTEGER NOT NULL
        PRIMARY KEY
        CONSTRAINT ibexa_product_specification_attr_single_measurement_value_fk
            REFERENCES ibexa_product_specification_attribute
            ON UPDATE CASCADE ON DELETE CASCADE,
    unit_id INTEGER
        CONSTRAINT ibexa_product_specification_attr_single_measurement_unit_fk
            REFERENCES ibexa_measurement_unit
            ON UPDATE CASCADE ON DELETE RESTRICT,
    value DOUBLE PRECISION
);

CREATE INDEX ibexa_product_specification_attr_value_measurement_unit_idx
    ON ibexa_product_specification_attribute_measurement_value (unit_id);

CREATE TABLE ibexa_product_specification_attribute_measurement_range
(
    id INTEGER NOT NULL
        PRIMARY KEY
        CONSTRAINT ibexa_product_specification_attr_range_measurement_range_fk
            REFERENCES ibexa_product_specification_attribute
            ON UPDATE CASCADE ON DELETE CASCADE,
    unit_id INTEGER
        CONSTRAINT ibexa_product_specification_attr_range_measurement_unit_fk
            REFERENCES ibexa_measurement_unit
            ON UPDATE CASCADE ON DELETE RESTRICT,
    min_value DOUBLE PRECISION,
    max_value DOUBLE PRECISION
);

CREATE INDEX ibexa_product_specification_attr_range_measurement_unit_idx
    ON ibexa_product_specification_attribute_measurement_range (unit_id);

-- Add measurement types and units, if missing
INSERT INTO ibexa_measurement_type (name)
SELECT DISTINCT
    iad.options ->> 'type' AS type_name
FROM ibexa_attribute_definition iad
    LEFT JOIN ibexa_measurement_type imt ON iad.options ->> 'type' = imt.name
WHERE (iad.options -> 'type') IS NOT NULL
    AND (iad.options -> 'unit') IS NOT NULL
    AND imt.id IS NULL;

INSERT INTO ibexa_measurement_unit (type_id, identifier)
SELECT DISTINCT
    imt.id AS type_id,
    iad.options ->> 'unit' AS unit_identifier
FROM ibexa_attribute_definition iad
    JOIN ibexa_measurement_type imt ON iad.options ->> 'type' = imt.name
    LEFT JOIN ibexa_measurement_unit imu ON iad.options ->> 'unit' = imu.identifier
WHERE (iad.options -> 'type') IS NOT NULL
    AND (iad.options -> 'unit') IS NOT NULL
    AND imu.id IS NULL;

-- Update attribute discriminator for measurements
UPDATE ibexa_attribute_definition iad
SET type = CASE CAST(iad.options ->> 'inputType' AS INTEGER)
    WHEN 0 THEN 'measurement_single'
    ELSE 'measurement_range'
END
WHERE iad.type = 'measurement';

UPDATE ibexa_product_specification_attribute ipsa
SET discriminator = CASE (ipsasc.value -> 'value') IS NOT NULL
    WHEN true THEN 'measurement_single'
    ELSE 'measurement_range'
END
FROM ibexa_product_specification_attribute_simple_custom ipsasc
WHERE ipsa.discriminator = 'measurement' AND ipsa.id = ipsasc.id;

-- Convert attribute data and insert them into new tables
INSERT INTO ibexa_product_specification_attribute_measurement_range (id, unit_id, min_value, max_value)
SELECT
    ipsa.id,
    imu.id,
    CAST(ipsasc.value ->> 'measurementRangeMinimumValue' AS DOUBLE PRECISION),
    CAST(ipsasc.value ->> 'measurementRangeMaximumValue' AS DOUBLE PRECISION)
FROM ibexa_product_specification_attribute ipsa
    LEFT JOIN ibexa_product_specification_attribute_measurement_range ipsamr ON ipsa.id = ipsamr.id
    JOIN ibexa_product_specification_attribute_simple_custom ipsasc ON ipsa.id = ipsasc.id
    JOIN ibexa_attribute_definition iad ON ipsa.attribute_definition_id = iad.id
    JOIN ibexa_measurement_type imt ON imt.name = COALESCE(ipsasc.value ->> 'measurementType', iad.options ->> 'type')
    JOIN ibexa_measurement_unit imu ON imu.identifier = COALESCE(ipsasc.value ->> 'measurementUnit', iad.options ->> 'unit')
        AND imt.id = imu.type_id
WHERE ipsa.discriminator = 'measurement_range'
    AND ipsamr.id IS NULL
    AND (ipsasc.value -> 'measurementRangeMinimumValue') IS NOT NULL
    AND (ipsasc.value -> 'measurementRangeMaximumValue') IS NOT NULL
;

INSERT INTO ibexa_product_specification_attribute_measurement_value (id, unit_id, value)
SELECT
    ipsa.id,
    imu.id,
    CAST(ipsasc.value ->> 'value' AS DOUBLE PRECISION)
FROM ibexa_product_specification_attribute ipsa
    LEFT JOIN ibexa_product_specification_attribute_measurement_value ipsamv ON ipsa.id = ipsamv.id
    JOIN ibexa_product_specification_attribute_simple_custom ipsasc ON ipsa.id = ipsasc.id
    JOIN ibexa_attribute_definition iad ON ipsa.attribute_definition_id = iad.id
    JOIN ibexa_measurement_type imt ON imt.name = COALESCE(ipsasc.value ->> 'measurementType', iad.options ->> 'type')
    JOIN ibexa_measurement_unit imu ON imu.identifier = COALESCE(ipsasc.value ->> 'measurementUnit', iad.options ->> 'unit')
        AND imt.id = imu.type_id
WHERE ipsa.discriminator = 'measurement_single'
  AND ipsamv.id IS NULL
  AND (ipsasc.value -> 'value') IS NOT NULL
;

-- Remove previous entries that were moved to new tables
DELETE FROM ibexa_product_specification_attribute_simple_custom ipsasc
WHERE EXISTS (
    SELECT FROM ibexa_product_specification_attribute_measurement_value ipsamv WHERE ipsamv.id = ipsasc.id
);

DELETE FROM ibexa_product_specification_attribute_simple_custom ipsasc
WHERE EXISTS (
    SELECT FROM ibexa_product_specification_attribute_measurement_range ipsamr WHERE ipsamr.id = ipsasc.id
);
