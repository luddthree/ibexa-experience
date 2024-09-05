@IbexaHeadless @IbexaExperience @IbexaCommerce @javascript
Feature: Product catalog filters

    Background:
        Given I am logged as admin

    Scenario: Catalog with standard filters Category, Type and Price contains all products
        Given I open "New Catalog" page in admin SiteAccess
        And I set fields
            | label       | value                         |
            | Name        | Catalog with default filters  |
            | Identifier  | CatalogWithDefaultFilters     |
        When I switch to "Products" field group
        Then I should see products on catalog form
        | Name                   | Code  |
        | ProductWithMemory1024  | 0442  |
        | ProductWithMemory2048  | 0239  |
        | ProductWithMemory4096  | 0888  |
        | PT Memory4096          | 9217  |

    Scenario: Catalog with filter can be added
        Given I open "Catalogs" page in admin SiteAccess
        And I click on the edit action bar button "Create"
        And I set fields
            | label       | value                |
            | Name        | Catalog with filter  |
            | Identifier  | CatalogWithFilter    |
        And I switch to "Filters" field group
        When I add filter "Memory" to catalog
        And I set filter range parameters to "1024" and "4096"
        Then there is "Memory" filter with value "(1024 - 4096)" on filters list
        And I should see products on catalog form
            | Name                   | Code  |
            | PT Memory4096          | 9217  |
            | ProductWithMemory1024  | 0442  |
            | ProductWithMemory2048  | 0239  |
            | ProductWithMemory4096  | 0888  |
        And I click on the edit action bar button "Save and close"
        And success notification that "Catalog 'Catalog with filter' created." appears
        And I should see a Catalog with values
            | Name                 | Identifier         |
            | Catalog with filter  | CatalogWithFilter  |
        And catalog status is "Draft"

    Scenario: Catalog with filter can be edited
        Given I open "Catalog with filter" Catalog page in admin SiteAccess
        And I should see a Catalog with values
            | Name                 | Identifier         |
            | Catalog with filter  | CatalogWithFilter  |
        And catalog status is "Draft"
        When I click on the edit action bar button "Edit"
        And I switch to "Filters" field group
        And there is "Memory" filter with value "(1024 - 4096)" on filters list
        And I should see products on catalog form
            | Name                   | Code  |
            | PT Memory4096          | 9217  |
            | ProductWithMemory1024  | 0442  |
            | ProductWithMemory2048  | 0239  |
            | ProductWithMemory4096  | 0888  |
        And I edit "Memory" filter from filters list
        And I set filter range parameters to "1024" and "2048"
        And there is "Memory" filter with value "(1024 - 2048)" on filters list
        And I should see products on catalog form
            | Name                   | Code  |
            | ProductWithMemory1024  | 0442  |
            | ProductWithMemory2048  | 0239  |
        And I should not see products on catalog form
            | Name                   | Code  |
            | PT Memory4096          | 9217  |
            | ProductWithMemory4096  | 0888  |
        And I set fields
            | label       | value                       |
            | Name        | Catalog with filter edited  |
            | Identifier  | CatalogWithFilterEdited     |
        And I click on the edit action bar button "Save and close"
        And success notification that "Catalog 'Catalog with filter edited' updated." appears
        Then I should see a Catalog with values
            | Name                       | Identifier               |
            | Catalog with filter edited | CatalogWithFilterEdited  |
        And catalog status is "Draft"

    Scenario: Catalog with filter can be deleted
        Given I open "Catalog with filter edited" Catalog page in admin SiteAccess
        And I should see a Catalog with values
            | Name                        | Identifier               |
            | Catalog with filter edited  | CatalogWithFilterEdited  |
        And catalog status is "Draft"
        When I delete the Catalog from Catalog page
        And success notification that "Catalog 'Catalog with filter edited' deleted." appears
        Then there's no "Catalog with filter edited" on Catalogs list

    Scenario: Catalog with filter can be canceled
        Given I open "Catalogs" page in admin SiteAccess
        And I click on the edit action bar button "Create"
        And I set fields
            | label       | value                         |
            | Name        | Catalog with filter canceled  |
            | Identifier  | CatalogWithFilterCanceled     |
        And I switch to "Filters" field group
        And I add filter "Memory" to catalog
        And I set filter range parameters to "2048" and "2048"
        And there is "Memory" filter with value "(2048 - 2048)" on filters list
        And I should see products on catalog form
            | Name                   | Code  |
            | ProductWithMemory2048  | 0239  |
        When I click on the edit action bar button "Discard"
        Then there's no "Catalog with filter canceled" on Catalogs list

    Scenario: Catalog's custom filter can be added
        Given I open "New Catalog" page in admin SiteAccess
        And I set fields
            | label       | value                       |
            | Name        | Catalog with custom filter  |
            | Identifier  | CatalogWithCustomFilter     |
        And I switch to "Filters" field group
        When I add filter "Number of USB ports" to catalog
        And I set filter range parameters to "5" and "7"
        Then there is "Number of USB ports" filter with value "(5 - 7)" on filters list
        And I should see products on catalog form
            | Name                   | Code  |
            | PT Memory4096          | 9217  |
            | ProductWithMemory2048  | 0239  |
            | ProductWithMemory4096  | 0888  |
        And I should not see products on catalog form
            | Name                   | Code  |
            | ProductWithMemory1024  | 0442  |

    Scenario: Catalog's standard Availability filter can be added
        Given I open "New Catalog" page in admin SiteAccess
        And I set fields
            | label       | value                                      |
            | Name        | Catalog with standard availability filter  |
            | Identifier  | CatalogWithStandardAvailabilityFilter      |
        And I switch to "Filters" field group
        When I add filter "Availability" to catalog
        And I select "Unavailable" option in product type filter
        And I save editing the filter
        Then there is "Availability" filter with value "Unavailable" on filters list
        And I should see products on catalog form
            | Name                   | Code  |
            | ProductWithMemory2048  | 0239  |
        And I should not see products on catalog form
            | Name                   | Code  |
            | PT Memory4096          | 9217  |
            | ProductWithMemory1024  | 0442  |
            | ProductWithMemory4096  | 0888  |

    Scenario: Catalog's not default filter can be edited
        Given I open "New Catalog" page in admin SiteAccess
        And I set fields
            | label       | value                       |
            | Name        | Catalog with edited filter  |
            | Identifier  | CatalogWithSEditedFilter    |
        And I switch to "Filters" field group
        And I add filter "Code" to catalog
        And I fill in Product code with "0239" value
        And there is "Code" filter with value "0239" on filters list
        And I should see products on catalog form
            | Name                   | Code  |
            | ProductWithMemory2048  | 0239  |
        And I should not see products on catalog form
            | Name                   | Code  |
            | ProductWithMemory4096  | 0888  |
        When I edit "Code" filter from filters list
        And I fill in Product code with "0888" value
        Then there is "Code" filter with value "0239 (+1)" on filters list
        And I should see products on catalog form
            | Name                   | Code  |
            | ProductWithMemory2048  | 0239  |
            | ProductWithMemory4096  | 0888  |

    Scenario: Catalog's not default filter can be deleted
        Given I open "New Catalog" page in admin SiteAccess
        And I set fields
            | label       | value                        |
            | Name        | Catalog with deleted filter  |
            | Identifier  | CatalogWithDeletedFilter     |
        And I switch to "Filters" field group
        And I add filter "Availability" to catalog
        And I select "Available" option in product type filter
        And I save editing the filter
        And there is "Availability" filter with value "Available" on filters list
        And I should see products on catalog form
            | Name                   | Code  |
            | PT Memory4096          | 9217  |
            | ProductWithMemory1024  | 0442  |
            | ProductWithMemory4096  | 0888  |
        And I should not see products on catalog form
            | Name                   | Code  |
            | ProductWithMemory2048  | 0239  |
        When I delete "Availability" filter from filters list
        Then there's no "Availability" filter on filters list
        And I should see products on catalog form
            | Name                   | Code  |
            | ProductWithMemory1024  | 0442  |
            | ProductWithMemory2048  | 0239  |
            | ProductWithMemory4096  | 0888  |
            | PT Memory4096          | 9217  |

    Scenario: Adding a new filter to catalog can be discarded
        Given I open "New Catalog" page in admin SiteAccess
        And I set fields
            | label       | value                                     |
            | Name        | Catalog with Product Availability filter  |
            | Identifier  | CatalogWithProductAvailabilityFilter      |
        And I switch to "Filters" field group
        When I add filter "Availability" to catalog
        And I select "Unavailable" option in product type filter
        And I cancel editing the filter
        Then there's no "Availability" filter on filters list

    Scenario: Catalog's default filter can be edited
        Given I open "New Catalog" page in admin SiteAccess
        And I set fields
            | label       | value                          |
            | Name        | Catalog default filter edited  |
            | Identifier  | CatalogDefaultFilterEdited     |
        And I switch to "Filters" field group
        And there is "Type" filter on filters list
        And I should see products on catalog form
            | Name                   | Code  |
            | ProductWithMemory1024  | 0442  |
            | ProductWithMemory2048  | 0239  |
            | ProductWithMemory4096  | 0888  |
            | PT Memory4096          | 9217  |
        When I edit "Type" filter from filters list
        And I select "Product Type For Filter Type" option in product type filter
        And I save editing the filter
        Then I should see products on catalog form
            | Name                  | Code  |
            | PT Memory4096         | 9217  |
        And there is "Type" filter on filters list
        And I should not see products on catalog form
            | Name                   | Code  |
            | ProductWithMemory1024  | 0442  |
            | ProductWithMemory2048  | 0239  |
            | ProductWithMemory4096  | 0888  |
