ALTER TABLE `ezcontentobject_link` ADD INDEX `ezco_link_cca_id` (`contentclassattribute_id`);
ALTER TABLE `ezcontentclass_attribute` ADD INDEX `ezcontentclass_attr_dts` (`data_type_string`);
ALTER TABLE `ezurl_object_link` ADD INDEX `ezurl_ol_coa_id_cav` (`contentobject_attribute_id`,`contentobject_attribute_version`);
ALTER TABLE `ezcontentobject_attribute` ADD INDEX `ezcontentobject_attribute_co_id_ver` (`contentobject_id`,`version`);
