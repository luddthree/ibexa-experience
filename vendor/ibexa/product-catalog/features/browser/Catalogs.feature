@IbexaHeadless @IbexaExperience @IbexaCommerce @javascript @remotePIM
Feature: Product catalog management

    Background:
        Given I am logged as admin

    Scenario: Catalog can be added
        Given I open "Catalogs" page in admin SiteAccess
        And I click on the edit action bar button "Create"
        When I set fields
            | label       | value                  |
            | Name        | Test Catalog           |
            | Identifier  | TestCatalogIdentifier  |
        And I click on the edit action bar button "Save and close"
        And success notification that "Catalog 'Test Catalog' created." appears
        Then I should see a Catalog with values
            | Name         | Identifier             |
            | Test Catalog | TestCatalogIdentifier  |
        And catalog status is "Draft"

    Scenario: Catalog can be published from catalog page
        Given I open "Test Catalog" Catalog page in admin SiteAccess
        And I should see a Catalog with values
            | Name         | Identifier             |
            | Test Catalog | TestCatalogIdentifier  |
        And catalog status is "Draft"
        When I publish the Catalog from Catalog page
        And success notification that "Catalog status has been changed." appears
        Then catalog status is "Published"

    Scenario: Catalog can be archived from catalog page
        Given I open "Test Catalog" Catalog page in admin SiteAccess
        And I should see a Catalog with values
            | Name         | Identifier             |
            | Test Catalog | TestCatalogIdentifier  |
        And catalog status is "Published"
        When I archive the Catalog from Catalog page
        And success notification that "Catalog status has been changed." appears
        Then catalog status is "Archived"

    Scenario: Catalog can be edited from catalog page
        Given I open "Test Catalog" Catalog page in admin SiteAccess
        And I should see a Catalog with values
            | Name         | Identifier             |
            | Test Catalog | TestCatalogIdentifier  |
        And catalog status is "Archived"
        When I click on the edit action bar button "Edit"
        And I set fields
            | label       | value                        |
            | Name        | Test Catalog edited          |
            | Identifier  | TestCatalogIdentifierEdited  |
        And I click on the edit action bar button "Save and close"
        And success notification that "Catalog 'Test Catalog edited' updated." appears
        Then I should see a Catalog with values
            | Name                | Identifier                   |
            | Test Catalog edited | TestCatalogIdentifierEdited  |
        And catalog status is "Archived"

    Scenario: Catalog can be copied from catalog page
        Given I open "Test Catalog edited" Catalog page in admin SiteAccess
        And I should see a Catalog with values
            | Name                 | Identifier                   |
            | Test Catalog edited  | TestCatalogIdentifierEdited  |
        And catalog status is "Archived"
        When I copy the Catalog from Catalog page
        And success notification that "Catalog 'Test Catalog edited' copied." appears
        And I set fields
            | label       | value                        |
            | Name        | Test Catalog copied          |
            | Identifier  | TestCatalogIdentifierCopied  |
        And I click on the edit action bar button "Save and close"
        And success notification that "Catalog 'Test Catalog copied' updated." appears
        Then I should see a Catalog with values
            | Name                 | Identifier                   |
            | Test Catalog copied  | TestCatalogIdentifierCopied  |
        And catalog status is "Draft"

    Scenario: Catalog can be deleted from catalog page
        Given I open "Test Catalog copied" Catalog page in admin SiteAccess
        And I should see a Catalog with values
            | Name                 | Identifier                   |
            | Test Catalog copied  | TestCatalogIdentifierCopied  |
        And catalog status is "Draft"
        When I delete the Catalog from Catalog page
        And success notification that "Catalog 'Test Catalog copied' deleted." appears
        Then there's no "Test Catalog copied" on Catalogs list

    Scenario: Catalog can be edited from catalogs page
        Given I open "Catalogs" page in admin SiteAccess
        And there is a Catalog "Test Catalog edited"
        And I edit "Test Catalog edited" Catalog
        When I set fields
            | label       | value                                  |
            | Name        | Test Catalog edited second time        |
            | Identifier  | TestCatalogIdentifierEditedSecondTime  |
        And I click on the edit action bar button "Save and close"
        Then I should see a Catalog with values
            | Name                             | Identifier                             |
            | Test Catalog edited second time  | TestCatalogIdentifierEditedSecondTime  |
        And success notification that "Catalog 'Test Catalog edited second time' updated." appears
        And catalog status is "Archived"

    Scenario: Catalog can be copied from catalogs page
        Given I open "Catalogs" page in admin SiteAccess
        And there is a Catalog "Test Catalog edited second time"
        And I copy "Test Catalog edited second time" Catalog
        When I set fields
            | label       | value                                        |
            | Name        | Test Catalog copied from catalogs page       |
            | Identifier  | TestCatalogIdentifierCopiedFromCatalogsPage  |
        And I click on the edit action bar button "Save and close"
        Then I should see a Catalog with values
            | Name                                    | Identifier                                   |
            | Test Catalog copied from catalogs page  | TestCatalogIdentifierCopiedFromCatalogsPage  |
        And success notification that "Catalog 'Test Catalog copied from catalogs page' updated." appears
        And catalog status is "Draft"

    Scenario: Catalog can be deleted from catalogs page
        Given I open "Catalogs" page in admin SiteAccess
        And there is a Catalog "Test Catalog copied from catalogs page"
        When I delete the Catalog "Test Catalog copied from catalogs page"
        And success notification that "Catalog 'Test Catalog copied from catalogs page' deleted." appears
        Then there's no "Test Catalog copied from catalogs page" on Catalogs list
