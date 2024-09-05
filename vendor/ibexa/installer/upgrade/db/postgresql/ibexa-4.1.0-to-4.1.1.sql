ALTER TABLE "ibexa_attribute_definition_assignment"
DROP CONSTRAINT "ibexa_attribute_definition_assignment_attribute_definition_fk";

ALTER TABLE "ibexa_attribute_definition_assignment"
    ADD CONSTRAINT "ibexa_attribute_definition_assignment_attribute_definition_fk"
        FOREIGN KEY (attribute_definition_id) REFERENCES "ibexa_attribute_definition"
            ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE "ibexa_product_specification_attribute"
DROP CONSTRAINT "ibexa_product_specification_attribute_aid";

ALTER TABLE "ibexa_product_specification_attribute"
    ADD CONSTRAINT "ibexa_product_specification_attribute_aid"
        FOREIGN KEY (attribute_definition_id) REFERENCES "ibexa_attribute_definition"
            ON UPDATE CASCADE ON DELETE RESTRICT;
