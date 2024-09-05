ALTER TABLE ibexa_taxonomy_entries ALTER COLUMN names type text USING encode(names, 'escape');
