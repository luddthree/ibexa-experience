SELECT SETVAL('ezdatebasedpublisher_scheduled_entries_id_seq', COALESCE(MAX(id), 1)) FROM ezdatebasedpublisher_scheduled_entries;
