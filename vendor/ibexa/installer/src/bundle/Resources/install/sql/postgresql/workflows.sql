SELECT SETVAL('ezeditorialworkflow_markings_id_seq', COALESCE(MAX(id), 1)) FROM ezeditorialworkflow_markings;
SELECT SETVAL('ezeditorialworkflow_transitions_id_seq', COALESCE(MAX(id), 1)) FROM ezeditorialworkflow_transitions;
SELECT SETVAL('ezeditorialworkflow_workflows_id_seq', COALESCE(MAX(id), 1)) FROM ezeditorialworkflow_workflows;
