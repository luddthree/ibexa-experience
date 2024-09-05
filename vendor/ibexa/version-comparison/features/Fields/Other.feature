@javascript @IbexaHeadless @IbexaExperience @IbexaCommerce
Feature:
  As a user
  I want to compare different Versions of the same Content side by side
  So I can easily see what has changed

  Scenario: Create Content Items for the next Scenario
    Given I create "folder" Content items
      | short_name           | parentPath        | language |
      | VersionCompareOther  | root              | eng-GB   |

  Scenario Outline: Compare version with given field - side by side
    Given I create a "<fieldName> Version compare" content type in "Content" with "<fieldTypeIdentifier>VersionCompare" identifier
      | Field Type  | Name  | Identifier            | Settings        |
      | <fieldName> | Field | <fieldTypeIdentifier> | <fieldSettings> |
      | Text line   | Name  | name	                |                 |
    And a "folder" Content item named "VersionCompareOther" exists in root
      | name                | short_name          |
      | VersionCompareOther | VersionCompareOther |
    And I create "<fieldTypeIdentifier>VersionCompare" Content items in VersionCompareOther in "eng-GB"
      | <fieldTypeIdentifier> | name                  |
      | <initialValue>        | <fieldTypeIdentifier> |
    And I edit "VersionCompareOther/<contentName>" Content item in "eng-GB"
      | <fieldTypeIdentifier> |
      | <editedValue>         |
    And I am logged as admin
    When I'm on Side by Side comparison page for "VersionCompareOther/<contentName>" between versions 2 and 1
    Then I should see correct data for the right side comparison
      | fieldName | fieldTypeIdentifier   | value          |
      | Field     | <fieldTypeIdentifier> | <displayedEditedValue> |
    And I should see correct for the left side comparison
      | fieldName | fieldTypeIdentifier   | value           |
      | Field     | <fieldTypeIdentifier> | <displayedValue> |

    Examples:
      | fieldTypeIdentifier | fieldName     | contentName      | initialValue                | editedValue                   | displayedValue              | displayedEditedValue          | fieldSettings                                                     |
      | ezselection         | Selection     | Option1          | 0                           | 2                             | Option1                     | Option3                       | is_multiple:false,options:Option1-Option2-Option3-Option4-Option5 |
      | ezboolean           | Checkbox      | 1                | true                        | false                         | true                        | false                         |                                                                   |
      | ezemail             | Email address | mail-example.com | mail@example.com            | edited@example.com            | mail@example.com            | edited@example.com            |                                                                   |
      | ezfloat             | Float         | 12.34            | 12.34                       | 43.21                         | 12.34                       | 43.21                         |                                                                   |
      | ezisbn              | ISBN          | 0-13-048257-9    | 0-13-048257-9               | 978-3-16-148410-0             | 0-13-048257-9               | 978-3-16-148410-0             |                                                                   |
      | ezinteger           | Integer       | 1234             | 1234                        | 4321                          | 1234                        | 4321                          |                                                                   |
      # | ezkeyword           | Keywords      | ezkeyword        | TestKeyword1                | TestKeyword2                  | TestKeyword1                | TestKeyword2                  |                                                                   | Uncomment after: https://issues.ibexa.co/browse/IBX-3857
      | ezmatrix            | Matrix        | ezmatrix         | col1:col2,11:12,21:22,31:32 | col1:col2,e11:e12,21:22,31:32 | col1:col2,11:12,21:22,31:32 | col1:col2,e11:e12,21:22,31:32 | Min_rows:2,Columns:col1-col2                                      |

  Scenario Outline: Compare version with given field - single column
    Given I am logged as admin
    When I'm on single column comparison page for "VersionCompareOther/<contentName>" between versions 2 and 1
    Then I should see correct data added
      | fieldName | fieldTypeIdentifier   | valueAdded   |
      | Field     | <fieldTypeIdentifier> | <valueAdded> |
    And I should see correct data removed
      | fieldName | fieldTypeIdentifier   | valueRemoved   |
      | Field     | <fieldTypeIdentifier> | <valueRemoved> |

    Examples:
      | fieldTypeIdentifier | contentName      | valueRemoved      | valueAdded         |
      | ezselection         | Option1          | Option1           | Option3            |
      | ezboolean           | 1                | Yes               | No                 |
      | ezemail             | mail-example.com | mail@example.com  | edited@example.com |
      | ezfloat             | 12.34            | 12.34             | 43.21              |
#      | ezisbn              | 0-13-048257-9    | 0-13-048257-9     | 978-3-16-148410-0  | Uncomment after: https://issues.ibexa.co/browse/IBX-3858
      | ezinteger           | 1234             | 1234              | 4321               |
#      | ezkeyword           | ezkeyword        | TestKeyword1                | TestKeyword2                   | Uncomment after: https://issues.ibexa.co/browse/IBX-3857
      | ezmatrix            | ezmatrix         | 11 12             | e11 e12            |
