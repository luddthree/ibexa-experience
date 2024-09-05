@javascript @IbexaHeadless @IbexaExperience @IbexaCommerce
Feature:
  As a user
  I want to compare different Versions of the same Content in a single column
  So I can easily see what has changed

  Background:
    Given I am logged as admin

  Scenario: Enter Version Comparison view for Archived version
    Given I create "folder" Content items in root in "eng-GB"
      | name                          | short_name                    |
      | VersionComparisonSingleColumn | VersionComparisonSingleColumn |
    And I edit "root/VersionComparisonSingleColumn" Content item in "eng-GB"
      | name                                |
      | VersionComparisonSingleColumnEdited |
    And I'm on Content view Page for "root/VersionComparisonSingleColumn"
    And I am using the DXP with Focus mode disabled
    And I switch to "Versions" tab in Content structure
    When I enter version compare for version 1 from "Archived versions"
    And I switch to single column view
    And I should be on Single Column Version compare page with "Version 1" on the left side
    And I select "Version 2" for the right side of the comparison
    And I should be on Single Column Version compare page with "Version 2" on the right side and "Version 1" on the left side
    Then I should see correct data added
      | fieldName | fieldTypeIdentifier | valueAdded                          |
      | Name      | ezstring            | VersionComparisonSingleColumnEdited |
    And I should see correct data removed
      | fieldName | fieldTypeIdentifier | valueRemoved                  |
      | Name      | ezstring            | VersionComparisonSingleColumn |

  Scenario: Enter Version Comparison view for Published version
    Given I create "folder" Content items in VersionComparisonSingleColumn in "eng-GB"
      | name                                 | short_name                           |
      | VersionComparisonSingleColumnPublished | VersionComparisonSingleColumnPublished |
    And I edit "root/VersionComparisonSingleColumn/VersionComparisonSingleColumnPublished" Content item in "eng-GB"
      | name                                       |
      | VersionComparisonSingleColumnPublishedEdited |
    And I'm on Content view Page for "root/VersionComparisonSingleColumn/VersionComparisonSingleColumnPublished"
    And I am using the DXP with Focus mode disabled
    And I switch to "Versions" tab in Content structure
    When I enter version compare for version 2 from "Published version"
    And I switch to single column view
    And I should be on Single Column Version compare page with "Version 2" on the left side
    And I select "Version 1" for the right side of the comparison
    And I should be on Single Column Version compare page with "Version 1" on the right side and "Version 2" on the left side
    Then I should see correct data added
      | fieldName | fieldTypeIdentifier | valueAdded                             |
      | Name      | ezstring            | VersionComparisonSingleColumnPublished |
    And I should see correct data removed
      | fieldName | fieldTypeIdentifier | valueRemoved                                 |
      | Name      | ezstring            | VersionComparisonSingleColumnPublishedEdited |

  Scenario: Enter Version Comparison view for Draft version
    Given I create "folder" Content items in VersionComparisonSingleColumn in "eng-GB"
      | name                               | short_name                         |
      | VersionComparisonSingleColumnDraft | VersionComparisonSingleColumnDraft |
    And I create a new Draft for "/root/VersionComparisonSingleColumn/VersionComparisonSingleColumnDraft" Content item in "eng-GB"
      | name                                     |
      | VersionComparisonSingleColumnDraftEdited |
    And I'm on Content view Page for "root/VersionComparisonSingleColumn/VersionComparisonSingleColumnDraft"
    And I am using the DXP with Focus mode disabled
    And I switch to "Versions" tab in Content structure
    When I enter version compare for version 2 from "Open drafts"
    And I switch to single column view
    And I should be on Single Column Version compare page with "Version 2" on the left side
    And I select "Version 1" for the right side of the comparison
    And I should be on Single Column Version compare page with "Version 1" on the right side and "Version 2" on the left side
    Then I should see correct data added
      | fieldName | fieldTypeIdentifier | valueAdded                         |
      | Name      | ezstring            | VersionComparisonSingleColumnDraft |
    And I should see correct data removed
      | fieldName | fieldTypeIdentifier | valueRemoved                             |
      | Name      | ezstring            | VersionComparisonSingleColumnDraftEdited |

  Scenario: Switch left side comparison to a different version
    Given I create "folder" Content items in VersionComparisonSingleColumn in "eng-GB"
      | name                                      | short_name                                |
      | VersionComparisonSingleColumnVersion1Base | VersionComparisonSingleColumnVersion1Base |
    And I edit "root/VersionComparisonSingleColumn/VersionComparisonSingleColumnVersion1Base" Content item in "eng-GB"
      | name                                           |
      | VersionComparisonSingleColumnVersion2Published |
    And I create a new Draft for "/root/VersionComparisonSingleColumn/VersionComparisonSingleColumnVersion1Base" Content item in "eng-GB"
      | name                                       |
      | VersionComparisonSingleColumnVersion3Draft |
    And I'm on Content view Page for "root/VersionComparisonSingleColumn/VersionComparisonSingleColumnVersion1Base"
    And I am using the DXP with Focus mode disabled
    And I switch to "Versions" tab in Content structure
    And I enter version compare for version 2 from "Published version"
    And I switch to single column view
    And I should be on Single Column Version compare page with "Version 2" on the left side
    And I select "Version 1" for the right side of the comparison
    And I should be on Single Column Version compare page with "Version 1" on the right side and "Version 2" on the left side
    And I should see correct data added
      | fieldName | fieldTypeIdentifier | valueAdded                                |
      | Name      | ezstring            | VersionComparisonSingleColumnVersion1Base |
    And I should see correct data removed
      | fieldName | fieldTypeIdentifier | valueRemoved                                   |
      | Name      | ezstring            | VersionComparisonSingleColumnVersion2Published |
    When I select "Version 3" for the left side of the comparison
    And I should be on Single Column Version compare page with "Version 1" on the right side and "Version 3" on the left side
    And I select "Version 2" for the right side of the comparison
    Then I should be on Single Column Version compare page with "Version 2" on the right side and "Version 3" on the left side
    And I should see correct data added
      | fieldName | fieldTypeIdentifier | valueAdded                                     |
      | Name      | ezstring            | VersionComparisonSingleColumnVersion2Published |
    And I should see correct data removed
      | fieldName | fieldTypeIdentifier | valueRemoved                               |
      | Name      | ezstring            | VersionComparisonSingleColumnVersion3Draft |
