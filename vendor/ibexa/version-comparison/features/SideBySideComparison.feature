@javascript @IbexaHeadless @IbexaExperience @IbexaCommerce
Feature:
  As a user
  I want to compare different Versions of the same Content side by side
  So I can easily see what has changed

  Background:
    Given I am logged as admin

  Scenario: Enter Version Comparison view for Published version
   Given I create "folder" Content items in root in "eng-GB"
     | name                        | short_name                  |
     | VersionComparisonSideBySide | VersionComparisonSideBySide |
   And I edit "root/VersionComparisonSideBySide" Content item in "eng-GB"
     | name                              |
     | VersionComparisonSideBySideEdited |
    And I'm on Content view Page for "root/VersionComparisonSideBySide"
    And I am using the DXP with Focus mode disabled
    And I switch to "Versions" tab in Content structure
    When I enter version compare for version 2 from "Published version"
    And I should be on Side by Side Version compare page with "Version 2" on the left side
    And I select "Version 1" for the right side of the comparison
    And I should be on Side by Side Version compare page with "Version 1" on the right side and "Version 2" on the left side
    Then I should see correct data for the right side comparison
      | fieldName | fieldTypeIdentifier | value                       |
      | Name      | ezstring            | VersionComparisonSideBySide |
    And I should see correct for the left side comparison
      | fieldName | fieldTypeIdentifier | value                             |
      | Name      | ezstring            | VersionComparisonSideBySideEdited |

  Scenario: Enter Version Comparison view for Archived version
   Given I create "folder" Content items in VersionComparisonSideBySide in "eng-GB"
     | name                               | short_name                         |
     | VersionComparisonSideBySideArchive | VersionComparisonSideBySideArchive |
   And I edit "root/VersionComparisonSideBySide/VersionComparisonSideBySideArchive" Content item in "eng-GB"
     | name                                     |
     | VersionComparisonSideBySideArchiveEdited |
    And I'm on Content view Page for "root/VersionComparisonSideBySide/VersionComparisonSideBySideArchive"
    And I am using the DXP with Focus mode disabled
    And I switch to "Versions" tab in Content structure
    When I enter version compare for version 1 from "Archived versions"
    And I should be on Side by Side Version compare page with "Version 1" on the left side
    And I select "Version 2" for the right side of the comparison
    And I should be on Side by Side Version compare page with "Version 2" on the right side and "Version 1" on the left side
    Then I should see correct data for the right side comparison
      | fieldName | fieldTypeIdentifier | value                                    |
      | Name      | ezstring            | VersionComparisonSideBySideArchiveEdited |
    And I should see correct for the left side comparison
      | fieldName | fieldTypeIdentifier | value                              |
      | Name      | ezstring            | VersionComparisonSideBySideArchive |

  Scenario: Enter Version Comparison view for Draft version
    Given I create "folder" Content items in VersionComparisonSideBySide in "eng-GB"
      | name                             | short_name                       |
      | VersionComparisonSideBySideDraft | VersionComparisonSideBySideDraft |
    And I create a new Draft for "/root/VersionComparisonSideBySide/VersionComparisonSideBySideDraft" Content item in "eng-GB"
      | name                                   |
      | VersionComparisonSideBySideDraftEdited |
    And I'm on Content view Page for "root/VersionComparisonSideBySide/VersionComparisonSideBySideDraft"
    And I am using the DXP with Focus mode disabled
    And I switch to "Versions" tab in Content structure
    When I enter version compare for version 2 from "Open drafts"
    And I should be on Side by Side Version compare page with "Version 2" on the left side
    And I select "Version 1" for the right side of the comparison
    And I should be on Side by Side Version compare page with "Version 1" on the right side and "Version 2" on the left side
    Then I should see correct data for the right side comparison
      | fieldName | fieldTypeIdentifier | value                            |
      | Name      | ezstring            | VersionComparisonSideBySideDraft |
    And I should see correct for the left side comparison
      | fieldName | fieldTypeIdentifier | value                                  |
      | Name      | ezstring            | VersionComparisonSideBySideDraftEdited |

  Scenario: Switch right side comparison to a different version
    Given I create "folder" Content items in VersionComparisonSideBySide in "eng-GB"
      | name                                    | short_name                              |
      | VersionComparisonSideBySideVersion1Base | VersionComparisonSideBySideVersion1Base |
    And I edit "root/VersionComparisonSideBySide/VersionComparisonSideBySideVersion1Base" Content item in "eng-GB"
      | name                                         |
      | VersionComparisonSideBySideVersion2Published |
    And I create a new Draft for "/root/VersionComparisonSideBySide/VersionComparisonSideBySideVersion1Base" Content item in "eng-GB"
      | name                                     |
      | VersionComparisonSideBySideVersion3Draft |
    And I'm on Content view Page for "root/VersionComparisonSideBySide/VersionComparisonSideBySideVersion1Base"
    And I am using the DXP with Focus mode disabled
    And I switch to "Versions" tab in Content structure
    And I enter version compare for version 2 from "Published version"
    And I should be on Side by Side Version compare page with "Version 2" on the left side
    And I select "Version 1" for the right side of the comparison
    And I should be on Side by Side Version compare page with "Version 1" on the right side and "Version 2" on the left side
    And I should see correct data for the right side comparison
      | fieldName | fieldTypeIdentifier | value                                   |
      | Name      | ezstring            | VersionComparisonSideBySideVersion1Base |
    And I should see correct for the left side comparison
      | fieldName | fieldTypeIdentifier | value                                        |
      | Name      | ezstring            | VersionComparisonSideBySideVersion2Published |
    When I select "Version 3" for the left side of the comparison
    And I should be on Side by Side Version compare page with "Version 1" on the right side and "Version 3" on the left side
    When I select "Version 2" for the right side of the comparison
    Then I should be on Side by Side Version compare page with "Version 2" on the right side and "Version 3" on the left side
    And I should see correct data for the right side comparison
      | fieldName | fieldTypeIdentifier | value                                        |
      | Name      | ezstring            | VersionComparisonSideBySideVersion2Published |
    And I should see correct for the left side comparison
      | fieldName | fieldTypeIdentifier | value                                    |
      | Name      | ezstring            | VersionComparisonSideBySideVersion3Draft |
