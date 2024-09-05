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
    And a "folder" Content item named "VersionCompareDate" exists in root
      | name               | short_name         |
      | VersionCompareDate | VersionCompareDate |
    And I create "<fieldTypeIdentifier>VersionCompare" Content items in VersionCompareDate in "eng-GB"
      | <fieldTypeIdentifier> | name                  |
      | <initialValue>        | <fieldTypeIdentifier> |
    And I edit "VersionCompareDate/<contentName>" Content item in "eng-GB"
      | <fieldTypeIdentifier> |
      | <editedValue>         |
    And I am logged as admin
    When I'm on Side by Side comparison page for "VersionCompareDate/<contentName>" between versions 2 and 1
    Then I should see correct data for the right side comparison
      | fieldName | fieldTypeIdentifier   | <label1>        | <label2>      |
      | Field     | <fieldTypeIdentifier> | <editedValue1> | <editedValue2> |
    And I should see correct for the left side comparison
      | fieldName | fieldTypeIdentifier   | <label1>        | <label2>        |
      | Field     | <fieldTypeIdentifier> | <initialValue1> | <initialValue2> |

    Examples:
      | fieldTypeIdentifier | fieldName     | contentName               | initialValue        | editedValue         | label1 | initialValue1 | label2 | initialValue2 | editedValue1 | editedValue2 |
      | ezdate              | Date          | Saturday 23 November 2019 | 2019-11-23          | 2018-12-31          | value  | 2019-11-23    |        |               | 2018-12-31   |              |
      | ezdatetime          | Date and time | Sat 2019-23-11 14:45:00   | 2019-11-23 14:45:00 | 2018-12-31 13:55:00 | date   | 2019-11-23    | time   | 14:45:00      | 2018-12-31   | 13:55:00     |
      | eztime              | Time          | 2:45:00 pm                | 14:45:00            | 13:55:00            | value  | 14:45:00      |        |               | 13:55:00     |              |

  Scenario Outline: Compare version with given field - single column
    Given I am logged as admin
    When I'm on single column comparison page for "VersionCompareDate/<contentName>" between versions 2 and 1
    Then I should see correct data added
      | fieldName | fieldTypeIdentifier   | valueAdded   |
      | Field     | <fieldTypeIdentifier> | <valueAdded> |
    And I should see correct data removed
      | fieldName | fieldTypeIdentifier   | valueRemoved   |
      | Field     | <fieldTypeIdentifier> | <valueRemoved> |

    Examples:
      | fieldTypeIdentifier | contentName               | valueRemoved            | valueAdded              |
      | ezdate              | Saturday 23 November 2019 | November 23, 2019       | December 31, 2018       |
      | ezdatetime          | Sat 2019-23-11 14:45:00   | November 23, 2019 14:45 | December 31, 2018 13:55 |
      | eztime              | 2:45:00 pm                | 14:45                   | 13:55                   |
