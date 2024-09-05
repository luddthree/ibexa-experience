INSERT INTO "ezpolicy_limitation_value" ("id", "limitation_id", "value") VALUES (484,251,'3');
INSERT INTO "ezpolicy_limitation_value" ("id", "limitation_id", "value") VALUES (485,251,'6');

-- Repeated sequence reset related to modified eZ Platform Community Edition tables
SELECT SETVAL('ezpolicy_limitation_value_id_seq', COALESCE(MAX(id), 1) ) FROM ezpolicy_limitation_value;
