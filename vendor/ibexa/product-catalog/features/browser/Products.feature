@IbexaHeadless @IbexaExperience @IbexaCommerce @javascript
Feature: Products management

    Background:
        Given I am logged as admin

    Scenario: Changes can be discarded while creating Product
        Given I open "Products" page in admin SiteAccess
        When I click on the edit action bar button "Create"
        And I select "ProductType" Product Type
        And I set content fields
            | label       | value           |
            | Name        | TestProduct     |
            | Description | TestDescription |
        And I set content fields
            | label                 | Code     |
            | Product Specification | TestCode |
        And I click on the edit action bar button "Discard"
        Then I should be on "Products" page
        And there's no "TestProduct" on Products list

    Scenario: Product can be added
        Given I open "Products" page in admin SiteAccess
        When I click on the edit action bar button "Create"
        And I select "ProductType" Product Type
        And I set content fields
            | label       | value           |
            | Name        | TestProduct     |
            | Description | TestDescription |
        And I set content fields
            | label                 | Code     |
            | Product Specification | TestCode |
        And I click on the edit action bar button "Save and close"
        And success notification that "Product 'TestProduct' created." appears
        Then I should be on Product page for "TestProduct"
        And product attributes equal
            | label        | value           | type     |
            | Name         | TestProduct     | ezstring |
            | Product code | TestCode        | ezstring |
            | Description  | TestDescription | ezstring |

    Scenario: Product with attribute can be added
        Given I open "Products" page in admin SiteAccess
        When I click on the edit action bar button "Create"
        And I select "ProductTypeWithAttribute" Product Type
        And I set content fields
            | label       | value                    |
            | Name        | TestProductWithAttribute |
            | Description | TestDescription          |
        And I set content fields
            | label                 | Code              |
            | Product Specification | TestCodeAttribute |
        And I switch to "Attributes" field group
        And I set product attributes
            | label             | value | attributeType |
            | Mouse Type        | Wired | selection     |
            | Number of buttons | 2     | integer       |
            | Weight            | 1.5   | float         |
        And I click on the edit action bar button "Save and close"
        And success notification that "Product 'TestProductWithAttribute' created." appears
        And I should be on Product page for "TestProductWithAttribute"
        And product attributes equal
            | label        | value                    | type     |
            | Name         | TestProductWithAttribute | ezstring |
            | Product code | TestCodeAttribute        | ezstring |
            | Description  | TestDescription          | ezstring |
        Then I change tab to "Attributes"
        And product attributes equal
            | label             | value | type      |
            | Mouse Type        | Wired | selection |
            | Number of buttons | 2     | integer   |
            | Weight            | 1.5   | float     |

    Scenario: Base price in EUR can be added to Product
        Given I open "TestProduct" Product page in admin SiteAccess
        When I change tab to "Prices"
        And I set a Base price to "10.00" in "EUR"
        And I click on the edit action bar button "Save and close"
        Then I should see a Base price with "10.00 €" value

    Scenario: Base price in USD can be added to Product
        Given I open "TestProduct" Product page in admin SiteAccess
        When I change tab to "Prices"
        And I set a Base price to "12.00" in "USD"
        And I click on the edit action bar button "Save and close"
        Then I should see a Base price with "12.00 $" value

    Scenario: Availability can be added to Product
        Given I open "TestProduct" Product page in admin SiteAccess
        When I change tab to "Availability"
        And I start adding availability to product
        And I set an Availability to "true"
        And I set a Stock to "5"
        And I click on the edit action bar button "Save and close"
        Then I should see an Availability with "true" value
        And I should see a Stock with "5" value

    Scenario: Changes can be discarded while editing Product from list
        Given I open "Products" page in admin SiteAccess
        And I change category filter to "Uncategorized products"
        And there's a "TestProduct" on Products list
        When I start editing Product "TestProduct"
        And I set content fields
            | label | value             |
            | Name  | TestProductEdited |
        And I click on the edit action bar button "Discard"
        And I change category filter to "Uncategorized products"
        Then there's no "TestProductEdited" on Products list

    Scenario: Product Type can be edited from list
        Given I open "Products" page in admin SiteAccess
        And I change category filter to "Uncategorized products"
        And there's a "TestProduct" on Products list
        When I start editing Product "TestProduct"
        And I set content fields
            | label | value             |
            | Name  | TestProductEdited |
        And I click on the edit action bar button "Save and close"
        And success notification that "Product 'TestProductEdited' updated." appears
        Then I should be on Product page for "TestProductEdited"

    Scenario: Product can be deleted from list
        Given I open "Products" page in admin SiteAccess
        And I change category filter to "Uncategorized products"
        And there's a "TestProductEdited" on Products list
        When I delete "TestProductEdited" Product
        Then success notification that "Product 'TestProductEdited' deleted." appears
        And I change category filter to "Uncategorized products"
        And there's no "TestProductEdited" on Products list

    Scenario: Product can be edited from product page
        Given I open "TestProductWithAttribute" Product page in admin SiteAccess
        And I click on the edit action bar button "Edit"
        And I set content fields
            | label       | value                          |
            | Name        | TestProductWithAttributeEdited |
            | Description | TestDescriptionEdited          |
        And I set content fields
            | label                 | Code                           |
            | Product Specification | TestProductWithAttributeEdited |
        When I click on the edit action bar button "Save and close"
        Then success notification that "Product 'TestProductWithAttributeEdited' updated." appears
        And I should be on Product page for "TestProductWithAttributeEdited"

    Scenario: Product can be deleted from product page
        Given I open "TestProductWithAttributeEdited" Product page in admin SiteAccess
        When I click on the edit action bar button "Delete"
        And I confirm deletion of product from product page
        Then success notification that "Product 'TestProductWithAttributeEdited' deleted." appears
        And I change category filter to "Uncategorized products"
        And there's no "TestProductWithAttributeEdited" on Products list
