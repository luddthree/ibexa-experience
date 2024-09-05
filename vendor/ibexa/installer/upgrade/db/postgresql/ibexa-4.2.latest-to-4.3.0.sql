CREATE TABLE ibexa_corporate_application_state
(
    id SERIAL PRIMARY KEY,
    application_id INTEGER NOT NULL
        CONSTRAINT ibexa_corporate_application_state_application_fk
            REFERENCES ezcontentobject (id)
                ON DELETE CASCADE,
    company_id INTEGER
        CONSTRAINT ibexa_corporate_application_state_company_fk
            REFERENCES ezcontentobject (id)
                ON DELETE SET NULL,
    state VARCHAR(128) NOT NULL
);

CREATE UNIQUE INDEX ibexa_corporate_application_state_application_unique
    ON ibexa_corporate_application_state (application_id);

CREATE UNIQUE INDEX ibexa_corporate_application_state_company_unique
    ON ibexa_corporate_application_state (company_id);

CREATE INDEX ibexa_corporate_application_state_state_idx
    ON ibexa_corporate_application_state (state);

CREATE TABLE ibexa_corporate_company_history
(
    id SERIAL PRIMARY KEY,
    application_id INTEGER NOT NULL
        CONSTRAINT ibexa_corporate_company_history_application_fk
            REFERENCES ezcontentobject
                ON UPDATE CASCADE ON DELETE CASCADE,
    company_id INTEGER
        CONSTRAINT ibexa_corporate_company_history_company_fk
            REFERENCES ezcontentobject
                ON UPDATE CASCADE ON DELETE SET NULL,
    user_id INTEGER
        CONSTRAINT ibexa_corporate_company_history_user_fk
            REFERENCES ezcontentobject
                ON UPDATE CASCADE ON DELETE SET NULL,
    created TIMESTAMP(0) DEFAULT CURRENT_TIMESTAMP NOT NULL,
    event_name VARCHAR(255) NOT NULL,
    event_data JSON
);

CREATE INDEX ibexa_corporate_company_history_company_idx
    ON ibexa_corporate_company_history (company_id);

CREATE INDEX ibexa_corporate_company_history_application_idx
    ON ibexa_corporate_company_history (application_id);

CREATE INDEX ibexa_corporate_company_history_user_idx
    ON ibexa_corporate_company_history (user_id);
