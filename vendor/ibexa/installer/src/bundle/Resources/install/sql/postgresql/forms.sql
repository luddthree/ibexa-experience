SELECT SETVAL('ezform_field_attributes_id_seq', COALESCE(MAX(id), 1)) FROM ezform_field_attributes;
SELECT SETVAL('ezform_field_validators_id_seq', COALESCE(MAX(id), 1)) FROM ezform_field_validators;
SELECT SETVAL('ezform_fields_id_seq', COALESCE(MAX(id), 1)) FROM ezform_fields;
SELECT SETVAL('ezform_form_submission_data_id_seq', COALESCE(MAX(id), 1)) FROM ezform_form_submission_data;
SELECT SETVAL('ezform_form_submissions_id_seq', COALESCE(MAX(id), 1)) FROM ezform_form_submissions;
SELECT SETVAL('ezform_forms_id_seq', COALESCE(MAX(id), 1)) FROM ezform_forms;
