@IbexaHeadless @IbexaExperience @IbexaCommerce @javascript
Feature: Product variants management

    Background:
        Given I am logged as admin

    Scenario: Product variant can be added automatically by generator
        Given I open "Products" page in admin SiteAccess
        And I click on the edit action bar button "Create"
        And I select "Product Type For Product Variants" Product Type
        And I set content fields
            | label       | value                             |
            | Name        | Product with variants             |
            | Description | Product with variants description |
        And I set content fields
            | label                 | Code            |
            | Product Specification | ProductVariant1 |
        And I click on the edit action bar button "Save and close"
        And success notification that "Product 'Product with variants' created." appears
        And I should be on Product page for "Product with variants"
        And product attributes equal
            | label        | value                             | type     |
            | Name         | Product with variants             | ezstring |
            | Product code | ProductVariant1                   | ezstring |
            | Description  | Product with variants description | ezstring |
        And I change tab to "Variants"
        When I click on Generate variants button
        And I set product variant attributes in input fields on generate popup window
            | label              | value       |
            | Processor color    | #00ff40     |
            | Cache amount       | 16          |
        And I set product variant attributes in dropdown fields on generate popup window
            | label              | value       |
            | Processor options  | No          |
            | Processor socket   | Socket 1200 |
        And I confirm generating Product Variants
        Then I should be on Product page for "Product with variants"
        And there's a Product Variant "Product with variants No/#00ff40/16/Socket 1200" on Variants list with Product code "ProductVariant1-1"

    Scenario: Product variant can be edited from Variants list
        Given I open "Product with variants" Product page in admin SiteAccess
        And I change tab to "Variants"
        And there's a Product Variant "Product with variants No/#00ff40/16/Socket 1200" on Variants list with Product code "ProductVariant1-1"
        When I start editing Product Variant "Product with variants No/#00ff40/16/Socket 1200"
        And I set fields
            | label            | value                     |
            | Code             | ProductVariant1-1-edited  |
        And I switch to 'Attributes' field group
        And I set product variant attributes values in input fields on update page
            | label             | value   |
            | Processor options | Yes     |
            | Processor color   | #c5ff40 |
            | Cache amount      | 32      |
        And I set product variant attributes values in dropdown fields on update page
            | label              | value       |
            | Processor socket   | Socket 1150 |
        And I click on the edit action bar button "Save and close"
        Then I should be on Product page for "Product with variants"
        And product attributes equal
            | label             | value        | type      |
            | Processor options | Yes          | checkbox  |
            | Processor color   | #c5ff40      | color     |
            | Cache amount      | 32           | float     |
            | Processor socket  | Socket 1150  | selection |

    Scenario: Editing product variant can be cancelled
        Given I open "ProductVariant1-1-edited" Product variant page in admin SiteAccess
        And product attributes equal
            | label             | value        | type      |
            | Processor options | Yes          | checkbox  |
            | Processor color   | #c5ff40      | color     |
            | Cache amount      | 32           | float     |
            | Processor socket  | Socket 1150  | selection |
        And I click on the edit action bar button "Edit"
        And I set fields
            | label         | value                            |
            | Code          | ProductVariant1-1-edited-cancel  |
        And I switch to 'Attributes' field group
        And I set product variant attributes values in input fields on update page
            | label             | value   |
            | Processor options | No      |
            | Processor color   | #c9ee50 |
            | Cache amount      | 128     |
        And I set product variant attributes values in dropdown fields on update page
            | label              | value       |
            | Processor socket   | Socket 1200 |
        When I click on the edit action bar button "Discard"
        Then I should be on Product page for "Product with variants"
        And product attributes equal
            | label             | value        | type      |
            | Processor options | Yes          | checkbox  |
            | Processor color   | #c5ff40      | color     |
            | Cache amount      | 32           | float     |
            | Processor socket  | Socket 1150  | selection |

    Scenario: Generating product variants can be cancelled
        Given I open "Products" page in admin SiteAccess
        And I click on the edit action bar button "Create"
        And I select "Product Type For Product Variants" Product Type
        And I set content fields
            | label       | value                                     |
            | Name        | Product with variants discard             |
            | Description | Product with variants description discard |
        And I set content fields
            | label                 | Code            |
            | Product Specification | ProductVariant2 |
        And I click on the edit action bar button "Save and close"
        And success notification that "Product 'Product with variants discard' created." appears
        And I should be on Product page for "Product with variants discard"
        And product attributes equal
            | label        | value                                     | type     |
            | Name         | Product with variants discard             | ezstring |
            | Product code | ProductVariant2                           | ezstring |
            | Description  | Product with variants description discard | ezstring |
        And I change tab to "Variants"
        When I click on Generate variants button
        And I set product variant attributes in input fields on generate popup window
            | label              | value       |
            | Processor color    | #09ff47     |
            | Cache amount       | 128         |
        And I set product variant attributes in dropdown fields on generate popup window
            | label              | value       |
            | Processor options  | Yes         |
            | Processor socket   | Socket 1150 |
        And I cancel generating Product Variants
        Then I should be on Product page for "Product with variants discard"
        And variants list is empty

    Scenario: Product variant can be deleted from Variants list
        Given I open "Product with variants" Product page in admin SiteAccess
        And I change tab to "Variants"
        And there's a Product Variant "Product with variants Yes/#c5ff40/32/Socket 1150" on Variants list with Product code "ProductVariant1-1-edited"
        When I delete "Product with variants Yes/#c5ff40/32/Socket 1150" Product Variant
        Then success notification that "Product 'Product with variants Yes/#c5ff40/32/Socket 1150' deleted." appears
        And there's no "Product with variants Yes/#c5ff40/32/Socket 1150" on Variants list with Product code "ProductVariant1-1-edited"
        And I should be on Product page for "Product with variants"
        And variants list is empty

    Scenario: Product variant can be added manually from Variants list
        Given I open "Product with variants" Product page in admin SiteAccess
        And I change tab to "Variants"
        And variants list is empty
        When I click on add Product Variant button
        And I set fields
            | label        | value           |
            | Code         | ProductVariant3 |
        And I switch to 'Attributes' field group
        And I set product variant attributes values in input fields on update page
            | label             | value   |
            | Processor options | Yes     |
            | Processor color   | #0312e2 |
            | Cache amount      | 64      |
        And I set product variant attributes values in dropdown fields on update page
            | label              | value       |
            | Processor socket   | Socket 1150 |
        And I click on the edit action bar button "Save and close"
        Then I should be on Product page for "Product with variants"
        And product attributes equal
            | label             | value        | type      |
            | Processor options | Yes          | checkbox  |
            | Processor color   | #0312e2      | color     |
            | Cache amount      | 64           | float     |
            | Processor socket  | Socket 1150  | selection |

    Scenario: Product variant can not be added second time with the same product variant options
        Given I open "Product with variants" Product page in admin SiteAccess
        And I change tab to "Variants"
        And there's a Product Variant "Product with variants Yes/#0312e2/64/Socket 1150" on Variants list with Product code "ProductVariant3"
        When I click on add Product Variant button
        And I set fields
            | label         | value           |
            | Code          | ProductVariant4 |
        And I switch to 'Attributes' field group
        And I set product variant attributes values in input fields on update page
            | label             | value   |
            | Processor options | Yes     |
            | Processor color   | #0312e2 |
            | Cache amount      | 64      |
        And I set product variant attributes values in dropdown fields on update page
            | label              | value       |
            | Processor socket   | Socket 1150 |
        And I click on the edit action bar button "Save and close"
        Then warning notification that "Attribute combination already exists in Product Variant ProductVariant3" appears

    Scenario: Product variant can be edited from Product variant page
        Given I open "ProductVariant3" Product variant page in admin SiteAccess
        And product attributes equal
            | label             | value        | type      |
            | Processor options | Yes          | checkbox  |
            | Processor color   | #0312e2      | color     |
            | Cache amount      | 64           | float     |
            | Processor socket  | Socket 1150  | selection |
        When I click on the edit action bar button "Edit"
        And I set fields
            | label  | value                   |
            | Code   | ProductVariant3-edited  |
        And I switch to 'Attributes' field group
        And I set product variant attributes values in input fields on update page
            | label             | value   |
            | Processor options | Yes     |
            | Processor color   | #f50505 |
            | Cache amount      | 96      |
        And I set product variant attributes values in dropdown fields on update page
            | label              | value       |
            | Processor socket   | Socket 1200 |
        And I click on the edit action bar button "Save and close"
        Then I should be on Product page for "Product with variants"
        And product attributes equal
            | label             | value       | type      |
            | Processor options | Yes         | checkbox  |
            | Processor color   | #f50505     | color     |
            | Cache amount      | 96          | float     |
            | Processor socket  | Socket 1200 | selection |

    Scenario: Product variant can be deleted from Product variant page
        Given I open "ProductVariant3-edited" Product variant page in admin SiteAccess
        When I click on the edit action bar button "Delete"
        And I confirm deletion of product from product page
        Then success notification that "Product 'Product with variants Yes/#f50505/96/Socket 1200' deleted." appears
        And there's no "Product with variants Yes/#f50505/96/Socket 1200" on Variants list with Product code "ProductVariant3"
        And variants list is empty

    Scenario: Product variants can be added automatically by generator
        Given I open "Products" page in admin SiteAccess
        And I click on the edit action bar button "Create"
        And I select "Product Type For Product Variants" Product Type
        And I set content fields
            | label       | value                                                 |
            | Name        | Product with variants for delete scenario             |
            | Description | Product with variants for delete scenario description |
        And I set content fields
            | label                 | Code                 |
            | Product Specification | ProductVariantDelete |
        And I click on the edit action bar button "Save and close"
        And success notification that "Product 'Product with variants for delete scenario' created." appears
        And I should be on Product page for "Product with variants for delete scenario"
        And product attributes equal
            | label        | value                                                 | type     |
            | Name         | Product with variants for delete scenario             | ezstring |
            | Product code | ProductVariantDelete                                  | ezstring |
            | Description  | Product with variants for delete scenario description | ezstring |
        And I change tab to "Variants"
        When I click on Generate variants button
        And I set product variant attributes in input fields on generate popup window
            | label              | value       |
            | Processor color    | #99FF50     |
            | Cache amount       | 256         |
        And I set product variant attributes in dropdown fields on generate popup window
            | label              | value       |
            | Processor options  | Yes         |
            | Processor socket   | Socket 1150 |
            | Processor options  | No          |
            | Processor socket   | Socket 1200 |
        And I confirm generating Product Variants
        Then I should be on Product page for "Product with variants for delete scenario"
        And there's a Product Variant "Product with variants for delete scenario Yes/#99FF50/256/Socket 1150" on Variants list with Product code "ProductVariantDelete-1"
        And there's a Product Variant "Product with variants for delete scenario No/#99FF50/256/Socket 1150" on Variants list with Product code "ProductVariantDelete-2"
        And there's a Product Variant "Product with variants for delete scenario Yes/#99FF50/256/Socket 1200" on Variants list with Product code "ProductVariantDelete-3"
        And there's a Product Variant "Product with variants for delete scenario No/#99FF50/256/Socket 1200" on Variants list with Product code "ProductVariantDelete-4"

    Scenario: Product with product variants can be deleted from products list
        Given I open "Products" page in admin SiteAccess
        And I change category filter to "Uncategorized products"
        And there's a Product "Product with variants for delete scenario" on Products list with Product code "ProductVariantDelete" and Variant status "Yes (4)"
        When I delete "Product with variants for delete scenario" Product
        Then success notification that "Product 'Product with variants for delete scenario' deleted." appears
        And I change category filter to "Uncategorized products"
        And there's no "Product with variants for delete scenario" on Products list
