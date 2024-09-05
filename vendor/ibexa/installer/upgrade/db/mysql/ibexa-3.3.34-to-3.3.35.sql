-- IBX-6255: Form builder's `field_id` columns in some tables require indices to improve performance
ALTER TABLE `ezform_field_attributes` ADD INDEX `ezform_fa_f_id` (`field_id`);
ALTER TABLE `ezform_field_validators` ADD INDEX `ezform_fv_f_id` (`field_id`);
