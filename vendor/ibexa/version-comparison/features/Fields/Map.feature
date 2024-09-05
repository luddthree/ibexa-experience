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
    And a "folder" Content item named "VersionCompareMap" exists in root
      | name              | short_name        |
      | VersionCompareMap | VersionCompareMap |
    And I create "<fieldTypeIdentifier>VersionCompare" Content items in VersionCompareMap in "eng-GB"
      | <fieldTypeIdentifier> | name                  |
      | <initialValue>        | <fieldTypeIdentifier> |
    And I edit "VersionCompareMap/<contentName>" Content item in "eng-GB"
      | <fieldTypeIdentifier> |
      | <editedValue>         |
    And I am logged as admin
    When I'm on Side by Side comparison page for "VersionCompareMap/<contentName>" between versions 2 and 1
    Then I should see correct data for the right side comparison
      | fieldName | fieldTypeIdentifier   | <label1>        | <label2>      | <label3>       |
      | Field     | <fieldTypeIdentifier> | <editedValue1> | <editedValue2> | <editedValue3> |
    And I should see correct for the left side comparison
      | fieldName | fieldTypeIdentifier   | <label1>        | <label2>        | <label3>        |
      | Field     | <fieldTypeIdentifier> | <initialValue1> | <initialValue2> | <initialValue3> |

    Examples:
      | fieldTypeIdentifier | fieldName    | contentName                     | initialValue | editedValue | label1   | initialValue1 | label2    | initialValue2   | label3  | initialValue3                      | editedValue1 | editedValue2 | editedValue3                 |
      | ezgmaplocation      | Map location | toranomon-minato-ku-tokio-japan | Tokio        | Katowice    | latitude | 35.7          | longitude | 139.7           | address | Toranomon, Minato-ku, Tokio, Japan | 50.3         | 19.0         | Gliwicka 6, Katowice, Poland |
      | ezcountry           | Country      | Belgium                         | BE           | FR          | value    | Belgium       |           |                 |         |               | France       |              |                              |

  Scenario Outline: Compare version with given field - single column
    Given I am logged as admin
    When I'm on single column comparison page for "VersionCompareMap/<contentName>" between versions 2 and 1
    Then I should see correct data added
      | fieldName | fieldTypeIdentifier   | valueAdded   |
      | Field     | <fieldTypeIdentifier> | <valueAdded> |
    And I should see correct data removed
      | fieldName | fieldTypeIdentifier   | valueRemoved   |
      | Field     | <fieldTypeIdentifier> | <valueRemoved> |

    Examples:
      | fieldTypeIdentifier | contentName                     | valueRemoved                                          | valueAdded                                     |
      | ezgmaplocation      | toranomon-minato-ku-tokio-japan | Toranomon, Minato-ku, Tokio, Japan 35.67048 139.74931 | Gliwicka 6, Katowice, Poland 50.26045 19.01125 |
      | ezcountry           | Belgium                         | Belgium                                               | France                                         |
