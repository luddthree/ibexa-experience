-- Order management
create table ibexa_order
(
    id int auto_increment
        primary key,
    identifier varchar(190) not null,
    status varchar(190) not null,
    created datetime not null comment '(DC2Type:datetime_immutable)',
    modified datetime not null comment '(DC2Type:datetime_immutable)',
    source varchar(255) default null,
    context longtext null comment '(DC2Type:json)',
    constraint ibexa_order_identifier_idx
        unique (identifier)

)
    collate=utf8mb4_unicode_520_ci;

create index ibexa_order_status_created_idx
    on ibexa_order (status, created);

create index ibexa_order_created_idx
    on ibexa_order (created);

create index ibexa_order_modified_idx
    on ibexa_order (modified);

create table ibexa_order_buyer_company
(
    id int auto_increment
        primary key,
    company_id int default null,
    company_name varchar(190) default null,
    constraint ibexa_order_buyer_company_content_fk
        foreign key (company_id) references ezcontentobject (id)
            on delete set null
)
    collate=utf8mb4_unicode_520_ci;

create index ibexa_order_buyer_company_company_id_idx
    on ibexa_order_buyer_company (company_id);

create index ibexa_order_buyer_company_company_name_idx
    on ibexa_order_buyer_company (company_name);

create table ibexa_order_buyer_company_assignment
(
    order_id int not null,
    assignment_company_id int not null,
    primary key (order_id, assignment_company_id),
    constraint ibexa_order_buyer_company_assignment_order_buyer_company_fk
        foreign key (assignment_company_id) references ibexa_order_buyer_company (id)
            on update cascade on delete cascade,
    constraint ibexa_order_buyer_company_assignment_order_fk
        foreign key (order_id) references ibexa_order (id)
            on update cascade on delete cascade
)
    collate=utf8mb4_unicode_520_ci;

create index ibexa_order_buyer_company_assignment_order_id_idx
    on ibexa_order_buyer_company_assignment (order_id);

create index ibexa_order_buyer_company_assignment_company_id_idx
    on ibexa_order_buyer_company_assignment (assignment_company_id);

create table ibexa_order_buyer_user
(
    id int auto_increment
        primary key,
    user_id int default null,
    user_name varchar(190) default null,
    user_email varchar(190) default null,
    constraint ibexa_order_buyer_user_user_fk
        foreign key (user_id) references ezuser (contentobject_id)
            on delete set null
)
    collate=utf8mb4_unicode_520_ci;

create index ibexa_order_buyer_user_user_id_idx
    on ibexa_order_buyer_user (user_id);

create index ibexa_order_buyer_user_user_name_idx
    on ibexa_order_buyer_user (user_name);

create index ibexa_order_buyer_user_user_email_idx
    on ibexa_order_buyer_user (user_email);

create table ibexa_order_buyer_user_assignment
(
    order_id int not null,
    assignment_user_id int not null,
    primary key (order_id, assignment_user_id),
    constraint ibexa_order_buyer_user_assignment_order_buyer_user_fk
        foreign key (assignment_user_id) references ibexa_order_buyer_user (id)
            on update cascade on delete cascade,
    constraint ibexa_order_buyer_user_assignment_order_fk
        foreign key (order_id) references ibexa_order (id)
            on update cascade on delete cascade
)
    collate=utf8mb4_unicode_520_ci;

create index ibexa_order_buyer_user_assignment_order_id_idx
    on ibexa_order_buyer_user_assignment (order_id);

create index ibexa_order_buyer_user_assignment_user_id_idx
    on ibexa_order_buyer_user_assignment (assignment_user_id);

create table ibexa_order_currency
(
    id int auto_increment
        primary key,
    currency_id int default null,
    currency_code varchar(3) not null,
    constraint ibexa_order_currency_currency_fk
        foreign key (currency_id) references ibexa_currency (id)
            on delete set null
)
    collate=utf8mb4_unicode_520_ci;

create index ibexa_order_currency_currency_id_idx
    on ibexa_order_currency (currency_id);

create index ibexa_order_currency_currency_code_idx
    on ibexa_order_currency (currency_code);

create table ibexa_order_currency_assignment
(
    order_id int not null,
    assignment_currency_id int not null,
    primary key (order_id, assignment_currency_id),
    constraint ibexa_order_item_currency_assignment_currency_fk
        foreign key (assignment_currency_id) references ibexa_order_currency (id)
            on update cascade on delete cascade,
    constraint ibexa_order_currency_assignment_order_fk
        foreign key (order_id) references ibexa_order (id)
            on update cascade on delete cascade
)
    collate=utf8mb4_unicode_520_ci;

create index ibexa_order_currency_assignment_order_id_idx
    on ibexa_order_currency_assignment (order_id);

create index ibexa_order_currency_assignment_currency_id_idx
    on ibexa_order_currency_assignment (assignment_currency_id);

create table ibexa_order_value
(
    order_id int not null
        primary key,
    vat decimal(19,4) not null,
    total_gross decimal(19,4) not null,
    total_net decimal(19,4) not null,
    constraint ibexa_order_value_order_fk
        foreign key (order_id) references ibexa_order (id)
            on update cascade on delete cascade
)
    collate=utf8mb4_unicode_520_ci;

create index ibexa_order_value_total_gross_idx
    on ibexa_order_value (total_gross);

create index ibexa_order_value_total_net_idx
    on ibexa_order_value (total_net);

create table ibexa_order_item
(
    id int auto_increment
        primary key,
    order_id int not null,
    quantity int not null,
    context longtext null comment '(DC2Type:json)',
    constraint ibexa_order_item_order_fk
        foreign key (order_id) references ibexa_order (id)
            on update cascade on delete cascade
)
    collate=utf8mb4_unicode_520_ci;

create index ibexa_order_item_order_id_idx
    on ibexa_order_item (order_id);

create table ibexa_order_item_product
(
    id int auto_increment
        primary key,
    product_id int default null,
    product_code varchar(64) not null,
    product_name varchar(190) not null
)
    collate=utf8mb4_unicode_520_ci;

create index ibexa_order_item_product_product_id_idx
    on ibexa_order_item_product (product_id);

create index ibexa_order_item_product_product_code_idx
    on ibexa_order_item_product (product_code);

create index ibexa_order_item_product_product_name_idx
    on ibexa_order_item_product (product_name);

create table ibexa_order_item_product_assignment
(
    order_item_id int not null,
    product_id int not null,
    primary key (order_item_id, product_id),
    constraint ibexa_order_item_product_assignment_order_item_fk
        foreign key (order_item_id) references ibexa_order_item (id)
            on update cascade on delete cascade,
    constraint ibexa_order_item_product_assignment_product_fk
        foreign key (product_id) references ibexa_order_item_product (id)
            on update cascade on delete cascade
)
    collate=utf8mb4_unicode_520_ci;

create index ibexa_order_item_product_assignment_order_item_id_idx
    on ibexa_order_item_product_assignment (order_item_id);

create index ibexa_order_item_product_assignment_product_id_idx
    on ibexa_order_item_product_assignment (product_id);

create table ibexa_order_item_value
(
    order_item_id int not null
        primary key,
    unit_price_gross decimal(19,4) not null,
    unit_price_net decimal(19,4) not null,
    vat_rate decimal(5,2) not null,
    subtotal_price_gross decimal(19,4) not null,
    subtotal_price_net decimal(19,4) not null,
    constraint ibexa_order_item_value_order_item_fk
        foreign key (order_item_id) references ibexa_order_item (id)
            on update cascade on delete cascade
)
    collate=utf8mb4_unicode_520_ci;

-- Shipping method
create table ibexa_shipping_method
(
    id int auto_increment
        primary key,
    identifier varchar(64) not null,
    enabled tinyint(1) default 0 not null,
    type varchar(32) not null,
    constraint shipping_method_identifier_idx
        unique (identifier)
)
    collate=utf8mb4_unicode_520_ci;

create table ibexa_shipping_method_ml
(
    id int auto_increment
        primary key,
    shipping_method_id int not null,
    language_id bigint not null,
    name varchar(190) not null,
    name_normalized varchar(190) not null,
    description text null,
    constraint ibexa_shipping_method_ml_uidx
        unique (shipping_method_id, language_id),
    constraint ibexa_shipping_method_ml_language_fk
        foreign key (language_id) references ezcontent_language (id)
            on update cascade on delete cascade,
    constraint ibexa_shipping_method_ml_shipping_method_fk
        foreign key (shipping_method_id) references ibexa_shipping_method (id)
            on update cascade on delete cascade
)
    collate=utf8mb4_unicode_520_ci;

create index ibexa_shipping_method_ml_language_idx
    on ibexa_shipping_method_ml (language_id);

create index ibexa_shipping_method_ml_name_idx
    on ibexa_shipping_method_ml (name_normalized);

create index ibexa_shipping_method_ml_shipping_method_idx
    on ibexa_shipping_method_ml (shipping_method_id);

create table ibexa_shipping_method_region
(
    id int auto_increment
        primary key,
    shipping_method_id int not null,
    region_identifier varchar(190) not null,
    vat_category_identifier varchar(64) not null,
    constraint shipping_method_region_uidx
        unique (shipping_method_id, region_identifier),
    constraint ibexa_shipping_method_region_shipping_method_fk
        foreign key (shipping_method_id) references ibexa_shipping_method (id)
            on update cascade on delete cascade
)
    collate=utf8mb4_unicode_520_ci;

create index ibexa_shipping_method_region_region_identifier_idx
    on ibexa_shipping_method_region (region_identifier);

create index ibexa_shipping_method_region_shipping_method_idx
    on ibexa_shipping_method_region (shipping_method_id);

create table ibexa_shipping_method_region_flat
(
    id int auto_increment
        primary key,
    shipping_method_region_id int not null,
    currency_id int not null,
    amount decimal(19,4) not null,
    constraint shipping_method_region_flat_uidx
        unique (shipping_method_region_id, currency_id),
    constraint ibexa_shipping_method_region_flat_shipping_method_region_fk
        foreign key (shipping_method_region_id) references ibexa_shipping_method_region (id)
            on update cascade on delete cascade
)
    collate=utf8mb4_unicode_520_ci;

create index ibexa_shipping_method_flat_currency_idx
    on ibexa_shipping_method_region_flat (currency_id);

create index ibexa_shipping_method_region_flat_shipping_method_region_idx
    on ibexa_shipping_method_region_flat (shipping_method_region_id);

create table ibexa_shipping_method_region_free
(
    id int auto_increment
        primary key,
    shipping_method_region_id int not null,
    currency_id int not null,
    amount decimal(19,4) not null,
    constraint shipping_method_region_free_uidx
        unique (shipping_method_region_id, currency_id),
    constraint ibexa_shipping_method_region_free_shipping_method_region_fk
        foreign key (shipping_method_region_id) references ibexa_shipping_method_region (id)
            on update cascade on delete cascade
)
    collate=utf8mb4_unicode_520_ci;

create index ibexa_shipping_method_free_currency_idx
    on ibexa_shipping_method_region_free (currency_id);

create index ibexa_shipping_method_region_free_shipping_method_region_idx
    on ibexa_shipping_method_region_free (shipping_method_region_id);

-- Shipment
create table ibexa_shipment
(
    id int auto_increment
        primary key,
    method_id int not null,
    order_id int not null,
    owner_id int null,
    identifier varchar(64) not null,
    amount decimal(19,4) not null,
    currency varchar(3) not null,
    status varchar(32) not null,
    context longtext null comment '(DC2Type:json)',
    created_at datetime not null comment '(DC2Type:datetime_immutable)',
    updated_at datetime not null comment '(DC2Type:datetime_immutable)',
    constraint shipment_identifier_idx
        unique (identifier),
    constraint ibexa_shipment_shipping_method_fk
        foreign key (method_id) references ibexa_shipping_method (id)
            on update cascade on delete cascade,
    constraint ibexa_shipment_order_fk
        foreign key (order_id) references ibexa_order (id)
            on update cascade on delete cascade,
    constraint ibexa_shipment_user_fk
        foreign key (owner_id) references ezuser (contentobject_id)
)
    collate=utf8mb4_unicode_520_ci;

create index ibexa_shipment_identifier_idx
    on ibexa_shipment (identifier);

create index ibexa_shipment_method_idx
    on ibexa_shipment (method_id);

create index ibexa_shipment_order_idx
    on ibexa_shipment (order_id);

create index ibexa_shipment_status_idx
    on ibexa_shipment (status);

create index ibexa_shipping_method_enabled_idx
    on ibexa_shipping_method (enabled);

create index ibexa_shipping_method_identifier_idx
    on ibexa_shipping_method (identifier);

create table ibexa_payment_method
(
    id int auto_increment not null,
    identifier varchar(64) not null,
    type varchar(32) not null,
    enabled tinyint(1) not null,
    options json default null,
    created_at datetime not null comment '(DC2Type:datetime_immutable)',
    updated_at datetime not null comment '(DC2Type:datetime_immutable)',
    index ibexa_payment_method_identifier_idx (identifier),
    index ibexa_payment_method_enabled_idx (enabled),
    unique index ibexa_payment_method_identifier_uc (identifier),
    primary key (id)
) collate utf8mb4_unicode_520_ci;

create table ibexa_payment_method_ml
(
    id int auto_increment not null,
    payment_method_id int not null,
    language_id bigint not null,
    name varchar(190) not null,
    description longtext default null,
    index ibexa_payment_method_ml_language_idx (language_id),
    index ibexa_payment_method_ml_payment_method_idx (payment_method_id),
    unique index ibexa_payment_method_ml_uidx (payment_method_id, language_id),
    primary key (id)
) collate utf8mb4_unicode_520_ci;

create table ibexa_payment
(
    id int auto_increment not null,
    method_id int not null,
    order_id int not null,
    owner_id int default null,
    identifier varchar(64) not null,
    amount numeric(19, 4) not null,
    currency varchar(3) not null,
    status varchar(32) not null,
    context json default null,
    created_at datetime not null comment '(DC2Type:datetime_immutable)',
    updated_at datetime not null comment '(DC2Type:datetime_immutable)',
    index ibexa_payment_method_idx (method_id),
    index ibexa_payment_order_idx (order_id),
    index ibexa_payment_owner_idx (owner_id),
    index ibexa_payment_identifier_idx (identifier),
    index ibexa_payment_status_idx (status),
    unique index ibexa_payment_identifier_uc (identifier),
    primary key (id)
) collate utf8mb4_unicode_520_ci;

alter table ibexa_payment_method_ml
    add constraint ibexa_payment_method_ml_to_language_fk
        foreign key (language_id)
        references ezcontent_language (id)
        on update cascade on delete cascade;

alter table ibexa_payment_method_ml
    add constraint ibexa_payment_method_to_payment_method_fk
        foreign key (payment_method_id)
        references ibexa_payment_method (id)
        on update cascade on delete cascade;

alter table ibexa_payment
    add constraint ibexa_payment_to_payment_method_fk
        foreign key (method_id)
        references ibexa_payment_method (id)
        on update cascade on delete cascade;

alter table ibexa_payment
    add constraint ibexa_payment_to_order_fk
        foreign key (order_id)
        references ibexa_order (id)
        on update cascade on delete cascade;

alter table ibexa_payment
    add constraint ibexa_payment_to_owner_fk
        foreign key (owner_id)
        references ezuser (contentobject_id)
        on delete restrict;
