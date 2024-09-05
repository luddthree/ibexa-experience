-- IBX-6255: Form builder's `field_id` columns in some tables require indices to improve performance
CREATE INDEX "ezform_fa_f_id" ON "ezform_field_attributes" ("field_id");
CREATE INDEX "ezform_fv_f_id" ON "ezform_field_validators" ("field_id");
