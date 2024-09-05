-- Order management
create table ibexa_order
(
    id serial primary key,
    identifier varchar(190) not null,
    status varchar(190) not null,
    created timestamp(0) not null,
    modified timestamp(0) not null,
    source varchar(255) default null,
    context json
);

comment on column ibexa_order.created is '(DC2Type:datetime_immutable)';

comment on column ibexa_order.modified is '(DC2Type:datetime_immutable)';

create unique index ibexa_order_identifier_idx on ibexa_order (identifier);

create index ibexa_order_status_created_idx on ibexa_order (status, created);

create index ibexa_order_created_idx on ibexa_order (created);

create index ibexa_order_modified_idx on ibexa_order (modified);

create table ibexa_order_buyer_company
(
    id serial primary key,
    company_id int default null
        constraint ibexa_order_buyer_company_content_fk
            references ezcontentobject (id)
            on delete set null,
    company_name varchar(190) default null
);

create index ibexa_order_buyer_company_company_id_idx on ibexa_order_buyer_company (company_id);

create index ibexa_order_buyer_company_company_name_idx on ibexa_order_buyer_company (company_name);

create table ibexa_order_buyer_company_assignment
(
    order_id int not null
        constraint ibexa_order_buyer_company_assignment_order_fk
            references ibexa_order (id)
            on update cascade on delete cascade,
    assignment_company_id int not null
        constraint ibexa_order_buyer_company_assignment_order_buyer_company_fk
            references ibexa_order_buyer_company (id)
            on update cascade on delete cascade,
    primary key (order_id, assignment_company_id)
);

create index ibexa_order_buyer_company_assignment_order_id_idx on ibexa_order_buyer_company_assignment (order_id);

create index ibexa_order_buyer_company_assignment_company_id_idx on ibexa_order_buyer_company_assignment (assignment_company_id);

create table ibexa_order_buyer_user
(
    id serial primary key,
    user_id int default null
        constraint ibexa_order_buyer_user_user_fk
            references ezuser (contentobject_id)
            on delete set null,
    user_name varchar(190) default null,
    user_email varchar(190) default null
);

create index ibexa_order_buyer_user_user_id_idx on ibexa_order_buyer_user (user_id);

create index ibexa_order_buyer_user_user_name_idx on ibexa_order_buyer_user (user_name);

create index ibexa_order_buyer_user_user_email_idx on ibexa_order_buyer_user (user_email);

create table ibexa_order_buyer_user_assignment
(
    order_id int not null
        constraint ibexa_order_buyer_user_assignment_order_fk
            references ibexa_order (id)
            on update cascade on delete cascade,
    assignment_user_id int not null
        constraint ibexa_order_buyer_user_assignment_order_buyer_user_fk
            references ibexa_order_buyer_user (id)
            on update cascade on delete cascade,
    primary key (order_id, assignment_user_id)
);

create index ibexa_order_buyer_user_assignment_order_id_idx on ibexa_order_buyer_user_assignment (order_id);

create index ibexa_order_buyer_user_assignment_user_id_idx on ibexa_order_buyer_user_assignment (assignment_user_id);

create table ibexa_order_currency
(
    id serial primary key,
    currency_id int default null
        constraint ibexa_order_currency_currency_fk
            references ibexa_currency (id)
            on delete set null,
    currency_code varchar(3) not null
);

create index ibexa_order_currency_currency_id_idx on ibexa_order_currency (currency_id);

create index ibexa_order_currency_currency_code_idx on ibexa_order_currency (currency_code);

create table ibexa_order_currency_assignment
(
    order_id int not null
        constraint ibexa_order_currency_assignment_order_fk
            references ibexa_order (id)
            on update cascade on delete cascade,
    assignment_currency_id int not null
        constraint ibexa_order_item_currency_assignment_currency_fk
            references ibexa_order_currency (id)
            on update cascade on delete cascade,
    primary key (order_id, assignment_currency_id)
);

create index ibexa_order_currency_assignment_order_id_idx on ibexa_order_currency_assignment (order_id);

create index ibexa_order_currency_assignment_currency_id_idx on ibexa_order_currency_assignment (assignment_currency_id);

create table ibexa_order_value
(
    order_id int not null primary key
        constraint ibexa_order_value_order_fk
            references ibexa_order (id)
            on update cascade on delete cascade,
    vat decimal(19,4) not null,
    total_gross decimal(19,4) not null,
    total_net decimal(19,4) not null
);

create index ibexa_order_value_total_gross_idx on ibexa_order_value (total_gross);

create index ibexa_order_value_total_net_idx on ibexa_order_value (total_net);

create table ibexa_order_item
(
    id serial primary key,
    order_id int not null
        constraint ibexa_order_item_order_fk
            references ibexa_order (id)
            on update cascade on delete cascade,
    quantity int not null,
    context json
);

create index ibexa_order_item_order_id_idx on ibexa_order_item (order_id);

create table ibexa_order_item_product
(
    id serial primary key,
    product_id int default null,
    product_code varchar(64) not null,
    product_name varchar(190) not null
);

create index ibexa_order_item_product_product_id_idx on ibexa_order_item_product (product_id);

create index ibexa_order_item_product_product_code_idx on ibexa_order_item_product (product_code);

create index ibexa_order_item_product_product_name_idx on ibexa_order_item_product (product_name);

create table ibexa_order_item_product_assignment
(
    order_item_id int not null
        constraint ibexa_order_item_product_assignment_order_item_fk
            references ibexa_order_item (id)
            on update cascade on delete cascade,
    product_id int not null
        constraint ibexa_order_item_product_assignment_product_fk
            references ibexa_order_item_product (id)
            on update cascade on delete cascade,
    primary key (order_item_id, product_id)
);

create index ibexa_order_item_product_assignment_order_item_id_idx on ibexa_order_item_product_assignment (order_item_id);

create index ibexa_order_item_product_assignment_product_id_idx on ibexa_order_item_product_assignment (product_id);

create table ibexa_order_item_value
(
    order_item_id int not null primary key
        constraint ibexa_order_item_value_order_item_fk
            references ibexa_order_item (id)
            on update cascade on delete cascade,
    unit_price_gross decimal(19,4) not null,
    unit_price_net decimal(19,4) not null,
    vat_rate decimal(5,2) not null,
    subtotal_price_gross decimal(19,4) not null,
    subtotal_price_net decimal(19,4) not null
);

-- Shipping method
create table ibexa_shipping_method
(
    id serial
        primary key,
    identifier varchar(64) not null,
    enabled boolean default false not null,
    type varchar(32) not null
);

create index ibexa_shipping_method_identifier_idx on ibexa_shipping_method (identifier);

create index ibexa_shipping_method_enabled_idx on ibexa_shipping_method (enabled);

create unique index shipping_method_identifier_idx on ibexa_shipping_method (identifier);

create table ibexa_shipping_method_ml
(
    id serial
        primary key,
    shipping_method_id integer not null
        constraint ibexa_shipping_method_ml_shipping_method_fk
            references ibexa_shipping_method (id)
            on update cascade on delete cascade,
    language_id bigint not null
        constraint ibexa_shipping_method_ml_language_fk
            references ezcontent_language (id)
            on update cascade on delete cascade,
    name varchar(190) not null,
    name_normalized varchar(190) not null,
    description text
);

create index ibexa_shipping_method_ml_language_idx on ibexa_shipping_method_ml (language_id);

create index ibexa_shipping_method_ml_shipping_method_idx on ibexa_shipping_method_ml (shipping_method_id);

create index ibexa_shipping_method_ml_name_idx on ibexa_shipping_method_ml (name_normalized);

create unique index ibexa_shipping_method_ml_uidx on ibexa_shipping_method_ml (shipping_method_id, language_id);

create table ibexa_shipping_method_region
(
    id serial
        primary key,
    shipping_method_id integer not null
        constraint ibexa_shipping_method_region_shipping_method_fk
            references ibexa_shipping_method (id)
            on update cascade on delete cascade,
    region_identifier varchar(190) not null,
    vat_category_identifier varchar(64) not null
);

create index ibexa_shipping_method_region_region_identifier_idx on ibexa_shipping_method_region (region_identifier);

create index ibexa_shipping_method_region_shipping_method_idx on ibexa_shipping_method_region (shipping_method_id);

create unique index shipping_method_region_uidx on ibexa_shipping_method_region (shipping_method_id, region_identifier);

create table ibexa_shipping_method_region_free
(
    id serial
        primary key,
    shipping_method_region_id integer not null
        constraint ibexa_shipping_method_region_free_shipping_method_region_fk
            references ibexa_shipping_method_region (id)
            on update cascade on delete cascade,
    currency_id integer not null,
    amount numeric(19,4) not null
);

create index ibexa_shipping_method_region_free_shipping_method_region_idx on ibexa_shipping_method_region_free (shipping_method_region_id);

create index ibexa_shipping_method_free_currency_idx on ibexa_shipping_method_region_free (currency_id);

create unique index shipping_method_region_free_uidx on ibexa_shipping_method_region_free (shipping_method_region_id, currency_id);

create table ibexa_shipping_method_region_flat
(
    id serial
        primary key,
    shipping_method_region_id integer not null
        constraint ibexa_shipping_method_region_flat_shipping_method_region_fk
            references ibexa_shipping_method_region (id)
            on update cascade on delete cascade,
    currency_id integer not null,
    amount numeric(19,4) not null
);

create index ibexa_shipping_method_region_flat_shipping_method_region_idx on ibexa_shipping_method_region_flat (shipping_method_region_id);

create index ibexa_shipping_method_flat_currency_idx on ibexa_shipping_method_region_flat (currency_id);

create unique index shipping_method_region_flat_uidx on ibexa_shipping_method_region_flat (shipping_method_region_id, currency_id);

-- Shipment
create table ibexa_shipment
(
    id serial primary key,
    method_id integer not null
        constraint ibexa_shipment_shipping_method_fk
            references ibexa_shipping_method (id)
            on update cascade on delete cascade,
    order_id integer not null
        constraint ibexa_shipment_order_fk
            references ibexa_order (id)
            on update cascade on delete cascade,
    owner_id integer null
        constraint ibexa_shipment_owner_fk
            references ezuser (contentobject_id),
    identifier varchar(64) not null,
    amount numeric(19,4) not null,
    currency varchar(3) not null,
    status varchar(32) not null,
    context json,
    created_at timestamp(0) not null,
    updated_at timestamp(0) not null
);

comment on column ibexa_shipment.created_at is '(DC2Type:datetime_immutable)';

comment on column ibexa_shipment.updated_at is '(DC2Type:datetime_immutable)';

create index ibexa_shipment_identifier_idx on ibexa_shipment (identifier);

create index ibexa_shipment_status_idx on ibexa_shipment (status);

create index ibexa_shipment_method_idx on ibexa_shipment (method_id);

create index ibexa_shipment_order_idx on ibexa_shipment (order_id);

create index ibexa_shipment_owner_idx on ezuser (contentobject_id);

create unique index shipment_identifier_idx on ibexa_shipment (identifier);

create table ibexa_payment_method
(
    id SERIAL not null,
    identifier varchar(64) not null,
    type varchar(32) not null,
    enabled BOOLEAN not null,
    options JSON default null,
    created_at timestamp(0) without time zone not null,
    updated_at timestamp(0) without time zone not null,
    primary key (id)
);

create index ibexa_payment_method_identifier_idx on ibexa_payment_method (identifier);

create index ibexa_payment_method_enabled_idx on ibexa_payment_method (enabled);

create unique index ibexa_payment_method_identifier_uc on ibexa_payment_method (identifier);

comment on column ibexa_payment_method.created_at is '(DC2Type:datetime_immutable)';

comment on column ibexa_payment_method.updated_at is '(DC2Type:datetime_immutable)';

create table ibexa_payment_method_ml
(
    id SERIAL not null,
    payment_method_id int not null,
    language_id BIGINT not null,
    name varchar(190) not null,
    description TEXT default null,
    primary key (id)
);

create index ibexa_payment_method_ml_language_idx on ibexa_payment_method_ml (language_id);

create index ibexa_payment_method_ml_payment_method_idx on ibexa_payment_method_ml (payment_method_id);

create unique index ibexa_payment_method_ml_uidx on ibexa_payment_method_ml (payment_method_id, language_id);

create table ibexa_payment
(
    id SERIAL not null,
    method_id int not null,
    order_id int not null,
    owner_id int default null,
    identifier varchar(64) not null,
    amount numeric(19, 4) not null,
    currency varchar(3) not null,
    status varchar(32) not null,
    context JSON default null,
    created_at timestamp(0) without time zone not null,
    updated_at timestamp(0) without time zone not null,
    primary key (id)
);

create index ibexa_payment_method_idx on ibexa_payment (method_id);

create index ibexa_payment_order_idx on ibexa_payment (order_id);

create index ibexa_payment_owner_idx on ibexa_payment (owner_id);

create index ibexa_payment_identifier_idx on ibexa_payment (identifier);

create index ibexa_payment_status_idx on ibexa_payment (status);

create unique index ibexa_payment_identifier_uc on ibexa_payment (identifier);

comment on column ibexa_payment.created_at is '(DC2Type:datetime_immutable)';

comment on column ibexa_payment.updated_at is '(DC2Type:datetime_immutable)';

alter table ibexa_payment_method_ml
    add constraint ibexa_payment_method_ml_to_language_fk
        foreign key (language_id)
        references ezcontent_language (id)
        on update cascade  on delete cascade;

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
