@IbexaHeadless @IbexaExperience @IbexaCommerce @javascript @remotePIM
Feature: Customer groups management

    Background:
        Given I am logged as admin

    Scenario: Customer group can be added
        Given I open "Customer groups" page in admin SiteAccess
        And I click on the edit action bar button "Create"
        When I set fields
            | label       | value                        |
            | Name        | Test Customer group          |
            | Identifier  | TestCustomerGroupIdentifier  |
        And I set global price rate to "15"
        And I click on the edit action bar button "Save and close"
        And success notification that "Customer Group 'Test Customer group' created." appears
        Then I should see a Customer group with values
            | Name                 | Identifier                   | Global price rate  |
            | Test Customer group  | TestCustomerGroupIdentifier  | 15%                |

    Scenario Outline: Customer group can be edited
        Given I open "Customer groups" page in admin SiteAccess
        And there is a "<CustomerGroupName>" Customer group
        And I edit "<CustomerGroupName>" Customer group
        When I set fields
            | label       | value                         |
            | Name        | <NewCustomerGroupName>        |
            | Identifier  | <CustomerGroupIdentifier>     |
        And I set global price rate to "<GlobalPriceRate>"
        And I click on the edit action bar button "Save and close"
        Then I should see a Customer group with values
            | Name                    | Identifier                    | Global price rate   |
            | <NewCustomerGroupName>  | <CustomerGroupIdentifier>     | <GlobalPriceRate>%  |

        Examples:
            | CustomerGroupName                       | NewCustomerGroupName                    | CustomerGroupIdentifier                      | GlobalPriceRate  |
            | Test Customer group                     | Test Customer group edited              | TestCustomerGroupIdentifierEdited            | 0                |
            | Test Customer group edited              | Test Customer group edited second time  | TestCustomerGroupIdentifierEditedSecondTime  | 99.9             |
            | Test Customer group edited second time  | Test Customer group edited third time   | TestCustomerGroupIdentifierEditedThirdTime   | -5               |

    Scenario: Customer group can be deleted
        Given I open "Test Customer group edited third time" Customer group page in admin SiteAccess
        And I should see a Customer group with values
            | Name                                   | Identifier                                  | Global price rate  |
            | Test Customer group edited third time  | TestCustomerGroupIdentifierEditedThirdTime  | -5%                |
        When I delete the Customer group
        And success notification that "Customer Group 'Test Customer group edited third time' deleted." appears
        Then I should be on "Customer groups" page
