ALTER TABLE `ibexa_product_specification_availability`
    ADD COLUMN `is_infinite` TINYINT(1) NOT NULL DEFAULT '0';

UPDATE `ibexa_product_specification_availability`
    SET is_infinite = IF(stock IS NULL, 1, 0);

CREATE TABLE IF NOT EXISTS ibexa_measurement_type
(
    id   int auto_increment primary key,
    name varchar(192) not null,
    constraint ibexa_measurement_type_name
        unique (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ibexa_measurement_unit
(
    id         int auto_increment primary key,
    type_id    int          not null,
    identifier varchar(192) not null,
    constraint ibexa_measurement_unit_type_identifier
        unique (type_id, identifier),
    constraint ibexa_measurement_unit_type_fk
        foreign key (type_id) references ibexa_measurement_type (id)
            on update cascade
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ibexa_measurement_range_value
(
    id               int auto_increment primary key,
    content_field_id int    not null,
    version_no       int    not null,
    unit_id          int    not null,
    min_value        double not null,
    max_value        double not null,
    constraint ibexa_measurement_range_value_attr_ver
        unique (content_field_id, version_no),
    constraint ibexa_measurement_range_value_attr_ver_type_unit
        unique (content_field_id, version_no, unit_id),
    constraint ibexa_measurement_range_value_attr_fk
        foreign key (content_field_id, version_no) references ezcontentobject_attribute (id, version)
            on update cascade on delete cascade,
    constraint ibexa_measurement_range_value_unit_fk
        foreign key (unit_id) references ibexa_measurement_unit (id)
            on update cascade
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX ibexa_measurement_range_value_unit_id
    on ibexa_measurement_range_value (unit_id);

CREATE INDEX ibexa_measurement_unit_type_id
    on ibexa_measurement_unit (type_id);

CREATE TABLE IF NOT EXISTS ibexa_measurement_value
(
    id               int auto_increment primary key,
    content_field_id int    not null,
    version_no       int    not null,
    unit_id          int    not null,
    value            double not null,
    constraint ibexa_measurement_value_attr_ver
        unique (content_field_id, version_no),
    constraint ibexa_measurement_value_attr_ver_unit
        unique (content_field_id, version_no, unit_id),
    constraint ibexa_measurement_value_attr_fk
        foreign key (content_field_id, version_no) references ezcontentobject_attribute (id, version)
            on update cascade on delete cascade,
    constraint ibexa_measurement_value_unit_fk
        foreign key (unit_id) references ibexa_measurement_unit (id)
            on update cascade
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX ibexa_measurement_value_unit_id
    on ibexa_measurement_value (unit_id);

