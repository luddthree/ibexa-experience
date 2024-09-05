@IbexaHeadless @IbexaExperience @IbexaCommerce @javascript
Feature: Product catalog management

    Background:
        Given I am logged as admin

    Scenario: Attribute group can be added
        Given I open "Attribute groups" page in admin SiteAccess
        And I click on the edit action bar button "Create"
        When I set fields
            | label       | value                         |
            | Name        | Test Attribute group          |
            | Identifier  | TestAttributeGroupIdentifier  |
            | Position    | 3                             |
        And I click on the edit action bar button "Save and close"
        And success notification that "Attribute Group 'Test Attribute group' created." appears
        Then I should see an Attribute group with values
            | Name                  | Identifier                    | Position  |
            | Test Attribute group  | TestAttributeGroupIdentifier  | 3         |

    Scenario: Attribute group can be edited
        Given I open "Attribute groups" page in admin SiteAccess
        And there is an Attribute group "Test Attribute group"
        And I edit "Test Attribute group" Attribute group
        When I set fields
            | label       | value                               |
            | Name        | Test Attribute group edited         |
            | Identifier  | TestAttributeGroupIdentifierEdited  |
            | Position    | 4                                   |
        And I click on the edit action bar button "Save and close"
        Then I should see an Attribute group with values
            | Name                         | Identifier                          | Position  |
            | Test Attribute group edited  | TestAttributeGroupIdentifierEdited  | 4         |

    Scenario: Attribute group can be deleted
        Given I open "Test Attribute group edited" Attribute group page in admin SiteAccess
        And I should see an Attribute group with values
            | Name                        | Identifier                          | Position  |
            | Test Attribute group edited | TestAttributeGroupIdentifierEdited  | 4         |
        When I delete the Attribute group
        And success notification that "Attribute group 'Test Attribute group edited' deleted." appears
        Then I should be on "Attribute groups" page
