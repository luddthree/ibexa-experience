@javascript @IbexaHeadless @IbexaExperience @IbexaCommerce
Feature:
  As a user
  I want to compare different Versions of the same Content side by side
  So I can easily see what has changed

  Scenario Outline: Compare version with noneditable Content Query field
   Given I create a "<fieldName> Version compare" content type in "Content" with "<fieldTypeIdentifier>VersionCompare" identifier
     | Field Type  | Name  | Identifier            | Settings        |
     | <fieldName> | Field | <fieldTypeIdentifier> | <fieldSettings> |
     | Text line   | Name  | name	                |                 |
    And a "folder" Content item named "VersionCompareContentQuery" exists in root
      | name                       | short_name                |
      | VersionCompareContentQuery | VersionCompareContentQuery|
   And I create "<fieldTypeIdentifier>VersionCompare" Content items in VersionCompareContentQuery in "eng-GB"
     | name           |
     | <initialValue> |
   And I edit "VersionCompareContentQuery/<initialValue>" Content item in "eng-GB"
     | name          |
     | <editedValue> |
    And I am logged as admin
    When I'm on Side by Side comparison page for "VersionCompareContentQuery/<initialValue>" between versions 2 and 1
    Then I should see correct data for the right side comparison
      | fieldName | fieldTypeIdentifier | value         |
      | Name      | ezstring            | <editedValue> |
    And I should see correct for the left side comparison
      | fieldName | fieldTypeIdentifier | value          |
      | Name      | ezstring            | <initialValue> |

    Examples:
      | fieldTypeIdentifier | fieldName     | initialValue | editedValue         | fieldSettings                                                                                                   |
      | ezcontentquery      | Content query | ContentQuery | Content QueryEdited | QueryType-Folders under media,ContentType-folder,ItemsPerPage-100,Parameters-contentTypeId:folder;locationId:43 |

  Scenario Outline: Compare version with given field - single column
    Given I am logged as admin
    When I'm on single column comparison page for "VersionCompareContentQuery/<valueAdded>" between versions 2 and 1
    Then I should see correct data added
      | fieldName | fieldTypeIdentifier   | valueAdded   |
      | Name      | <fieldTypeIdentifier> | <valueAdded> |
    And I should see correct data removed
      | fieldName | fieldTypeIdentifier   | valueRemoved   |
      | Name      | <fieldTypeIdentifier> | <valueRemoved> |

    Examples:
      | fieldTypeIdentifier | valueRemoved | valueAdded          |
      | ezstring            | ContentQuery | Content QueryEdited |
