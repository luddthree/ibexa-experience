-- Cart

CREATE TABLE ibexa_cart (
    id INT AUTO_INCREMENT NOT NULL,
    owner_id INT NOT NULL,
    currency_id INT NOT NULL,
    identifier CHAR(36) NOT NULL COMMENT '(DC2Type:guid)',
    name VARCHAR(190) DEFAULT NULL,
    created DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    modified DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    INDEX IDX_A86D0D3338248176 (currency_id),
    INDEX ibexa_cart_owner_idx (owner_id),
    INDEX ibexa_cart_identifier_idx (identifier),
    UNIQUE INDEX cart_identifier_idx (identifier),
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_520_ci` ENGINE = InnoDB;

CREATE TABLE ibexa_cart_entry (
    id INT AUTO_INCREMENT NOT NULL,
    cart_id INT NOT NULL,
    currency_id INT NOT NULL,
    identifier CHAR(36) NOT NULL COMMENT '(DC2Type:guid)',
    added DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    price NUMERIC(19, 4) NOT NULL,
    quantity INT NOT NULL,
    product_code VARCHAR(64) NOT NULL,
    INDEX IDX_C540E3581AD5CDBF (cart_id),
    INDEX ibexa_cart_entry_currency_idx (currency_id),
    INDEX ibexa_cart_entry_product_code_idx (product_code),
    INDEX ibexa_cart_entry_identifier_idx (identifier),
    UNIQUE INDEX cart_entry_identifier_idx (identifier),
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_520_ci` ENGINE = InnoDB;

CREATE TABLE ibexa_cart_entry_ml (
    id INT AUTO_INCREMENT NOT NULL,
    cart_entry_id INT NOT NULL,
    language_id BIGINT NOT NULL,
    name VARCHAR(190) NOT NULL,
    name_normalized VARCHAR(190) NOT NULL,
    INDEX ibexa_cart_entry_ml_language_idx (language_id),
    INDEX ibexa_cart_entry_ml_cart_idx (cart_entry_id),
    INDEX ibexa_cart_entry_ml_name_idx (name_normalized),
    UNIQUE INDEX ibexa_cart_entry_ml_uidx (cart_entry_id, language_id),
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_520_ci` ENGINE = InnoDB;

ALTER TABLE
  ibexa_cart
ADD
  CONSTRAINT ibexa_cart_user_fk
      FOREIGN KEY (owner_id)
          REFERENCES ezuser (contentobject_id)
          ON UPDATE CASCADE
          ON DELETE CASCADE;

ALTER TABLE
  ibexa_cart
ADD
  CONSTRAINT ibexa_cart_currency_fk
      FOREIGN KEY (currency_id)
          REFERENCES ibexa_currency (id)
          ON UPDATE CASCADE
          ON DELETE CASCADE;

ALTER TABLE
  ibexa_cart_entry
ADD
  CONSTRAINT ibexa_cart_entry_cart_fk
      FOREIGN KEY (cart_id)
          REFERENCES ibexa_cart (id)
          ON UPDATE CASCADE
          ON DELETE CASCADE;

ALTER TABLE
  ibexa_cart_entry
ADD
  CONSTRAINT ibexa_cart_entry_currency_fk
      FOREIGN KEY (currency_id)
          REFERENCES ibexa_currency (id)
          ON UPDATE CASCADE
          ON DELETE CASCADE;

ALTER TABLE
  ibexa_cart_entry_ml
ADD
  CONSTRAINT ibexa_cart_entry_ml_language_fk
      FOREIGN KEY (language_id)
          REFERENCES ezcontent_language (id)
          ON UPDATE CASCADE
          ON DELETE CASCADE;

ALTER TABLE
  ibexa_cart_entry_ml
ADD
  CONSTRAINT ibexa_cart_entry_ml_cart_fk
      FOREIGN KEY (cart_entry_id)
          REFERENCES ibexa_cart_entry (id)
          ON UPDATE CASCADE
          ON DELETE CASCADE;
