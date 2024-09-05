CREATE TABLE ibexa_corporate_application_state (
    id INT AUTO_INCREMENT PRIMARY KEY,
    application_id INT NOT NULL,
    company_id INT DEFAULT NULL,
    state VARCHAR(128) NOT NULL,
    CONSTRAINT ibexa_corporate_application_state_application_unique UNIQUE (application_id),
    CONSTRAINT ibexa_corporate_application_state_application_fk
        FOREIGN KEY (application_id) REFERENCES ezcontentobject (id)
            ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT ibexa_corporate_application_state_company_unique UNIQUE (company_id),
    CONSTRAINT ibexa_corporate_application_state_company_fk
        FOREIGN KEY (company_id) REFERENCES ezcontentobject (id)
            ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX ibexa_corporate_application_state_state_idx
    ON ibexa_corporate_application_state (state);

CREATE TABLE ibexa_corporate_company_history
(
    id INT AUTO_INCREMENT PRIMARY KEY,
    application_id INT NOT NULL,
    company_id INT DEFAULT NULL,
    user_id INT DEFAULT NULL,
    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    event_name VARCHAR(255) NOT NULL,
    event_data TEXT,
    CONSTRAINT ibexa_corporate_company_history_application_fk
        FOREIGN KEY (application_id) REFERENCES ezcontentobject (id)
            ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT ibexa_corporate_company_history_company_fk
        FOREIGN KEY (company_id) REFERENCES ezcontentobject (id)
            ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT ibexa_corporate_company_history_user_fk
        FOREIGN KEY (user_id) REFERENCES ezcontentobject (id)
            ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX ibexa_corporate_company_history_company_idx
    ON ibexa_corporate_company_history (company_id);

CREATE INDEX ibexa_corporate_company_history_application_idx
    ON ibexa_corporate_company_history (application_id);

CREATE INDEX ibexa_corporate_company_history_user_idx
    ON ibexa_corporate_company_history (user_id);
