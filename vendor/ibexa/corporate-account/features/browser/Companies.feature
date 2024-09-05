Feature: Companies management

  Background:
    Given I am logged as admin

  @IbexaCommerce @javascript
  Scenario: Company can be added
    Given I open "Companies" page in admin SiteAccess
    And I click on the edit action bar button "Create"
    And I create a user "SalesRepFirstCreate" with last name "SalesRepLastCreate" in group "Sales representatives"
    And I set content fields
      | label                | value                                                              |
      | Is active            | true                                                               |
      | Name                 | Ibexa Poland Sp. z o.o.                                            |
      | Tax ID               | 200027649902100895                                                 |
      | Website              | www.ibexa.co                                                       |
      | Customer group       | Customer group for corporate account                               |
      | Sales representative | Users/Sales representatives/SalesRepFirstCreate SalesRepLastCreate |
    And I switch to "Billing address" field group
    And I set content fields
      | label            | Name                     | Country  | First name | Last name | Tax ID             | Region   | City      | Street        | Postal Code  | Email                 | Phone        | fieldPosition |
      | Billing address  | Ibexa Poland Sp. z o.o.  | Poland   | John       | Smith     | 200027649902100895 | Śląskie  | Katowice  | Gliwicka 6/5  | 40-079       | info.poland@ibexa.co  | +48327850550 | 1             |
    When I click on the edit action bar button "Save and close"
    Then success notification that "Company created." appears
    And Company "Ibexa Poland Sp. z o.o." exists and has "Active" status on Companies page

  @IbexaCommerce @javascript
  Scenario: Company can be viewed
    Given I open "Companies" page in admin SiteAccess
    When I view "Ibexa Poland Sp. z o.o." Company
    Then I should see a Company summary with values
      | Company name             | Location           | Sales Representative                    | Tax ID              | Website       |
      | Ibexa Poland Sp. z o.o.  | Katowice (Poland)  | SalesRepFirstCreate SalesRepLastCreate  | 200027649902100895  | www.ibexa.co  |
    And Company status is "Active" on Company page
    And I switch to "Company profile" tab in Content structure
    And I should see a Company profile with values
      | Company name             | Tax ID              | Website       | Customer Group                        | Sales Representative                    | Name                     | Email                 | Phone         | Address                          |
      | Ibexa Poland Sp. z o.o.  | 200027649902100895  | www.ibexa.co  | Customer group for corporate account  | SalesRepFirstCreate SalesRepLastCreate  | Ibexa Poland Sp. z o.o.  | info.poland@ibexa.co  | +48327850550  | Ibexa Poland Sp. z o.o., Poland  |

  @IbexaCommerce @javascript
  Scenario: Company can be edited from Companies page
    Given I open "Companies" page in admin SiteAccess
    And I create a user "SalesRepFirstEdit" with last name "SalesRepLastEdit" in group "Sales representatives"
    And Company "Ibexa Poland Sp. z o.o." exists and has "Active" status on Companies page
    When I edit "Ibexa Poland Sp. z o.o." Company
    And I set content fields
      | label                | value                                                           |
      | Name                 | Ibexa AS                                                        |
      | Tax ID               | 709927649902103001                                              |
      | Customer group       | Customer group for corporate account                            |
      | Sales representative | Users/Sales representatives/SalesRepFirstEdit SalesRepLastEdit  |
      | Website              | www.ibexa.no/at                                                 |
    And I set content fields
      | label            | Name      | Country  | First name | Last name | Tax ID             | Region  | City    | Street           | Postal Code  | Email             | Phone        | fieldPosition  |
      | Billing address  | Ibexa AS  | Austria  | Joe        | Doe       | 693327649902123907 | Vienna  | Vienna  | Florianigasse 2  | 0254         | info.at@ibexa.co  | +4735587020  | 9              |
    And I click on the edit action bar button "Save and close"
    Then success notification that "Company 'Ibexa Poland Sp. z o.o.' updated." appears
    And I should see a Company summary with values
      | Company name  | Location          | Sales Representative                | Tax ID              | Website          |
      | Ibexa AS      | Vienna (Austria)  | SalesRepFirstEdit SalesRepLastEdit  | 709927649902103001  | www.ibexa.no/at  |
    And Company status is "Active" on Company page
    And I switch to "Company profile" tab in Content structure
    And I should see a Company profile with values
      | Company name  | Tax ID              | Website          | Customer Group                        | Sales Representative                | Name      | Email             | Phone        | Address            |
      | Ibexa AS      | 709927649902103001  | www.ibexa.no/at  | Customer group for corporate account  | SalesRepFirstEdit SalesRepLastEdit  | Ibexa AS  | info.at@ibexa.co  | +4735587020  | Ibexa AS, Austria  |

  @IbexaCommerce @javascript
  Scenario: Company can be edited from Company page
    Given I open "Ibexa AS" Company page in admin SiteAccess
    And I create a user "SalesRepFirstEditSecond" with last name "SalesRepLastEditSecond" in group "Sales representatives"
    And I should see a Company summary with values
      | Company name  | Location          | Sales Representative                | Tax ID              | Website          |
      | Ibexa AS      | Vienna (Austria)  | SalesRepFirstEdit SalesRepLastEdit  | 709927649902103001  | www.ibexa.no/at  |
    And Company status is "Active" on Company page
    When I click on the edit action bar button "Edit"
    And I set content fields
      | label                | value                                                                       |
      | Name                 | Ibexa AS Australia                                                          |
      | Tax ID               | 123927649902103123                                                          |
      | Website              | www.ibexa.co/au                                                             |
      | Customer group       | Customer group for corporate account                                        |
      | Sales representative | Users/Sales representatives/SalesRepFirstEditSecond SalesRepLastEditSecond  |
    And I set content fields
      | label            | Name             | Country    | First name | Last name | Tax ID             | Region  | City    | Street                             | Postal Code  | Email             | Phone         | fieldPosition  |
      | Billing address  | Ibexa Australia  | Australia  | Tom        | Deer      | 253327645102123380 | Sydney  | Sydney  | 100/104 Boulevard du Montparnasse  | 75014        | info.au@ibexa.co  | +33144931550  | 9              |
    And I click on the edit action bar button "Save and close"
    Then success notification that "Company 'Ibexa AS' updated." appears
    And I should see a Company summary with values
      | Company name        | Location            | Sales Representative                            | Tax ID              | Website          |
      | Ibexa AS Australia  | Sydney (Australia)  | SalesRepFirstEditSecond SalesRepLastEditSecond  | 123927649902103123  | www.ibexa.co/au  |
    And Company status is "Active" on Company page
    And I switch to "Company profile" tab in Content structure
    And I should see a Company profile with values
      | Company name        | Tax ID              | Website          | Customer Group                        | Sales Representative                            | Name             | Email             | Phone         | Address                     |
      | Ibexa AS Australia  | 123927649902103123  | www.ibexa.co/au  | Customer group for corporate account  | SalesRepFirstEditSecond SalesRepLastEditSecond  | Ibexa Australia  | info.au@ibexa.co  | +33144931550  | Ibexa Australia, Australia  |

  @IbexaCommerce @javascript
  Scenario: Company can be deactivated
    Given I open "Companies" page in admin SiteAccess
    And Company "Ibexa AS Australia" exists and has "Active" status on Companies page
    When I deactivate "Ibexa AS Australia" Company
    Then Company "Ibexa AS Australia" exists and has "De-activated" status on Companies page

  @IbexaCommerce @javascript
  Scenario: Company can be activated
    Given I open "Companies" page in admin SiteAccess
    And Company "Ibexa AS Australia" exists and has "De-activated" status on Companies page
    When I activate "Ibexa AS Australia" Company
    Then Company "Ibexa AS Australia" exists and has "Active" status on Companies page

  @IbexaExperience @javascript
  Scenario: Company can be added
    Given I open "Companies" page in admin SiteAccess
    And I click on the edit action bar button "Create"
    And I create a user "SalesRepFirstCreate" with last name "SalesRepLastCreate" in group "Sales representatives"
    And I set content fields
      | label                | value                                                              |
      | Is active            | true                                                               |
      | Name                 | Ibexa Poland Sp. z o.o.                                            |
      | Tax ID               | 200027649902100895                                                 |
      | Website              | www.ibexa.co                                                       |
      | Customer group       | Customer group for corporate account                               |
      | Sales representative | Users/Sales representatives/SalesRepFirstCreate SalesRepLastCreate |
    And I switch to "Billing address" field group
    And I set content fields
      | label            | Name                     | Country  | Region   | City      | Street        | Postal Code  | Email                 | Phone        | fieldPosition |
      | Billing address  | Ibexa Poland Sp. z o.o.  | Poland   | Śląskie  | Katowice  | Gliwicka 6/5  | 40-079       | info.poland@ibexa.co  | +48327850550 | 1             |
    When I click on the edit action bar button "Save and close"
    Then success notification that "Company created." appears
    And Company "Ibexa Poland Sp. z o.o." exists and has "Active" status on Companies page

  @IbexaExperience @javascript
  Scenario: Company can be viewed
    Given I open "Companies" page in admin SiteAccess
    When I view "Ibexa Poland Sp. z o.o." Company
    Then I should see a Company summary with values
      | Company name             | Location           | Sales Representative                    | Tax ID              | Website       |
      | Ibexa Poland Sp. z o.o.  | Katowice (Poland)  | SalesRepFirstCreate SalesRepLastCreate  | 200027649902100895  | www.ibexa.co  |
    And Company status is "Active" on Company page
    And I switch to "Company profile" tab in Content structure
    And I should see a Company profile with values
      | Company name             | Tax ID              | Website       | Customer Group                        | Sales Representative                    | Name                     | Email                 | Phone         | Address                          |
      | Ibexa Poland Sp. z o.o.  | 200027649902100895  | www.ibexa.co  | Customer group for corporate account  | SalesRepFirstCreate SalesRepLastCreate  | Ibexa Poland Sp. z o.o.  | info.poland@ibexa.co  | +48327850550  | Ibexa Poland Sp. z o.o., Poland  |

  @IbexaExperience @javascript
  Scenario: Company can be edited from Companies page
    Given I open "Companies" page in admin SiteAccess
    And I create a user "SalesRepFirstEdit" with last name "SalesRepLastEdit" in group "Sales representatives"
    And Company "Ibexa Poland Sp. z o.o." exists and has "Active" status on Companies page
    When I edit "Ibexa Poland Sp. z o.o." Company
    And I set content fields
      | label                | value                                                           |
      | Name                 | Ibexa AS                                                        |
      | Tax ID               | 709927649902103001                                              |
      | Customer group       | Customer group for corporate account                            |
      | Sales representative | Users/Sales representatives/SalesRepFirstEdit SalesRepLastEdit  |
      | Website              | www.ibexa.no/at                                                 |
    And I set content fields
      | label            | Name      | Country  | Region  | City    | Street           | Postal Code  | Email             | Phone        | fieldPosition  |
      | Billing address  | Ibexa AS  | Austria  | Vienna  | Vienna  | Florianigasse 2  | 0254         | info.at@ibexa.co  | +4735587020  | 9              |
    And I click on the edit action bar button "Save and close"
    Then success notification that "Company 'Ibexa Poland Sp. z o.o.' updated." appears
    And I should see a Company summary with values
      | Company name  | Location          | Sales Representative                | Tax ID              | Website          |
      | Ibexa AS      | Vienna (Austria)  | SalesRepFirstEdit SalesRepLastEdit  | 709927649902103001  | www.ibexa.no/at  |
    And Company status is "Active" on Company page
    And I switch to "Company profile" tab in Content structure
    And I should see a Company profile with values
      | Company name  | Tax ID              | Website          | Customer Group                        | Sales Representative                | Name      | Email             | Phone        | Address            |
      | Ibexa AS      | 709927649902103001  | www.ibexa.no/at  | Customer group for corporate account  | SalesRepFirstEdit SalesRepLastEdit  | Ibexa AS  | info.at@ibexa.co  | +4735587020  | Ibexa AS, Austria  |

  @IbexaExperience @javascript
  Scenario: Company can be edited from Company page
    Given I open "Ibexa AS" Company page in admin SiteAccess
    And I create a user "SalesRepFirstEditSecond" with last name "SalesRepLastEditSecond" in group "Sales representatives"
    And I should see a Company summary with values
      | Company name  | Location          | Sales Representative                | Tax ID              | Website          |
      | Ibexa AS      | Vienna (Austria)  | SalesRepFirstEdit SalesRepLastEdit  | 709927649902103001  | www.ibexa.no/at  |
    And Company status is "Active" on Company page
    When I click on the edit action bar button "Edit"
    And I set content fields
      | label                | value                                                                       |
      | Name                 | Ibexa AS Australia                                                          |
      | Tax ID               | 123927649902103123                                                          |
      | Website              | www.ibexa.co/au                                                             |
      | Customer group       | Customer group for corporate account                                        |
      | Sales representative | Users/Sales representatives/SalesRepFirstEditSecond SalesRepLastEditSecond  |
    And I set content fields
      | label            | Name             | Country    | Region  | City    | Street                             | Postal Code  | Email             | Phone         | fieldPosition  |
      | Billing address  | Ibexa Australia  | Australia  | Sydney  | Sydney  | 100/104 Boulevard du Montparnasse  | 75014        | info.au@ibexa.co  | +33144931550  | 9              |
    And I click on the edit action bar button "Save and close"
    Then success notification that "Company 'Ibexa AS' updated." appears
    And I should see a Company summary with values
      | Company name        | Location            | Sales Representative                            | Tax ID              | Website          |
      | Ibexa AS Australia  | Sydney (Australia)  | SalesRepFirstEditSecond SalesRepLastEditSecond  | 123927649902103123  | www.ibexa.co/au  |
    And Company status is "Active" on Company page
    And I switch to "Company profile" tab in Content structure
    And I should see a Company profile with values
      | Company name        | Tax ID              | Website          | Customer Group                        | Sales Representative                            | Name             | Email             | Phone         | Address                     |
      | Ibexa AS Australia  | 123927649902103123  | www.ibexa.co/au  | Customer group for corporate account  | SalesRepFirstEditSecond SalesRepLastEditSecond  | Ibexa Australia  | info.au@ibexa.co  | +33144931550  | Ibexa Australia, Australia  |

  @IbexaExperience @javascript
  Scenario: Company can be deactivated
    Given I open "Companies" page in admin SiteAccess
    And Company "Ibexa AS Australia" exists and has "Active" status on Companies page
    When I deactivate "Ibexa AS Australia" Company
    Then Company "Ibexa AS Australia" exists and has "De-activated" status on Companies page

  @IbexaExperience @javascript
  Scenario: Company can be activated
    Given I open "Companies" page in admin SiteAccess
    And Company "Ibexa AS Australia" exists and has "De-activated" status on Companies page
    When I activate "Ibexa AS Australia" Company
    Then Company "Ibexa AS Australia" exists and has "Active" status on Companies page
