@IbexaHeadless @IbexaExperience @IbexaCommerce @javascript @remotePIM
Feature: Currencies management

    Background:
        Given I am logged as admin

    Scenario: Currency can be added
        Given I open "Currencies" page in admin SiteAccess
        And I click on the edit action bar button "Create"
        When I set fields
            | label                       | value |
            | Code                        | ABC   |
            | Number of fractional places | 2     |
        And I click on the edit action bar button "Save and close"
        Then success notification that "Currency ABC created." appears
        And I search for a "ABC" Currency
        And there's a "ABC" on Currencies list
        And Currency "ABC" is disabled

    Scenario: Currency can be edited from list
        Given I open "Currencies" page in admin SiteAccess
        And I search for a "ABC" Currency
        And there's a "ABC" on Currencies list
        When I start editing Currency "ABC"
        And I set fields
            | label | value |
            | Code  | ABD   |
        And I click on the edit action bar button "Save and close"
        Then success notification that "Currency ABD updated." appears
        And I search for a "ABD" Currency
        And there's a "ABD" on Currencies list

    Scenario: Currency can be enabled
        Given I open "Currencies" page in admin SiteAccess
        And I search for a "ABD" Currency
        And there's a "ABD" on Currencies list
        When I start editing Currency "ABD"
        And I enable currency
        And I click on the edit action bar button "Save and close"
        Then success notification that "Currency ABD updated." appears
        And I search for a "ABD" Currency
        And there's a "ABD" on Currencies list
        And Currency "ABD" is enabled

    Scenario: Currency can be deleted from list
        Given I open "Currencies" page in admin SiteAccess
        And I search for a "ABD" Currency
        And there's a "ABD" on Currencies list
        When I delete "ABD" Currency
        Then success notification that "Currency 'ABD' deleted." appears
        And I search for a "ABD" Currency
        And there's no "ABD" on Currencies list
