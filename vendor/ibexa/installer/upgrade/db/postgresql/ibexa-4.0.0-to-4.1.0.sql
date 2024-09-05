ALTER TABLE "ibexa_product_specification_availability" ADD "is_infinite" boolean DEFAULT false NOT NULL;

UPDATE "ibexa_product_specification_availability" SET "is_infinite" = CASE WHEN "stock" IS NULL THEN true END;

CREATE TABLE IF NOT EXISTS ibexa_measurement_type
(
    id serial primary key,
    name varchar(192) not null
);

CREATE UNIQUE INDEX IF NOT EXISTS ibexa_measurement_type_name ON ibexa_measurement_type (name);

CREATE TABLE IF NOT EXISTS ibexa_measurement_unit
(
    id serial primary key,
    type_id integer not null
    constraint ibexa_measurement_unit_type_fk
        references ibexa_measurement_type
        on update cascade on delete restrict,
    identifier varchar(192) not null
);

CREATE INDEX IF NOT EXISTS ibexa_measurement_unit_type_id ON ibexa_measurement_unit (type_id);

CREATE UNIQUE INDEX IF NOT EXISTS ibexa_measurement_unit_type_identifier ON ibexa_measurement_unit (type_id, identifier);

CREATE TABLE IF NOT EXISTS ibexa_measurement_value
(
    id serial primary key,
    content_field_id integer not null,
    version_no integer not null,
    unit_id integer not null
    constraint ibexa_measurement_value_unit_fk
        references ibexa_measurement_unit
        on update cascade on delete restrict,
    value double precision not null,
    constraint ibexa_measurement_value_attr_fk
        foreign key (content_field_id, version_no) references ezcontentobject_attribute
            on update cascade on delete cascade
);

CREATE INDEX IF NOT EXISTS ibexa_measurement_value_unit_id ON ibexa_measurement_value (unit_id);

CREATE UNIQUE INDEX IF NOT EXISTS ibexa_measurement_value_attr_ver ON ibexa_measurement_value (content_field_id, version_no);

CREATE UNIQUE INDEX IF NOT EXISTS ibexa_measurement_value_attr_ver_unit ON ibexa_measurement_value (content_field_id, version_no, unit_id);

CREATE TABLE IF NOT EXISTS ibexa_measurement_range_value
(
    id serial primary key,
    content_field_id integer not null,
    version_no       integer not null,
    unit_id          integer not null
    constraint ibexa_measurement_range_value_unit_fk
        references ibexa_measurement_unit
        on update cascade on delete restrict,
    min_value        double precision not null,
    max_value        double precision not null,
    constraint ibexa_measurement_range_value_attr_fk
        foreign key (content_field_id, version_no) references ezcontentobject_attribute
            on update cascade on delete cascade
);

CREATE INDEX IF NOT EXISTS ibexa_measurement_range_value_unit_id ON ibexa_measurement_range_value (unit_id);

CREATE UNIQUE INDEX IF NOT EXISTS ibexa_measurement_range_value_attr_ver
    on ibexa_measurement_range_value (content_field_id, version_no);

CREATE UNIQUE INDEX IF NOT EXISTS ibexa_measurement_range_value_attr_ver_type_unit
    on ibexa_measurement_range_value (content_field_id, version_no, unit_id);
