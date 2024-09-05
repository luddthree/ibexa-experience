@javascript @IbexaHeadless @IbexaExperience @IbexaCommerce
Feature:
  As a user
  I want to compare different Versions of the same Content side by side
  So I can easily see what has changed

  Scenario Outline: Compare version with given field - side by side
    Given I create a "<fieldName> Version compare" content type in "Content" with "<fieldTypeIdentifier>VersionCompare" identifier
      | Field Type  | Name  | Identifier            |
      | <fieldName> | Field | <fieldTypeIdentifier> |
      | Text line   | Name  | name	                |
    And a "folder" Content item named "VersionCompareRelations" exists in root
      | name                    | short_name              |
      | VersionCompareRelations | VersionCompareRelations |
    And I create "<fieldTypeIdentifier>VersionCompare" Content items in VersionCompareRelations in "eng-GB"
      | <fieldTypeIdentifier> | name                  |
      | <initialValue>        | <fieldTypeIdentifier> |
    And I edit "VersionCompareRelations/<contentName>" Content item in "eng-GB"
      | <fieldTypeIdentifier> |
      | <editedValue>         |
    And I am logged as admin
    When I'm on Side by Side comparison page for "VersionCompareRelations/<contentName>" between versions 2 and 1
    Then I should see correct data for the right side comparison
      | fieldName | fieldTypeIdentifier   | <label1>        | <label2>      |
      | Field     | <fieldTypeIdentifier> | <editedValue1> | <editedValue2> |
    And I should see correct for the left side comparison
      | fieldName | fieldTypeIdentifier   | <label1>        | <label2>        |
      | Field     | <fieldTypeIdentifier> | <initialValue1> | <initialValue2> |

    Examples:
      | fieldTypeIdentifier  | fieldName                    | contentName  | initialValue       | editedValue       | label1    | initialValue1 | label2     | initialValue2 | editedValue1 | editedValue2 |
      | ezobjectrelation     | Content relation (single)    | Images       | Media/Images       | Media             | value     | Media/Images  |            |               | Media        |              |
      | ezobjectrelationlist | Content relations (multiple) | Media-Images | Media,Media/Images | Media,Media/Files | firstItem | Media         | secondItem | Media/Images  | Media        | Media/Files  |

  Scenario Outline: Compare version with given field - single column
    Given I am logged as admin
    When I'm on single column comparison page for "VersionCompareRelations/<contentName>" between versions 2 and 1
    Then I should see correct data added
      | fieldName | fieldTypeIdentifier   | valueAdded   |
      | Field     | <fieldTypeIdentifier> | <valueAdded> |
    And I should see correct data removed
      | fieldName | fieldTypeIdentifier   | valueRemoved   |
      | Field     | <fieldTypeIdentifier> | <valueRemoved> |

    Examples:
      | fieldTypeIdentifier  | contentName  | valueRemoved       | valueAdded   |
      | ezobjectrelation     | Images       | Images Folder      | Media Folder |
      | ezobjectrelationlist | Media-Images | Images Folder      | Files Folder |
