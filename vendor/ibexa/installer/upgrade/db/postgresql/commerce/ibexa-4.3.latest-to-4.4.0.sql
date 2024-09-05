-- Cart

CREATE TABLE ibexa_cart (
    id SERIAL NOT NULL,
    owner_id INT NOT NULL,
    currency_id INT NOT NULL,
    identifier UUID NOT NULL,
    name VARCHAR(190) DEFAULT NULL,
    created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    modified TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    PRIMARY KEY(id)
);

CREATE INDEX IDX_A86D0D3338248176 ON ibexa_cart (currency_id);

CREATE INDEX ibexa_cart_owner_idx ON ibexa_cart (owner_id);

CREATE INDEX ibexa_cart_identifier_idx ON ibexa_cart (identifier);

CREATE UNIQUE INDEX cart_identifier_idx ON ibexa_cart (identifier);

COMMENT ON COLUMN ibexa_cart.created IS '(DC2Type:datetime_immutable)';

COMMENT ON COLUMN ibexa_cart.modified IS '(DC2Type:datetime_immutable)';

CREATE TABLE ibexa_cart_entry (
    id SERIAL NOT NULL,
    cart_id INT NOT NULL,
    currency_id INT NOT NULL,
    identifier UUID NOT NULL,
    added TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    price NUMERIC(19, 4) NOT NULL,
    quantity INT NOT NULL,
    product_code VARCHAR(64) NOT NULL,
    PRIMARY KEY(id)
);

CREATE INDEX IDX_C540E3581AD5CDBF ON ibexa_cart_entry (cart_id);

CREATE INDEX ibexa_cart_entry_currency_idx ON ibexa_cart_entry (currency_id);

CREATE INDEX ibexa_cart_entry_product_code_idx ON ibexa_cart_entry (product_code);

CREATE INDEX ibexa_cart_entry_identifier_idx ON ibexa_cart_entry (identifier);

CREATE UNIQUE INDEX cart_entry_identifier_idx ON ibexa_cart_entry (identifier);

COMMENT ON COLUMN ibexa_cart_entry.added IS '(DC2Type:datetime_immutable)';

CREATE TABLE ibexa_cart_entry_ml (
    id SERIAL NOT NULL,
    cart_entry_id INT NOT NULL,
    language_id BIGINT NOT NULL,
    name VARCHAR(190) NOT NULL,
    name_normalized VARCHAR(190) NOT NULL,
    PRIMARY KEY(id)
);

CREATE INDEX ibexa_cart_entry_ml_language_idx ON ibexa_cart_entry_ml (language_id);

CREATE INDEX ibexa_cart_entry_ml_cart_idx ON ibexa_cart_entry_ml (cart_entry_id);

CREATE INDEX ibexa_cart_entry_ml_name_idx ON ibexa_cart_entry_ml (name_normalized);

CREATE UNIQUE INDEX ibexa_cart_entry_ml_uidx ON ibexa_cart_entry_ml (cart_entry_id, language_id);

ALTER TABLE
    ibexa_cart
    ADD
        CONSTRAINT ibexa_cart_user_fk
            FOREIGN KEY (owner_id)
                REFERENCES ezuser (contentobject_id)
                ON UPDATE CASCADE
                ON DELETE CASCADE
                NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE
    ibexa_cart
    ADD
        CONSTRAINT ibexa_cart_currency_fk
            FOREIGN KEY (currency_id)
                REFERENCES ibexa_currency (id)
                ON UPDATE CASCADE
                ON DELETE CASCADE
                NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE
    ibexa_cart_entry
    ADD
        CONSTRAINT ibexa_cart_entry_cart_fk
            FOREIGN KEY (cart_id)
                REFERENCES ibexa_cart (id)
                ON UPDATE CASCADE
                ON DELETE CASCADE
                NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE
    ibexa_cart_entry
    ADD
        CONSTRAINT ibexa_cart_entry_currency_fk
            FOREIGN KEY (currency_id)
                REFERENCES ibexa_currency (id)
                ON UPDATE CASCADE
                ON DELETE CASCADE
                NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE
    ibexa_cart_entry_ml
    ADD
        CONSTRAINT ibexa_cart_entry_ml_language_fk
            FOREIGN KEY (language_id)
                REFERENCES ezcontent_language (id)
                ON UPDATE CASCADE
                ON DELETE CASCADE
                NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE
    ibexa_cart_entry_ml
    ADD
        CONSTRAINT ibexa_cart_entry_ml_cart_fk
            FOREIGN KEY (cart_entry_id)
                REFERENCES ibexa_cart_entry (id)
                ON UPDATE CASCADE
                ON DELETE CASCADE
                NOT DEFERRABLE INITIALLY IMMEDIATE;
