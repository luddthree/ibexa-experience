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
    And a "folder" Content item named "VersionCompareText" exists in root
      | name               | short_name         |
      | VersionCompareText | VersionCompareText |
    And I create "<fieldTypeIdentifier>VersionCompare" Content items in VersionCompareText in "eng-GB"
      | <fieldTypeIdentifier> | name                  |
      | <initialValue>        | <fieldTypeIdentifier> |
    And I edit "VersionCompareText/<contentName>" Content item in "eng-GB"
      | <fieldTypeIdentifier> |
      | <editedValue>         |
    And I am logged as admin
    When I'm on Side by Side comparison page for "VersionCompareText/<contentName>" between versions 2 and 1
    Then I should see correct data for the right side comparison
      | fieldName | fieldTypeIdentifier   | <label1>        | <label2>      |
      | Field     | <fieldTypeIdentifier> | <editedValue1> | <editedValue2> |
    And I should see correct for the left side comparison
      | fieldName | fieldTypeIdentifier   | <label1>        | <label2>        |
      | Field     | <fieldTypeIdentifier> | <initialValue1> | <initialValue2> |

    Examples:
      | fieldTypeIdentifier | fieldName   | contentName   | initialValue                 | editedValue                      | label1 | initialValue1 | label2 | initialValue2       | editedValue1        | editedValue2        |
      | ezstring            | Text line   | StringValue   | StringValue                  | StringValueEdited                | value  | StringValue   |        |                     | StringValueEdited   |                     |
      | ezauthor            | Authors     | AuthorName    | AuthorName,nospam@ibexa.no   | AuthorNameEdited,nospam@ibexa.co | name   | AuthorName    | email  | nospam@ibexa.no     | AuthorNameEdited    | nospam@ibexa.co     |
      | ezrichtext          | Rich text   | RichtextValue | RichtextValue                | RichtextValueEdited              | value  | RichtextValue |        |                     | RichtextValueEdited |                     |
      | eztext              | Text block  | TextValue     | TextValue                    | TextValueEdited                  | value  | TextValue     |        |                     | TextValueEdited     |                     |
      | ezurl               | URL         | IbexaDXP      | http://www.ibexa.no,IbexaDXP | http://www.ibexa.co,Ibexa DXP    | text   | IbexaDXP      | url    | http://www.ibexa.no | Ibexa DXP           | http://www.ibexa.co |

  Scenario Outline: Compare version with given field - single column
      Given I am logged as admin
      When I'm on single column comparison page for "VersionCompareText/<contentName>" between versions 2 and 1
      Then I should see correct data added
        | fieldName | fieldTypeIdentifier   | valueAdded   |
        | Field     | <fieldTypeIdentifier> | <valueAdded> |
     And I should see correct data removed
       | fieldName | fieldTypeIdentifier   | valueRemoved   |
       | Field     | <fieldTypeIdentifier> | <valueRemoved> |

      Examples:
        | fieldTypeIdentifier | contentName   | valueRemoved                 | valueAdded                       |
        | ezstring            | StringValue   | StringValue                  | StringValueEdited                |
        | ezauthor            | AuthorName    | AuthorName nospam@ibexa.no   | AuthorNameEdited nospam@ibexa.co |
        | ezrichtext          | RichtextValue | RichtextValue                | RichtextValueEdited              |
        | eztext              | TextValue     | TextValue                    | TextValueEdited                  |
        | ezurl               | IbexaDXP      | http://www.ibexa.no IbexaDXP | http://www.ibexa.co Ibexa DXP    |
