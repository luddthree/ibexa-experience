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
    And a "folder" Content item named "VersionCompareMedia" exists in root
      | name                | short_name          |
      | VersionCompareMedia | VersionCompareMedia |
    And a "image" Content item named "VersionCompareImageAsset1" exists in "/Media/Images/"
      | name                      | image                                              |
      | VersionCompareImageAsset1  | vendor/ibexa/behat/src/lib/Data/Images/small1.jpg |
    And a "image" Content item named "VersionCompareImageAsset2" exists in "/Media/Images/"
      | name                      | image                                              |
      | VersionCompareImageAsset2  | vendor/ibexa/behat/src/lib/Data/Images/small2.jpg |
    And I create "<fieldTypeIdentifier>VersionCompare" Content items in VersionCompareMedia in "eng-GB"
      | <fieldTypeIdentifier> | name                  |
      | <initialValue>        | <fieldTypeIdentifier> |
    And I edit "VersionCompareMedia/<contentName>" Content item in "eng-GB"
      | <fieldTypeIdentifier> |
      | <editedValue>         |
    And I am logged as admin
    When I'm on Side by Side comparison page for "VersionCompareMedia/<contentName>" between versions 2 and 1
    Then I should see correct data for the right side comparison
      | fieldName | fieldTypeIdentifier   | value          |
      | Field     | <fieldTypeIdentifier> | <displayedEditedValue> |
    And I should see correct for the left side comparison
      | fieldName | fieldTypeIdentifier   | value           |
      | Field     | <fieldTypeIdentifier> | <displayedValue> |

    Examples:
      | fieldTypeIdentifier | fieldName   | contentName               | initialValue                                      | editedValue                                       | displayedValue | displayedEditedValue |
      | ezimage             | Image       | small1.jpg                | vendor/ibexa/behat/src/lib/Data/Images/small1.jpg | vendor/ibexa/behat/src/lib/Data/Images/small2.jpg | small1.jpg     | small2.jpg           |
      | ezbinaryfile        | File        | file1.txt                 | vendor/ibexa/behat/src/lib/Data/Files/file1.txt   | vendor/ibexa/behat/src/lib/Data/Files/file2.txt   | file1.txt      | file2.txt            |
      | ezmedia             | Media       | video1.mp4                | vendor/ibexa/behat/src/lib/Data/Videos/video1.mp4 | vendor/ibexa/behat/src/lib/Data/Videos/video2.mp4 | video1.mp4     | video2.mp4           |
      | ezimageasset        | Image Asset | VersionCompareImageAsset1 | /Media/Images/VersionCompareImageAsset1           | /Media/Images/VersionCompareImageAsset2           | small1.jpg     | small2.jpg           |

  Scenario Outline: Compare version with given field - single column
    Given I am logged as admin
    When I'm on single column comparison page for "VersionCompareMedia/<contentName>" between versions 2 and 1
    Then I should see correct data added
      | fieldName | fieldTypeIdentifier   | valueAdded   |
      | Field     | <fieldTypeIdentifier> | <valueAdded> |
    And I should see correct data removed
      | fieldName | fieldTypeIdentifier   | valueRemoved   |
      | Field     | <fieldTypeIdentifier> | <valueRemoved> |

    Examples:
      | fieldTypeIdentifier | contentName               | valueRemoved | valueAdded  |
      | ezimage             | small1.jpg                | small1.jpg   | small2.jpg  |
      | ezbinaryfile        | file1.txt                 | file1.txt    | file2.txt   |
      | ezmedia             | video1.mp4                | video1.mp4   | video2.mp4  |
      | ezimageasset        | VersionCompareImageAsset1 | small1.jpg   | small2.jpg  |
