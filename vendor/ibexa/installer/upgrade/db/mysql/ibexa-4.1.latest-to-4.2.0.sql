-- IBX-2560: Attribute Storage Schema
ALTER TABLE ibexa_product_specification_attribute
    ADD discriminator VARCHAR(190) DEFAULT '';

CREATE TABLE ibexa_product_specification_attribute_boolean
(
    id INT NOT NULL PRIMARY KEY,
    value TINYINT(1) NULL,
    CONSTRAINT ibexa_product_specification_attribute_boolean_fk
        FOREIGN KEY (id) REFERENCES ibexa_product_specification_attribute (id)
            ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE INDEX ibexa_product_specification_attribute_boolean_value_idx
    ON ibexa_product_specification_attribute_boolean (value);

CREATE TABLE ibexa_product_specification_attribute_float
(
    id INT NOT NULL PRIMARY KEY,
    value DOUBLE NULL,
    CONSTRAINT ibexa_product_specification_attribute_float_fk
        FOREIGN KEY (id) REFERENCES ibexa_product_specification_attribute (id)
            ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE INDEX ibexa_product_specification_attribute_float_value_idx
    ON ibexa_product_specification_attribute_float (value);

CREATE TABLE ibexa_product_specification_attribute_integer
(
    id INT NOT NULL PRIMARY KEY,
    value INT NULL,
    CONSTRAINT ibexa_product_specification_attribute_integer_fk
        FOREIGN KEY (id) REFERENCES ibexa_product_specification_attribute (id)
            ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE INDEX ibexa_product_specification_attribute_integer_value_idx
    ON ibexa_product_specification_attribute_integer (value);

CREATE TABLE ibexa_product_specification_attribute_selection
(
    id INT NOT NULL PRIMARY KEY,
    value VARCHAR(190) NULL,
    CONSTRAINT ibexa_product_specification_attribute_selection_fk
        FOREIGN KEY (id) REFERENCES ibexa_product_specification_attribute (id)
            ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE INDEX ibexa_product_specification_attribute_selection_value_idx
    ON ibexa_product_specification_attribute_selection (value);

CREATE TABLE ibexa_product_specification_attribute_simple_custom
(
    id INT NOT NULL PRIMARY KEY,
    value JSON NULL,
    CONSTRAINT ibexa_product_specification_attribute_simple_custom_fk
        FOREIGN KEY (id) REFERENCES ibexa_product_specification_attribute (id)
            ON UPDATE CASCADE ON DELETE CASCADE
);

UPDATE ibexa_product_specification_attribute ipsa
    JOIN ibexa_attribute_definition iad ON iad.id = ipsa.attribute_definition_id
    SET ipsa.discriminator = iad.type;

INSERT INTO ibexa_product_specification_attribute_selection (id, value)
SELECT
    ipsa.id,
    CASE
        WHEN JSON_TYPE(ipsa.value) = 'STRING'
            THEN JSON_UNQUOTE(ipsa.value)
        END AS value
FROM ibexa_product_specification_attribute ipsa
WHERE ipsa.discriminator IN ('selection', 'color');

INSERT INTO ibexa_product_specification_attribute_integer (id, value)
SELECT
    ipsa.id,
    CASE
        WHEN JSON_TYPE(ipsa.value) = 'INTEGER'
            THEN ipsa.value
        END AS value
FROM ibexa_product_specification_attribute ipsa
WHERE ipsa.discriminator IN ('integer');

INSERT INTO ibexa_product_specification_attribute_float (id, value)
SELECT
    ipsa.id,
    CASE
        WHEN JSON_TYPE(ipsa.value) IN ('INTEGER', 'DOUBLE')
            THEN ipsa.value
        END AS value
FROM ibexa_product_specification_attribute ipsa
WHERE ipsa.discriminator IN ('float');

INSERT INTO ibexa_product_specification_attribute_boolean (id, value)
SELECT
    ipsa.id,
    CASE
        WHEN JSON_TYPE(ipsa.value) = 'BOOLEAN'
            THEN ipsa.value
        END AS value
FROM ibexa_product_specification_attribute ipsa
WHERE ipsa.discriminator IN ('checkbox');

INSERT INTO ibexa_product_specification_attribute_simple_custom (id, value)
SELECT
    ipsa.id,
    ipsa.value
FROM ibexa_product_specification_attribute ipsa
WHERE ipsa.discriminator NOT IN ('boolean', 'color', 'float', 'integer', 'selection');

ALTER TABLE ibexa_product_specification_attribute DROP COLUMN value;

ALTER TABLE ibexa_product_specification_attribute
    MODIFY COLUMN discriminator VARCHAR(190) NOT NULL;

ALTER TABLE ibexa_product_specification
    MODIFY COLUMN content_id INT NULL,
    MODIFY COLUMN field_id INT NULL,
    MODIFY COLUMN version_no INT NULL,
    ADD COLUMN base_product_id INT NULL DEFAULT NULL;

CREATE INDEX ibexa_product_specification_base_pid
    ON ibexa_product_specification (base_product_id);

-- IBX-2666: Taxonomy Field type
DROP INDEX ibexa_taxonomy_assignments_unique_entry_content_idx ON ibexa_taxonomy_assignments;
DROP INDEX ibexa_taxonomy_assignments_content_id_idx ON ibexa_taxonomy_assignments;

-- Add new `version_no` column
ALTER TABLE ibexa_taxonomy_assignments ADD version_no INT NOT NULL DEFAULT 0;

-- Fill `version_no` value on all existing records
UPDATE `ibexa_taxonomy_assignments` AS `ta`
SET `version_no` = COALESCE(
        (
            SELECT `version`
            FROM `ezcontentobject_version` AS `v`
            WHERE
                `v`.`contentobject_id` = `ta`.`content_id`
                AND `v`.`status` = 1
        ),
        (
            SELECT `version`
            FROM `ezcontentobject_version` AS `v`
            WHERE `v`.`contentobject_id` = `ta`.`content_id`
            ORDER BY `v`.`version` DESC
            LIMIT 1
        ),
        0
    )
WHERE `ta`.`version_no` = 0;

ALTER TABLE ibexa_taxonomy_assignments ALTER COLUMN version_no DROP DEFAULT;

-- Update indexes
CREATE UNIQUE INDEX ibexa_taxonomy_assignments_unique_entry_content_idx ON ibexa_taxonomy_assignments (entry_id, content_id, version_no);

CREATE INDEX ibexa_taxonomy_assignments_content_id_version_no_idx ON ibexa_taxonomy_assignments (content_id, version_no);

CREATE TABLE ibexa_catalog
(
    id           int auto_increment        primary key,
    identifier   varchar(64)                  not null,
    creator_id   int                          not null,
    created      int                          not null,
    modified     int                          not null,
    status       varchar(255) default 'draft' not null,
    query_string longtext                     not null,
    CONSTRAINT ibexa_catalog_identifier_idx
        UNIQUE (identifier)
);

CREATE INDEX ibexa_catalog_creator_idx
    ON ibexa_catalog (creator_id);

CREATE TABLE ibexa_catalog_ml
(
    id              int auto_increment primary key,
    catalog_id      int            not null,
    language_id     bigint         not null,
    name            varchar(190)   not null,
    name_normalized varchar(190)   not null,
    description     varchar(10000) not null,
    CONSTRAINT ibexa_catalog_ml_catalog_language_uidx
        UNIQUE (catalog_id, language_id),
    CONSTRAINT ibexa_catalog_ml_fk
        FOREIGN KEY (catalog_id) REFERENCES ibexa_catalog (id)
            ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE INDEX ibexa_catalog_catalog_idx
    ON ibexa_catalog_ml (catalog_id);

CREATE INDEX ibexa_catalog_language_idx
    ON ibexa_catalog_ml (language_id);

CREATE TABLE IF NOT EXISTS ibexa_user_invitations
(
    id               int auto_increment primary key,
    email            varchar(255) not null,
    site_access_name varchar(255) not null,
    hash             varchar(255) not null,
    creation_date    int          not null,
    used             tinyint(1)   null,
    constraint ibexa_user_invitations_email_uindex
    unique (email(191)),
    constraint ibexa_user_invitations_hash_uindex
    unique (hash(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS ibexa_user_invitations_assignments
(
    id               int auto_increment primary key,
    invitation_id    int          not null,
    user_group_id    int          null,
    role_id          int          null,
    limitation_type  varchar(255) null,
    limitation_value varchar(255) null,
    constraint ibexa_user_invitations_assignments_ibexa_user_invitations_id_fk
    foreign key (invitation_id) references ibexa_user_invitations (id)
    on update cascade on delete cascade
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

CREATE TABLE ibexa_product_specification_asset
(
    id                       int auto_increment primary key,
    product_specification_id int          not null,
    uri                      varchar(255) not null,
    tags                     json         null
);

CREATE INDEX ibexa_product_specification_asset_pid
    on ibexa_product_specification_asset (product_specification_id);

-- IBX-3333: Segment user map is not cleaned up after removing segments
DELETE `su` FROM `ibexa_segment_user_map` AS `su`
    LEFT JOIN `ibexa_segments` AS `s` ON `su`.`segment_id` = `s`.`id`
    WHERE `s`.`id` IS NULL;

CREATE TABLE ibexa_corporate_member_assignment
(
    id                        int auto_increment primary key,
    member_id                 int          not null,
    member_role               varchar(128) not null,
    company_id                int          not null,
    company_location_id       int          not null,
    member_role_assignment_id int          not null,
    constraint ibexa_corporate_member_assignment_unique_idx
        unique (member_id, company_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

CREATE INDEX ibexa_corporate_member_assignment_company_idx
    ON ibexa_corporate_member_assignment (company_id);

CREATE INDEX ibexa_corporate_member_assignment_location_idx
    ON ibexa_corporate_member_assignment (company_location_id);

CREATE INDEX ibexa_corporate_member_assignment_member_idx
    ON ibexa_corporate_member_assignment (member_id);

CREATE INDEX ibexa_corporate_member_assignment_member_role_assignment_idx
    ON ibexa_corporate_member_assignment (member_role_assignment_id);

-- IBX-3203
CREATE TABLE ibexa_product_specification_attribute_measurement_range
(
    id INT NOT NULL PRIMARY KEY,
    unit_id INT NULL,
    min_value DOUBLE NULL,
    max_value DOUBLE NULL,
    CONSTRAINT ibexa_product_specification_attr_range_measurement_range_fk
        FOREIGN KEY (id) REFERENCES ibexa_product_specification_attribute (id)
            ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT ibexa_product_specification_attr_range_measurement_unit_fk
        FOREIGN KEY (unit_id) REFERENCES ibexa_measurement_unit (id)
            ON UPDATE CASCADE
);

CREATE INDEX ibexa_product_specification_attr_range_measurement_unit_idx
    ON ibexa_product_specification_attribute_measurement_range (unit_id);

CREATE TABLE ibexa_product_specification_attribute_measurement_value
(
    id INT NOT NULL PRIMARY KEY,
    unit_id INT NULL,
    value DOUBLE NULL,
    CONSTRAINT ibexa_product_specification_attr_single_measurement_unit_fk
        FOREIGN KEY (unit_id) REFERENCES ibexa_measurement_unit (id)
            ON UPDATE CASCADE,
    CONSTRAINT ibexa_product_specification_attr_single_measurement_value_fk
        FOREIGN KEY (id) REFERENCES ibexa_product_specification_attribute (id)
            ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE INDEX ibexa_product_specification_attr_value_measurement_unit_idx
    ON ibexa_product_specification_attribute_measurement_value (unit_id);

-- Add measurement types and units, if missing
INSERT INTO ibexa_measurement_type (name)
SELECT DISTINCT
    JSON_UNQUOTE(JSON_EXTRACT(iad.options, '$.type')) AS type_name
FROM ibexa_attribute_definition iad
    LEFT JOIN ibexa_measurement_type imt ON JSON_UNQUOTE(JSON_EXTRACT(iad.options, '$.type')) = imt.name
WHERE JSON_CONTAINS_PATH(iad.options, 'all', '$.type', '$.unit') AND imt.id IS NULL;

INSERT INTO ibexa_measurement_unit (type_id, identifier)
SELECT DISTINCT
    imt.id AS type_id,
    JSON_UNQUOTE(JSON_EXTRACT(iad.options, '$.unit')) AS unit_identifier
FROM ibexa_attribute_definition iad
    JOIN ibexa_measurement_type imt ON JSON_UNQUOTE(JSON_EXTRACT(iad.options, '$.type')) = imt.name
    LEFT JOIN ibexa_measurement_unit imu ON JSON_UNQUOTE(JSON_EXTRACT(iad.options, '$.unit')) = imu.identifier
WHERE JSON_CONTAINS_PATH(iad.options, 'all', '$.type', '$.unit') AND imu.id IS NULL;

-- Update attribute discriminator for measurements
UPDATE ibexa_product_specification_attribute ipsa
    JOIN ibexa_product_specification_attribute_simple_custom ipsasc ON ipsa.id = ipsasc.id
SET ipsa.discriminator = IF(JSON_CONTAINS_PATH(ipsasc.value, 'all', '$.value') = 1, 'measurement_single', 'measurement_range')
WHERE ipsa.discriminator = 'measurement';

UPDATE ibexa_attribute_definition iad
SET type = IF(JSON_EXTRACT(iad.options, '$.inputType') = 0, 'measurement_single', 'measurement_range')
WHERE iad.type = 'measurement';

-- Convert attribute data and insert them into new tables
INSERT INTO ibexa_product_specification_attribute_measurement_range (id, unit_id, min_value, max_value)
SELECT
    ipsa.id,
    imu.id,
    JSON_EXTRACT(ipsasc.value, '$.measurementRangeMinimumValue'),
    JSON_EXTRACT(ipsasc.value, '$.measurementRangeMaximumValue')
FROM ibexa_product_specification_attribute ipsa
    LEFT JOIN ibexa_product_specification_attribute_measurement_range ipsamr ON ipsa.id = ipsamr.id
    JOIN ibexa_product_specification_attribute_simple_custom ipsasc ON ipsa.id = ipsasc.id
    JOIN ibexa_attribute_definition iad ON ipsa.attribute_definition_id = iad.id
    JOIN ibexa_measurement_type imt ON imt.name = COALESCE(
        JSON_UNQUOTE(JSON_EXTRACT(ipsasc.value, '$.measurementType')),
        JSON_UNQUOTE(JSON_EXTRACT(iad.options, '$.type'))
    )
    JOIN ibexa_measurement_unit imu ON
        imu.identifier = COALESCE(
            JSON_UNQUOTE(JSON_EXTRACT(ipsasc.value, '$.measurementUnit')),
            JSON_UNQUOTE(JSON_EXTRACT(iad.options, '$.unit'))
        )
        AND imt.id = imu.type_id
    WHERE ipsa.discriminator = 'measurement_range'
        AND ipsamr.id IS NULL
        AND JSON_CONTAINS_PATH(ipsasc.value, 'all', '$.measurementRangeMaximumValue', '$.measurementRangeMinimumValue')
;

INSERT INTO ibexa_product_specification_attribute_measurement_value (id, unit_id, value)
SELECT
    ipsa.id,
    imu.id,
    JSON_EXTRACT(ipsasc.value, '$.value')
FROM ibexa_product_specification_attribute ipsa
    LEFT JOIN ibexa_product_specification_attribute_measurement_value ipsamv ON ipsa.id = ipsamv.id
    JOIN ibexa_product_specification_attribute_simple_custom ipsasc ON ipsa.id = ipsasc.id
    JOIN ibexa_attribute_definition iad ON ipsa.attribute_definition_id = iad.id
    JOIN ibexa_measurement_type imt ON imt.name = COALESCE(
        JSON_UNQUOTE(JSON_EXTRACT(ipsasc.value, '$.measurementType')),
        JSON_UNQUOTE(JSON_EXTRACT(iad.options, '$.type'))
    )
    JOIN ibexa_measurement_unit imu ON
        imu.identifier = COALESCE(
            JSON_UNQUOTE(JSON_EXTRACT(ipsasc.value, '$.measurementUnit')),
            JSON_UNQUOTE(JSON_EXTRACT(iad.options, '$.unit'))
        ) AND imt.id = imu.type_id
    WHERE ipsa.discriminator = 'measurement_single'
        AND ipsamv.id IS NULL
        AND JSON_CONTAINS_PATH(ipsasc.value, 'all', '$.value')
;

-- Remove previous entries that were moved to new tables
DELETE ipsasc FROM ibexa_product_specification_attribute_simple_custom ipsasc
    JOIN ibexa_product_specification_attribute_measurement_value ipsamv ON ipsamv.id = ipsasc.id;

DELETE ipsasc FROM ibexa_product_specification_attribute_simple_custom ipsasc
    JOIN ibexa_product_specification_attribute_measurement_range ipsamr ON ipsamr.id = ipsasc.id;
