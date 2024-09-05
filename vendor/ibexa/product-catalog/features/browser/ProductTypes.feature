@IbexaHeadless @IbexaExperience @IbexaCommerce @javascript
Feature: Product Types management

    Background:
        Given I am logged as admin

    Scenario: Changes can be cancelled while creating Product Type
        Given I open "Product Types" page in admin SiteAccess
        And I start creating a "Physical" Product Type
        And I set fields
            | label                | value                     |
            | Name                 | Test Product Type         |
            | Identifier           | TestProductTypeIdentifier |
            | Description          | Test Description          |
            | Product name pattern | <name>                    |
        And I click on the edit action bar button "Discard"
        Then I should be on "Product Types" page
        And there's no "Test Product Type" on Product Types list

    Scenario: Product Type without Attributes can be added
        Given I open "Product Types" page in admin SiteAccess
        And I start creating a "Physical" Product Type
        And I set fields
            | label                | value                     |
            | Name                 | Test Product Type         |
            | Identifier           | TestProductTypeIdentifier |
            | Description          | Test Description          |
            | Product name pattern | <name>                    |
        And I add field "Text block" to Product Type definition
        And I set "Name" to "Text field" for "Text block" Product Type field
        And I click on the edit action bar button "Save and close"
        Then I should be on Product Type page for "Test Product Type"
        And Product Type has proper Global properties
            | label                | value                     |
            | Name                 | Test Product Type         |
            | Identifier           | TestProductTypeIdentifier |
            | Description          | Test Description          |
            | Product name pattern | <name>                    |
        And Product Type "Test Product Type" has proper fields
            | fieldName             | fieldType                   |
            | Name                  | ezstring                    |
            | Product Specification | ibexa_product_specification |
            | Description           | ezrichtext                  |
            | Image                 | ezimageasset                |
            | Text field            | eztext                      |

   Scenario: Product Type with Attribute and VAT Rates can be added
        Given I open "Product Types" page in admin SiteAccess
        And I start creating a "Physical" Product Type
        And I set fields
            | label                | value                              |
            | Name                 | Product Type with attribute        |
            | Identifier           | ProductTypeWithAttributeIdentifier |
            | Description          | Test Description                   |
            | Product name pattern | <name>                             |
        And I add attribute "TestPT Attribute" to Product Type definition
        And I switch to "VAT Rates" field group
        And I set VAT rate for "germany" region to "reduced (6.00%)"
        And I click on the edit action bar button "Save and close"
        Then I should be on Product Type page for "Product Type with attribute"
        And Product Type has proper Global properties
            | label                | value                              |
            | Name                 | Product Type with attribute        |
            | Identifier           | ProductTypeWithAttributeIdentifier |
            | Description          | Test Description                   |
            | Product name pattern | <name>                             |
        And Product Type "Product Type with attribute" has proper fields
            | fieldName             | fieldType                   |
            | Name                  | ezstring                    |
            | Product Specification | ibexa_product_specification |
            | Description           | ezrichtext                  |
            | Image                 | ezimageasset                |
        And Product Type "Product Type with attribute" has proper attributes
            | attributeIdentifier       | attributeName    |
            | TestPTAttributeIdentifier | TestPT Attribute |
        And Product Type "Product Type with attribute" has proper VAT Rates
            | vatRegion | vatIdentifier | vatValue |
            | germany   | reduced       | 6%       |

    Scenario: Changes can be cancelled while editing Product Type from list
        Given I open "Product Types" page in admin SiteAccess
        And there's a "Test Product Type" on Product Types list
        When I start editing Product Type "Test Product Type"
        And I set fields
            | label | value                    |
            | Name  | Test Product Type edited |
        And I click on the edit action bar button "Discard"
        Then I should be on Product Type page for "Test Product Type"

    Scenario: Product Type can be edited from list
        Given I open "Product Types" page in admin SiteAccess
        And there's a "Test Product Type" on Product Types list
        When I start editing Product Type "Test Product Type"
        And I set fields
            | label | value                    |
            | Name  | Test Product Type edited |
        And I click on the edit action bar button "Save and close"
        Then I should be on Product Type page for "Test Product Type edited"

    Scenario: Product Type can be deleted from list
        Given I open "Product Types" page in admin SiteAccess
        And there's a "Test Product Type edited" on Product Types list
        When I delete "Test Product Type edited" Product Type
        And success notification that "Product Type 'Test Product Type edited' deleted." appears
        Then there's no "Test Product Type edited" on Product Types list

    Scenario: Product Type with attributes and selected options Required and Used for product variants can be added
        Given I open "Product Types" page in admin SiteAccess
        And I start creating a "Physical" Product Type
        And I set fields
            | label                | value                                                       |
            | Name                 | Product Type with attributes checkbox and color             |
            | Identifier           | ProductTypeWithAttributesCheckboxAndColor                   |
            | Description          | Product Type with attributes checkbox and color description |
            | Product name pattern | <name>                                                      |
        And I add attribute group "Processor Attributes" to Product Type definition
        And I set Required field for an attribute type "checkbox" to enabled in Product Type field definition
        And I set Used for product variants field for an attribute type "color" to enabled in Product Type field definition
        When I click on the edit action bar button "Save and close"
        Then I should be on Product Type page for "Product Type with attributes checkbox and color"
        And Product Type has proper Global properties
            | label                | value                                                        |
            | Name                 | Product Type with attributes checkbox and color              |
            | Identifier           | ProductTypeWithAttributesCheckboxAndColor                    |
            | Description          | Product Type with attributes checkbox and color description  |
            | Product name pattern | <name>                                                       |
        And Product Type "Product Type with attributes checkbox and color" has proper fields
            | fieldName             | fieldType                       |
            | Name                  | ezstring                        |
            | Product Specification | ibexa_product_specification     |
            | Description           | ezrichtext                      |
            | Image                 | ezimageasset                    |
            | Category              | ibexa_taxonomy_entry_assignment |
            | SEO                   | ibexa_seo                       |
        And Product Type "Product Type with attributes checkbox and color" has proper attributes
            | attributeName     | attributeIdentifier      |
            | Processor options | processor_options_second |
            | Processor color   | processor_color_second   |
